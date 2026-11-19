<?php

namespace App\Http\Controllers\Api\DmsAdmin;

use App\Http\Controllers\Controller;
use App\Models\DmsAdminRevision;
use App\Services\DmsAdmin\LiteSpeedPurge;
use App\Services\DmsAdmin\TemplateGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Read and edit whitelisted Blade templates (footer, nav, blog detail,
 * schema include, category page...). Every write: compile + lint check,
 * "before" copy saved as a revision, compiled views cleared, cache purged.
 */
class TemplateController extends Controller
{
    public function index()
    {
        return response()->json(['data' => TemplateGuard::listAllowed()]);
    }

    public function show(Request $request)
    {
        [$rel, $abs, $err] = $this->resolve($request->query('path', ''));
        if ($err) {
            return $err;
        }
        if (! is_file($abs)) {
            return response()->json(['error' => 'File not found.', 'path' => $rel], 404);
        }
        $content = file_get_contents($abs);

        // Optional: return only the lines around a search string (keeps responses small).
        if ($q = $request->query('search')) {
            $lines = explode("\n", $content);
            $hits = [];
            foreach ($lines as $i => $line) {
                if (stripos($line, $q) !== false) {
                    $from = max(0, $i - 3);
                    $hits[] = ['line' => $i + 1, 'context' => implode("\n", array_slice($lines, $from, 7))];
                }
            }
            return response()->json(['path' => $rel, 'search' => $q, 'matches' => count($hits), 'hits' => array_slice($hits, 0, 50)]);
        }

        return response()->json([
            'path' => $rel,
            'bytes' => strlen($content),
            'lines' => substr_count($content, "\n") + 1,
            'content' => $content,
        ]);
    }

    /**
     * Body: path, and EITHER content (full file) OR find + replace (+ optional replace_all).
     * Optional: dry_run (check only), note.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'path' => 'required|string|max:255',
            'content' => 'nullable|string',
            'find' => 'nullable|string',
            'replace' => 'nullable|string',
            'replace_all' => 'nullable|boolean',
            'dry_run' => 'nullable|boolean',
            'note' => 'nullable|string|max:500',
        ]);

        [$rel, $abs, $err] = $this->resolve($data['path']);
        if ($err) {
            return $err;
        }
        if (! is_file($abs)) {
            return response()->json(['error' => 'File not found. Creating new templates is not allowed.', 'path' => $rel], 404);
        }

        $before = file_get_contents($abs);

        if (isset($data['content'])) {
            $after = $data['content'];
        } elseif (isset($data['find'])) {
            $count = substr_count($before, $data['find']);
            if ($count === 0) {
                return response()->json(['error' => 'find text not found.', 'path' => $rel], 422);
            }
            if ($count > 1 && empty($data['replace_all'])) {
                return response()->json(['error' => "find text occurs {$count} times; add more context or set replace_all=true.", 'path' => $rel], 422);
            }
            $after = str_replace($data['find'], $data['replace'] ?? '', $before);
        } else {
            return response()->json(['error' => 'Send content, or find + replace.'], 422);
        }

        if (strlen($after) > (int) config('dms_admin.template_max_bytes')) {
            return response()->json(['error' => 'New content is larger than template_max_bytes.'], 422);
        }
        if ($after === $before) {
            return response()->json(['message' => 'No change.', 'path' => $rel]);
        }

        if ($problem = TemplateGuard::check($after)) {
            return response()->json(['error' => 'Rejected: '.$problem, 'path' => $rel, 'saved' => false], 422);
        }

        if (! empty($data['dry_run'])) {
            return response()->json(['message' => 'Dry run OK - the new version compiles.', 'path' => $rel, 'bytes_before' => strlen($before), 'bytes_after' => strlen($after)]);
        }

        $rev = DmsAdminRevision::create([
            'kind' => 'template',
            'target' => $rel,
            'content' => $before,
            'note' => $data['note'] ?? null,
        ]);

        $tmp = $abs.'.dmstmp';
        file_put_contents($tmp, $after);
        rename($tmp, $abs);

        Artisan::call('view:clear');
        LiteSpeedPurge::queue(['*']);

        return LiteSpeedPurge::attachHeader(response()->json([
            'message' => 'Template saved. Compiled views cleared and full LiteSpeed purge sent.',
            'path' => $rel,
            'revision_id' => $rev->id,
            'bytes_before' => strlen($before),
            'bytes_after' => strlen($after),
        ]));
    }

    public function revisions(Request $request)
    {
        [$rel, , $err] = $this->resolve($request->query('path', ''));
        if ($err) {
            return $err;
        }
        $revs = DmsAdminRevision::where('kind', 'template')->where('target', $rel)
            ->orderByDesc('id')->limit(50)->get(['id', 'note', 'actor', 'created_at']);
        return response()->json(['path' => $rel, 'data' => $revs]);
    }

    public function restore(Request $request, int $id)
    {
        $rev = DmsAdminRevision::where('kind', 'template')->findOrFail($id);
        [$rel, $abs, $err] = $this->resolve($rev->target);
        if ($err) {
            return $err;
        }
        $current = is_file($abs) ? file_get_contents($abs) : '';
        $back = DmsAdminRevision::create([
            'kind' => 'template',
            'target' => $rel,
            'content' => $current,
            'note' => "Before restoring revision {$id}",
        ]);
        file_put_contents($abs, $rev->content);
        Artisan::call('view:clear');
        LiteSpeedPurge::queue(['*']);

        return LiteSpeedPurge::attachHeader(response()->json([
            'message' => "Restored revision {$id}.",
            'path' => $rel,
            'undo_revision_id' => $back->id,
        ]));
    }

    /** @return array{0:?string,1:?string,2:?\Illuminate\Http\JsonResponse} */
    protected function resolve(string $name): array
    {
        $rel = TemplateGuard::normalise($name);
        if (! $rel) {
            return [null, null, response()->json(['error' => 'Invalid template path.'], 422)];
        }
        if (! TemplateGuard::allowed($rel)) {
            return [$rel, null, response()->json(['error' => 'This template is not on the whitelist (config/dms_admin.php).', 'path' => $rel], 403)];
        }
        return [$rel, TemplateGuard::absolute($rel), null];
    }
}
