<?php

namespace App\Http\Resources\Api\Shopping;

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
        $items = $this->items->map(fn ($item): array => [
            'id' => $item->id,
            'ingredient_id' => $item->ingredient_id,
            'ingredient' => $item->ingredient === null
                ? null
                : new IngredientsShowResource($item->ingredient),
            'name' => $item->name,
            'quantity' => $item->quantity,
            'unit' => $item->unit,
            'date' => $item->date->toDateString(),
            'is_completed' => $item->is_completed,
            'status' => $item->status,
        ]);

        return [
            'id' => $this->id,
            'week_date' => $this->week_date,
            'name' => $this->name,
            'status' => $this->status,
            'items' => $items
                ->groupBy(fn (array $item): string => $item['name'])
                ->map(function ($group): array {
                    $item = $group->sortBy('date')->first();

                    if ($group->count() === 1) {
                        return $item;
                    }

                    $hasQuantity = $group->contains(fn (array $groupItem): bool => $groupItem['quantity'] !== null);
                    $quantityHundredths = $group->sum(fn (array $groupItem): int => $groupItem['quantity'] === null
                        ? 0
                        : (int) round((float) $groupItem['quantity'] * 100));

                    $item['quantity'] = $hasQuantity
                        ? number_format($quantityHundredths / 100, 2, '.', '')
                        : null;
                    $item['is_completed'] = $group->every(fn (array $groupItem): bool => $groupItem['is_completed'] === true);

                    return $item;
                })
                ->values(),
        ];
    }
}
