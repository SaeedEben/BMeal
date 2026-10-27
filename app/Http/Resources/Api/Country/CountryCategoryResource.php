<?php

namespace App\Http\Resources\Api\Country;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $recipe = $this->recipes->first();

        return [
            'name' => $this->name,
            'description' => $this->description,
            'image' => $recipe?->image?->preview(),
            'country_id' => $this->id,
        ];
    }
}
