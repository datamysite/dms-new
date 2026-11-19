<?php

namespace App\Services\DmsAdmin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class IndexNow
{
    /**
     * Submit up to 10,000 URLs. Returns ['ok'=>bool,'status'=>int,'count'=>int,'error'=>?string].
     * Every call is written to the dms_indexnow_log table (the "proof" T-237 / TT-17 ask for).
     */
    public static function submit(array $urls): array
    {
        $key = (string) config('dms_admin.indexnow.key');
        $site = config('dms_admin.site_url');
        $host = parse_url($site, PHP_URL_HOST);

        $urls = array_values(array_unique(array_filter(array_map(function ($u) use ($site) {
            $u = trim((string) $u);
            if ($u === '') {
                return null;
            }
            return str_starts_with($u, 'http') ? $u : $site.'/'.ltrim($u, '/');
        }, $urls))));

        if ($key === '') {
            return static::log($urls, 0, 'INDEXNOW_KEY is not set in .env');
        }
        // The site is served from the Laravel root (public_html), not public/,
        // so the key file can live in either place. It must be reachable at
        // {site_url}/{key}.txt for IndexNow to verify it.
        if (! is_file(public_path($key.'.txt')) && ! is_file(base_path($key.'.txt'))) {
            return static::log($urls, 0, "Key file {$key}.txt not found in public_html/ or public/");
        }
        if (empty($urls)) {
            return static::log($urls, 0, 'No URLs given');
        }

        try {
            $resp = Http::timeout(20)->acceptJson()->post(config('dms_admin.indexnow.endpoint'), [
                'host' => $host,
                'key' => $key,
                'keyLocation' => $site.'/'.$key.'.txt',
                'urlList' => array_slice($urls, 0, 10000),
            ]);
            $status = $resp->status();
            $ok = in_array($status, [200, 202], true);
            return static::log($urls, $status, $ok ? null : mb_substr($resp->body(), 0, 300));
        } catch (\Throwable $e) {
            return static::log($urls, 0, $e->getMessage());
        }
    }

    protected static function log(array $urls, int $status, ?string $error): array
    {
        $ok = $error === null && in_array($status, [200, 202], true);
        try {
            if (Schema::hasTable('dms_indexnow_log')) {
                DB::table('dms_indexnow_log')->insert([
                    'url_count' => count($urls),
                    'urls' => json_encode(array_slice($urls, 0, 500)),
                    'http_status' => $status,
                    'ok' => $ok,
                    'error' => $error,
                    'created_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('IndexNow log write failed: '.$e->getMessage());
        }
        return ['ok' => $ok, 'status' => $status, 'count' => count($urls), 'error' => $error];
    }
}