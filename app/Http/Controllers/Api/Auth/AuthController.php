<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ForgetPasswordRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\VerifyRequest;
use App\Mail\EmailVerificationCode;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

        $challengeToken = $this->issueEmailChallenge($user, 'register');

        return self::success(
            ['challenge_token' => $challengeToken, 'purpose' => 'register'],
            __('responses.api.auth.verification')
        );
    }

    /**
     * Verify the email challenge and issue an authentication token.
     */
    public function verifyEmail(VerifyRequest $request)
    {
        $data = $request->validated();
        $cache = Cache::store('redis');
        $purpose = $data['purpose'];
        $cacheKey = $this->emailChallengeCacheKey($purpose, $data['challenge_token']);
        $challenge = $cache->get($cacheKey);

        if (
            ! is_array($challenge) ||
            ! isset($challenge['user_id'], $challenge['email'], $challenge['purpose'], $challenge['code']) ||
            ! is_string($challenge['email']) ||
            $challenge['purpose'] !== $purpose ||
            ! is_string($challenge['code']) ||
            ! hash_equals($challenge['code'], $data['code'])
        ) {
            return self::error(__('responses.errors.auth.verify_code_failed'), null, 400);
        }

        $emailCacheKey = $this->emailChallengeIndexKey($purpose, $challenge['email']);

        if ($cache->get($emailCacheKey) !== $data['challenge_token']) {
            return self::error(__('responses.errors.auth.verify_code_failed'), null, 400);
        }

        $user = User::query()->find($challenge['user_id']);

        if (! $user || Str::lower($user->email) !== $challenge['email']) {
            $cache->forget($cacheKey);
            $cache->forget($emailCacheKey);

            return self::error(__('responses.errors.auth.verify_code_failed'), null, 400);
        }

        if ($purpose === 'register') {
            if ($user->email_verified_at !== null) {
                $cache->forget($cacheKey);
                $cache->forget($emailCacheKey);

                return self::error(__('responses.errors.auth.verify_code_failed'), null, 400);
            }

            $user->forceFill(['email_verified_at' => now()])->save();
        } else {
            $user->forceFill(['password' => $data['password']])->save();
        }

        $cache->forget($cacheKey);
        $cache->forget($emailCacheKey);

        $responseData = $purpose === 'register'
            ? ['token' => $user->createToken('auth_token')->plainTextToken]
            : null;
        $message = $purpose === 'register'
            ? __('responses.api.auth.verification_success')
            : __('responses.api.auth.password_reset_success');

        return self::success($responseData, $message);
    }

    /**
     * Generate a password reset token and store it so the user can reset their password securely.
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $data = $request->validated();
        $user = User::query()->where('email', $data['email'])->firstOrFail();
        $challengeToken = $this->issueEmailChallenge($user, 'forget_password');

        return self::success(
            ['challenge_token' => $challengeToken, 'purpose' => 'forget_password'],
            __('responses.api.auth.verification')
        );
    }

    private function issueEmailChallenge(User $user, string $purpose): string
    {
        $challengeToken = Str::random(64);
        // $verificationCode = (string) random_int(100000, 999999);
        $verificationCode = '336699';
        $cache = Cache::store('redis');
        $emailCacheKey = $this->emailChallengeIndexKey($purpose, $user->email);
        $previousChallengeToken = $cache->get($emailCacheKey);

        if (is_string($previousChallengeToken)) {
            $cache->forget($this->emailChallengeCacheKey($purpose, $previousChallengeToken));
        }

        $expiresAt = now()->addMinutes(self::VERIFICATION_TTL_MINUTES);

        $cache->put(
            $this->emailChallengeCacheKey($purpose, $challengeToken),
            [
                'user_id' => (string) $user->getKey(),
                'email' => Str::lower($user->email),
                'purpose' => $purpose,
                'code' => $verificationCode,
            ],
            $expiresAt
        );
        $cache->put($emailCacheKey, $challengeToken, $expiresAt);

        // Mail::to($user->email)->send(new EmailVerificationCode($verificationCode));

        return $challengeToken;
    }

    private function emailChallengeCacheKey(string $purpose, string $challengeToken): string
    {
        return 'email-verification:'.$purpose.':'.$challengeToken;
    }

    private function emailChallengeIndexKey(string $purpose, string $email): string
    {
        return 'email-verification-email:'.$purpose.':'.hash('sha256', Str::lower($email));
    }

    public function login_with_google(Request $request)
    {
        return self::error('Google login is not implemented yet.', null, 501);
    }
}
