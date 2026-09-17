<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiter
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'api:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 60)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'message' => 'Too many requests. Please try again in ' . $seconds . ' seconds.',
            ], 429)->header('Retry-After', $seconds);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
