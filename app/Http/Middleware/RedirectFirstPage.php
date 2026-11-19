<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectFirstPage
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->query('page') === '1') {
            $query = $request->except('page');
            $url = $request->url() . (empty($query) ? '' : '?' . http_build_query($query));

            return redirect()->to($url, 301);
        }

        return $next($request);
    }
}