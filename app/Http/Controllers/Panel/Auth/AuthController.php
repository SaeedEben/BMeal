<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\Auth\LoginRequest;
use App\Http\Requests\Panel\Auth\LogoutRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error(__('responses.errors.auth.login_failed'), 401);
        }

        $user = Auth::user();
        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return $this->success([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ], __('responses.auth.login'));
    }

    public function logout(LogoutRequest $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, __('responses.auth.logout'));
    }
}
