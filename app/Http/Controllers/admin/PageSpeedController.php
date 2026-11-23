<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\PageSpeedService;
use Illuminate\Http\Request;

/**
 * PageSpeed Checker (admin tool): lists the site's pages and runs Google PageSpeed Insights on them.
 * The API key stays on the server (config/services.php -> PAGESPEED_API_KEY); the browser only talks to run().
 * The Google call, page list and report trimming live in App\Services\PageSpeedService (shared with `php artisan seo:pagespeed`).
 */
class PageSpeedController extends Controller
{
    public function index(PageSpeedService $psi)
    {
        $data['menu'] = 'seo.pagespeed';
        $data['bases'] = $psi->bases();
        $data['groups'] = $psi->pageGroups();
        $data['hasKey'] = $psi->hasKey();

        return view('admin.seo.pagespeed.index')->with($data);
    }

    /** POST: run PageSpeed Insights for one URL + strategy and return a trimmed report */
    public function run(Request $request, PageSpeedService $psi)
    {
        $request->validate(['url' => 'required|url|max:300', 'strategy' => 'required|in:mobile,desktop']);

        [$status, $body] = $psi->run($request->input('url'), $request->input('strategy'), $request->boolean('refresh'));

        return response()->json($body, $status);
    }
}
