<?php

namespace App\Http\Resources\Api\Plan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealPlanIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"              => $this->id,
            "user_id"         => $this->user_id,
            "week_start_date" => $this->week_start_date,
            "status"          => $this->status,
            "created_at"      => $this->created_at,
            "updated_at"      => $this->updated_at,
            "items"           => MealPlanItemsIndexResource::collection($this->items),
        ];
    }
}
