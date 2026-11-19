<?php
// File: app/Http/Controllers/Api/BlogApiController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogRequest;
use App\Models\Blogs;
use App\Models\MetaTags;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BlogApiController extends Controller
{
    /**
     * Status conventions for the `blogs.status` column.
     * 0 = draft/hidden, 1 = published. DB default is 1, so status is
     * ALWAYS set explicitly here - never rely on the column default.
     */
    private const STATUS_DRAFT     = 0;
    private const STATUS_PUBLISHED = 1;

    /** Create a blog. Defaults to DRAFT so nothing goes live without review. */
    public function store(StoreBlogRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['slug']      = $this->uniqueSlug($data['slug'] ?? Str::slug($data['heading']));
        $data['read_time'] = $data['read_time'] ?? $this->readTime($data['description']);
        $data['status']    = $request->filled('status') ? (int) $data['status'] : self::STATUS_DRAFT;

        // FIX #2: only wrap when schema exists, and never double-wrap.
        // Empty/null schema stores '' (no junk empty <script> tag on the page).
        $data['faq_schema'] = $this->wrapSchema($request->input('faq_schema'));

        $authorId = $data['author_id'] ?? config('services.blog_api.author_id');
        if (empty($authorId)) {
            return response()->json([
                'message' => 'author_id is required. Set BLOG_API_AUTHOR_ID in .env or pass author_id in the payload.',
            ], 422);
        }
        $data['author_id']  = (int) $authorId;
        $data['created_by'] = (int) $authorId;

        // NOTE: Blogs::create() is the model's custom static and returns the new ID.
        $blogId = Blogs::create($data);
        $blog   = Blogs::find($blogId);

        $mt              = new MetaTags;
        $mt->url         = 'https://datamysite.com/' . $data['slug'];
        $mt->title       = $data['heading'];
        $mt->description = $data['short_description'];
        $mt->created_by  = $data['author_id'];
        $mt->save();

        // FIX #1: the message now tells the truth about the status.
        $statusWord = $data['status'] === self::STATUS_PUBLISHED ? 'PUBLISHED' : 'DRAFT (not live)';

        return response()->json([
            'message' => 'Blog created as ' . $statusWord . '.',
            'data'    => $blog,
        ], 201);
    }

    /** Update an existing blog (partial updates allowed). */
    public function update(Request $request, Blogs $blog): JsonResponse
    {
        $data = $request->validate([
            'heading'           => ['sometimes', 'required', 'string', 'max:255'],
            'slug'              => ['sometimes', 'nullable', 'string', 'max:255', 'unique:blogs,slug,' . $blog->id],
            'description'       => ['sometimes', 'required', 'string'],
            'short_description' => ['sometimes', 'nullable', 'string'],
            'faq_schema'        => ['sometimes', 'nullable'],
            'banner'            => ['sometimes', 'nullable', 'string', 'max:255'],
            'banner_alt'        => ['sometimes', 'nullable', 'string', 'max:500'],
            'category_id'       => ['sometimes', 'nullable', 'integer'],
            'author_id'         => ['sometimes', 'nullable', 'integer'],
            'read_time'         => ['sometimes', 'nullable', 'string', 'max:150'],
            'status'            => ['sometimes', 'integer', 'in:0,1'],

            // Surgical body edits (applied to the CURRENT description, in this order):
            'find_replace'           => ['sometimes', 'array', 'max:50'],
            'find_replace.*.find'    => ['required_with:find_replace', 'string'],
            'find_replace.*.replace' => ['present', 'nullable', 'string'],
            'insert_before'          => ['sometimes', 'array'],
            'insert_before.marker'   => ['required_with:insert_before', 'string'],
            'insert_before.html'     => ['required_with:insert_before', 'string'],
            'insert_after'           => ['sometimes', 'array'],
            'insert_after.marker'    => ['required_with:insert_after', 'string'],
            'insert_after.html'      => ['required_with:insert_after', 'string'],
            'append_html'            => ['sometimes', 'string'],
        ]);

        // --- Surgical description edits -------------------------------------
        // Lets a caller change part of a long article without resending it.
        // Every "find" / "marker" must occur EXACTLY ONCE, or nothing is saved.
        $ops = array_intersect_key($data, array_flip(['find_replace', 'insert_before', 'insert_after', 'append_html']));
        $data = array_diff_key($data, $ops);

        if (! empty($ops)) {
            if (array_key_exists('description', $data)) {
                return response()->json([
                    'message' => 'Send either a full "description" or surgical edits (find_replace / insert_before / insert_after / append_html), not both.',
                ], 422);
            }

            $body = (string) $blog->description;

            foreach ($ops['find_replace'] ?? [] as $i => $op) {
                $count = substr_count($body, $op['find']);
                if ($count !== 1) {
                    return response()->json([
                        'message' => "find_replace[$i]: the find text occurs $count times in the description (must be exactly 1). Nothing was saved.",
                    ], 422);
                }
                $body = str_replace($op['find'], (string) ($op['replace'] ?? ''), $body);
            }

            foreach (['insert_before', 'insert_after'] as $kind) {
                if (! isset($ops[$kind])) {
                    continue;
                }
                $marker = $ops[$kind]['marker'];
                $count  = substr_count($body, $marker);
                if ($count !== 1) {
                    return response()->json([
                        'message' => "$kind: the marker occurs $count times in the description (must be exactly 1). Nothing was saved.",
                    ], 422);
                }
                $html = $ops[$kind]['html'];
                $body = str_replace($marker, $kind === 'insert_before' ? $html . $marker : $marker . $html, $body);
            }

            if (isset($ops['append_html'])) {
                $body .= $ops['append_html'];
            }

            $data['description'] = $body;
        }
        // ---------------------------------------------------------------------

        $oldUrl = 'https://datamysite.com/' . $blog->slug;

        if (array_key_exists('slug', $data) && $data['slug']) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $blog->id);
        }

        // FIX #2 (consistency): update uses the SAME wrapping as store,
        // so schema format in the DB no longer depends on which endpoint ran last.
        if (array_key_exists('faq_schema', $data)) {
            $data['faq_schema'] = $this->wrapSchema($data['faq_schema']);
        }

        $blog->update($data);

        // FIX #3: keep the MetaTags row in sync when slug/heading/description change.
        // Only touch fields that actually changed in this request, so a body-only
        // edit never overwrites a custom SEO title / meta description.
        $mt = MetaTags::where('url', $oldUrl)->first();
        if ($mt) {
            $touched = false;
            if (array_key_exists('slug', $data)) {
                $mt->url = 'https://datamysite.com/' . $blog->slug;
                $touched = true;
            }
            if (array_key_exists('heading', $data) && $request->boolean('sync_meta_title', false)) {
                $mt->title = $blog->heading;
                $touched = true;
            }
            if (array_key_exists('short_description', $data) && $request->boolean('sync_meta_description', true)) {
                $mt->description = $blog->short_description;
                $touched = true;
            }
            if ($touched) {
                $mt->save();
            }
        }

        $statusNote = array_key_exists('status', $data)
            ? ($data['status'] === self::STATUS_PUBLISHED ? ' Status: PUBLISHED.' : ' Status: DRAFT (unpublished).')
            : '';

        return response()->json(['message' => 'Blog updated.' . $statusNote, 'data' => $blog->fresh()]);
    }

    /** Flip a draft to published - the deliberate, separate "go live" action. */
    public function publish(Blogs $blog): JsonResponse
    {
        $blog->update(['status' => self::STATUS_PUBLISHED]);

        return response()->json(['message' => 'Blog published.', 'data' => $blog->fresh()]);
    }

    /** Unpublish a live post - the recovery action that never existed before. */
    public function unpublish(Blogs $blog): JsonResponse
    {
        $blog->update(['status' => self::STATUS_DRAFT]);

        return response()->json(['message' => 'Blog unpublished (set to draft).', 'data' => $blog->fresh()]);
    }

    /**
     * Get one blog by numeric ID OR by slug.
     *   GET /blogs/953
     *   GET /blogs/unipole-advertising-dubai
     */
    public function show($blog): JsonResponse
    {
        $key = trim((string) $blog, "/ \t\n\r");

        $model = ctype_digit($key)
            ? Blogs::findOrFail((int) $key)
            : Blogs::where('slug', $key)->firstOrFail();

        return response()->json(['data' => $model]);
    }

    /**
     * List blogs.
     * Filters (all optional):
     *   ?status=0|1
     *   ?slug=exact-slug
     *   ?slugs=slug-one,slug-two          (exact match, max 50)
     *   ?search=text                      (matches slug or heading)
     *   ?contains=text                    (matches the article body, short description or FAQ schema)
     *   ?contains_any=text1|text2|...     (same, any of up to 20 texts, separated by |)
     *   ?per_page=1..100                  (default 20)
     *   ?lite=1                           (only id, slug, heading, status, category_id, updated_at)
     *
     * Link-audit extras (read-only):
     *   ?with_snippets=1                  each blog gets "matches": [{field, term, snippet}]
     *   ?include_sections=1               adds "sections": service sections whose content holds a term
     *   ?compare_backup=YYYYMMDD          adds "changed_since_backup": ids whose text differs from
     *                                     blogs_bak_YYYYMMDD / service_sections_bak_YYYYMMDD
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min($perPage, 100));

        $slugs = [];
        if ($request->filled('slugs')) {
            $raw   = $request->query('slugs');
            $slugs = is_array($raw) ? $raw : explode(',', (string) $raw);
            $slugs = array_slice(array_values(array_filter(array_map(
                fn ($s) => trim((string) $s, "/ \t\n\r"),
                $slugs
            ))), 0, 50);
        }

        $terms = $this->auditTerms($request);

        $blogs = Blogs::query()
            ->when($request->boolean('lite'), fn ($q) => $q->select([
                'id', 'slug', 'heading', 'status', 'category_id', 'updated_at',
            ]))
            ->when($request->filled('status'), fn ($q) => $q->where('status', (int) $request->status))
            ->when($request->filled('slug'), fn ($q) => $q->where('slug', trim((string) $request->query('slug'), "/ \t\n\r")))
            ->when(! empty($slugs), fn ($q) => $q->whereIn('slug', $slugs))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $this->escapeLike((string) $request->query('search')) . '%';
                $q->where(fn ($w) => $w->where('slug', 'like', $term)->orWhere('heading', 'like', $term));
            })
            ->when(! empty($terms), function ($q) use ($terms) {
                $q->where(function ($w) use ($terms) {
                    foreach ($terms as $t) {
                        $like = '%' . $this->escapeLike($t) . '%';
                        $w->orWhere('description', 'like', $like)
                          ->orWhere('short_description', 'like', $like)
                          ->orWhere('faq_schema', 'like', $like);
                    }
                });
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        $snippets = $request->boolean('with_snippets');

        if ($snippets && ! empty($terms)) {
            $bodies = Blogs::whereIn('id', $blogs->getCollection()->pluck('id'))
                ->get(['id', 'description', 'short_description', 'faq_schema'])
                ->keyBy('id');

            $blogs->getCollection()->transform(function ($b) use ($bodies, $terms) {
                $row = $bodies->get($b->id);
                $b->setAttribute('matches', $row ? $this->findMatches([
                    'description'       => $row->description,
                    'short_description' => $row->short_description,
                    'faq_schema'        => $row->faq_schema,
                ], $terms) : []);
                return $b;
            });
        }

        $out = $blogs->toArray();

        if ($request->boolean('include_sections') && ! empty($terms)) {
            $out['sections'] = $this->sectionMatches($terms, $snippets);
        }

        if ($request->filled('compare_backup')) {
            $out['changed_since_backup'] = $this->changedSinceBackup((string) $request->query('compare_backup'));
        }

        return response()->json($out);
    }

    // ----------------- link-audit helpers (read-only) -----------------

    /** Search terms from ?contains= and ?contains_any= (max 20, each max 200 chars). */
    private function auditTerms(Request $request): array
    {
        $terms = [];
        if ($request->filled('contains')) {
            $terms[] = (string) $request->query('contains');
        }
        if ($request->filled('contains_any')) {
            $terms = array_merge($terms, explode('|', (string) $request->query('contains_any')));
        }
        $terms = array_values(array_unique(array_filter(array_map(
            fn ($t) => mb_substr(trim((string) $t), 0, 200),
            $terms
        ), fn ($t) => $t !== '')));

        return array_slice($terms, 0, 20);
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    /** Up to 5 snippets per field and term, 80 characters either side of the match. */
    private function findMatches(array $fields, array $terms): array
    {
        $matches = [];
        foreach ($fields as $field => $text) {
            $text = (string) $text;
            if ($text === '') {
                continue;
            }
            foreach ($terms as $term) {
                $offset = 0;
                $found  = 0;
                while ($found < 5 && ($pos = mb_stripos($text, $term, $offset)) !== false) {
                    $from = max(0, $pos - 80);
                    $matches[] = [
                        'field'   => $field,
                        'term'    => $term,
                        'snippet' => preg_replace('/\s+/', ' ', mb_substr($text, $from, mb_strlen($term) + 160)),
                    ];
                    $offset = $pos + mb_strlen($term);
                    $found++;
                }
            }
        }

        return $matches;
    }

    /** Service sections whose content holds any term (max 300 rows). */
    private function sectionMatches(array $terms, bool $withSnippets): array
    {
        $rows = DB::table('service_sections')
            ->select(['id', 'service_id', 'section_key', 'area', 'is_active', 'content'])
            ->where(function ($w) use ($terms) {
                foreach ($terms as $t) {
                    $w->orWhere('content', 'like', '%' . $this->escapeLike($t) . '%');
                }
            })
            ->orderBy('service_id')
            ->limit(300)
            ->get();

        return $rows->map(function ($r) use ($terms, $withSnippets) {
            $item = [
                'id'          => $r->id,
                'service_id'  => $r->service_id,
                'section_key' => $r->section_key,
                'area'        => $r->area,
                'is_active'   => (bool) $r->is_active,
            ];
            if ($withSnippets) {
                $item['matches'] = $this->findMatches(['content' => $r->content], $terms);
            }
            return $item;
        })->all();
    }

    /** Ids whose text differs from the backup tables made by a SQL fix (suffix = YYYYMMDD). */
    private function changedSinceBackup(string $suffix): array
    {
        if (! preg_match('/^\d{8}$/', $suffix)) {
            return ['error' => 'compare_backup must be 8 digits, e.g. 20261111'];
        }

        $blogTable    = (new Blogs)->getTable();
        $blogBackup   = $blogTable . '_bak_' . $suffix;
        $sectionTable = 'service_sections';
        $sectionBak   = $sectionTable . '_bak_' . $suffix;
        $out          = [];

        $out['blogs'] = Schema::hasTable($blogBackup)
            ? DB::table($blogTable . ' as b')
                ->join($blogBackup . ' as k', 'k.id', '=', 'b.id')
                ->whereRaw('NOT (b.description <=> k.description)')
                ->orderBy('b.id')
                ->get(['b.id', 'b.slug', 'b.status'])
                ->all()
            : 'backup table ' . $blogBackup . ' not found';

        $out['sections'] = Schema::hasTable($sectionBak)
            ? DB::table($sectionTable . ' as s')
                ->join($sectionBak . ' as k', 'k.id', '=', 's.id')
                ->whereRaw('NOT (s.content <=> k.content)')
                ->orderBy('s.id')
                ->get(['s.id', 's.service_id', 's.section_key'])
                ->all()
            : 'backup table ' . $sectionBak . ' not found';

        return $out;
    }

    // ----------------- helpers -----------------

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug) ?: 'blog';
        $candidate = $base;
        $i = 2;

        while (
            Blogs::where('slug', $candidate)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $i++;
        }

        return $candidate;
    }

    private function readTime(string $content): string
    {
        $words = str_word_count(strip_tags($content));
        $minutes = max(1, (int) ceil($words / 200)); // ~200 wpm

        return $minutes . ' min read';
    }

    /**
     * FIX #2 implementation.
     * Normalizes any input (array | JSON string | pre-wrapped <script> block | null)
     * into EXACTLY ONE of:
     *   ''                                            when there is no schema, or
     *   '<script type="application/ld+json">{...}</script>'   wrapped exactly once.
     */
    private function wrapSchema($schema): string
    {
        if (is_null($schema) || $schema === '' || $schema === []) {
            return '';
        }

        if (is_array($schema)) {
            $json = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            $json = trim((string) $schema);
            // Strip any existing <script ...> wrapper(s) so we never double-wrap.
            $json = preg_replace('/^\s*<script[^>]*>/i', '', $json);
            $json = preg_replace('/<\/script>\s*$/i', '', $json);
            $json = trim($json);
        }

        if ($json === '' || $json === 'null') {
            return '';
        }

        return '<script type="application/ld+json">' . $json . '</script>';
    }
}