<?php

namespace App\Http\Resources\Api\Recipe\Ingredients;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'image'       => $this->image?->preview(),
            'pivot'       => new RecipeIngredientResource($this->pivot),
        ];
    }
}
