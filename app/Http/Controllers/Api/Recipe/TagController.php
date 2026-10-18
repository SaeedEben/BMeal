<?php

namespace App\Http\Controllers\Api\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Recipe\Tag\TagListRequest;
use App\Http\Resources\Panel\Recipe\Tag\TagListResource;
use App\Models\Recipe\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
      public function list(TagListRequest $request)
    {
        $tags = Tag::all();

        return self::resource(TagListResource::collection($tags), __('responses.api.recipe.tags.list'));
    }
}
