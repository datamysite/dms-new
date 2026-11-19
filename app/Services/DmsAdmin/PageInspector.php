<?php

namespace App\Services\DmsAdmin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

/**
 * Fetches a live page the way a visitor gets it (through LiteSpeed) and
 * reports what is actually served: title, meta, H1s, headings, schema
 * types, placeholders, images without alt, links that hit redirects.
 * This is what lets the agent verify work without waiting for a crawl.
 */
class PageInspector
{
    public static function inspect(string $path, bool $withText = false, bool $bypassCache = false): array
    {
        $site = config('dms_admin.site_url');
        $path = '/'.ltrim(parse_url($path, PHP_URL_PATH) ?: $path, '/');
        $url = $site.$path;
        if ($bypassCache) {
            $url .= (str_contains($url, '?') ? '&' : '?').'nocache='.time();
        }

        $resp = Http::timeout(25)
            ->withHeaders(['User-Agent' => 'DMS-Admin-Inspector/1.0 (+'.$site.')'])
            ->withOptions(['allow_redirects' => false])
            ->get($url);

        $status = $resp->status();
        $out = [
            'path' => $path,
            'status' => $status,
            'location' => $resp->header('Location') ?: null,
            'cache' => $resp->header('X-LiteSpeed-Cache') ?: null,
        ];
        if ($status !== 200) {
            return $out;
        }

        $html = $resp->body();
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();
        $xp = new \DOMXPath($dom);

        $text = fn ($n) => trim(preg_replace('/\s+/u', ' ', $n->textContent ?? ''));

        $title = ($t = $xp->query('//title')->item(0)) ? $text($t) : null;
        $meta = null;
        foreach ($xp->query('//meta[@name="description"]') as $m) {
            $meta = trim($m->getAttribute('content'));
        }
        $canonical = ($c = $xp->query('//link[@rel="canonical"]')->item(0)) ? $c->getAttribute('href') : null;
        $robots = ($r = $xp->query('//meta[@name="robots"]')->item(0)) ? $r->getAttribute('content') : null;

        $h1 = [];
        foreach ($xp->query('//h1') as $n) {
            $h1[] = $text($n);
        }
        $headings = [];
        foreach ($xp->query('//h2|//h3') as $n) {
            $headings[] = strtoupper($n->nodeName).': '.$text($n);
        }

        // Schema types
        $schemaTypes = [];
        foreach ($xp->query('//script[@type="application/ld+json"]') as $s) {
            $json = json_decode(trim($s->textContent), true);
            $collect = function ($node) use (&$collect, &$schemaTypes) {
                if (! is_array($node)) {
                    return;
                }
                if (isset($node['@type'])) {
                    foreach ((array) $node['@type'] as $t) {
                        $schemaTypes[] = $t;
                    }
                }
                foreach ($node as $v) {
                    if (is_array($v)) {
                        $collect($v);
                    }
                }
            };
            $collect($json);
        }

        // Body text (scripts/styles removed) for placeholder search and word count.
        foreach (['//script', '//style', '//noscript'] as $q) {
            foreach (iterator_to_array($xp->query($q)) as $n) {
                $n->parentNode?->removeChild($n);
            }
        }
        $bodyNode = $xp->query('//body')->item(0);
        $bodyText = $bodyNode ? $text($bodyNode) : '';
        preg_match_all('/\[[^\]\n]{1,80}\]/u', $bodyText, $ph);
        $placeholders = array_values(array_unique(array_filter($ph[0], fn ($p) => preg_match('/[A-Za-z]/', $p))));

        $imgsNoAlt = [];
        foreach ($xp->query('//img') as $img) {
            if (trim($img->getAttribute('alt')) === '') {
                $imgsNoAlt[] = $img->getAttribute('src');
            }
        }

        // Internal links and which of them hit a stored redirect.
        $host = parse_url($site, PHP_URL_HOST);
        $links = [];
        foreach ($xp->query('//a[@href]') as $a) {
            $href = trim($a->getAttribute('href'));
            $h = parse_url($href, PHP_URL_HOST);
            if ($h && $h !== $host && $h !== 'www.'.$host) {
                continue;
            }
            $p = parse_url($href, PHP_URL_PATH);
            if ($p && str_starts_with($p, '/')) {
                $links[] = rtrim($p, '/') ?: '/';
            }
        }
        $links = array_values(array_unique($links));
        $linksToRedirects = [];
        $rt = config('dms_admin.tables.redirects');
        if ($links && Schema::hasTable($rt)) {
            $linksToRedirects = DB::table($rt)->whereIn('from_path', $links)->where('is_active', 1)
                ->get(['from_path', 'to_url'])->map(fn ($r) => $r->from_path.' -> '.$r->to_url)->all();
        }

        // Author box heuristic (TT-23)
        $authorBox = $xp->query('//*[contains(@class,"author")]')->length > 0
            || in_array('Person', $schemaTypes, true);

        $out += [
            'title' => $title,
            'title_length' => $title ? mb_strlen($title) : 0,
            'meta_description' => $meta,
            'meta_length' => $meta ? mb_strlen($meta) : 0,
            'canonical' => $canonical,
            'robots' => $robots,
            'h1' => $h1,
            'headings' => array_slice($headings, 0, 80),
            'schema_types' => array_values(array_unique($schemaTypes)),
            'word_count' => str_word_count(strip_tags($bodyText)),
            'placeholders' => $placeholders,
            'images_without_alt' => array_values(array_unique($imgsNoAlt)),
            'internal_link_count' => count($links),
            'links_to_redirects' => $linksToRedirects,
            'author_box_detected' => $authorBox,
        ];

        if ($withText) {
            $out['text'] = mb_substr($bodyText, 0, 60000);
        }
        return $out;
    }

    /** Find text on a live page, e.g. a figure, to check what is served. */
    public static function find(string $path, string $needle): array
    {
        $data = static::inspect($path, true);
        $text = $data['text'] ?? '';
        $hits = [];
        $offset = 0;
        while (($pos = mb_stripos($text, $needle, $offset)) !== false && count($hits) < 20) {
            $hits[] = mb_substr($text, max(0, $pos - 120), mb_strlen($needle) + 240);
            $offset = $pos + mb_strlen($needle);
        }
        return ['path' => $data['path'], 'status' => $data['status'], 'needle' => $needle, 'count' => count($hits), 'snippets' => $hits];
    }
}
