<?php

namespace App\Http\Controllers\Api\Shopping;

use App\Enum\Common\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Shopping\ShoppingListCheckRequest;
use App\Http\Requests\Api\Shopping\ShoppingListGenerateRequest;
use App\Http\Requests\Api\Shopping\ShoppingListShowRequest;
use App\Http\Resources\Api\Shopping\ShoppingListShowResource;
use App\Models\Plan\MealPlan;
use App\Models\Plan\MealPlanItem;
use App\Models\Shopping\ShoppingList;
use App\Models\Shopping\ShoppingListItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ShoppingListController extends Controller
{
    public function show(ShoppingListShowRequest $request): JsonResponse
    {
        $weekStart = Carbon::parse($request->validated('week'))->startOfWeek()->toDateString();
        $shoppingList = ShoppingList::query()
            ->with('items', 'items.ingredient')
            ->where('user_id', $request->user()->id)
            ->whereDate('week_date', $weekStart)
            ->first();

        if ($shoppingList === null) {
            return $this->success(data: [], message: __('responses.api.shopping.shopping_list.show'));
        }

        return $this->collection(new ShoppingListShowResource($shoppingList), __('responses.api.shopping.shopping_list.show'));
    }

    public function generate(ShoppingListGenerateRequest $request): JsonResponse
    {
        $weekStart = Carbon::parse($request->validated('week_start_date'))->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();
        $userId = $request->user()->id;

        $shoppingList = DB::transaction(function () use ($weekStart, $weekEnd, $userId): ShoppingList {
            DB::table('users')->where('id', $userId)->lockForUpdate()->first();

            $mealPlans = MealPlan::query()
                ->where('user_id', $userId)
                ->where('status', StatusEnum::ACTIVE->value)
                ->whereBetween('week_start_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->get();

            $mealPlanItems = MealPlanItem::query()
                ->whereIn('meal_plan_id', $mealPlans->modelKeys())
                ->where('status', StatusEnum::ACTIVE->value)
                ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->with([
                    'recipe.recipeIngredients' => fn ($query) => $query->where('status', StatusEnum::ACTIVE->value),
                    'recipe.recipeIngredients.ingredient',
                    'recipe.recipeIngredients.unit',
                ])
                ->get();

            $aggregatedIngredients = [];

            foreach ($mealPlanItems as $mealPlanItem) {
                foreach ($mealPlanItem->recipe->recipeIngredients as $recipeIngredient) {
                    $ingredient = $recipeIngredient->ingredient;

                    if ($ingredient === null) {
                        continue;
                    }

                    $unit = $recipeIngredient->unit?->symbol;
                    $key = $this->shoppingListItemKey($ingredient->id, $unit);

                    $aggregatedIngredients[$key] ??= [
                        'ingredient_id' => $ingredient->id,
                        'name' => $ingredient->name,
                        'unit' => $unit,
                        'quantity_hundredths' => 0,
                        'has_quantity' => false,
                    ];

                    if ($recipeIngredient->quantity !== null) {
                        $aggregatedIngredients[$key]['quantity_hundredths'] += (int) round((float) $recipeIngredient->quantity * 100);
                        $aggregatedIngredients[$key]['has_quantity'] = true;
                    }
                }
            }

            $shoppingList = ShoppingList::query()->firstOrNew(
                [
                    'user_id' => $userId,
                    'week_date' => $weekStart->toDateString(),
                ],
            );
            $shoppingList->fill([
                'meal_plan_id' => $mealPlans->first()?->id,
                'name' => 'Shopping List',
                'status' => StatusEnum::ACTIVE->value,
            ]);
            $shoppingList->save();

            $existingRecipeItems = $shoppingList->items()
                ->whereNotNull('ingredient_id')
                ->get()
                ->keyBy(fn (ShoppingListItem $item): string => $this->shoppingListItemKey($item->ingredient_id, $item->unit));

            foreach ($aggregatedIngredients as $key => $ingredientData) {
                $attributes = [
                    'ingredient_id' => $ingredientData['ingredient_id'],
                    'name' => $ingredientData['name'],
                    'quantity' => $ingredientData['has_quantity']
                        ? number_format($ingredientData['quantity_hundredths'] / 100, 2, '.', '')
                        : null,
                    'unit' => $ingredientData['unit'],
                    'status' => StatusEnum::ACTIVE->value,
                ];
                $shoppingListItem = $existingRecipeItems->get($key);

                if ($shoppingListItem) {
                    $shoppingListItem->update($attributes);

                    continue;
                }

                $shoppingList->items()->create($attributes + ['is_completed' => false]);
            }

            $staleItemIds = $existingRecipeItems
                ->reject(fn (ShoppingListItem $item, string $key): bool => array_key_exists($key, $aggregatedIngredients))
                ->modelKeys();

            if ($staleItemIds !== []) {
                $shoppingList->items()->whereIn('id', $staleItemIds)->delete();
            }

            return $shoppingList->load('items');
        });

        return $this->resource(new ShoppingListShowResource($shoppingList), __('responses.api.shopping.shopping_list.show'));
    }

    public function check(ShoppingListCheckRequest $request): JsonResponse
    {
        $items = $request->validated('items');
        $userId = $request->user()->id;

        DB::transaction(function () use ($items, $userId): void {
            $shoppingListItems = ShoppingListItem::query()
                ->whereIn('id', array_column($items, 'item_id'))
                ->whereHas('shoppingList', fn (Builder $query): Builder => $query->where('user_id', $userId))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            abort_unless($shoppingListItems->count() === count($items), 404);

            foreach ($items as $item) {
                $shoppingListItems->get($item['item_id'])->update([
                    'is_completed' => $item['is_completed'],
                ]);
            }
        });

        return $this->success(message: __('responses.api.shopping.shopping_list.check'));
    }

    private function shoppingListItemKey(string $ingredientId, ?string $unit): string
    {
        return $ingredientId.'|'.($unit ?? '');
    }
}
