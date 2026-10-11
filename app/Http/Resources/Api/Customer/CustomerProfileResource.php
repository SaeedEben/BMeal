<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User\User;
/**
 * @mixin User
 */
class CustomerProfileResource extends JsonResource
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
            "full_name"         => $this->full_name,
            "email"             => $this->email,
            "email_verified_at" => $this->email_verified_at,
            "avatar"            => $this->avatar?->preview(),
            "status"            => $this->status,
            "last_active"       => $this->last_active,
            "created_at"        => $this->created_at,
            "updated_at"        => $this->updated_at,
        ];
    }
}
