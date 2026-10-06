<?php

namespace App\Http\Middleware;

use App\Models\User\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('sanctum')->user();

        if ($user instanceof User) {
            DB::table($user->getTable())
                ->where($user->getKeyName(), $user->getAuthIdentifier())
                ->update(['last_active' => now()]);
        }

        return $next($request);
    }
}
