<?php

namespace Tests\Support;

use Illuminate\Testing\TestResponse;

/**
 * Helpers for the SEO tests: request a page as if it were served from a given host and read headings, JSON-LD, images and
 * links out of the HTML. Pages are rendered in-process (no web server, no network).
 */
trait CrawlsPages
{
    public const PRODUCTION = 'https://datamysite.com';
    public const STAGING = 'https://staging.datamysite.com';

    /** the redesigned pages whose markup is owned by the Blade templates */
    public const TEMPLATE_PAGES = ['/', '/services', '/about-us', '/contact-us', '/faq', '/blogs', '/blogs/categories', '/client-case-studies', '/privacy-policy', '/terms-conditions'];

    /** a few service pages (their section text comes from the database) */
    public const SERVICE_PAGES = ['/seo-services', '/outdoor-advertising', '/lead-generation'];

    protected function page(string $path, string $host = self::PRODUCTION): TestResponse
    {
        return $this->get(rtrim($host, '/') . $path);
    }

    protected function html(string $path, string $host = self::PRODUCTION): string
    {
        $r = $this->page($path, $host);
        $r->assertOk();
        return $r->getContent();
    }

    /** @return \DOMXPath */
    protected function xpath(string $html): \DOMXPath
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        return new \DOMXPath($dom);
    }

    /** JSON-LD blocks decoded, as a flat list of nodes (blocks that use @graph are expanded) */
    protected function jsonLd(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $nodes = [];
        foreach ($m[1] as $json) {
            $data = json_decode($json, true);
            $this->assertSame(JSON_ERROR_NONE, json_last_error(), 'Invalid JSON-LD: ' . json_last_error_msg() . ' in ' . substr($json, 0, 120));
            foreach (($data['@graph'] ?? [$data]) as $node) $nodes[] = $node;
        }
        return $nodes;
    }

    protected function jsonLdTypes(string $html): array
    {
        $types = [];
        foreach ($this->jsonLd($html) as $n) foreach ((array) ($n['@type'] ?? []) as $t) $types[] = $t;
        return $types;
    }

    /** heading levels inside <main>, in document order */
    protected function headingLevels(string $html): array
    {
        $main = preg_match('#<main\b.*?</main>#s', $html, $m) ? $m[0] : $html;
        preg_match_all('#<h([1-6])\b#', $main, $hs);
        return array_map('intval', $hs[1]);
    }

    /** every <img> tag in the page */
    protected function images(string $html): array
    {
        preg_match_all('#<img\b[^>]*>#i', $html, $m);
        return $m[0];
    }
}
