<?php

namespace App\Http\Resources\Panel\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'phone'       => $this->phone,
            'full_name'   => $this->full_name,
            'username'    => $this->username,
            'city'        => $this->city->name,
            'role'        => $this->roles()->first()?->name,
            'last_active' => verta($this->last_active)->format('Y-m-d H:i'),
            'created_at'  => verta($this->created_at)->format('Y-m-d H:i'),
            'updated_at'  => verta($this->updated_at)->format('Y-m-d H:i'),
        ];
    }
}
