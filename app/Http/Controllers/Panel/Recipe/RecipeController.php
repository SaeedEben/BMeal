<?php

namespace App\Http\Controllers\Panel\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\Recipe\Recipe\RecipeIndexRequest;
use App\Http\Requests\Panel\Recipe\Recipe\RecipeListRequest;
use App\Http\Requests\Panel\Recipe\Recipe\RecipeStoreRequest;
use App\Http\Requests\Panel\Recipe\Recipe\RecipeUpdateRequest;
use App\Http\Resources\Panel\Recipe\Recipe\RecipeIndexResource;
use App\Http\Resources\Panel\Recipe\Recipe\RecipeListResource;
use App\Http\Resources\Panel\Recipe\Recipe\RecipeShowResource;
use App\Models\Recipe\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RecipeIndexRequest $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 10);

        $recipes = Recipe::query();

        if ($request->has('search')) {
            $search = '%'.$request->string('search')->trim().'%';
            $recipes->where(function ($query) use ($search) {
                $query->where('name', 'like', $search);
            });
        }

        $recipes = $recipes->paginate($perPage)
            ->withQueryString();

        return $this->collection(RecipeIndexResource::collection($recipes), __('responses.recipes.index'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RecipeStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $recipe = DB::transaction(function () use ($validated): Recipe {
                $relations = Arr::only($validated, ['ingredients', 'categories', 'tags', 'steps']);
                $recipe = Recipe::create(Arr::except($validated, array_keys($relations)));

                $this->syncRelations($recipe, $relations);

                return $recipe;
            });

            return $this->success($recipe, __('responses.recipes.store'));

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe): JsonResponse
    {
        if (Gate::denies('PanelShow', $recipe)) {
            abort(403, __('responses.errors.unauthorized'));
        }

        return $this->resource(new RecipeShowResource($recipe), __('responses.recipes.show'), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RecipeUpdateRequest $request, Recipe $recipe): JsonResponse
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($recipe, $validated): void {
                $relations = Arr::only($validated, ['ingredients', 'categories', 'tags', 'steps']);
                $recipe->fill(Arr::except($validated, array_keys($relations)));
                $recipe->save();

                $this->syncRelations($recipe, $relations);
            });

            return $this->success($recipe, __('responses.recipes.update'));

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe): JsonResponse
    {
        if (Gate::denies('PanelDelete', $recipe)) {
            abort(403, __('responses.errors.unauthorized'));
        }

        $recipe->delete();

        return $this->success(message: __('responses.recipes.destroy'));
    }

    /**
     * List the resource.
     */
    public function list(RecipeListRequest $request): JsonResponse
    {
        $recipes = Recipe::query();

        if ($request->has('role')) {
            $recipes->whereHas('roles', function ($query) use ($request) {
                $query->where('name', $request->role);
            });
        }

        $recipes = $recipes->get();

        return $this->collection(RecipeListResource::collection($recipes), __('responses.recipes.index'));
    }

    /**
     * @param  array<string, mixed>  $relations
     */
    private function syncRelations(Recipe $recipe, array $relations): void
    {
        if (array_key_exists('ingredients', $relations)) {
            $existingPivotIds = $recipe->recipeIngredients()->pluck('id', 'ingredient_id')->all();
            $ingredientPivotData = [];

            foreach ($relations['ingredients'] ?? [] as $ingredient) {
                $ingredientId = $ingredient['ingredient_id'];
                $pivotAttributes = Arr::only($ingredient, [
                    'unit_id', 'quantity', 'notes', 'sort_order', 'status',
                ]);
                $pivotAttributes['id'] = $existingPivotIds[$ingredientId] ?? (string) Str::uuid();
                $ingredientPivotData[$ingredientId] = $pivotAttributes;
            }

            $recipe->ingredients()->sync($ingredientPivotData);
        }

        if (array_key_exists('categories', $relations)) {
            $recipe->categories()->sync($relations['categories'] ?? []);
        }

        if (array_key_exists('tags', $relations)) {
            $recipe->tags()->sync($relations['tags'] ?? []);
        }

        if (array_key_exists('steps', $relations)) {
            $steps = $relations['steps'] ?? [];

            if ($steps === []) {
                $recipe->steps()->delete();

                return;
            }

            $stepNumbers = array_column($steps, 'step_number');
            $recipe->steps()->whereNotIn('step_number', $stepNumbers)->delete();

            foreach ($steps as $step) {
                $recipe->steps()->updateOrCreate(
                    ['step_number' => $step['step_number']],
                    $step,
                );
            }
        }
    }
}
