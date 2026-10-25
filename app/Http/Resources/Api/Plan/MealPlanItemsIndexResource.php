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
                    'id'                   => $this->id,
                    'country_id'           => $this->country_id,
                    'name'                 => $this->name,
                    'slug'                 => $this->slug,
                    'description'          => $this->description,
                    'meal_type'            => $this->meal_type,
                    'prep_time_minutes'    => $this->prep_time_minutes,
                    'cook_time_minutes'    => $this->cook_time_minutes,
                    'servings'             => $this->servings,
                    'difficulty'           => $this->difficulty,
                    'calories_per_serving' => $this->calories_per_serving,
                    'status'               => $this->status,
                    'created_at'           => $this->created_at,
                    'updated_at'           => $this->updated_at,
                    'image'                => $this->image?->preview(),
             ]
        ];
    }
}
