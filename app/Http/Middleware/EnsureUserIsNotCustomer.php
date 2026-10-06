<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->hasRole('customer')) {
            abort(403, __('responses.errors.unauthorized'));
        }

        return $next($request);
    }
}
