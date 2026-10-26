<?php

namespace App\Http\Resources\Api\Shopping;

use App\Http\Resources\Panel\Recipe\Ingredients\IngredientsIndexResource;
use App\Http\Resources\Panel\Recipe\Ingredients\IngredientsShowResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingListShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'week_date' => $this->week_date,
            'name' => $this->name,
            'status' => $this->status,
            'items' => $this->items->map(fn ($item): array => [
                'id'            => $item->id,
                'ingredient_id' => $item->ingredient_id,
                'ingredient'    => new IngredientsShowResource($item['ingredient']),
                'name'          => $item->name,
                'quantity'      => $item->quantity,
                'unit'          => $item->unit,
                'is_completed'  => $item->is_completed,
                'status'        => $item->status,
            ])->values(),
        ];
    }
}
