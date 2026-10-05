<?php

namespace App\Http\Resources\Panel\Recipe\Recipe;

use App\Http\Resources\Panel\Recipe\Category\CategoryShowResource;
use App\Http\Resources\Panel\Recipe\Ingredients\IngredientsShowResource;
use App\Http\Resources\Panel\Recipe\Tag\TagShowResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"                   => $this->id,
            "country_id"           => $this->country_id,
            "name"                 => $this->name,
            "slug"                 => $this->slug,
            "description"          => $this->description,
            "image"                => $this->image,
            "meal_type"            => $this->meal_type,
            "prep_time_minutes"    => $this->prep_time_minutes,
            "cook_time_minutes"    => $this->cook_time_minutes,
            "servings"             => $this->servings,
            "difficulty"           => $this->difficulty,
            "calories_per_serving" => $this->calories_per_serving,
            "status"               => $this->status,
            "created_at"           => $this->created_at,
            "updated_at"           => $this->updated_at,
            "tags"                 => TagShowResource::collection($this->tags),
            "categories"           => CategoryShowResource::collection($this->categories),
            "ingredients"          => $this->recipeIngredients,
            "steps"                => $this->steps,
        ];
    }
}
