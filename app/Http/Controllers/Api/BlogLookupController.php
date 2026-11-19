<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blogs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Look up blog IDs by slug.
 *
 * GET {api-prefix}/blogs/lookup?slug=unipole-advertising-dubai
 * GET {api-prefix}/blogs/lookup?slugs=slug-one,slug-two,slug-three
 *
 * Read-only: this controller never changes a blog.
 */
class BlogLookupController extends Controller
{
    /** Max slugs per request, to keep the query small. */
    private const MAX_SLUGS = 50;

    public function __invoke(Request $request): JsonResponse
    {
        $slugs = $this->slugsFromRequest($request);

        if (empty($slugs)) {
            return response()->json([
                'message' => 'Pass ?slug=your-slug or ?slugs=slug-one,slug-two',
            ], 422);
        }

        if (count($slugs) > self::MAX_SLUGS) {
            return response()->json([
                'message' => 'Too many slugs. Maximum is ' . self::MAX_SLUGS . ' per request.',
            ], 422);
        }

        $blogs = Blogs::query()
            ->whereIn('slug', $slugs)
            ->get()
            ->keyBy('slug');

        $found = [];
        $missing = [];

        foreach ($slugs as $slug) {
            $blog = $blogs->get($slug);

            if (! $blog) {
                $missing[] = $slug;
                continue;
            }

            $found[] = [
                'id'          => $blog->id,
                'slug'        => $blog->slug,
                'heading'     => $blog->heading ?? null,
                'status'      => $blog->status ?? null,
                'category_id' => $blog->category_id ?? null,
                'updated_at'  => optional($blog->updated_at)->toIso8601String(),
            ];
        }

        // Single-slug request: return one object (404 if not found).
        if ($request->filled('slug') && ! $request->filled('slugs')) {
            if (empty($found)) {
                return response()->json([
                    'message' => 'No blog found with slug: ' . $slugs[0],
                ], 404);
            }

            return response()->json(['data' => $found[0]]);
        }

        // Multi-slug request: return everything found plus what was missing.
        return response()->json([
            'data'    => $found,
            'missing' => $missing,
        ]);
    }

    /**
     * Accepts ?slug=a, ?slugs=a,b,c or ?slugs[]=a&slugs[]=b.
     * Leading/trailing slashes and full URLs are tolerated.
     */
    private function slugsFromRequest(Request $request): array
    {
        $raw = [];

        if ($request->filled('slug')) {
            $raw[] = (string) $request->query('slug');
        }

        $slugs = $request->query('slugs');
        if (is_array($slugs)) {
            $raw = array_merge($raw, $slugs);
        } elseif (is_string($slugs) && $slugs !== '') {
            $raw = array_merge($raw, explode(',', $slugs));
        }

        $clean = [];
        foreach ($raw as $value) {
            $value = trim((string) $value);
            // Allow "https://datamysite.com/some-slug" or "/some-slug".
            $value = preg_replace('#^https?://[^/]+#i', '', $value);
            $value = trim($value, "/ \t\n\r");
            if ($value !== '') {
                $clean[] = $value;
            }
        }

        return array_values(array_unique($clean));
    }
}
