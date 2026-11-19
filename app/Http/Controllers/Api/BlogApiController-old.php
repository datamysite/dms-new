<?php
// File: app/Http/Controllers/Api/BlogApiController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogRequest;
use App\Models\Blogs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Author;
use App\Models\MetaTags;

class BlogApiController extends Controller
{
    /**
     * Status conventions for the `blogs.status` column.
     * Your table defaults to 1. We treat 0 = draft/hidden, 1 = published.
     * If your app uses a different convention, change these two constants.
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
        $data['faq_schema'] = '<script type="application/ld+json">'.$this->normalizeSchema($request->input('faq_schema')).'</script>';

        $authorId = $data['author_id'] ?? config('services.blog_api.author_id');
        if (empty($authorId)) {
            return response()->json([
                'message' => 'author_id is required. Set BLOG_API_AUTHOR_ID in .env or pass author_id in the payload.',
            ], 422);
        }
        $data['author_id']  = rand(1,5);
        $data['created_by'] = (int) $authorId;

        $blog = Blogs::create($data);

        $meta_url = 'https://datamysite.com/'.$data['slug'];

        $mt = new MetaTags;
        $mt->url = $meta_url;
        $mt->title = $data['heading'];
        $mt->description = $data['short_description'];
        $mt->created_by = $data['author_id'];
        $mt->save();

        return response()->json([
            'message' => 'Blog created as published' . '.',
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
            'read_time'         => ['sometimes', 'nullable', 'string', 'max:150'],
            'status'            => ['sometimes', 'integer', 'in:0,1'],
        ]);

        if (array_key_exists('slug', $data) && $data['slug']) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $blog->id);
        }
        if (array_key_exists('faq_schema', $data)) {
            $data['faq_schema'] = $this->normalizeSchema($data['faq_schema']);
        }

        $blog->update($data);

        return response()->json(['message' => 'Blog updated.', 'data' => $blog]);
    }

    /** Flip a draft to published — the deliberate, separate "go live" action. */
    public function publish(Blogs $blog): JsonResponse
    {
        $blog->update(['status' => self::STATUS_PUBLISHED]);

        return response()->json(['message' => 'Blog published.', 'data' => $blog]);
    }

    public function show(Blogs $blog): JsonResponse
    {
        return response()->json(['data' => $blog]);
    }

    public function index(Request $request): JsonResponse
    {
        $blogs = Blogs::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', (int) $request->status))
            ->latest('id')
            ->paginate(20);

        return response()->json($blogs);
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

    /** Accepts an array (encodes to JSON) or a JSON string (stores as-is). */
    private function normalizeSchema($schema): ?string
    {
        if (is_null($schema)) {
            return null;
        }

        return is_array($schema)
            ? json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            : (string) $schema;
    }
}
