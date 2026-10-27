<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Enum\Common\StatusEnum;
use App\Enum\Recipe\MealTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\DashboardDataRequest;
use App\Http\Resources\Api\Dashboard\DashboardDataResource;
use App\Models\Plan\MealPlanItem;
use App\Models\Recipe\Recipe;
use App\Models\User\RecentlySaw;
use App\Models\User\UserFavorite;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboardData(DashboardDataRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $weekPlanItems = MealPlanItem::query()
            ->where('status', StatusEnum::ACTIVE->value)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->whereHas('mealPlan', fn (Builder $query): Builder => $query
                ->where('user_id', $userId)
                ->where('status', StatusEnum::ACTIVE->value)
                ->whereDate('week_start_date', $weekStart->toDateString()))
            ->with('recipe.image')
            ->orderBy('date')
            ->orderBy('meal_type')
            ->get();

        $todayItems = $weekPlanItems->filter(fn (MealPlanItem $item): bool => $item->date->isSameDay($today));
        $todayMeals = $todayItems->isEmpty()
            ? null
            : collect(['breakfast', 'lunch', 'dinner'])
                ->mapWithKeys(fn (string $mealType): array => [
                    $mealType => $todayItems
                        ->first(fn (MealPlanItem $item): bool => ($item->meal_type instanceof MealTypeEnum
                            ? $item->meal_type->value
                            : $item->meal_type) === $mealType)
                        ?->recipe,
                ])
                ->all();

        $favoriteRecipeIds = UserFavorite::query()
            ->where('user_id', $userId)
            ->where('status', StatusEnum::ACTIVE->value)
            ->orderByDesc('created_at')
            ->orderByDesc('recipe_id')
            ->limit(3)
            ->pluck('recipe_id');

        $recentRecipeIds = RecentlySaw::query()
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->orderByDesc('recipe_id')
            ->limit(3)
            ->pluck('recipe_id');

        $sourceRecipeIds = $favoriteRecipeIds->merge($recentRecipeIds)->unique()->values();
        $tagIds = $sourceRecipeIds->isEmpty()
            ? collect()
            : DB::table('recipe_tags')
                ->join('tags', 'tags.id', '=', 'recipe_tags.tag_id')
                ->whereIn('recipe_tags.recipe_id', $sourceRecipeIds)
                ->where('recipe_tags.status', StatusEnum::ACTIVE->value)
                ->where('tags.status', StatusEnum::ACTIVE->value)
                ->distinct()
                ->pluck('recipe_tags.tag_id');

        $recommendations = null;

        if ($tagIds->isNotEmpty()) {
            $matchingTags = function (Builder $query) use ($tagIds): void {
                $query->whereIn('tags.id', $tagIds)
                    ->where('tags.status', StatusEnum::ACTIVE->value)
                    ->where('recipe_tags.status', StatusEnum::ACTIVE->value);
            };

            $recommendations = Recipe::query()
                ->with('image')
                ->withCount(['tags as matched_tags_count' => $matchingTags])
                ->where('recipes.status', StatusEnum::ACTIVE->value)
                ->whereNotIn('recipes.id', $sourceRecipeIds)
                ->whereHas('tags', $matchingTags)
                ->orderByDesc('matched_tags_count')
                ->orderBy('recipes.id')
                ->limit(3)
                ->get();

            if ($recommendations->isEmpty()) {
                $recommendations = null;
            }
        }

        return $this->resource(new DashboardDataResource([
            'today_meals' => $todayMeals,
            'current_week_plan' => $weekPlanItems->isEmpty() ? null : $weekPlanItems,
            'recommendations' => $recommendations,
        ]), __('responses.api.dashboard.data'));
    }
}
