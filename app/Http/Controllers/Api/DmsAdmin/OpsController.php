<?php

namespace App\Http\Controllers\Api\DmsAdmin;

use App\Http\Controllers\Controller;
use App\Models\DmsAdminRevision;
use App\Services\DmsAdmin\IndexNow;
use App\Services\DmsAdmin\LiteSpeedPurge;
use App\Services\DmsAdmin\PageInspector;
use App\Services\DmsAdmin\SitemapBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Cache purge per URL, IndexNow per URL list, sitemap audit/rebuild,
 * live-page inspection, duplicate meta rows, blog visibility audit.
 */
class OpsController extends Controller
{
    /** Body: paths[] (or "*"), views (bool), routes (bool). */
    public function purge(Request $request)
    {
        $data = $request->validate([
            'paths' => 'nullable|array',
            'paths.*' => 'string|max:500',
            'views' => 'nullable|boolean',
            'routes' => 'nullable|boolean',
        ]);
        $paths = $data['paths'] ?? ['*'];
        LiteSpeedPurge::queue($paths);
        $ran = LiteSpeedPurge::clearLaravel((bool) ($data['views'] ?? true), true);
        if (! empty($data['routes'])) {
            Artisan::call('route:clear');
            $ran[] = 'route:clear';
        }
        return LiteSpeedPurge::attachHeader(response()->json([
            'message' => 'Purge sent.',
            'litespeed' => LiteSpeedPurge::headerValue() ?? implode(', ', $paths),
            'laravel' => $ran,
        ]));
    }

    /** Body: urls[] (paths or full URLs). Logged to dms_indexnow_log. */
    public function indexnow(Request $request)
    {
        $data = $request->validate(['urls' => 'required|array|min:1|max:10000', 'urls.*' => 'string|max:500']);
        $res = IndexNow::submit($data['urls']);
        return response()->json($res, $res['ok'] ? 200 : 502);
    }

    public function indexnowLog()
    {
        if (! Schema::hasTable('dms_indexnow_log')) {
            return response()->json(['data' => []]);
        }
        return response()->json(['data' => DB::table('dms_indexnow_log')->orderByDesc('id')->limit(50)
            ->get(['id', 'url_count', 'http_status', 'ok', 'error', 'created_at'])]);
    }

    /**
     * GET: compare the live sitemap.xml with the database - lists URLs in the
     * sitemap that redirect / are excluded, and published URLs missing from it.
     * POST with write=true: rebuild sitemap.xml from the database.
     */
    public function sitemapAudit()
    {
        $should = array_keys(SitemapBuilder::collect());
        $file = config('dms_admin.sitemap.path');
        $current = [];
        if (is_file($file)) {
            preg_match_all('#<loc>([^<]+)</loc>#', file_get_contents($file), $m);
            $current = array_map('html_entity_decode', $m[1]);
        }
        $site = config('dms_admin.site_url');
        $rt = config('dms_admin.tables.redirects');
        $redirected = Schema::hasTable($rt) ? DB::table($rt)->where('is_active', 1)->pluck('from_path')->map(fn ($p) => $site.(rtrim($p, '/') ?: '/'))->all() : [];

        return response()->json([
            'sitemap_file' => $file,
            'in_sitemap' => count($current),
            'should_be' => count($should),
            'in_sitemap_but_redirecting' => array_values(array_intersect($current, $redirected)),
            'in_sitemap_but_not_expected' => array_values(array_slice(array_diff($current, $should), 0, 300)),
            'missing_from_sitemap' => array_values(array_slice(array_diff($should, $current), 0, 300)),
        ]);
    }

    public function sitemapRebuild(Request $request)
    {
        $request->validate(['confirm' => 'accepted']);
        return response()->json(SitemapBuilder::write());
    }

    /** GET ?path=/x[&text=1][&nocache=1][&find=AED 1,500] */
    public function inspect(Request $request)
    {
        $path = (string) $request->query('path', '/');
        if ($find = $request->query('find')) {
            return response()->json(PageInspector::find($path, $find));
        }
        return response()->json(PageInspector::inspect($path, (bool) $request->query('text'), (bool) $request->query('nocache')));
    }

