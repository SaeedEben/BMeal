<?php

namespace Tests\Feature\Panel\User;

use App\Models\User\Permission;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_syncs_permissions_from_the_payload(): void
    {
        $user = $this->createAdmin(['panel.user.role.store']);
        $permissions = collect(['panel.recipe.category.index', 'panel.recipe.category.store'])
            ->map(fn (string $name): Permission => Permission::query()->create([
                'name' => $name,
                'guard_name' => 'web',
            ]));

        $response = $this->actingAs($user, 'sanctum')->postJson('/panel/v1/users/role', [
            'name' => 'maintainer',
            'permissions' => $permissions->pluck('uuid')->all(),
        ]);

        $response->assertOk();
        $role = Role::query()->where('name', 'maintainer')->firstOrFail();
        $actualPermissionUuids = $role->permissions()->pluck('uuid')->all();
        $expectedPermissionUuids = $permissions->pluck('uuid')->all();
        sort($actualPermissionUuids);
        sort($expectedPermissionUuids);

        $this->assertSame($expectedPermissionUuids, $actualPermissionUuids);
    }

    public function test_update_replaces_permissions_with_the_payload(): void
    {
        $user = $this->createAdmin(['panel.user.role.update']);
        $existingPermission = Permission::query()->create([
            'name' => 'panel.recipe.category.index',
            'guard_name' => 'web',
        ]);
        $permissions = collect(['panel.recipe.category.store', 'panel.recipe.category.update'])
            ->map(fn (string $name): Permission => Permission::query()->create([
                'name' => $name,
                'guard_name' => 'web',
            ]));
        $role = Role::query()->create([
            'name' => 'maintainer',
            'guard_name' => 'web',
        ]);
        $role->givePermissionTo($existingPermission);

        $response = $this->actingAs($user, 'sanctum')->putJson(
            "/panel/v1/users/role/{$role->uuid}",
            [
                'name' => 'maintainer',
                'permissions' => $permissions->pluck('uuid')->all(),
            ],
        );

        $response->assertOk();
        $actualPermissionUuids = $role->fresh()->permissions()->pluck('uuid')->all();
        $expectedPermissionUuids = $permissions->pluck('uuid')->all();
        sort($actualPermissionUuids);
        sort($expectedPermissionUuids);

        $this->assertSame($expectedPermissionUuids, $actualPermissionUuids);
    }

    public function test_store_rejects_permission_ids_that_do_not_exist(): void
    {
        $user = $this->createAdmin(['panel.user.role.store']);

        $response = $this->actingAs($user, 'sanctum')->postJson('/panel/v1/users/role', [
            'name' => 'maintainer',
            'permissions' => [Str::uuid()->toString()],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('permissions.0');
        $this->assertDatabaseMissing('roles', ['name' => 'maintainer']);
    }

    public function test_update_rejects_permission_ids_that_do_not_exist(): void
    {
        $user = $this->createAdmin(['panel.user.role.update']);
        $permission = Permission::query()->create([
            'name' => 'panel.recipe.category.index',
            'guard_name' => 'web',
        ]);
        $role = Role::query()->create([
            'name' => 'maintainer',
            'guard_name' => 'web',
        ]);
        $role->givePermissionTo($permission);

        $response = $this->actingAs($user, 'sanctum')->putJson(
            "/panel/v1/users/role/{$role->uuid}",
            [
                'name' => 'maintainer',
                'permissions' => [Str::uuid()->toString()],
            ],
        );

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('permissions.0');
        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $role->uuid,
            'permission_id' => $permission->uuid,
        ]);
    }

    /** @param array<int, string> $permissionNames */
    private function createAdmin(array $permissionNames): User
    {
        $user = User::query()->create([
            'full_name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        $adminRole = Role::query()->create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $permissions = collect($permissionNames)
            ->map(fn (string $name): Permission => Permission::query()->create([
                'name' => $name,
                'guard_name' => 'web',
            ]));

        $adminRole->givePermissionTo($permissions);
        $user->assignRole($adminRole);

        return $user;
    }
}
