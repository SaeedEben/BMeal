<?php

namespace App\Http\Controllers\Api\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Recipe\Category\CategoryListRequest;
use App\Http\Resources\Panel\Recipe\Category\CategoryListResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function list(CategoryListRequest $request)
    {
        $categories = \App\Models\Recipe\Category::all();

        return self::resource(CategoryListResource::collection($categories), __('responses.api.recipe.categories.list'));
    }
}
