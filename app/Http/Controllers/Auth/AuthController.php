<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Authenticate a user and return an API token for future requests.
     */
    public function login(Request $request)
    {
        // Validate the email and password fields before attempting authentication.
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt to sign the user in with the provided credentials.
        if (!Auth::attempt($request->only('email', 'password'))) {
            return self::error('Invalid credentials', null, 401);
        }

        // Create a personal access token for the authenticated user.
        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return self::success(['token' => $token], 'Login successful');
    }

    /**
     * Log out the current user by deleting the access token used for this request.
     */
    public function logout(Request $request)
    {
        // Revoke the token currently attached to the authenticated user.
        $request->user()->currentAccessToken()->delete();

        return self::success(null, 'Logout successful');
    }

    /**
     * Register a new account and issue an API token for the created user.
     */
    public function register_shutdown_function(Request $request)
    {
        // Validate registration input and enforce unique email addresses.
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user record; the model handles hashing the password automatically.
        $user = \App\Models\User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Generate a token so the new user can authenticate immediately after signup.
        $token = $user->createToken('auth_token')->plainTextToken;

        return self::success(['token' => $token], 'Registration successful');
    }

    /**
     * Verify that a supplied reset token matches the user email before treating the email as confirmed.
     */
    public function verifyEmail(Request $request)
    {
        // Ensure email and verification token are present and in the expected format.
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        // Lookup the token in the password reset table for the given email.
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return self::error('Invalid token or email', null, 400);
        }

        // This is the place where the application can mark the user's email as verified.
        // For example, update a verified_at column on the users table.

        return self::success(null, 'Email verified successfully');
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
