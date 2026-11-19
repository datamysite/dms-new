<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DmsAdminToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('dms_admin.token');

        if ($expected === '' || strlen($expected) < 32) {
            return response()->json(['error' => 'DMS_ADMIN_API_TOKEN is not set (or shorter than 32 characters).'], 503);
        }

        if (! hash_equals($expected, (string) $request->bearerToken())) {
            return response()->json(['error' => 'Unauthorised.'], 401);
        }

        $isWrite = ! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true);
        if ($isWrite && ! config('dms_admin.writes_enabled')) {
            return response()->json(['error' => 'Writes are switched off (DMS_ADMIN_WRITES=false).'], 423);
        }

        return $next($request);
    }
}
