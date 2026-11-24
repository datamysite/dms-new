<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps a staging / local copy of the site on its own address.
 *
 * Page content stored in the database (service sections, blog posts, their JSON-LD) contains absolute links to the live site,
 * e.g. <a href="https://datamysite.com/website-development/...">. On staging those links would leave the staging site.
 * The switch is the APP_URL variable of the server's .env:
 *   production: APP_URL=https://datamysite.com           -> this middleware does nothing (output is untouched)
 *   staging:    APP_URL=https://staging.datamysite.com   -> every https://datamysite.com URL in HTML (and in redirect Location headers)
 *                                                           is rewritten to the staging address
 * E-mail addresses (name@datamysite.com) are not URLs and are never touched.
 */
class RewriteProductionUrls
{
    /** matches http(s)://, //, and JSON-escaped https:\/\/ forms of the live host; group 1 = scheme part, 2 = slashes */
    private const PATTERN = '~(https?:)?([\\\\]?/[\\\\]?/)(?:www\.)?datamysite\.com(?![\w-]|\.[\w])~i';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $base = self::baseFor((string) config('app.url'));
        if ($base === null) {
            return $response;
        }

        $location = $response->headers->get('Location');
        if ($location && stripos($location, 'datamysite.com') !== false) {
            $response->headers->set('Location', self::rewrite($location, $base));
        }

        $type = (string) $response->headers->get('Content-Type');
        if (stripos($type, 'text/html') !== false && !$response instanceof \Symfony\Component\HttpFoundation\StreamedResponse
            && !$response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            $html = $response->getContent();
            if (is_string($html) && stripos($html, 'datamysite.com') !== false) {
                $response->setContent(self::rewrite($html, $base));
            }
        }

        return $response;
    }

    /** The address to rewrite to, or null when this server IS the live site (or APP_URL is not a usable URL). */
    public static function baseFor(string $appUrl): ?string
    {
        $appUrl = rtrim(trim($appUrl), '/');
        $host = strtolower((string) parse_url($appUrl, PHP_URL_HOST));
        if ($host === '' || in_array($host, StagingNoindex::PRODUCTION_HOSTS, true)) {
            return null;
        }
        return $appUrl;
    }

    public static function rewrite(string $text, string $base): string
    {
        $noScheme = preg_replace('~^https?:~i', '', $base);          // //staging.datamysite.com[/prefix]
        return preg_replace_callback(self::PATTERN, function (array $m) use ($base, $noScheme) {
            $to = $m[1] === '' ? $noScheme : $base;                   // keep scheme-relative links scheme-relative
            // JSON-escaped input (https:\/\/...) stays escaped
            return strpos($m[2], '\\') !== false ? str_replace('/', '\\/', $to) : $to;
        }, $text);
    }
}
