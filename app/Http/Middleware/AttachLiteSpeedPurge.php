<?php

namespace App\Http\Middleware;

use App\Services\DmsAdmin\LiteSpeedPurge;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds X-LiteSpeed-Purge to any response when something queued a purge
 * during the request (e.g. the publish observer). Register it as a global
 * "web" middleware (see README) so CMS saves in the admin panel purge too.
 */
class AttachLiteSpeedPurge
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $value = LiteSpeedPurge::headerValue();
        if ($value !== null && ! $response->headers->has('X-LiteSpeed-Purge')) {
            $response->headers->set('X-LiteSpeed-Purge', $value);
        }
        return $response;
    }
}
