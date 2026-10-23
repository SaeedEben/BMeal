<?php

namespace App\Http\Controllers\Api\Recipe;

use App\Enum\Recipe\DifficultyEnum;
use App\Enum\Recipe\MealTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Recipe\Recipe\RecipeDifficultiesListRequest;
use App\Http\Requests\Api\Recipe\Recipe\RecipeIndexRequest;
use App\Http\Requests\Api\Recipe\Recipe\RecipeMealTypeListRequest;
use App\Http\Resources\Api\Recipe\Recipe\RecipeIndexResource;
use App\Http\Resources\Api\Recipe\Recipe\RecipeShowResource;
use App\Models\Recipe\Recipe;
use Exception;
use Illuminate\Support\Facades\Log;

class RecipeController extends Controller
{
    public function index(RecipeIndexRequest $request)
    {
        $validated = $request->validated();
        $hasFilters = collect(['search', 'meal_type', 'country_id', 'tags', 'categories'])
            ->contains(fn (string $filter): bool => filled($validated[$filter] ?? null));

        try {
            $recipes = Recipe::query()->with('image');

            if (filled($validated['search'] ?? null)) {
                $search = $validated['search'];

                $recipes->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhereHas('ingredients', function ($query) use ($search) {
                            $query->where('name', 'like', '%'.$search.'%');
                        });
                });
            }

            if (filled($validated['meal_type'] ?? null)) {
                $recipes->where('meal_type', $validated['meal_type']);
            }

            if (filled($validated['difficulty'] ?? null)) {
                $recipes->where('difficulty', $validated['difficulty']);
            }

            if (filled($validated['country_id'] ?? null)) {
                $recipes->where('country_id', $validated['country_id']);
            }

            if (filled($validated['tags'] ?? null)) {
                $recipes->whereHas('tags', function ($query) use ($validated) {
                    $query->whereIn('tags.id', $validated['tags']);
                });
            }

            if (filled($validated['categories'] ?? null)) {
                $recipes->whereHas('categories', function ($query) use ($validated) {
                    $query->whereIn('categories.id', $validated['categories']);
                });
            }

            if (! $hasFilters) {
                $recipes->inRandomOrder()->limit(9);
            }

            $recipes = $recipes->get();

            return $this->resource(RecipeIndexResource::collection($recipes), __('responses.api.recipe.recipes.index'));

        } catch (Exception $e) {
            Log::error('Error in RecipeController@index: '.$e->getMessage(), ['exception' => $e]);

            return $this->error();
        }
    }

    public function show(Recipe $recipe)
    {
        $recipe->load('country', 'steps', 'ingredients', 'categories', 'tags', 'image');

        return $this->resource(new RecipeShowResource($recipe), __('responses.api.recipe.recipes.show'));
    }

    public function mealTypesList(RecipeMealTypeListRequest $request)
    {
        $mealTypes = MealTypeEnum::options();

        return $this->collection($mealTypes, __('responses.api.recipe.recipes.meal_types_list'));
    }

    public function difficultiesList(RecipeDifficultiesListRequest $request)
    {
        $difficulties = DifficultyEnum::options();

        return $this->collection($difficulties, __('responses.api.recipe.recipes.difficulties_list'));
    }
}
