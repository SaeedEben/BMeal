<?php

namespace App\Http\Controllers\Panel\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Panel\User\Role\RoleIndexRequest;
use App\Http\Requests\Panel\User\Role\RoleStoreRequest;
use App\Http\Requests\Panel\User\Role\RoleUpdateRequest;
use App\Http\Requests\Panel\User\Role\RoleListRequest;
use App\Http\Resources\Panel\User\Role\RoleIndexResource;
use App\Http\Resources\Panel\User\Role\RoleShowResource;
use App\Http\Resources\Panel\User\Role\RoleListResource;
use App\Models\User\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index(RoleIndexRequest $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 10);

        $roles = Role::query();

        if ($request->has('search')) {
            $search = '%' . $request->string('search')->trim() . '%';
            $roles->where(function ($query) use ($search) {
                $query->where('full_name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        $roles = $roles->paginate($perPage)
            ->withQueryString();

        return $this->collection(RoleIndexResource::collection($roles), __('responses.roles.index'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleStoreRequest $request): JsonResponse
    {
        $validated = $request->only([
            'name',
        ]);

        try {

            $role = new Role();
            $role->fill($validated);
            $role->guard_name = 'web';
            $role->save();

            return $this->success($role, __('responses.roles.store'));

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): JsonResponse
    {
        if (Gate::denies('PanelShow', $role)) {
            abort(403, __('responses.errors.unauthorized'));
        }

        return $this->resource(new RoleShowResource($role), __('responses.roles.show'), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleUpdateRequest $request, Role $role): JsonResponse
    {
        $validated = $request->only([
            'name',
        ]);

        try {

            $role->fill($validated);
            $role->save();

            return $this->success($role, __('responses.roles.update'));

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): JsonResponse
    {
        if (Gate::denies('PanelDelete', $role)) {
            abort(403, __('responses.errors.unauthorized'));
        }

        $role->delete();

        return $this->success(message: __('responses.roles.destroy'));
    }

    /**
     * List the resource.
     */
    public function list(RoleListRequest $request): JsonResponse
    {
        $roles = Role::query()->get();

        return $this->collection(RoleListResource::collection($roles), __('responses.roles.list'));
    }
}
