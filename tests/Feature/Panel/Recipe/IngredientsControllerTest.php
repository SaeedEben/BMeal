<?php

namespace Tests\Feature\Panel\Recipe;

use App\Models\Recipe\Ingredient;
use App\Models\User\Permission;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_route_binds_ingredient_and_persists_its_changes(): void
    {
        $user = User::query()->create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $permission = Permission::query()->create([
            'name' => 'panel.recipe.ingredient.update',
            'guard_name' => 'web',
        ]);
        $role = Role::query()->create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $ingredient = Ingredient::query()->create([
            'name' => 'Chicken Breast',
            'slug' => 'chicken-breast',
            'description' => 'Original description.',
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson(
            "/panel/v1/recipes/ingredient/{$ingredient->id}",
            [
                'name' => 'Updated Chicken Breast',
                'slug' => 'chicken-breast',
                'description' => 'Updated description.',
            ],
        );

        $response->assertOk();
        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'name' => 'Updated Chicken Breast',
            'slug' => 'chicken-breast',
            'description' => 'Updated description.',
        ]);
    }
}
