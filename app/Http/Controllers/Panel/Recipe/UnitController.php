<?php

namespace App\Http\Controllers\Panel\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\Recipe\Unit\UnitIndexRequest;
use App\Http\Requests\Panel\Recipe\Unit\UnitListRequest;
use App\Http\Requests\Panel\Recipe\Unit\UnitStoreRequest;
use App\Http\Requests\Panel\Recipe\Unit\UnitUpdateRequest;
use App\Http\Resources\Panel\Recipe\Unit\UnitIndexResource;
use App\Http\Resources\Panel\Recipe\Unit\UnitListResource;
use App\Http\Resources\Panel\Recipe\Unit\UnitShowResource;
use App\Models\Recipe\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
       /**
     * Display a listing of the resource.
     */
    public function index(UnitIndexRequest $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 10);

        $units = Unit::query();

        if ($request->has('search')) {
            $search = '%'.$request->string('search')->trim().'%';
            $units->where(function ($query) use ($search) {
                $query->where('name', 'like', $search);
            });
        }

        $units = $units->paginate($perPage)
            ->withQueryString();

        return $this->collection(UnitIndexResource::collection($units), __('responses.units.index'));
    }

      /**
     * Store a newly created resource in storage.
     */
    public function store(UnitStoreRequest $request): JsonResponse
    {
        $validated = $request->only([
            "name","symbol","slug","type",
            "conversion_factor","is_metric","status"
        ]);

        try {

            $Unit = new Unit;
            $Unit->fill($validated);
            $Unit->save();

            return $this->success($Unit, __('responses.units.store'));

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

      /**
     * Display the specified resource.
     */
    public function show(Unit $Unit): JsonResponse
    {
        if (Gate::denies('PanelShow', $Unit)) {
            abort(403, __('responses.errors.unauthorized'));
        }

        return $this->resource(new UnitShowResource($Unit), __('responses.units.show'), 200);
    }

      /**
     * Update the specified resource in storage.
     */
    public function update(UnitUpdateRequest $request, Unit $Unit): JsonResponse
    {
        $validated = $request->only([
            "name","symbol","slug","type",
            "conversion_factor","is_metric","status"
        ]);

        try {

            $Unit->fill($validated);
            $Unit->save();

            return $this->success($Unit, __('responses.units.update'));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

      /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $Unit): JsonResponse
    {
        if (Gate::denies('PanelDelete', $Unit)) {
            abort(403, __('responses.errors.unauthorized'));
        }

        $Unit->delete();

        return $this->success(message: __('responses.units.destroy'));
    }

      /**
     * List the resource.
     */
    public function list(UnitListRequest $request): JsonResponse
    {
        $units = Unit::query();

        if ($request->has('role')) {
            $units->whereHas('roles', function ($query) use ($request) {
                $query->where('name', $request->role);
            });
        }

        $units = $units->get();

        return $this->collection(UnitListResource::collection($units), __('responses.units.index'));
    }
}
