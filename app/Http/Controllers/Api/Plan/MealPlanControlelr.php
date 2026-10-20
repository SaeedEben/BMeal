<?php

namespace App\Http\Controllers\Api\Plan;

use App\Enum\Common\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Plan\MealPlanStoreRequest;
use App\Models\Plan\MealPlan;
use App\Models\Plan\MealPlanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MealPlanControlelr extends Controller
{
    public function store(MealPlanStoreRequest $request)
    {
        $validated = $request->validated();

        $user_id = $request->user()->id;

        $mealPlan = DB::transaction(function () use ($user_id , $validated){
            $mealPlan =  MealPlan::create([
                'user_id'         => $user_id,
                'week_start_date' => $validated['week_start_date'],
                'status'          => StatusEnum::ACTIVE,
            ]);

            foreach ($validated['items'] as $item){
                MealPlanItem::create([
                    'meal_plan_id' => $mealPlan->id,
                    'recipe_id'    => $validated['recipe_id'],
                    'date'         => $validated['date'],
                    'meal_type'    => $validated['meal_type'],
                    'servings'     => $validated['servings'],
                    'status'       => StatusEnum::ACTIVE,

                ]);
            }
        });

        return $this->success($mealPlan, __('responses.api.plan.meal_plan.store'));
    }
}
