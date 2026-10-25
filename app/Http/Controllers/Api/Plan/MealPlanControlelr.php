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
            ->whereBetween('week_start_date', [$weekStart, $weekEnd])
            ->get();

        return $this->resource(MealPlanIndexResource::collection($mealPlans), __('responses.api.plan.meal_plan.index'));
    }

    public function store(MealPlanStoreRequest $request)
    {
        $validated = $request->validated();

        $user_id = $request->user()->id;

        $mealPlan = DB::transaction(function () use ($user_id, $validated) {
            $mealPlan = MealPlan::create([
                'user_id' => $user_id,
                'week_start_date' => $validated['week_start_date'],
                'status' => StatusEnum::ACTIVE,
            ]);

            foreach ($validated['items'] as $item) {
                MealPlanItem::create([
                    'meal_plan_id' => $mealPlan->id,
                    'recipe_id' => $item['recipe_id'],
                    'date' => $item['date'],
                    'meal_type' => $item['meal_type'],
                    'servings' => $item['servings'],
                    'status' => StatusEnum::ACTIVE,

                ]);
            }
        });

        return $this->success($mealPlan, __('responses.api.plan.meal_plan.store'));
    }

    public function destroy(MealPlan $mealPlan)
    {
        $mealPlan->delete();

        return $this->success(message: __('responses.api.plan.meal_plan.destroy'));
    }
}
