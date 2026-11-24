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
 * Second job, same variable: when APP_URL has a folder (local XAMPP: APP_URL=http://localhost/datamysite) root-relative links (href="/x", from templates
 * and stored content) get that folder, so they do not jump to http://localhost/x. No folder (staging, production) = nothing to do.
 * E-mail addresses (name@datamysite.com) are not URLs and are never touched.
 */
class RewriteProductionUrls
{
    /** matches http(s)://, //, and JSON-escaped https:\/\/ forms of the live host; group 1 = scheme part, 2 = slashes */
    private const PATTERN = '~(https?:)?([\\\\]?/[\\\\]?/)(?:www\.)?datamysite\.com(?![\w-]|\.[\w])~i';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $appUrl = (string) config('app.url');
        $base = self::baseFor($appUrl);          // job 1: live-site URLs -> APP_URL (null on the live site)
        $prefix = self::prefixFor($appUrl);      // job 2: root-relative links get the folder of APP_URL ('' unless served from a sub-folder)
        if ($base === null && $prefix === '') {
            return $response;
        }

        $location = $response->headers->get('Location');
        if ($location) {
            if ($base !== null && stripos($location, 'datamysite.com') !== false) {
                $location = self::rewrite($location, $base);
            }
            if ($prefix !== '') {
                $location = self::prefixBasePath($location, $prefix, true);
            }
            $response->headers->set('Location', $location);
        }

        $type = (string) $response->headers->get('Content-Type');
        if (stripos($type, 'text/html') !== false && !$response instanceof \Symfony\Component\HttpFoundation\StreamedResponse
            && !$response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            $html = $response->getContent();
            if (is_string($html)) {
                if ($base !== null && stripos($html, 'datamysite.com') !== false) {
                    $html = self::rewrite($html, $base);
                }
                if ($prefix !== '') {
                    $html = self::prefixBasePath($html, $prefix);
                }
                $response->setContent($html);
            }
        }

        return $response;
    }

    /** The folder part of APP_URL ("/datamysite" for http://localhost/datamysite), '' when the site is served from the host root. */
    public static function prefixFor(string $appUrl): string
    {
        $path = (string) parse_url(trim($appUrl), PHP_URL_PATH);
        $path = '/' . trim($path, '/');
        return $path === '/' ? '' : $path;
    }

    /**
     * Prefix root-relative URLs (href="/x") with the site's folder so they work when the site is not at the host root.
     * Left alone: //cdn/x, https://x, #x, mailto:, tel:, and URLs that already start with the folder.
     * $isUrl = true treats the whole string as one URL (a Location header).
     */
    public static function prefixBasePath(string $text, string $prefix, bool $isUrl = false): string
    {
        if ($prefix === '') {
            return $text;
        }
        $already = preg_quote(ltrim($prefix, '/'), '~');
        if ($isUrl) {
            return preg_match('~^/(?!/)(?!' . $already . '(?:[/?#]|$))~', $text) ? $prefix . $text : $text;
        }
        $pattern = '~(\s(?:href|src|action|poster|formaction|data-[\w-]*(?:href|url|src))\s*=\s*)(["\'])/(?!/)(?!' . $already . '(?:[/"\'?#]|$))~i';
        return (string) preg_replace($pattern, '$1$2' . $prefix . '/', $text);
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
