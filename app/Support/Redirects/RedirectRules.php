<?php
// File: app/Support/Redirects/RedirectRules.php
// Pure PHP (no framework calls) so the rules can be unit-tested on their own.

namespace App\Support\Redirects;

class RedirectRules
{
    /**
     * Normalise an old URL (the redirect source) to a path like "/old-slug".
     * Returns [path, error]. error is null when valid.
     */
    public static function normalizeFrom(string $input, string $ownHost = ''): array
    {
        $value = trim($input);

        if ($value === '') {
            return [null, 'from_path is empty'];
        }

        // Accept a full URL on our own domain and keep only the path.
        if (preg_match('#^https?://#i', $value)) {
            $host = strtolower((string) parse_url($value, PHP_URL_HOST));
            $own  = strtolower(preg_replace('/^www\./', '', $ownHost));
            if ($own !== '' && preg_replace('/^www\./', '', $host) !== $own) {
                return [null, 'from_path must be on this site'];
            }
            $value = (string) (parse_url($value, PHP_URL_PATH) ?? '/');
        }

        if (strpbrk($value, '?#') !== false) {
            return [null, 'from_path must not contain ? or #'];
        }
        if ($value[0] !== '/') {
            $value = '/' . $value;
        }
        if (str_contains($value, '//') || str_contains($value, '..') || preg_match('/\s/', $value)) {
            return [null, 'from_path contains //, .. or spaces'];
        }

        $value = rawurldecode($value);
        if ($value !== '/') {
            $value = rtrim($value, '/');
        }
        if (strlen($value) > 255) {
            return [null, 'from_path is longer than 255 characters'];
        }

        return [$value, null];
    }

    /**
     * Normalise a target. Internal targets become "/path" (query string allowed);
     * external targets must be https and are only allowed when $allowExternal is true.
     * Returns [target, isExternal, error].
     */
    public static function normalizeTo(string $input, string $ownHost = '', bool $allowExternal = false): array
    {
        $value = trim($input);

        if ($value === '') {
            return [null, false, 'to_url is empty'];
        }

        if (preg_match('#^https?://#i', $value)) {
            $host = strtolower((string) parse_url($value, PHP_URL_HOST));
            $own  = strtolower(preg_replace('/^www\./', '', $ownHost));

            if ($own !== '' && preg_replace('/^www\./', '', $host) === $own) {
                // Same site written as a full URL: keep it as a path.
                $path  = (string) (parse_url($value, PHP_URL_PATH) ?? '/');
                $query = parse_url($value, PHP_URL_QUERY);
                $value = $path . ($query ? '?' . $query : '');
            } else {
                if (! $allowExternal) {
                    return [null, true, 'external targets are not allowed (config redirects.allow_external_targets)'];
                }
                if (stripos($value, 'https://') !== 0) {
                    return [null, true, 'external targets must use https'];
                }
                return [$value, true, null];
            }
        }

        if ($value[0] !== '/') {
            $value = '/' . $value;
        }
        if (str_starts_with($value, '//') || preg_match('/\s/', $value)) {
            return [null, false, 'to_url is not a valid path'];
        }

        // Strip a trailing slash from the path part (not from the query).
        $parts = explode('?', $value, 2);
        if ($parts[0] !== '/') {
            $parts[0] = rtrim($parts[0], '/');
        }
        $value = $parts[0] . (isset($parts[1]) && $parts[1] !== '' ? '?' . $parts[1] : '');

        if (strlen($value) > 2048) {
            return [null, false, 'to_url is too long'];
        }

        return [$value, false, null];
    }

    /** Path part of an internal target (no query string). */
    public static function pathOf(string $target): string
    {
        $path = explode('?', $target, 2)[0];
        return $path === '' ? '/' : $path;
    }

    public static function isReserved(string $path, array $reservedPrefixes): bool
    {
        foreach ($reservedPrefixes as $prefix) {
            $prefix = rtrim($prefix, '/');
            if ($prefix !== '' && ($path === $prefix || str_starts_with($path, $prefix . '/'))) {
                return true;
            }
        }
        return false;
    }

