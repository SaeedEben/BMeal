<?php

namespace App\Http\Controllers\Api\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Recipe\Recipe\RecipeIndexRequest;
use App\Http\Resources\Api\Recipe\Recipe\RecipeIndexResource;
use App\Models\Recipe\Recipe;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecipeController extends Controller
{
    public function index(RecipeIndexRequest $request) {
        $validated = $request->validated();

        try{
            $recipes = Recipe::query();

            if ($request->has('search')) {
                $recipes->where('name' , 'like', '%' . $validated['search'] . '%')
                    ->orWhereHas('ingredients', function ($query) use($validated) {
                        $query->where('name', 'like',  '%' . $validated['search'] . '%');
                    });
            }

            if ($request->has('meal_type')) {
                $recipes->where('meal_type', $validated['meal_type']);
            }

            if ($request->has('country_id')) {
                $recipes->where('country_id' , $validated['counntry_id']);
            }

            if ($request->has('tag_id')) {
                $recipes->whereHas('tags' , function ($query) use($validated) {
                    $query->where('id', $validated['tag_id']);
                });
            }

            if ($request->has('category_id')) {
                $recipes->whereHas('categories' , function ($query) use($validated) {
                    $query->where('id', $validated['category_id']);
                });
            }

            $recipes = $recipes->get();

            return $this->resource(RecipeIndexResource::collection($recipes), __('responses.api.recipe.recipes.index'));

        }catch(Exception $e) {
            Log::error("Error in RecipeController@index: " . $e->getMessage(), ['exception' => $e]);

            return $this->error();
        }
    }
}
