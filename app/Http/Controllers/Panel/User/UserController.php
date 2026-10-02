<?php

namespace App\Http\Controllers\Panel\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\User\User\UserChangePasswordRequest;
use App\Http\Requests\Panel\User\User\UserIndexRequest;
use App\Http\Requests\Panel\User\User\UserListRequest;
use App\Http\Requests\Panel\User\User\UserStoreRequest;
use App\Http\Requests\Panel\User\User\UserUpdateRequest;
use App\Http\Resources\Panel\User\User\UserIndexResource;
use App\Http\Resources\Panel\User\User\UserListResource;
use App\Http\Resources\Panel\User\User\UserShowResource;
use Illuminate\Support\Facades\Gate;
use App\Models\User\User;
use App\Models\User\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UserIndexRequest $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 10);

        $users = User::query();

        if ($request->has('search')) {
            $search = '%' . $request->string('search')->trim() . '%';
            $users->where(function ($query) use ($search) {
                $query->where('full_name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        if ($request->has('role') && $request->role != null) {
            $users->whereHas('roles', function ($query) use ($request) {
                $query->where('uuid', $request->string('role'));
            });
        }

        $users = $users->paginate($perPage)
            ->withQueryString();

        return $this->collection(UserIndexResource::collection($users), __('responses.users.index'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $validated = $request->only([
            'email', 'full_name',
            'password', 'role_id'
        ]);

        try {
            $role = Role::query()->where('uuid', $request->role_id)->first();

            $user = new User();
            $user->fill($validated);
            $user->save();

            $user->assignRole($role);

            return $this->success($user, __('responses.users.store'));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        if (Gate::denies('PanelShow', $user)) {
            abort(403, __('responses.errors.unauthorized'));
        }

        $user->load('roles');
        return $this->resource(new UserShowResource($user), __('responses.users.show'), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        $validated = $request->only([
            'email', 'full_name',
            'role_id'
        ]);

        try {
            $role = Role::query()->where('uuid', $request->role_id)->firstOrFail();

            $user->fill($validated);
            $user->assignRole($role);
            $user->save();

            return $this->success($user, __('responses.users.update'));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        if (Gate::denies('PanelDelete', $user)) {
            abort(403, __('responses.errors.unauthorized'));
        }

        $user->delete();

        return $this->success(message: __('responses.users.destroy'));
    }

    /**
     * List the resource.
     */
    public function list(UserListRequest $request): JsonResponse
    {
        $users = User::query();

        if ($request->has('role')) {
            $users->whereHas('roles', function ($query) use ($request) {
                $query->where('name', $request->role);
            });
        }

        $users = $users->get();

        return $this->collection(UserListResource::collection($users), __('responses.users.list'));
    }

    public function changePassword(UserChangePasswordRequest $request, User $user): JsonResponse
    {
        $user->update([
            'password' => $request->password,
        ]);

        return $this->success(message: __('responses.users.change_password'));
    }
}
