<?php

namespace App\Http\Resources\Api\Recipe\Recipe;

use App\Http\Resources\Api\Recipe\Category\CategoryResource;
use App\Http\Resources\Api\Recipe\Ingredients\IngredientsResource;
use App\Http\Resources\Api\Recipe\Steps\StepResource;
use App\Http\Resources\Api\Recipe\Tag\TagResource;
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
            'id'                   => $this->id,
            'name'                 => $this->name,
            'slug'                 => $this->slug,
            'description'          => $this->description,
            'image'                => $this->image->preview(),
            'meal_type'            => $this->meal_type,
            'prep_time_minutes'    => $this->prep_time_minutes,
            'cook_time_minutes'    => $this->cook_time_minutes,
            'servings'             => $this->servings,
            'difficulty'           => $this->difficulty,
            'calories_per_serving' => $this->calories_per_serving,
            'country'              => $this->country->name,
            'categories'           => CategoryResource::collection($this->categories),
            'steps'                => StepResource::collection($this->steps),
            'tags'                 => TagResource::collection($this->tags),
            'ingredients'          => IngredientsResource::collection($this->ingredients),
        ];
    }
}
