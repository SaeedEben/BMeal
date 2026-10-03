<?php

namespace App\Http\Resources\Panel\Recipe\Unit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitIndexResource extends JsonResource
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
            "type"              => $this->type,
            "status"            => $this->status,
            "created_at"        => $this->created_at,
            "updated_at"        => $this->updated_at
        ];
    }
}
