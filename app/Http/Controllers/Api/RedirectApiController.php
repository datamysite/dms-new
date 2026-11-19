<?php
// File: app/Http/Controllers/Api/RedirectApiController.php
// Manage 301 redirects through the API. Every write is checked (no chains, loops,
// soft-404s, protected URLs or dead targets) and saved as a revision that can be undone.

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use App\Models\RedirectRevision;
use App\Support\Redirects\RedirectMap;
use App\Support\Redirects\RedirectRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class RedirectApiController extends Controller
{
    /**
     * GET /redirects
     *   ?search=text      matches from_path or to_url
     *   ?active=0|1
     *   ?target=/path     redirects that point at this path
     *   ?per_page=1..100  (default 50)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = max(1, min((int) $request->query('per_page', 50), 100));

        $rows = Redirect::query()
            ->when($request->filled('active'), fn ($q) => $q->where('is_active', $request->boolean('active')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], (string) $request->query('search')) . '%';
                $q->where(fn ($w) => $w->where('from_path', 'like', $term)->orWhere('to_url', 'like', $term));
            })
            ->when($request->filled('target'), function ($q) use ($request) {
                $t = '/' . trim((string) $request->query('target'), '/');
                $q->where(fn ($w) => $w->where('to_url', $t)->orWhere('to_url', 'like', $t . '?%'));
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json($rows);
    }

    /** GET /redirects/{redirect} */
    public function show(Redirect $redirect): JsonResponse
    {
        return response()->json(['data' => $redirect]);
    }

    /**
     * POST /redirects  — create, or update the redirect with the same from_path.
     * Body: from_path, to_url, status_code (301), is_active (true), note
     * Flags (all optional, default false):
     *   retarget_incoming  redirects that point at from_path are moved to the new target
     *   allow_soft404      allow a target of / or /blogs
     *   allow_live_source  allow redirecting the URL of a published blog post
     *   skip_target_check  do not request the target before saving
     *   dry_run            run every check, save nothing
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'from_path'   => ['required', 'string', 'max:2048'],
            'to_url'      => ['required', 'string', 'max:2048'],
            'status_code' => ['sometimes', 'integer'],
            'is_active'   => ['sometimes', 'boolean'],
            'note'        => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        [$from, $err] = RedirectRules::normalizeFrom((string) $request->input('from_path'), $this->ownHost());
        if ($err) {
            return $this->fail($err);
        }

        $existing = Redirect::where('from_path', $from)->first();
        $status   = (int) $request->input('status_code', $existing->status_code ?? 301);
        $active   = $request->has('is_active') ? $request->boolean('is_active') : true;

        $check = $this->checkPair($from, (string) $request->input('to_url'), $status, $existing?->id, $request, $active);
        if ($check['errors']) {
            return $this->fail('Not saved.', $check);
        }

        if ($request->boolean('dry_run')) {
            return response()->json(['message' => 'Dry run: all checks passed, nothing saved.', 'checks' => $check]);
        }

        $actor = $this->actor($request);

        $saved = DB::transaction(function () use ($existing, $from, $check, $status, $active, $request, $actor) {
            $previous = $existing?->snapshot();
            $redirect = $existing ?? new Redirect(['from_path' => $from, 'created_by' => $actor]);

            $redirect->fill([
                'to_url'      => $check['to'],
                'status_code' => $status,
                'is_active'   => $active,
                'note'        => $request->has('note') ? $request->input('note') : $redirect->note,
                'updated_by'  => $actor,
            ])->save();

            RedirectRevision::record($existing ? 'update' : 'create', $redirect, $from, $previous, $redirect->snapshot(), $actor);

            $this->retargetIncoming($check['incoming'], $check['to'], $actor);

            return $redirect;
        });

        return response()->json([
            'message'    => ($existing ? 'Redirect updated.' : 'Redirect created.') . ($check['incoming'] ? ' Incoming redirects retargeted: ' . count($check['incoming']) . '.' : ''),
            'data'       => $saved->fresh(),
            'checks'     => $check,
        ], $existing ? 200 : 201);
    }

    /**
     * PATCH /redirects/{redirect}
     * Body (any of): to_url, status_code, is_active, note — plus the same flags as POST.
     * from_path cannot change: delete and create instead.
     */
    public function update(Request $request, Redirect $redirect): JsonResponse
    {
        $request->validate([
            'to_url'      => ['sometimes', 'string', 'max:2048'],
            'status_code' => ['sometimes', 'integer'],
            'is_active'   => ['sometimes', 'boolean'],
            'note'        => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        if ($request->has('from_path') && $request->input('from_path') !== $redirect->from_path) {
            return $this->fail('from_path cannot be changed. Delete this redirect and create a new one.');
        }

        $to     = (string) $request->input('to_url', $redirect->to_url);
        $status = (int) $request->input('status_code', $redirect->status_code);
        $active = $request->has('is_active') ? $request->boolean('is_active') : (bool) $redirect->is_active;

        $check = $this->checkPair($redirect->from_path, $to, $status, $redirect->id, $request, $active);
        if ($check['errors']) {
            return $this->fail('Not saved.', $check);
        }

        if ($request->boolean('dry_run')) {
            return response()->json(['message' => 'Dry run: all checks passed, nothing saved.', 'checks' => $check]);
        }

        $actor = $this->actor($request);

        DB::transaction(function () use ($redirect, $check, $status, $active, $request, $actor) {
            $previous = $redirect->snapshot();

            $redirect->fill([
                'to_url'      => $check['to'],
                'status_code' => $status,
                'is_active'   => $active,
                'note'        => $request->has('note') ? $request->input('note') : $redirect->note,
                'updated_by'  => $actor,
            ])->save();

            RedirectRevision::record('update', $redirect, $redirect->from_path, $previous, $redirect->snapshot(), $actor);

            $this->retargetIncoming($check['incoming'], $check['to'], $actor);
        });

        return response()->json(['message' => 'Redirect updated.', 'data' => $redirect->fresh(), 'checks' => $check]);
    }

    /** DELETE /redirects/{redirect} — the old URL stops redirecting. A revision is kept for undo. */
    public function destroy(Request $request, Redirect $redirect): JsonResponse
    {
        $actor = $this->actor($request);

        DB::transaction(function () use ($redirect, $actor) {
            RedirectRevision::record('delete', $redirect, $redirect->from_path, $redirect->snapshot(), null, $actor);
            $redirect->delete();
        });

        return response()->json([
            'message' => 'Redirect deleted. ' . $redirect->from_path . ' now answers whatever the site serves there (often a 404). Undo with POST /redirect-revisions/{id}/undo.',
        ]);
    }

    /** GET /redirect-revisions  ?from_path=/old  ?redirect_id=12  ?per_page= */
    public function revisions(Request $request): JsonResponse
    {
        $perPage = max(1, min((int) $request->query('per_page', 50), 100));

        $rows = RedirectRevision::query()
            ->when($request->filled('redirect_id'), fn ($q) => $q->where('redirect_id', (int) $request->query('redirect_id')))
            ->when($request->filled('from_path'), function ($q) use ($request) {
                [$from] = RedirectRules::normalizeFrom((string) $request->query('from_path'), $this->ownHost());
                $q->where('from_path', $from ?? '');
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json($rows);
    }

    /**
     * POST /redirect-revisions/{revision}/undo
     * Puts the redirect back to the state before that change (a create is undone by deleting).
     */
    public function undo(Request $request, RedirectRevision $revision): JsonResponse
    {
        $actor   = $this->actor($request);
        $current = Redirect::where('from_path', $revision->from_path)->first();
        $target  = $revision->previous; // null = it did not exist before the change

        $result = DB::transaction(function () use ($current, $target, $revision, $actor) {
            $before = $current?->snapshot();

            if ($target === null) {
                if ($current) {
                    $current->delete();
                }
                RedirectRevision::record('undo', null, $revision->from_path, $before, null, $actor);
                return null;
            }

            $redirect = $current ?? new Redirect(['from_path' => $revision->from_path, 'created_by' => $actor]);
            $redirect->fill([
                'to_url'      => $target['to_url'],
                'status_code' => $target['status_code'],
                'is_active'   => $target['is_active'],
                'note'        => $target['note'] ?? null,
                'updated_by'  => $actor,
            ])->save();

            RedirectRevision::record('undo', $redirect, $revision->from_path, $before, $redirect->snapshot(), $actor);
            return $redirect;
        });

        return response()->json([
            'message' => $result ? 'Change undone: redirect restored.' : 'Change undone: redirect removed.',
            'data'    => $result,
            'audit'   => RedirectRules::audit(RedirectMap::plain(), (array) config('redirects.soft_404_targets', [])),
        ]);
    }

    /**
     * GET /redirects-check?paths=/a,/b   (max 20 paths, this site only)
     * Requests each URL from the server and follows the redirects hop by hop.
     */
    public function check(Request $request): JsonResponse
    {
        $paths = array_slice(array_values(array_filter(array_map('trim', explode(',', (string) $request->query('paths', ''))))), 0, 20);
        if (! $paths) {
            return $this->fail('Send ?paths=/old-one,/old-two (max 20).');
        }

        $results = [];
        foreach ($paths as $p) {
            [$path, $err] = RedirectRules::normalizeFrom($p, $this->ownHost());
            $results[] = $err ? ['path' => $p, 'error' => $err] : $this->trace($path);
        }

        return response()->json(['data' => $results]);
    }

    /** GET /redirects-audit — chains, loops, soft-404 targets, case twins, live posts hidden by a redirect. */
    public function audit(): JsonResponse
    {
        $map    = RedirectMap::plain();
        $report = RedirectRules::audit($map, (array) config('redirects.soft_404_targets', []));

        $report['live_posts_hidden'] = [];
        if (class_exists(\App\Models\Blogs::class)) {
            $slugs = array_map(fn ($f) => ltrim($f, '/'), array_keys($map));
            foreach (array_chunk($slugs, 500) as $chunk) {
                \App\Models\Blogs::whereIn('slug', $chunk)->where('status', 1)->get(['id', 'slug'])
                    ->each(function ($b) use (&$report) {
                        $report['live_posts_hidden'][] = ['blog_id' => $b->id, 'path' => '/' . $b->slug];
                    });
            }
        }

        $report['totals'] = [
            'active'   => count($map),
            'inactive' => Redirect::where('is_active', false)->count(),
        ];

        return response()->json(['data' => $report]);
    }

    // ----------------- checks -----------------

    /**
     * Runs every rule for a proposed redirect.
     * Returns ['to' => normalised target, 'errors' => [], 'warnings' => [], 'incoming' => [ids], 'target_check' => ...].
     */
    private function checkPair(string $from, string $toInput, int $status, ?int $excludeId, Request $request, bool $active): array
    {
        $out = ['from' => $from, 'to' => null, 'errors' => [], 'warnings' => [], 'incoming' => [], 'target_check' => null];

        if (RedirectRules::isReserved($from, (array) config('redirects.reserved_prefixes', []))) {
            $out['errors'][] = "{$from} is under a reserved prefix (config redirects.reserved_prefixes).";
        }
        if (RedirectRules::isProtected($from, (array) config('redirects.protected', []), (bool) config('redirects.case_sensitive', true))) {
            $out['errors'][] = "{$from} is a protected (Lock-List) URL. Remove it from config/redirects.php first if this is intended.";
        }
        if (! in_array($status, (array) config('redirects.status_codes', [301]), true)) {
            $out['errors'][] = "status_code {$status} is not allowed.";
        }

        [$to, $external, $err] = RedirectRules::normalizeTo($toInput, $this->ownHost(), (bool) config('redirects.allow_external_targets', false));
        if ($err) {
            $out['errors'][] = $err;
            return $out;
        }
        $out['to'] = $to;

        if (! $external && RedirectRules::isSoft404($to, (array) config('redirects.soft_404_targets', [])) && ! $request->boolean('allow_soft404')) {
            $out['errors'][] = "{$to} counts as a soft 404 (page deleted). Point at the replacement page, or send allow_soft404=true.";
        }

        // Only active redirects take part in chains.
        if ($active) {
            foreach (RedirectRules::graphProblems($from, $to, RedirectMap::plain($excludeId), $external) as $problem) {
                if ($problem['code'] === 'incoming') {
                    $ids = Redirect::whereIn('from_path', $problem['detail']['sources'])->pluck('id')->all();
                    if ($request->boolean('retarget_incoming')) {
                        $out['incoming'] = $ids;
                        $out['warnings'][] = count($ids) . ' redirect(s) pointing at ' . $from . ' will be moved to ' . $to . ': ' . implode(', ', $problem['detail']['sources']);
                    } else {
                        $out['errors'][] = $problem['message'] . ': ' . implode(', ', $problem['detail']['sources']) . '. Send retarget_incoming=true to move them to the new target.';
                    }
                } elseif ($problem['code'] === 'chain') {
                    $out['errors'][] = $problem['message'] . ' (suggested: ' . $problem['detail']['suggested_to'] . ').';
                } else {
                    $out['errors'][] = $problem['message'] . '.';
                }
            }
        }

        // A published blog post at the old URL would disappear behind the redirect.
        if (class_exists(\App\Models\Blogs::class)) {
            $post = \App\Models\Blogs::where('slug', ltrim($from, '/'))->first(['id', 'status']);
            if ($post && (int) $post->status === 1) {
                if ($request->boolean('allow_live_source')) {
                    $out['warnings'][] = "Blog {$post->id} is published at {$from}; the redirect will hide it.";
                } else {
                    $out['errors'][] = "Blog {$post->id} is published at {$from}. Unpublish it first, or send allow_live_source=true.";
                }
            } elseif ($post) {
                $out['warnings'][] = "Draft blog {$post->id} uses this slug; keep it unpublished or the redirect will hide it once published.";
            }
        }

        // The target must answer 200 directly.
        if (! $external && $active && config('redirects.verify_targets', true) && ! $request->boolean('skip_target_check') && ! $out['errors']) {
            $trace = $this->trace(RedirectRules::pathOf($to), 1);
            $out['target_check'] = $trace;
            $first = $trace['hops'][0] ?? null;
            if (! $first || isset($trace['error'])) {
                $out['errors'][] = 'Could not check the target (' . ($trace['error'] ?? 'no response') . '). Send skip_target_check=true if the server cannot call itself.';
            } elseif ($first['status'] !== 200) {
                $out['errors'][] = "The target answers {$first['status']}" . ($first['location'] ? " (to {$first['location']})" : '') . ', not 200.';
            }
        }

        return $out;
    }

    /** Move redirects that pointed at a URL that is now itself redirected. */
    private function retargetIncoming(array $ids, string $to, string $actor): void
    {
        foreach (Redirect::whereIn('id', $ids)->get() as $r) {
            $previous = $r->snapshot();
            $r->fill(['to_url' => $to, 'updated_by' => $actor])->save();
            RedirectRevision::record('update', $r, $r->from_path, $previous, $r->snapshot(), $actor . ' (retarget)');
        }
    }

    /** Request a path on this site and follow redirects hop by hop (this host only). */
    private function trace(string $path, ?int $maxHops = null): array
    {
        $maxHops = $maxHops ?? (int) config('redirects.max_hops', 10);
        $base    = rtrim((string) config('app.url'), '/');
        $url     = $base . $path;
        $hops    = [];

        try {
            for ($i = 0; $i < $maxHops; $i++) {
                $response = Http::withOptions(['allow_redirects' => false])
                    ->withHeaders(['User-Agent' => 'DMS-RedirectCheck/1.0'])
                    ->timeout((int) config('redirects.http_timeout', 8))
                    ->get($url);

                $status   = $response->status();
                $location = $response->header('Location') ?: null;
                $hops[]   = ['url' => $url, 'status' => $status, 'location' => $location];

                if ($status < 300 || $status >= 400 || ! $location) {
                    break;
                }

                $next = str_starts_with($location, '/') ? $base . $location : $location;
                $host = parse_url($next, PHP_URL_HOST);
                if (preg_replace('/^www\./', '', (string) $host) !== $this->ownHost()) {
                    $hops[] = ['url' => $next, 'status' => null, 'location' => null, 'note' => 'external, not followed'];
                    break;
                }
                $url = $next;
            }
        } catch (Throwable $e) {
            return ['path' => $path, 'hops' => $hops, 'error' => $e->getMessage()];
        }

        $last         = end($hops) ?: null;
        $redirectHops = count(array_filter($hops, fn ($h) => isset($h['status']) && $h['status'] >= 300 && $h['status'] < 400));

        return [
            'path'          => $path,
            'hops'          => $hops,
            'redirect_hops' => $redirectHops,
            'final_url'     => $last['url'] ?? null,
            'final_status'  => $last['status'] ?? null,
            'ok'            => $last && $last['status'] === 200 && $redirectHops <= 1,
        ];
    }

    private function ownHost(): string
    {
        return preg_replace('/^www\./', '', strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST)));
    }

    private function actor(Request $request): string
    {
        return mb_substr((string) ($request->header('X-Actor') ?: optional($request->user())->email ?: 'api'), 0, 120);
    }

    private function fail(string $message, array $checks = []): JsonResponse
    {
        return response()->json(array_filter(['message' => $message, 'checks' => $checks ?: null]), 422);
    }
}
