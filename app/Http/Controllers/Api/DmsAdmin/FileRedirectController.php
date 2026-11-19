<?php

namespace App\Http\Controllers\Api\DmsAdmin;

use App\Http\Controllers\Controller;
use App\Models\DmsAdminRevision;
use App\Services\DmsAdmin\LiteSpeedPurge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Route::redirect(...) lines in routes/redirects.php.
 * The redirect table API cannot see these, and a file route wins over a
 * disabled DB row - which is why /bridge-banner-... kept answering 301 (T-228)
 * and why the 5 broken redirects of TT-02 can't be fixed from the CMS.
 * Lines are commented out, never deleted, so the history stays in the file.
 */
class FileRedirectController extends Controller
{
    protected const PATTERN = "/Route::(?:redirect|permanentRedirect)\s*\(\s*['\"]([^'\"]+)['\"]\s*,\s*['\"]([^'\"]+)['\"](?:\s*,\s*(\d{3}))?\s*\)\s*;/";

    public function index(Request $request)
    {
        $file = config('dms_admin.redirects_file');
        if (! is_file($file)) {
            return response()->json(['error' => 'redirects file not found', 'file' => $file], 404);
        }
        $out = [];
        foreach (file($file) as $i => $line) {
            if (preg_match(self::PATTERN, $line, $m)) {
                $trim = ltrim($line);
                $out[] = [
                    'line' => $i + 1,
                    'from' => $m[1],
                    'to' => $m[2],
                    'status' => isset($m[3]) ? (int) $m[3] : (str_contains($line, 'permanentRedirect') ? 301 : 302),
                    'active' => ! (str_starts_with($trim, '//') || str_starts_with($trim, '#')),
                ];
            }
        }
        if ($s = $request->query('search')) {
            $out = array_values(array_filter($out, fn ($r) => stripos($r['from'].' '.$r['to'], $s) !== false));
        }
        return response()->json(['file' => 'routes/redirects.php', 'count' => count($out), 'data' => $out]);
    }

    /**
     * Body: from (path) and action = disable | retarget (with to) | enable.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'from' => 'required|string|max:255',
            'action' => 'required|in:disable,enable,retarget',
            'to' => 'required_if:action,retarget|nullable|string|max:255',
            'note' => 'nullable|string|max:500',
        ]);

        $file = config('dms_admin.redirects_file');
        $lines = file($file);
        $before = implode('', $lines);
        $changed = [];

        foreach ($lines as $i => $line) {
            if (! preg_match(self::PATTERN, $line, $m) || rtrim($m[1], '/') !== rtrim($data['from'], '/')) {
                continue;
            }
            $trim = ltrim($line);
            $indent = substr($line, 0, strlen($line) - strlen($trim));
            $isActive = ! (str_starts_with($trim, '//') || str_starts_with($trim, '#'));

            if ($data['action'] === 'disable' && $isActive) {
                $lines[$i] = $indent.'// [disabled '.now()->format('Y-m-d').' via DMS admin API] '.$trim;
                $changed[] = $i + 1;
            } elseif ($data['action'] === 'enable' && ! $isActive) {
                $lines[$i] = $indent.preg_replace('#^(//|\#)\s*(\[disabled[^\]]*\]\s*)?#', '', $trim);
                $changed[] = $i + 1;
            } elseif ($data['action'] === 'retarget' && $isActive) {
                $to = '/'.ltrim($data['to'], '/');
                // Replace only the second argument (the target), never the source path.
                $pos = strpos($line, $m[1]) + strlen($m[1]);
                $lines[$i] = substr($line, 0, $pos).preg_replace('/'.preg_quote($m[2], '/').'/', $to, substr($line, $pos), 1);
                $changed[] = $i + 1;
            }
        }

        if (! $changed) {
            return response()->json(['message' => 'No matching line changed.', 'from' => $data['from']], 404);
        }

        $after = implode('', $lines);
        // Lint the new routes file before saving.
        $tmp = tempnam(sys_get_temp_dir(), 'dmsroutes');
        file_put_contents($tmp, $after);
        exec(escapeshellarg(PHP_BINARY).' -l '.escapeshellarg($tmp).' 2>&1', $o, $code);
        @unlink($tmp);
        if ($code !== 0) {
            return response()->json(['error' => 'Rejected: routes file would not parse.', 'detail' => implode(' ', $o)], 422);
        }

        $rev = DmsAdminRevision::create(['kind' => 'redirects_file', 'target' => 'routes/redirects.php', 'content' => $before, 'note' => $data['note'] ?? null]);
        file_put_contents($file, $after);
        Artisan::call('route:clear');
        LiteSpeedPurge::queue([$data['from']]);

        return LiteSpeedPurge::attachHeader(response()->json([
            'message' => 'routes/redirects.php updated; route cache cleared; source URL purged.',
            'lines_changed' => $changed,
            'revision_id' => $rev->id,
            'note' => 'If the site runs php artisan route:cache in production, run it again (or call POST cache/purge with routes=true).',
        ]));
    }

    public function restore(int $id)
    {
        $rev = DmsAdminRevision::where('kind', 'redirects_file')->findOrFail($id);
        $file = config('dms_admin.redirects_file');
        $back = DmsAdminRevision::create(['kind' => 'redirects_file', 'target' => 'routes/redirects.php', 'content' => file_get_contents($file), 'note' => "Before restoring revision {$id}"]);
        file_put_contents($file, $rev->content);
        Artisan::call('route:clear');
        LiteSpeedPurge::queue(['*']);
        return LiteSpeedPurge::attachHeader(response()->json(['message' => "Restored revision {$id}.", 'undo_revision_id' => $back->id]));
    }
}
