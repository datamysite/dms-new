<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GooglePlaces
{
    public const CACHE_KEY = 'home_v2_google_reviews';

    private const RETRY_KEY = 'google_reviews_retry_wait';

    private const LAST_GOOD_FILE = 'google-reviews.json';

    /**
     * The reviews every V2 page uses (footer chip + popup, testimonial score, home section). Cached; the first page that finds
     * the cache empty (expiry, cache clear) fetches them again, so no page depends on someone having opened the home page.
     * When Google fails, the last good copy (a file, so it survives a cache clear) is returned and Google is left alone for 10 minutes.
     */
    public static function cached(): ?array
    {
        $data = Cache::get(self::CACHE_KEY);
        if (is_array($data)) return $data;

        if (!Cache::has(self::RETRY_KEY)) {
            try {
                $data = (new static)->merged();
                if ($data) {
                    Cache::put(self::CACHE_KEY, $data, now()->addHours(max(1, (int) env('GOOGLE_REVIEWS_CACHE_HOURS', 6))));
                    Storage::disk('local')->put(self::LAST_GOOD_FILE, json_encode($data));
                    return $data;
                }
            } catch (\Throwable $e) {
                // fall through to the last good copy
            }
            Cache::put(self::RETRY_KEY, 1, now()->addMinutes(10));
        }

        try {
            $copy = json_decode((string) Storage::disk('local')->get(self::LAST_GOOD_FILE), true);
        } catch (\Throwable $e) {
            return null;
        }
        return !empty($copy['reviews']) ? $copy : null;
    }

    /** Google returns at most 5 reviews per call; "newest" is a different 5, so both are merged (de-duplicated). */
    private function merged(): ?array
    {
        $res = $this->details('most_relevant');
        if (empty($res['reviews'])) return null;
        $id = fn ($r) => mb_strtolower(($r['author'] ?? '') . '|' . mb_substr($r['text'] ?? '', 0, 40));
        $seen = array_map($id, $res['reviews']);
        foreach (($this->details('newest')['reviews'] ?? []) as $r) {
            if (!in_array($id($r), $seen, true)) { $res['reviews'][] = $r; $seen[] = $id($r); }
        }
        return $res;
    }

    public function details(?string $sort = null): array
    {
        $ttlHours = (int) env('GOOGLE_REVIEWS_CACHE_HOURS');

            $resp = Http::timeout(4)->get('https://maps.googleapis.com/maps/api/place/details/json', [
                'place_id' => env('GOOGLE_PLACE_ID'),
                'fields'   => 'name,rating,user_ratings_total,reviews',
                'reviews_sort' => $sort ?: 'most_relevant',
                'key'      => env('GOOGLE_PLACES_API_KEY'),
            ])->json();

            if (($resp['status'] ?? '') !== 'OK') {
                return ['name' => null, 'rating' => null, 'total' => 0, 'reviews' => []];
            }

            $r = $resp['result'];

            return [
                'name'   => $r['name'] ?? 'Google Reviews',
                'rating' => $r['rating'] ?? null,
                'total'  => $r['user_ratings_total'] ?? 0,
                'reviews'=> collect($r['reviews'] ?? [])->map(fn ($rev) => [
                    'author'  => $rev['author_name'] ?? 'Google user',
                    'profile' => $rev['author_url'] ?? null,
                    'rating'  => $rev['rating'] ?? null,
                    'text'    => $rev['text'] ?? '',
                    'time'    => $rev['relative_time_description'] ?? '',
                ])->sortByDesc('createTime')->take(10)->toArray(),
            ];
    }
}
