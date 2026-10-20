<?php

namespace App\Http\Resources\Api\Recipe\Unit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "name"              => $this->name,
            "symbol"            => $this->symbol,
            "slug"              => $this->slug,
            "type"              => $this->type,
            "conversion_factor" => $this->conversion_factor,
            "is_metric"         => $this->is_metric,
        ];
    }
}
