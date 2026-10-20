<?php

namespace App\Http\Resources\Api\Recipe\Ingredients;

use App\Http\Resources\Api\Recipe\Unit\UnitResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeIngredientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'quantity' => $this->quantity,
            'notes' => $this->notes,
            'sort_order' => $this->sort_order,
            'unit' => new UnitResource($this->unit),
        ];
    }
}
