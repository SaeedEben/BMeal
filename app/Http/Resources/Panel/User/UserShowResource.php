<?php

namespace App\Http\Resources\Panel\User;

use App\Http\Resources\Panel\Customer\CustomerShowResource;
use App\Http\Resources\Panel\Role\RoleIndexResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'phone'             => $this->phone,
            'full_name'         => $this->full_name,
            'username'          => $this->username,
            'phone_verified_at' => $this->phone_verified_at,
            'city_id'           => $this->city_id,
            'last_active'       => $this->last_active,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'roles'             => RoleIndexResource::collection($this->roles),
            'customer_data'     => new CustomerShowResource($this->customer),
        ];
    }
}
