<?php

namespace Tests\Feature\Http\Middleware;

use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateLastActiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_api_request_updates_last_active(): void
    {
        $user = User::query()->create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'last_active' => '2026-10-05 12:00:00',
        ]);
        Sanctum::actingAs($user);
        $this->travelTo('2026-10-06 12:00:00');

        $response = $this->getJson('/api/user');

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'last_active' => '2026-10-06 12:00:00',
        ]);
    }
}