    /** POST paths[] - inspect up to 20 pages, summary only. */
    public function inspectMany(Request $request)
    {
        $data = $request->validate(['paths' => 'required|array|min:1|max:20', 'paths.*' => 'string|max:500']);
        $out = [];
        foreach ($data['paths'] as $p) {
            try {
                $r = PageInspector::inspect($p);
                $out[] = array_intersect_key($r, array_flip(['path', 'status', 'location', 'title', 'title_length', 'meta_length', 'h1', 'schema_types', 'word_count', 'placeholders', 'images_without_alt', 'links_to_redirects', 'author_box_detected']));
            } catch (\Throwable $e) {
                $out[] = ['path' => $p, 'error' => $e->getMessage()];
            }
        }
        return response()->json(['data' => $out]);
    }

    /** Meta rows that share a URL (TT-15: ids 728/732, 911/920). */
    public function metaDuplicates()
    {
        $t = config('dms_admin.tables.meta_tags');
        if (! Schema::hasTable($t)) {
            return response()->json(['error' => "Table {$t} not found - set DMS_META_TABLE."], 404);
        }
        $dupUrls = DB::table($t)->select('url', DB::raw('COUNT(*) as n'))->groupBy('url')->having('n', '>', 1)->pluck('url');
        $rows = DB::table($t)->whereIn('url', $dupUrls)->orderBy('url')->orderBy('id')
            ->get(['id', 'url', 'title', 'description', 'updated_at']);
        return response()->json(['duplicate_urls' => count($dupUrls), 'rows' => $rows]);
    }

    /** DELETE meta row by id (copy kept as a revision). */
    public function metaDeleteRow(int $id)
    {
        $t = config('dms_admin.tables.meta_tags');
        $row = DB::table($t)->where('id', $id)->first();
        if (! $row) {
            return response()->json(['error' => 'Row not found.'], 404);
        }
        $siblings = DB::table($t)->where('url', $row->url)->count();
        if ($siblings < 2) {
            return response()->json(['error' => 'Refused: this is the only meta row for its URL. Use the normal meta tools to edit it.'], 422);
        }
        $rev = DmsAdminRevision::create(['kind' => 'meta_row', 'target' => "meta:{$id}", 'content' => json_encode($row)]);
        DB::table($t)->where('id', $id)->delete();
        LiteSpeedPurge::queue([parse_url($row->url, PHP_URL_PATH) ?: '/']);
        return LiteSpeedPurge::attachHeader(response()->json(['message' => "Meta row {$id} deleted.", 'url' => $row->url, 'revision_id' => $rev->id]));
    }

    /**
     * Published blogs whose slug also has an active redirect (they are hidden
     * behind the 301 but still appear in listings / related posts / sitemap).
     * POST with unpublish=true sets them to status 0.
     */
    public function hiddenBlogs(Request $request)
    {
        $bt = config('dms_admin.tables.blogs');
        $rt = config('dms_admin.tables.redirects');
        $from = DB::table($rt)->where('is_active', 1)->pluck('to_url', 'from_path');
        $slugs = $from->keys()->map(fn ($p) => ltrim($p, '/'))->all();
        $rows = DB::table($bt)->where('status', 1)->whereIn('slug', $slugs)->get(['id', 'slug']);

        if ($request->isMethod('post') && $request->boolean('unpublish')) {
            $ids = $rows->pluck('id')->all();
            if ($ids) {
                DB::table($bt)->whereIn('id', $ids)->update(['status' => 0, 'updated_at' => now()]);
            }
            LiteSpeedPurge::queue(['*']);
            return LiteSpeedPurge::attachHeader(response()->json(['message' => count($ids).' blog(s) set to draft (they already redirect).', 'ids' => $ids]));
        }

        return response()->json(['count' => $rows->count(), 'data' => $rows->map(fn ($r) => [
            'id' => $r->id, 'slug' => $r->slug, 'redirects_to' => $from['/'.$r->slug] ?? null,
        ])]);
    }
}
