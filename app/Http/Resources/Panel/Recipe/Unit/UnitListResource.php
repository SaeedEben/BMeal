<?php

namespace App\Http\Resources\Panel\Recipe\Unit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'vlaue' => $this->id,
            'label' => $this->name,
        ];
    }
}
