<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\Auth\ProfileRequest;
use App\Http\Resources\Panel\Permission\PermissionListResource;
use App\Http\Resources\Panel\User\UserShowResource;
use App\Models\User;

class ProfileController extends Controller
{
    public function profile(ProfileRequest $request) 
    {
        $user        = $request->user();
        $user        = User::query()->with('roles')->find($user->id);
        $permisisons = $user->getPermissionsByRole();

        return $this->success([
            'user'        => new UserShowResource($user),
            'permissions' => PermissionListResource::collection($permisisons)
        ],__('responses.auth.profile'));
    }
}
