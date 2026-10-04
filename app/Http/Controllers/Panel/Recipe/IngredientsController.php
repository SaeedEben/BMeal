<?php

namespace App\Http\Controllers\Panel\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\Recipe\Ingredients\IngredientsIndexRequest;
use App\Http\Requests\Panel\Recipe\Ingredients\IngredientsListRequest;
use App\Http\Requests\Panel\Recipe\Ingredients\IngredientsStoreRequest;
use App\Http\Requests\Panel\Recipe\Ingredients\IngredientsUpdateRequest;
use App\Http\Resources\Panel\Recipe\Ingredients\IngredientsIndexResource;
use App\Http\Resources\Panel\Recipe\Ingredients\IngredientsListResource;
use App\Http\Resources\Panel\Recipe\Ingredients\IngredientsShowResource;
use App\Models\Recipe\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class IngredientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IngredientsIndexRequest $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 10);

        $ingredients = Ingredient::query();

        if ($request->has('search')) {
            $search = '%'.$request->string('search')->trim().'%';
            $ingredients->where(function ($query) use ($search) {
                $query->where('name', 'like', $search);
            });
        }

        $ingredients = $ingredients->paginate($perPage)
            ->withQueryString();

        return $this->collection(IngredientsIndexResource::collection($ingredients), __('responses.ingredients.index'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IngredientsStoreRequest $request): JsonResponse
    {
        $validated = $request->only([
            'name', 'slug', 'status', 'description',
        ]);

        try {
            $Ingredients = DB::transaction(function () use ($validated): Ingredient {
                $ingredient = new Ingredient;
                $ingredient->fill($validated);
                $ingredient->save();

                return $ingredient;
            });

            return $this->success($Ingredients, __('responses.ingredients.store'));

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ingredient $Ingredient): JsonResponse
    {
        if (Gate::denies('PanelShow', $Ingredient)) {
            abort(403, __('responses.errors.unauthorized'));
        }

        return $this->resource(new IngredientsShowResource($Ingredient), __('responses.ingredients.show'), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IngredientsUpdateRequest $request, Ingredient $ingredient): JsonResponse
    {
        $validated = $request->only([
            'name', 'slug', 'status', 'description',
        ]);

        try {
            DB::transaction(function () use ($ingredient, $validated): void {
                $ingredient->fill($validated);
                $ingredient->save();
            });

            return $this->success($ingredient, __('responses.ingredients.update'));

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient): JsonResponse
    {
        if (Gate::denies('PanelDelete', $ingredient)) {
            abort(403, __('responses.errors.unauthorized'));
        }

        $ingredient->delete();

        return $this->success(message: __('responses.ingredients.destroy'));
    }

    /**
     * List the resource.
     */
    public function list(IngredientsListRequest $request): JsonResponse
    {
        $ingredients = Ingredient::query();

        if ($request->has('role')) {
            $ingredients->whereHas('roles', function ($query) use ($request) {
                $query->where('name', $request->role);
            });
        }

        $ingredients = $ingredients->get();

        return $this->collection(IngredientsListResource::collection($ingredients), __('responses.ingredients.index'));
    }
}
