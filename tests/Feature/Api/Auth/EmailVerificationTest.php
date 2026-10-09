<?php

namespace Tests\Feature\Api\Auth;

use App\Mail\EmailVerificationCode;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
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

        $registration = $this->postJson('/api/v1/auth/register', [
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $registration->assertOk()->assertJsonStructure(['data' => ['challenge_token', 'purpose']]);
        $registration->assertJsonPath('data.purpose', 'register');

        $challengeToken = $registration->json('data.challenge_token');
        $cacheKey = 'email-verification:register:'.$challengeToken;
        $challenge = Cache::store('redis')->get($cacheKey);
        $user = User::query()->where('email', 'customer@example.com')->firstOrFail();

        $this->assertNull($user->email_verified_at);
        $this->assertTrue($user->hasRole('customer'));
        $this->assertIsArray($challenge);
        Mail::assertSent(
            EmailVerificationCode::class,
            fn (EmailVerificationCode $mail): bool => $mail->hasTo($user->email) && $mail->code === $challenge['code']
        );

        $verification = $this->postJson('/api/v1/auth/verify', [
            'challenge_token' => $challengeToken,
            'purpose' => 'register',
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

        $registration = $this->postJson('/api/v1/auth/register', [
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $challengeToken = $registration->json('data.challenge_token');
        $challenge = Cache::store('redis')->get('email-verification:register:'.$challengeToken);
        $invalidCode = $challenge['code'] === '000000' ? '000001' : '000000';

        $this->postJson('/api/v1/auth/verify', [
            'challenge_token' => $challengeToken,
            'purpose' => 'register',
            'code' => $invalidCode,
        ])->assertStatus(400)->assertJsonPath('message', 'Invalid or expired verification code.');

        $user = User::query()->where('email', 'customer@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_re_registering_an_unverified_email_resends_a_fresh_challenge_for_the_existing_user(): void
    {
        config(['cache.stores.redis.driver' => 'array']);
        Role::query()->create(['name' => 'customer', 'guard_name' => 'web']);
        Mail::fake();

        $payload = [
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $firstRegistration = $this->postJson('/api/v1/auth/register', $payload)->assertOk();
        $firstChallengeToken = $firstRegistration->json('data.challenge_token');
        $cache = Cache::store('redis');
        $firstChallenge = $cache->get('email-verification:register:'.$firstChallengeToken);

        $secondRegistration = $this->postJson('/api/v1/auth/register', $payload);
        $secondRegistration->assertOk()->assertJsonStructure(['data' => ['challenge_token', 'purpose']]);

        $secondChallengeToken = $secondRegistration->json('data.challenge_token');
        $secondChallenge = $cache->get('email-verification:register:'.$secondChallengeToken);
        $this->assertNotSame($firstChallengeToken, $secondChallengeToken);
        $this->assertNull($cache->get('email-verification:register:'.$firstChallengeToken));
        $this->assertNotNull($secondChallenge);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        $this->postJson('/api/v1/auth/verify', [
            'challenge_token' => $firstChallengeToken,
            'purpose' => 'register',
            'code' => $firstChallenge['code'],
        ])->assertStatus(400);

        $this->postJson('/api/v1/auth/verify', [
            'challenge_token' => $secondChallengeToken,
            'purpose' => 'register',
            'code' => $secondChallenge['code'],
        ])->assertOk()->assertJsonStructure(['data' => ['token']]);

        $this->assertNotNull(User::query()->where('email', $payload['email'])->firstOrFail()->email_verified_at);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_verified_email_cannot_register_again(): void
    {
        $user = User::query()->create([
            'full_name' => 'Verified Customer',
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        $this->postJson('/api/v1/auth/register', [
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email'])
            ->assertJsonPath('errors.email.0', 'This email is already registered.');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_unverified_customer_cannot_log_in(): void
    {
        $user = User::query()->create([
            'full_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);
        $user->assignRole(Role::query()->create(['name' => 'customer', 'guard_name' => 'web']));

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertUnauthorized();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_forget_password_uses_its_purpose_and_changes_password_after_verification(): void
    {
        config(['cache.stores.redis.driver' => 'array']);
        Mail::fake();

        $user = User::query()->create([
            'full_name' => 'Verified Customer',
            'email' => 'customer@example.com',
            'password' => 'old-password',
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        $forgetPassword = $this->postJson('/api/v1/auth/forgot_password', [
            'email' => $user->email,
        ]);

        $forgetPassword->assertOk()->assertJsonStructure(['data' => ['challenge_token', 'purpose']]);
        $forgetPassword->assertJsonPath('data.purpose', 'forget_password');

        $challengeToken = $forgetPassword->json('data.challenge_token');
        $challengeKey = 'email-verification:forget_password:'.$challengeToken;
        $challenge = Cache::store('redis')->get($challengeKey);

        $this->assertSame('forget_password', $challenge['purpose']);
        $this->assertSame($user->email, $challenge['email']);
        Mail::assertSent(
            EmailVerificationCode::class,
            fn (EmailVerificationCode $mail): bool => $mail->hasTo($user->email) && $mail->code === $challenge['code']
        );

        $this->postJson('/api/v1/auth/verify', [
            'challenge_token' => $challengeToken,
            'purpose' => 'register',
            'code' => $challenge['code'],
        ])->assertStatus(400);

        $this->postJson('/api/v1/auth/verify', [
            'challenge_token' => $challengeToken,
            'purpose' => 'forget_password',
            'code' => $challenge['code'],
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ])->assertUnprocessable()->assertJsonValidationErrors(['password']);

        $this->postJson('/api/v1/auth/verify', [
            'challenge_token' => $challengeToken,
            'purpose' => 'forget_password',
            'code' => $challenge['code'],
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertOk()
            ->assertJsonPath('data', null)
            ->assertJsonPath('message', 'Password updated successfully.');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
        $this->assertFalse(Hash::check('old-password', $user->fresh()->password));
        $this->assertNull(Cache::store('redis')->get($challengeKey));
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