    public static function isProtected(string $path, array $protected, bool $caseSensitive = true): bool
    {
        $cmp = $caseSensitive ? $path : strtolower($path);
        foreach ($protected as $rule) {
            $rule = $caseSensitive ? $rule : strtolower($rule);
            if (str_ends_with($rule, '*')) {
                if (str_starts_with($cmp, rtrim(substr($rule, 0, -1), '/'))) {
                    return true;
                }
            } elseif ($cmp === ($rule === '/' ? '/' : rtrim($rule, '/'))) {
                return true;
            }
        }
        return false;
    }

    public static function isSoft404(string $target, array $soft404Targets): bool
    {
        $path = self::pathOf($target);
        foreach ($soft404Targets as $t) {
            if ($path === ($t === '/' ? '/' : rtrim($t, '/'))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check a proposed redirect against the existing map (from => to, active only,
     * excluding the record being edited). Returns a list of problems:
     *   ['code' => 'self_loop'|'chain'|'loop'|'incoming', 'message' => ..., 'detail' => ...]
     * 'incoming' is not fatal: the caller decides (retarget or refuse).
     */
    public static function graphProblems(string $from, string $to, array $map, bool $isExternal = false): array
    {
        $problems = [];

        if (! $isExternal) {
            $toPath = self::pathOf($to);

            if ($toPath === $from) {
                $problems[] = ['code' => 'self_loop', 'message' => 'the redirect points at itself', 'detail' => null];
                return $problems;
            }

            // Target is itself redirected: follow to the final destination.
            if (isset($map[$toPath])) {
                $seen  = [$from => true, $toPath => true];
                $hop   = $map[$toPath];
                $final = $hop;
                $steps = 1;
                while (isset($map[self::pathOf($hop)]) && $steps < 20) {
                    $p = self::pathOf($hop);
                    if (isset($seen[$p])) {
                        $problems[] = ['code' => 'loop', 'message' => 'the target leads back into a redirect loop', 'detail' => array_keys($seen)];
                        return $problems;
                    }
                    $seen[$p] = true;
                    $hop      = $map[$p];
                    $final    = $hop;
                    $steps++;
                }
                if (self::pathOf($final) === $from) {
                    $problems[] = ['code' => 'loop', 'message' => 'the target redirects back to this URL', 'detail' => array_keys($seen)];
                    return $problems;
                }
                $problems[] = [
                    'code'    => 'chain',
                    'message' => "the target {$toPath} is itself redirected; point at the final URL instead",
                    'detail'  => ['suggested_to' => $final],
                ];
            }
        }

        // Other redirects currently point at this URL: they would become a chain.
        $incoming = [];
        foreach ($map as $src => $dst) {
            if ($src !== $from && self::pathOf($dst) === $from) {
                $incoming[] = $src;
            }
        }
        if ($incoming) {
            $problems[] = [
                'code'    => 'incoming',
                'message' => 'other redirects point at this URL and would become chains',
                'detail'  => ['sources' => $incoming],
            ];
        }

        return $problems;
    }

    /** Whole-map audit: chains, loops, soft-404 targets, self loops, case twins. */
    public static function audit(array $map, array $soft404Targets): array
    {
        $out = ['chains' => [], 'loops' => [], 'soft_404' => [], 'self_loops' => [], 'case_twins' => []];

        foreach ($map as $from => $to) {
            $toPath = self::pathOf($to);
            if ($toPath === $from) {
                $out['self_loops'][] = $from;
                continue;
            }
            if (self::isSoft404($to, $soft404Targets)) {
                $out['soft_404'][] = ['from' => $from, 'to' => $to];
            }
            if (isset($map[$toPath])) {
                $seen = [$from => true];
                $p    = $toPath;
                $loop = false;
                while (isset($map[$p])) {
                    if (isset($seen[$p])) { $loop = true; break; }
                    $seen[$p] = true;
                    $p = self::pathOf($map[$p]);
                }
                if ($loop) {
                    $out['loops'][] = ['from' => $from, 'path' => array_keys($seen)];
                } else {
                    $out['chains'][] = ['from' => $from, 'via' => $toPath, 'final' => $p];
                }
            }
        }

        $lower = [];
        foreach (array_keys($map) as $from) {
            $lower[strtolower($from)][] = $from;
        }
        foreach ($lower as $group) {
            if (count($group) > 1) {
                $out['case_twins'][] = $group;
            }
        }

        return $out;
    }
}
