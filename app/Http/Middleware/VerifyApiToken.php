<?php
// File: app/Http/Middleware/VerifyApiToken.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiToken
{
    /**
     * Rejects any request that does not present the correct bearer token.
     * Uses hash_equals to avoid timing attacks.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $provided = $request->bearerToken();
        $expected = config('services.blog_api.token');

        if (empty($expected) || empty($provided) || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
