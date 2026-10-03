<?php

namespace App\Http\Resources\Panel\Recipe\Unit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"                => $this->id,
            "name"              => $this->name,
            "symbol"            => $this->symbol,
            "slug"              => $this->slug,
            "type"              => $this->type,
            "conversion_factor" => $this->conversion_factor,
            "is_metric"         => $this->is_metric,
            "status"            => $this->status,
            "created_at"        => $this->created_at,
            "updated_at"        => $this->updated_at
        ];
    }
}
