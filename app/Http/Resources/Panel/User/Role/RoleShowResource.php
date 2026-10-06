<?php

namespace App\Http\Resources\Panel\User\Role;

use App\Http\Resources\Panel\Permission\PermissionListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        "id"          => $this->uuid,
        "name"        => $this->name,
        "guard_name"  => $this->guard_name,
        "created_at"  => $this->created_at,
        "updated_at"  => $this->updated_at,
        "permissions" => PermissionListResource::collection($this->permissions)
        ];
    }
}
