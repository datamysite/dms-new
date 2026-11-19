<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MetaTags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Manage meta titles, descriptions, keywords and H1s (table: meta_tags / model: MetaTags).
 *
 * URLs are matched loosely: "https://datamysite.com/seo-services/", "/seo-services" and
 * "seo-services" all find the same row, whatever format is stored in the table.
 */
class MetaTagApiController extends Controller
{
    /** Soft SEO limits: exceeding them is allowed but reported back as a warning. */
    const TITLE_MAX = 60;
    const DESCRIPTION_MAX = 160;

    /**
     * GET /v1/meta-tags?search=&missing=h1|keywords&per_page=50
     */
    public function index(Request $request)
    {
        $query = MetaTags::query()->orderBy('url');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('url', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('h1_content', 'like', "%{$search}%");
            });
        }

        // Find gaps quickly, e.g. ?missing=h1
        if ($missing = $request->query('missing')) {
            $column = $missing === 'h1' ? 'h1_content' : $missing;
            if (in_array($column, ['h1_content', 'keywords'], true)) {
                $query->where(function ($q) use ($column) {
                    $q->whereNull($column)->orWhere($column, '');
                });
            }
        }

        $items = $query->paginate(min(200, max(1, (int) $request->query('per_page', 50))));

        return response()->json([
            'data' => $items->getCollection()->map(function ($m) {
                return $this->format($m);
            })->values(),
            'meta' => [
                'total' => $items->total(),
                'page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
            ],
        ]);
    }

    /**
     * GET /v1/meta-tags/lookup?url=https://datamysite.com/seo-services
     */
    public function lookup(Request $request)
    {
        $request->validate(['url' => 'required|string|max:255']);

        $meta = $this->findByUrl($request->query('url'));
        if (!$meta) {
            return response()->json(['message' => 'No meta tags found for this URL.'], 404);
        }

        return response()->json(['data' => $this->format($meta)]);
    }

    /**
     * GET /v1/meta-tags/{metaTag}
     */
    public function show(MetaTags $metaTag)
    {
        return response()->json(['data' => $this->format($metaTag)]);
    }

    /**
     * POST /v1/meta-tags
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request, true);

        if ($existing = $this->findByUrl($data['url'])) {
            throw ValidationException::withMessages([
                'url' => "Meta tags already exist for this URL (id {$existing->id}). Use PATCH /v1/meta-tags/{$existing->id} or POST /v1/meta-tags/upsert.",
            ]);
        }

        $data['created_by'] = $this->creatorId($request);
        $meta = MetaTags::create($data);

        return response()->json([
            'data' => $this->format($meta),
            'warnings' => $this->warnings($meta),
        ], 201);
    }

    /**
     * PUT|PATCH /v1/meta-tags/{metaTag}  — only the fields you send are changed.
     */
    public function update(Request $request, MetaTags $metaTag)
    {
        $data = $this->validateData($request, false, $metaTag);

        $before = $this->format($metaTag);
        $metaTag->fill($data)->save();

        return response()->json([
            'data' => $this->format($metaTag->fresh()),
            'previous' => $before,
            'warnings' => $this->warnings($metaTag),
        ]);
    }

    /**
     * POST /v1/meta-tags/upsert  — create or update by URL (the easiest call for Claude).
     * Body: { "url": "...", "title": "...", "description": "...", "keywords": "...", "h1_content": "..." }
     */
    public function upsert(Request $request)
    {
        $request->validate(['url' => 'required|string|max:255']);
        $existing = $this->findByUrl($request->input('url'));

        if ($existing) {
            $data = $this->validateData($request, false, $existing);
            unset($data['url']); // keep the stored URL format
            $before = $this->format($existing);
            $existing->fill($data)->save();

            return response()->json([
                'action' => 'updated',
                'data' => $this->format($existing->fresh()),
                'previous' => $before,
                'warnings' => $this->warnings($existing),
            ]);
        }

        $data = $this->validateData($request, true);
        $data['created_by'] = $this->creatorId($request);
        $meta = MetaTags::create($data);

        return response()->json([
            'action' => 'created',
            'data' => $this->format($meta),
            'warnings' => $this->warnings($meta),
        ], 201);
    }

    /**
     * POST /v1/meta-tags/bulk-upsert  — up to 100 pages in one call, all-or-nothing.
     * Body: { "items": [ { "url": "...", "title": "...", ... }, ... ] }
     */
    public function bulkUpsert(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1|max:100',
            'items.*.url' => 'required|string|max:255',
            'items.*.title' => 'sometimes|required|string|max:255',
            'items.*.description' => 'sometimes|required|string',
            'items.*.keywords' => 'sometimes|nullable|string',
            'items.*.h1_content' => 'sometimes|nullable|string|max:255',
        ]);

        $creator = $this->creatorId($request);

        $results = DB::transaction(function () use ($request, $creator) {
            $results = [];
            foreach ($request->input('items') as $i => $item) {
                $fields = array_intersect_key($item, array_flip(['title', 'description', 'keywords', 'h1_content']));
                $existing = $this->findByUrl($item['url']);

                if ($existing) {
                    $existing->fill($fields)->save();
                    $results[] = ['index' => $i, 'action' => 'updated', 'id' => $existing->id, 'url' => $existing->url, 'warnings' => $this->warnings($existing)];
                    continue;
                }

                if (empty($fields['title']) || empty($fields['description'])) {
                    throw ValidationException::withMessages([
                        "items.$i" => "No meta tags exist yet for {$item['url']}, so title and description are required to create them.",
                    ]);
                }

                $meta = MetaTags::create($fields + ['url' => trim($item['url']), 'created_by' => $creator]);
                $results[] = ['index' => $i, 'action' => 'created', 'id' => $meta->id, 'url' => $meta->url, 'warnings' => $this->warnings($meta)];
            }

            return $results;
        });

        return response()->json(['data' => $results]);
    }

    /**
     * DELETE /v1/meta-tags/{metaTag}
     */
    public function destroy(MetaTags $metaTag)
    {
        $deleted = $this->format($metaTag);
        $metaTag->delete();

        return response()->json(['message' => 'Meta tags deleted.', 'deleted' => $deleted]);
    }

    // ---------------------------------------------------------------- helpers

    protected function validateData(Request $request, $creating, MetaTags $current = null)
    {
        $req = $creating ? 'required' : 'sometimes|required';

        $data = $request->validate([
            'url' => $req . '|string|max:255',
            'title' => $req . '|string|max:255',
            'description' => $req . '|string',
            'keywords' => 'sometimes|nullable|string',
            'h1_content' => 'sometimes|nullable|string|max:255',
        ]);

        if (isset($data['url'])) {
            $data['url'] = trim($data['url']);
            // Changing the URL must not collide with another row.
            $other = $this->findByUrl($data['url']);
            if ($other && (!$current || $other->id !== $current->id) && !$creating) {
                throw ValidationException::withMessages(['url' => "Another row (id {$other->id}) already uses this URL."]);
            }
        }

        foreach (['title', 'description', 'keywords', 'h1_content'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        return $data;
    }

    /** Find a row whatever URL format was stored (full URL, /path, path, trailing slash). */
    protected function findByUrl($url)
    {
        $url = trim((string) $url);
        $path = trim((string) (parse_url($url, PHP_URL_PATH) ?: ''), '/');
        if ($path === '' && !preg_match('#^https?://#i', $url)) {
            $path = trim($url, '/');
        }

        $base = rtrim(url('/'), '/');
        $hosts = array_unique(array_filter([
            $base,
            preg_replace('#^http://#', 'https://', $base),
            preg_replace('#^https://#', 'http://', $base),
            preg_replace('#^(https?://)www\.#', '$1', $base),
            preg_replace('#^(https?://)(?!www\.)#', '$1www.', $base),
        ]));

        $candidates = [$url, $path, '/' . $path, $path . '/', '/' . $path . '/'];
        foreach ($hosts as $host) {
            $candidates[] = $path === '' ? $host : $host . '/' . $path;
            $candidates[] = ($path === '' ? $host : $host . '/' . $path) . '/';
        }
        $candidates = array_values(array_unique($candidates));

        return MetaTags::whereIn('url', $candidates)->orderBy('id')->first();
    }

    /** created_by is required by the table; the API token has no user, so use a configured id. */
    protected function creatorId(Request $request)
    {
        if ($request->user()) {
            return $request->user()->getKey();
        }

        return (int) env('API_META_CREATED_BY', 1);
    }

    protected function warnings(MetaTags $m)
    {
        $warnings = [];
        $titleLength = mb_strlen((string) $m->title);
        $descriptionLength = mb_strlen((string) $m->description);

        if ($titleLength > self::TITLE_MAX) {
            $warnings[] = "Title is {$titleLength} characters; Google usually shows about " . self::TITLE_MAX . '.';
        }
        if ($descriptionLength > self::DESCRIPTION_MAX) {
            $warnings[] = "Description is {$descriptionLength} characters; Google usually shows about " . self::DESCRIPTION_MAX . '.';
        }
        if ($m->h1_content === null || $m->h1_content === '') {
            $warnings[] = 'No H1 set for this URL.';
        }

        return $warnings;
    }

    protected function format(MetaTags $m)
    {
        return [
            'id' => $m->id,
            'url' => $m->url,
            'h1_content' => $m->h1_content,
            'title' => $m->title,
            'title_length' => mb_strlen((string) $m->title),
            'description' => $m->description,
            'description_length' => mb_strlen((string) $m->description),
            'keywords' => $m->keywords,
            'created_by' => $m->created_by,
            'created_at' => optional($m->created_at)->toIso8601String(),
            'updated_at' => optional($m->updated_at)->toIso8601String(),
        ];
    }
}