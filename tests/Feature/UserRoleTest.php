<?php

namespace Tests\Feature;

use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigning_a_role_replaces_the_users_existing_role(): void
    {
        $user = User::query()->create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $firstRole = Role::query()->create(['name' => 'first', 'guard_name' => 'web']);
        $secondRole = Role::query()->create(['name' => 'second', 'guard_name' => 'web']);

        $user->assignRole($firstRole);
        $user->assignRole($secondRole);

        $this->assertSame([$secondRole->uuid], $user->roles()->pluck('uuid')->all());
    }
}
