<?php

namespace Tests\Feature\Panel;

use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_role_is_forbidden_from_panel_apis(): void
    {
        $user = $this->createUserWithRole('customer');

        $response = $this->actingAs($user, 'sanctum')->getJson('/panel/v1/profile');

        $response->assertForbidden();
    }

    public function test_non_customer_role_can_access_panel_apis(): void
    {
        $user = $this->createUserWithRole('maintainer');

        $response = $this->actingAs($user, 'sanctum')->getJson('/panel/v1/profile');

        $response->assertOk();
    }

    private function createUserWithRole(string $roleName): User
    {
        $user = User::query()->create([
            'full_name' => 'Test User',
            'email' => $roleName.'@example.com',
            'password' => 'password',
        ]);
        $role = Role::query()->create([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        $user->assignRole($role);

        return $user;
    }
}
