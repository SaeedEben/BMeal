<?php

namespace App\Http\Controllers\Panel\User;

use App\Http\Controllers\Controller;
use App\Models\User\Permission;
use App\Http\Resources\Panel\User\Permission\PermissionIndexResource;
use App\Http\Requests\Panel\User\Permission\PermissionIndexRequest;

class PermissionController extends Controller
{
    public function index(PermissionIndexRequest $request)
    {
        $permissions = Permission::query()->get();

        return $this->collection(PermissionIndexResource::collection($permissions), __('responses.permissions.index'));
    }
}
