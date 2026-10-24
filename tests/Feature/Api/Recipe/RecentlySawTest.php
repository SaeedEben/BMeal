<?php

namespace Tests\Feature\Api\Recipe;

use App\Enum\File\StorageEnum;
use App\Enum\File\TypeEnum;
use App\Models\Country\Country;
use App\Models\File\File;
use App\Models\Recipe\Recipe;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RecentlySawTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_views_keep_only_the_nine_most_recent_recipes(): void
    {
        $user = $this->createCustomer();
        $country = Country::query()->create([
            'name' => 'Italy',
            'code' => 'IT',
            'slug' => 'italy',
        ]);
        $image = File::query()->create([
            'name' => 'recipe.png',
            'file_type' => TypeEnum::MEAL_PHOTO,
            'mime_type' => 'image/png',
            'size' => 1,
            'storage_path' => 'meals/recipe.png',
            'storage_provider' => StorageEnum::PUBLIC,
        ]);
        $recipes = [];

        for ($index = 1; $index <= 10; $index++) {
            $recipes[] = Recipe::query()->create([
                'country_id' => $country->id,
                'image_id' => $image->id,
                'name' => "Recipe {$index}",
                'slug' => "recipe-{$index}",
            ]);
        }

        $firstViewAt = now()->startOfSecond();

        foreach ($recipes as $index => $recipe) {
            $this->travelTo($firstViewAt->copy()->addSeconds($index + 1));

            $this->actingAs($user, 'sanctum')
                ->getJson("/api/v1/recipe/recipes/{$recipe->id}")
                ->assertOk();
        }

        $this->assertDatabaseCount('recently_saws', 9);
        $this->assertDatabaseMissing('recently_saws', [
            'user_id' => $user->id,
            'recipe_id' => $recipes[0]->id,
        ]);

        $response = $this->getJson('/api/v1/user/recently_saw');

        $response
            ->assertOk()
            ->assertJsonCount(9, 'data')
            ->assertJsonPath('data.0.recipe_id', $recipes[9]->id)
            ->assertJsonPath('data.8.recipe_id', $recipes[1]->id);

        $this->travelTo($firstViewAt->copy()->addSeconds(11));

        $this->getJson("/api/v1/recipe/recipes/{$recipes[0]->id}")->assertOk();

        $this->assertDatabaseCount('recently_saws', 9);
        $this->assertDatabaseMissing('recently_saws', [
            'user_id' => $user->id,
            'recipe_id' => $recipes[1]->id,
        ]);
        $this->getJson('/api/v1/user/recently_saw')
            ->assertJsonPath('data.0.recipe_id', $recipes[0]->id);
    }

    public function test_recently_saw_returns_only_the_authenticated_users_recipes(): void
    {
        $user = $this->createCustomer();
        $otherUser = $this->createCustomer();
        $recipe = Recipe::query()->create([
            'name' => 'Private Recent Recipe',
            'slug' => 'private-recent-recipe',
        ]);

        DB::table('recently_saws')->insert([
            'user_id' => $otherUser->id,
            'recipe_id' => $recipe->id,
            'created_at' => now(),
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/user/recently_saw')
            ->assertOk()
            ->assertJsonCount(0, 'data');
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
