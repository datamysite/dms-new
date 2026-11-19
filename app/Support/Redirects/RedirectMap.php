<?php
// File: app/Support/Redirects/RedirectMap.php
// Cached lookup table used by the middleware: one cache read per request, no database query.

namespace App\Support\Redirects;

use App\Models\Redirect;
use Illuminate\Support\Facades\Cache;

class RedirectMap
{
    /** @return array<string, array{0:string,1:int,2:int}>  key => [to_url, status_code, id] */
    public static function all(): array
    {
        return Cache::rememberForever(static::key(), function () {
            $caseSensitive = (bool) config('redirects.case_sensitive', true);
            $map = [];

            Redirect::query()
                ->active()
                ->orderBy('id')
                ->get(['id', 'from_path', 'to_url', 'status_code'])
                ->each(function (Redirect $r) use (&$map, $caseSensitive) {
                    $key = $caseSensitive ? $r->from_path : strtolower($r->from_path);
                    $map[$key] = [$r->to_url, (int) $r->status_code, (int) $r->id];
                });

            return $map;
        });
    }

    /** Plain from => to map of active redirects, for chain/loop checks. */
    public static function plain(?int $excludeId = null): array
    {
        $map = [];
        Redirect::query()->active()
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->get(['from_path', 'to_url'])
            ->each(function (Redirect $r) use (&$map) {
                $map[$r->from_path] = $r->to_url;
            });

        return $map;
    }

    public static function forget(): void
    {
        Cache::forget(static::key());
    }

    private static function key(): string
    {
        return (string) config('redirects.cache_key', 'redirects.map.v1');
    }
}
