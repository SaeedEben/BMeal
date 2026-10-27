<?php

namespace Tests\Feature\Api\Dashboard;

use App\Enum\Common\StatusEnum;
use App\Enum\File\StorageEnum;
use App\Enum\File\TypeEnum;
use App\Models\File\File;
use App\Models\Plan\MealPlan;
use App\Models\Plan\MealPlanItem;
use App\Models\Recipe\Recipe;
use App\Models\Recipe\Tag;
use App\Models\User\Role;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_dashboard_returns_todays_meals_week_plan_and_ranked_recommendations(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-28 12:00:00'));
        $user = $this->createCustomer();
        $breakfastImage = $this->createImage('breakfast.jpg');
        $dinnerImage = $this->createImage('dinner.jpg');
        $tomorrowImage = $this->createImage('tomorrow.jpg');
        $breakfast = $this->createRecipe('breakfast', 'breakfast', $breakfastImage->id);
        $dinner = $this->createRecipe('dinner', 'dinner', $dinnerImage->id);
        $tomorrowLunch = $this->createRecipe('tomorrow-lunch', 'lunch', $tomorrowImage->id);
        $mealPlan = MealPlan::query()->create([
            'user_id' => $user->id,
            'week_start_date' => now()->startOfWeek()->toDateString(),
            'status' => StatusEnum::ACTIVE,
        ]);
        $this->addMeal($mealPlan, $breakfast, now()->toDateString());
        $this->addMeal($mealPlan, $dinner, now()->toDateString());
        $this->addMeal($mealPlan, $tomorrowLunch, now()->addDay()->toDateString());

        $commonTag = $this->createTag('common');
        $secondTag = $this->createTag('second');
        $thirdTag = $this->createTag('third');
        $oldTag = $this->createTag('old');

        $favoriteRecipes = [
            $this->createRecipe('favorite-newest'),
            $this->createRecipe('favorite-second'),
            $this->createRecipe('favorite-third'),
            $this->createRecipe('favorite-too-old'),
        ];
        $this->attachTags($favoriteRecipes[0], [$commonTag, $secondTag]);
        $this->attachTags($favoriteRecipes[1], [$commonTag]);
        $this->attachTags($favoriteRecipes[2], [$thirdTag]);
        $this->attachTags($favoriteRecipes[3], [$oldTag]);
        $this->createFavorites($user, $favoriteRecipes);

        $recentRecipes = [
            $this->createRecipe('recent-newest'),
            $this->createRecipe('recent-second'),
            $this->createRecipe('recent-third'),
            $this->createRecipe('recent-too-old'),
        ];
        $this->attachTags($recentRecipes[0], [$commonTag]);
        $this->attachTags($recentRecipes[1], [$thirdTag]);
        $this->attachTags($recentRecipes[2], [$secondTag]);
        $this->attachTags($recentRecipes[3], [$oldTag]);
        $this->createRecentlySaw($user, $recentRecipes);

        $bestRecommendation = $this->createRecipe('recommendation-best');
        $secondRecommendation = $this->createRecipe('recommendation-second');
        $thirdRecommendation = $this->createRecipe('recommendation-third');
        $oldTagRecommendation = $this->createRecipe('recommendation-old-tag');
        $this->attachTags($bestRecommendation, [$commonTag, $secondTag, $thirdTag]);
        $this->attachTags($secondRecommendation, [$commonTag, $secondTag]);
        $this->attachTags($thirdRecommendation, [$commonTag]);
        $this->attachTags($oldTagRecommendation, [$oldTag]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/dashboard/');

        $response
            ->assertOk()
            ->assertJsonPath('data.today_meals.breakfast.id', $breakfast->id)
            ->assertJsonPath('data.today_meals.breakfast.image', $breakfastImage->preview())
            ->assertJsonPath('data.today_meals.lunch', null)
            ->assertJsonPath('data.today_meals.dinner.id', $dinner->id)
            ->assertJsonPath('data.current_week_plan.0.image', $breakfastImage->preview())
            ->assertJsonPath('data.current_week_plan.0.date', now()->toDateString())
            ->assertJsonPath('data.current_week_plan.0.meal_type', 'breakfast')
            ->assertJsonCount(3, 'data.current_week_plan')
            ->assertJsonCount(3, 'data.recommendations');

        $this->assertSame(
            [$bestRecommendation->id, $secondRecommendation->id, $thirdRecommendation->id],
            array_column($response->json('data.recommendations'), 'id'),
        );
    }

    public function test_dashboard_returns_null_for_sections_without_data(): void
    {
        $user = $this->createCustomer();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard/')
            ->assertOk()
            ->assertJsonPath('data.today_meals', null)
            ->assertJsonPath('data.current_week_plan', null)
            ->assertJsonPath('data.recommendations', null);
    }

    private function createCustomer(): User
    {
        $user = User::query()->create([
            'full_name' => 'Dashboard Customer',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]);
        $user->assignRole(Role::query()->firstOrCreate(['name' => 'customer', 'guard_name' => 'web']));

        return $user;
    }

    private function createImage(string $filename): File
    {
        return File::query()->create([
            'name' => $filename,
            'file_type' => TypeEnum::MEAL_PHOTO->value,
            'storage_path' => 'recipes/'.$filename,
            'storage_provider' => StorageEnum::LOCAL->value,
        ]);
    }

    private function createRecipe(string $slug, ?string $mealType = null, ?string $imageId = null): Recipe
    {
        return Recipe::query()->create([
            'name' => str($slug)->headline(),
            'slug' => $slug,
            'meal_type' => $mealType,
            'prep_time_minutes' => 20,
            'calories_per_serving' => '250.00',
            'image_id' => $imageId,
        ]);
    }

    private function addMeal(MealPlan $mealPlan, Recipe $recipe, string $date): void
    {
        MealPlanItem::query()->create([
            'meal_plan_id' => $mealPlan->id,
            'recipe_id' => $recipe->id,
            'date' => $date,
            'meal_type' => $recipe->meal_type,
            'servings' => 1,
            'status' => StatusEnum::ACTIVE,
        ]);
    }

    private function createTag(string $slug): Tag
    {
        return Tag::query()->create([
            'name' => str($slug)->headline(),
            'slug' => $slug,
        ]);
    }

    /** @param array<int, Tag> $tags */
    private function attachTags(Recipe $recipe, array $tags): void
    {
        $recipe->tags()->attach(array_map(fn (Tag $tag): string => $tag->id, $tags));
    }

    /** @param array<int, Recipe> $recipes */
    private function createFavorites(User $user, array $recipes): void
    {
        foreach ($recipes as $index => $recipe) {
            DB::table('user_favorites')->insert([
                'user_id' => $user->id,
                'recipe_id' => $recipe->id,
                'created_at' => now()->subDays($index),
                'status' => StatusEnum::ACTIVE->value,
            ]);
        }
    }

    /** @param array<int, Recipe> $recipes */
    private function createRecentlySaw(User $user, array $recipes): void
    {
        foreach ($recipes as $index => $recipe) {
            DB::table('recently_saws')->insert([
                'user_id' => $user->id,
                'recipe_id' => $recipe->id,
                'created_at' => now()->subDays($index),
            ]);
        }
    }
}
