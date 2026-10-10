<?php

namespace App\Http\Resources\Panel\Country;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"           => $this->id,
            "name"         => $this->name,
            "code"         => $this->code,
            "slug"         => $this->slug,
            "description"  => $this->description,
            "flag_id"      => $this->flag_id,
            "flag_preview" => $this->flag->preview() ?? null,
            "status"       => $this->status,
            "created_at"   => $this->created_at,
            "updated_at"   => $this->updated_at,
        ];
    }
}
