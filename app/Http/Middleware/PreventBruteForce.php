<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PreventBruteForce
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'brute_force:' . $request->ip();
        $maxAttempts = 5;
        $decayMinutes = 5;

        if (Cache::get($key, 0) >= $maxAttempts) {
            abort(429, 'Too many login attempts. Please try again in ' . $decayMinutes . ' minutes.');
        }

        return $next($request);
    }

    public static function incrementAttempts(string $ip): void
    {
        $key = 'brute_force:' . $ip;
        $attempts = Cache::get($key, 0);
        Cache::put($key, $attempts + 1, now()->addMinutes(5));
    }

    public static function clearAttempts(string $ip): void
    {
        Cache::forget('brute_force:' . $ip);
    }
}
