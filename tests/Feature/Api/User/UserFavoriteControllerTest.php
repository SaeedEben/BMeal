<?php

namespace Tests\Feature\Api\User;

use App\Models\Recipe\Recipe;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserFavoriteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy_deletes_only_the_authenticated_users_favorite(): void
    {
        $user = $this->createCustomer();
        $otherUser = $this->createCustomer();
        $recipe = Recipe::query()->create([
            'name' => 'Favorite Recipe',
            'slug' => 'favorite-recipe',
        ]);

        DB::table('user_favorites')->insert([
            [
                'user_id' => $user->id,
                'recipe_id' => $recipe->id,
                'created_at' => now(),
                'status' => 'active',
            ],
            [
                'user_id' => $otherUser->id,
                'recipe_id' => $recipe->id,
                'created_at' => now(),
                'status' => 'active',
            ],
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/v1/user/favorites', ['recipe_id' => $recipe->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('user_favorites', [
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);
        $this->assertDatabaseHas('user_favorites', [
            'user_id' => $otherUser->id,
            'recipe_id' => $recipe->id,
        ]);
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
}
