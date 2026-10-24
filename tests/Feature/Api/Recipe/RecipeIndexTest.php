<?php

namespace Tests\Feature\Api\Recipe;

use App\Models\Recipe\Recipe;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RecipeIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_ten_random_recipes_without_filters(): void
    {
        $user = $this->createCustomer();
        $this->createRecipes('dinner', 12);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/recipe/recipes');

        $response->assertOk()->assertJsonCount(9, 'data');
    }

    public function test_index_returns_all_matching_recipes_when_a_filter_is_present(): void
    {
        $user = $this->createCustomer();
        $this->createRecipes('dinner', 12);
        $this->createRecipes('breakfast', 1);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/recipe/recipes?meal_type=dinner');

        $response->assertOk()->assertJsonCount(12, 'data');
    }

    public function test_index_marks_only_the_current_users_favorite_recipes_as_saved(): void
    {
        $user = $this->createCustomer();
        $otherUser = $this->createCustomer();
        $userFavorite = $this->createRecipe('user-favorite');
        $otherUsersFavorite = $this->createRecipe('other-users-favorite');
        $notFavorite = $this->createRecipe('not-favorite');

        DB::table('user_favorites')->insert([
            [
                'user_id' => $user->id,
                'recipe_id' => $userFavorite->id,
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'user_id' => $otherUser->id,
                'recipe_id' => $otherUsersFavorite->id,
                'status' => 'active',
                'created_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/recipe/recipes');

        $response->assertOk();
        $recipesById = collect($response->json('data'))->keyBy('id');

        $this->assertTrue($recipesById[$userFavorite->id]['saved']);
        $this->assertFalse($recipesById[$otherUsersFavorite->id]['saved']);
        $this->assertFalse($recipesById[$notFavorite->id]['saved']);
    }

    private function createCustomer(): User
    {
        $user = User::query()->create([
            'full_name' => 'Test Customer',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]);
        $user->assignRole(Role::query()->firstOrCreate(['name' => 'customer', 'guard_name' => 'web']));

        return $user;
    }

    private function createRecipes(string $mealType, int $count): void
    {
        for ($index = 1; $index <= $count; $index++) {
            Recipe::query()->create([
                'name' => "{$mealType} Recipe {$index}",
                'slug' => "{$mealType}-recipe-{$index}",
                'meal_type' => $mealType,
            ]);
        }
    }

    private function createRecipe(string $slug): Recipe
    {
        return Recipe::query()->create([
            'name' => str($slug)->headline(),
            'slug' => $slug,
        ]);
    }
}
