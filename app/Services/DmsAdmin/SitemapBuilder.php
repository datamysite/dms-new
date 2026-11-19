<?php

namespace App\Services\DmsAdmin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Builds sitemap.xml from the database (TT-09):
 * published blogs + service pages + fixed paths,
 * minus anything with an active redirect, any excluded / noindex path,
 * drafts and pagination.
 */
class SitemapBuilder
{
    public static function collect(): array
    {
        $site = config('dms_admin.site_url');
        $t = config('dms_admin.tables');
        $cfg = config('dms_admin.sitemap');

        $urls = []; // path => lastmod

        foreach ($cfg['extra_paths'] ?? [] as $p) {
            $urls[$p] = now()->toAtomString();
        }

        if (Schema::hasTable($cfg['services_table'] ?? 'services')) {
            $svcTable = $cfg['services_table'] ?? 'services';
            $cols = Schema::getColumnListing($svcTable);
            $q = DB::table($svcTable);
            if (in_array('status', $cols, true)) {
                $q->where('status', 1);
            }
            foreach ($q->get() as $row) {
                $path = static::servicePath($row, $svcTable);
                if ($path) {
                    $urls[$path] = isset($row->updated_at) ? date(DATE_ATOM, strtotime($row->updated_at)) : now()->toAtomString();
                }
            }
        }

        if (Schema::hasTable($t['blogs'])) {
            foreach (DB::table($t['blogs'])->where('status', 1)->get(['slug', 'updated_at']) as $b) {
                $urls['/'.ltrim($b->slug, '/')] = date(DATE_ATOM, strtotime($b->updated_at ?? 'now'));
            }
        }

        $redirected = Schema::hasTable($t['redirects'])
            ? DB::table($t['redirects'])->where('is_active', 1)->pluck('from_path')->map(fn ($p) => rtrim($p, '/') ?: '/')->all()
            : [];
        $redirected = array_flip($redirected);
        $exclude = array_flip($cfg['exclude_paths'] ?? []);

        $final = [];
        foreach ($urls as $path => $lastmod) {
            $norm = rtrim($path, '/') ?: '/';
            if (isset($redirected[$norm]) || isset($exclude[$norm]) || str_contains($norm, '?')) {
                continue;
            }
            $final[$site.($norm === '/' ? '/' : $norm)] = $lastmod;
        }
        ksort($final);
        return $final;
    }

    /** Service URL: use a stored path/url column if present, else build from parent slugs. */
    protected static function servicePath(object $row, string $table): ?string
    {
        foreach (['path', 'url', 'url_path'] as $c) {
            if (! empty($row->{$c})) {
                return '/'.ltrim(parse_url($row->{$c}, PHP_URL_PATH) ?: $row->{$c}, '/');
            }
        }
        if (empty($row->slug)) {
            return null;
        }
        $parts = [$row->slug];
        $parent = $row->parent_id ?? 0;
        $guard = 0;
        while ($parent && $guard++ < 6) {
            $p = DB::table($table)->where('id', $parent)->first();
            if (! $p) {
                break;
            }
            array_unshift($parts, $p->slug);
            $parent = $p->parent_id ?? 0;
        }
        return '/'.implode('/', $parts);
    }

    public static function write(): array
    {
        $urls = static::collect();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $loc => $lastmod) {
            $xml .= '  <url><loc>'.htmlspecialchars($loc, ENT_XML1).'</loc><lastmod>'.$lastmod.'</lastmod></url>'."\n";
        }
        $xml .= '</urlset>'."\n";

        $path = config('dms_admin.sitemap.path');
        $tmp = $path.'.tmp';
        file_put_contents($tmp, $xml);
        rename($tmp, $path);

        return ['written' => $path, 'url_count' => count($urls), 'bytes' => strlen($xml)];
    }
}
