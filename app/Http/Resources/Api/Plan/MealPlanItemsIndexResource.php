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
             "recipe"       => $this->recipe->name,
             "date"         => $this->date,
             "meal_type"    => $this->meal_type,
             "servings"     => $this->servings,
             "status"       => $this->status,
             "created_at"   => $this->created_at,
             "updated_at"   => $this->updated_at
        ];
    }
}
