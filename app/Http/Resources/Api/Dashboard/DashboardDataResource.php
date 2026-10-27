<?php

namespace App\Http\Resources\Api\Dashboard;

use App\Enum\Recipe\MealTypeEnum;
use App\Models\Plan\MealPlanItem;
use App\Models\Recipe\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $formatRecipe = static fn (?Recipe $recipe): ?array => $recipe === null ? null : [
            'id' => $recipe->id,
            'name' => $recipe->name,
            'meal_type' => $recipe->meal_type,
            'prep_time_minutes' => $recipe->prep_time_minutes,
            'calories_per_serving' => $recipe->calories_per_serving,
            'image' => $recipe->image?->preview(),
        ];

        $todayMeals = $this->resource['today_meals'];
        $weekPlan = $this->resource['current_week_plan'];
        $recommendations = $this->resource['recommendations'];

        return [
            'today_meals' => $todayMeals === null
                ? null
                : collect($todayMeals)->map(fn (?Recipe $recipe): ?array => $formatRecipe($recipe)),
            'current_week_plan' => $weekPlan?->map(fn (MealPlanItem $item): array => [
                'image' => $item->recipe->image?->preview(),
                'date' => $item->date->toDateString(),
                'meal_type' => $item->meal_type instanceof MealTypeEnum
                    ? $item->meal_type->value
                    : $item->meal_type,
            ])->values(),
            'recommendations' => $recommendations?->map(fn (Recipe $recipe): ?array => $formatRecipe($recipe))->values(),
        ];
    }
}
