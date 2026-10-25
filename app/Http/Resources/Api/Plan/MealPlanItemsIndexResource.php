<?php

namespace App\Http\Resources\Api\Plan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealPlanItemsIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
             "id"           => $this->id,
             "meal_plan_id" => $this->meal_plan_id,
             "recipe_id"    => $this->recipe_id,
             "date"         => $this->date,
             "meal_type"    => $this->meal_type,
             "servings"     => $this->servings,
             "status"       => $this->status,
             "created_at"   => $this->created_at,
             "updated_at"   => $this->updated_at,
             "recipe"       => [
                    'id'                   => $this->recipe->id,
                    'country_id'           => $this->recipe->country_id,
                    'name'                 => $this->recipe->name,
                    'slug'                 => $this->recipe->slug,
                    'description'          => $this->recipe->description,
                    'meal_type'            => $this->recipe->meal_type,
                    'prep_time_minutes'    => $this->recipe->prep_time_minutes,
                    'cook_time_minutes'    => $this->recipe->cook_time_minutes,
                    'servings'             => $this->recipe->servings,
                    'difficulty'           => $this->recipe->difficulty,
                    'calories_per_serving' => $this->recipe->calories_per_serving,
                    'status'               => $this->recipe->status,
                    'created_at'           => $this->recipe->created_at,
                    'updated_at'           => $this->recipe->updated_at,
                    'image'                => $this->recipe->image?->preview(),
             ]
        ];
    }
}
