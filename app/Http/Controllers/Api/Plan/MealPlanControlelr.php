<?php

namespace App\Http\Controllers\Api\Plan;

use App\Enum\Common\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Plan\MealPlanIndexRequest;
use App\Http\Requests\Api\Plan\MealPlanStoreRequest;
use App\Http\Resources\Api\Plan\MealPlanIndexResource;
use App\Models\Plan\MealPlan;
use App\Models\Plan\MealPlanItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MealPlanControlelr extends Controller
{
    public function show(MealPlanIndexRequest $request)
    {
        $validated = $request->validated();

        $weekStart = Carbon::parse($validated['week'] ?? now())->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $mealPlans = MealPlan::query()->with('items', 'items.recipe', 'items.recipe.image')
            ->where('user_id', $request->user()->id)
            ->whereBetween('week_start_date', [$weekStart, $weekEnd])
            ->get();

        return $this->resource(MealPlanIndexResource::collection($mealPlans), __('responses.api.plan.meal_plan.index'));
    }

    public function store(MealPlanStoreRequest $request)
    {
        $validated = $request->validated();

        $userId = $request->user()->id;

        $mealPlan = DB::transaction(function () use ($userId, $validated): MealPlan {
            DB::table('users')->where('id', $userId)->lockForUpdate()->first();

            $mealPlan = MealPlan::query()
                ->forUserWeek($userId, $validated['week_start_date'])
                ->first();

            if ($mealPlan === null) {
                $mealPlan = MealPlan::query()->create([
                    'user_id' => $userId,
                    'week_start_date' => $validated['week_start_date'],
                    'status' => StatusEnum::ACTIVE,
                ]);
            }

            foreach ($validated['items'] as $item) {
                $mealPlan->items()->create([
                    'recipe_id' => $item['recipe_id'],
                    'date' => $item['date'],
                    'meal_type' => $item['meal_type'],
                    'servings' => $item['servings'],
                    'status' => StatusEnum::ACTIVE,
                ]);
            }

            return $mealPlan->load('items');
        });

        return $this->success($mealPlan, __('responses.api.plan.meal_plan.store'));
    }

    public function destroy(MealPlanItem $mealPlanItem)
    {
        $mealPlanItem->delete();

        return $this->success(message: __('responses.api.plan.meal_plan.destroy'));
    }
}
