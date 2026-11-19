<?php

namespace App\Services\DmsAdmin;

use Illuminate\Support\Facades\Artisan;

/**
 * Cache purge for a Laravel site behind LiteSpeed.
 *
 * LiteSpeed purges when ANY response carries an X-LiteSpeed-Purge header,
 * so the purge is attached to the API response itself (see attachHeader()).
 * Laravel's own view / config / route caches are cleared directly.
 */
class LiteSpeedPurge
{
    /** @var string[] */
    protected static array $pending = [];

    /** Queue paths (or "*") to purge on the current response. */
    public static function queue(array $paths): void
    {
        foreach ($paths as $p) {
            if ($p === '*') {
                static::$pending = ['*'];
                return;
            }
            $p = '/'.ltrim(parse_url($p, PHP_URL_PATH) ?: $p, '/');
            static::$pending[] = $p;
        }
        static::$pending = array_values(array_unique(static::$pending));
    }

    public static function pending(): array
    {
        return static::$pending;
    }

    /** Header value LiteSpeed understands: "*" or a comma list of paths. */
    public static function headerValue(): ?string
    {
        if (empty(static::$pending)) {
            return null;
        }
        return in_array('*', static::$pending, true) ? '*' : implode(', ', static::$pending);
    }

    public static function attachHeader($response)
    {
        $value = static::headerValue();
        if ($value !== null && method_exists($response, 'header')) {
            $response->header('X-LiteSpeed-Purge', $value);
        }
        static::$pending = [];
        return $response;
    }

    /** Clear Laravel's compiled views and app cache. Returns what ran. */
    public static function clearLaravel(bool $views = true, bool $cache = true): array
    {
        $ran = [];
        if ($views) {
            Artisan::call('view:clear');
            $ran[] = 'view:clear';
        }
        if ($cache) {
            Artisan::call('cache:clear');
            $ran[] = 'cache:clear';
        }
        return $ran;
    }
}
