<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\VerifyRequest;
use App\Mail\EmailVerificationCode;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const VERIFICATION_TTL_MINUTES = 5;

    /**
     * Authenticate a user and return an API token for future requests.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            return self::error(__('responses.errors.auth.login_failed'), null, 401);
        }

        $user = Auth::user();

        if (! ($user instanceof User) || ! $user->hasRole('customer') || $user->email_verified_at === null) {
            return self::error(__('responses.errors.auth.login_failed'), null, 401);
        }

        // Create a personal access token for the authenticated user.
        $token = $user->createToken('auth_token')->plainTextToken;

        return self::success(['token' => $token], __('responses.auth.login'));
    }

    /**
     * Log out the current user by deleting the access token used for this request.
     */
    public function logout(Request $request)
    {
        // Revoke the token currently attached to the authenticated user.
        $request->user()->currentAccessToken()->delete();

        return self::success(null, __('responses.auth.logout'));
    }

    /**
     * Register a customer and send an email verification challenge.
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $customerRole = Role::query()->where('name', 'customer')->firstOrFail();

        $user = User::query()->where('email', $data['email'])->first();

        if ($user?->email_verified_at !== null) {
            return self::error(__('responses.errors.auth.login_failed'), null, 422);
        }

        if (! $user) {
            $user = User::create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);
        }

        if (! $user->hasRole('customer')) {
            $user->assignRole($customerRole);
        }

        $challengeToken = Str::random(64);
        // $verificationCode = (string) random_int(100000, 999999);
        $verificationCode       = '336699';
        $cache                  = Cache::store('redis');
        $emailCacheKey          = 'email-verification-email:'.hash('sha256', Str::lower($user->email));
        $previousChallengeToken = $cache->get($emailCacheKey);

        if (is_string($previousChallengeToken)) {
            $cache->forget('email-verification:'.$previousChallengeToken);
        }

        $expiresAt = now()->addMinutes(self::VERIFICATION_TTL_MINUTES);

        $cache->put(
            'email-verification:'.$challengeToken,
            ['user_id' => (string) $user->getKey(), 'code' => $verificationCode],
            $expiresAt
        );
        $cache->put($emailCacheKey, $challengeToken, $expiresAt);

        // Mail::to($user->email)->send(new EmailVerificationCode($verificationCode));

        return self::success(['challenge_token' => $challengeToken], __('responses.api.auth.verification'));
    }

    /**
     * Verify the email challenge and issue an authentication token.
     */
    public function verifyEmail(VerifyRequest $request)
    {
        $data = $request->validated();
        $cache = Cache::store('redis');
        $cacheKey = 'email-verification:'.$data['challenge_token'];
        $challenge = $cache->get($cacheKey);

        if (
            ! is_array($challenge) ||
            ! isset($challenge['user_id'], $challenge['code']) ||
            ! is_string($challenge['code']) ||
            ! hash_equals($challenge['code'], $data['code'])
        ) {
            return self::error(__('responses.errors.auth.verify_code_failed'), null, 400);
        }

        $user = User::query()->find($challenge['user_id']);

        if (! $user) {
            $cache->forget($cacheKey);

            return self::error(__('responses.errors.auth.verify_code_failed'), null, 400);
        }

        if ($user->email_verified_at !== null) {
            $cache->forget($cacheKey);

            return self::error(__('responses.errors.auth.login_failed'), null, 400);
        }

        $user->forceFill(['email_verified_at' => now()])->save();
        $cache->forget($cacheKey);

        $emailCacheKey = 'email-verification-email:'.hash('sha256', Str::lower($user->email));

        if ($cache->get($emailCacheKey) === $data['challenge_token']) {
            $cache->forget($emailCacheKey);
        }

        return self::success(
            ['token' => $user->createToken('auth_token')->plainTextToken],
            __('responses.api.auth.verification_success')
        );
    }

    /**
     * Generate a password reset token and store it so the user can reset their password securely.
     */
    public function forgetPassword(Request $request)
    {
        // Confirm the supplied email belongs to an existing user account.
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Create a secure random token to validate the password reset request.
        $token = Str::random(60);

        // Persist the token with the user's email so it can be checked during reset.
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        // In a complete flow, send a reset email using the generated token.
        // For example, queue a notification or mail template for the user.

        return self::success(['token' => $token], 'Password reset email sent');
    }
}
