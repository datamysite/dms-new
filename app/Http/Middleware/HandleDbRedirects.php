<?php
// File: app/Http/Middleware/HandleDbRedirects.php
// Global middleware: answers a 301 for any URL listed in the redirects table.
// Runs before routing, so it behaves like the old Route::permanentRedirect lines.
// Fails open: if the table or cache is unavailable, the request continues normally.

namespace App\Http\Middleware;

use App\Support\Redirects\RedirectMap;
use App\Support\Redirects\RedirectRules;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HandleDbRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('redirects.enabled', true) || ! ($request->isMethod('GET') || $request->isMethod('HEAD'))) {
            return $next($request);
        }

        $path = rawurldecode($request->getPathInfo());
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        if (RedirectRules::isReserved($path, (array) config('redirects.reserved_prefixes', []))) {
            return $next($request);
        }

        try {
            $map = RedirectMap::all();
        } catch (Throwable $e) {
            report($e);            // e.g. table not migrated yet: never take the site down
            return $next($request);
        }

        $key = config('redirects.case_sensitive', true) ? $path : strtolower($path);
        if (! isset($map[$key])) {
            return $next($request);
        }

        [$to, $status, $id] = $map[$key];

        $target = preg_match('#^https?://#i', $to) ? $to : url($to);

        $query = $request->getQueryString();
        if ($query && config('redirects.preserve_query', true)) {
            $target .= (str_contains($target, '?') ? '&' : '?') . $query;
        }

        if (config('redirects.track_hits', true)) {
            app()->terminating(function () use ($id) {
                try {
                    // Query builder (not Eloquent): does not touch updated_at or clear the cache.
                    DB::table('redirects')->where('id', $id)->update([
                        'hits'        => DB::raw('hits + 1'),
                        'last_hit_at' => now(),
                    ]);
                } catch (Throwable $e) {
                    // hit counting must never affect the visitor
                }
            });
        }

        return redirect()->away($target, $status);
    }
}
