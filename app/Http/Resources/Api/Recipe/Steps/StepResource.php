<?php

namespace App\Http\Resources\Api\Recipe\Steps;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StepResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'step_number' => $this->step_number,
            'instruction' => $this->instruction,
            'image' => $this->image,
        ];
    }
}
