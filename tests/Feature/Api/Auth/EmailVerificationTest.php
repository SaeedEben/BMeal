<?php

namespace Tests\Feature\Api\Auth;

use App\Mail\EmailVerificationCode;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_verifies_email_and_returns_an_authentication_token(): void
    {
        config(['cache.stores.redis.driver' => 'array']);
        Role::query()->create(['name' => 'customer', 'guard_name' => 'web']);
        Mail::fake();

        $registration = $this->postJson('/api/auth/register', [
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $registration->assertOk()->assertJsonStructure(['data' => ['challenge_token']]);

        $challengeToken = $registration->json('data.challenge_token');
        $cacheKey = 'email-verification:'.$challengeToken;
        $challenge = Cache::store('redis')->get($cacheKey);
        $user = User::query()->where('email', 'customer@example.com')->firstOrFail();

        $this->assertNull($user->email_verified_at);
        $this->assertTrue($user->hasRole('customer'));
        $this->assertIsArray($challenge);
        Mail::assertSent(
            EmailVerificationCode::class,
            fn (EmailVerificationCode $mail): bool => $mail->hasTo($user->email) && $mail->code === $challenge['code']
        );

        $verification = $this->postJson('/api/auth/verify', [
            'challenge_token' => $challengeToken,
            'code' => $challenge['code'],
        ]);

        $verification->assertOk()->assertJsonStructure(['data' => ['token']]);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertNull(Cache::store('redis')->get($cacheKey));
    }

    public function test_invalid_verification_code_does_not_verify_email_or_issue_a_token(): void
    {
        config(['cache.stores.redis.driver' => 'array']);
        Role::query()->create(['name' => 'customer', 'guard_name' => 'web']);
        Mail::fake();

        $registration = $this->postJson('/api/auth/register', [
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $challengeToken = $registration->json('data.challenge_token');
        $challenge = Cache::store('redis')->get('email-verification:'.$challengeToken);
        $invalidCode = $challenge['code'] === '000000' ? '000001' : '000000';

        $this->postJson('/api/auth/verify', [
            'challenge_token' => $challengeToken,
            'code' => $invalidCode,
        ])->assertStatus(400)->assertJsonPath('message', 'Invalid or expired verification code.');

        $user = User::query()->where('email', 'customer@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_unverified_customer_cannot_log_in(): void
    {
        $user = User::query()->create([
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);
        $user->assignRole(Role::query()->create(['name' => 'customer', 'guard_name' => 'web']));

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertUnauthorized();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
