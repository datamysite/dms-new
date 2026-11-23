<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps every non-production host (staging.datamysite.com, localhost, ...) out of search engines.
 * Decided by the request host, not .env, so a copied .env can never noindex production or index staging.
 */
class StagingNoindex
{
    public const PRODUCTION_HOSTS = ['datamysite.com', 'www.datamysite.com'];

    public static function isStaging(?Request $request = null): bool
    {
        $request = $request ?: request();
        return !in_array(strtolower($request->getHost()), self::PRODUCTION_HOSTS, true);
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if (self::isStaging($request)) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        }
        return $response;
    }
}
