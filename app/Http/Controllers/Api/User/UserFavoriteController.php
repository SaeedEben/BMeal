<?php

namespace App\Http\Controllers\Api\User;

use App\Enum\Common\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Favorites\UserFavoriteIndexRequest;
use App\Http\Requests\Api\User\Favorites\UserFavoriteStoreRequest;
use App\Http\Resources\Api\User\Favorites\UserFavoriteIndexResource;
use App\Models\User\UserFavorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserFavoriteController extends Controller
{
    public function index(UserFavoriteIndexRequest $request)
    {
        $user = Auth::user();

        $favorites = UserFavorite::query()
            ->with('recipe', 'recipe.ingredients', 'recipe.tags', 'recipe.categories')
            ->where('user_id', $user->id)
            ->get()
            ->orderByDesc('created_at');

        return $this->resource(UserFavoriteIndexResource::collection($favorites), __('responses.api.user.favorites.index'));
    }


    public function store(UserFavoriteStoreRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $favorite = DB::transaction(function () use ($user, $validated){
             return UserFavorite::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'recipe_id' => $validated['recipe_id'],
                ],
                [
                    'status' => StatusEnum::ACTIVE,
                    'created_at' => now(),
                ]
            );
        });

        return $this->resource($favorite, __('responses.api.user.favorites.store'));
    }

    public function destroy(UserFavoriteStoreRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        return DB::transaction(function () use ($user, $validated) {
            $favorite = UserFavorite::where('user_id', $user->id)
            ->where('recipe_id', $validated['recipe_id'])
            ->first();

            if ($favorite) {
                $favorite->delete();
                return $this->success(__('responses.api.user.favorites.destroy'));
            }

            return $this->error(__('responses.api.user.favorites.not_found'), 404);
        });
    }
}
