<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * Performance rules that EVERY rewritten (DB-driven redesign) service page must keep: see "Checklist for every rewritten page"
 * in .claude/rules/performance-playbook.md. Rewritten pages are the ones in config/v2.php fast_pages: add the path here too.
 */
class RewrittenPagesPerfTest extends TestCase
{
    use CrawlsPages;

    private const PAGES = [
        'outdoor-advertising' => '/outdoor-advertising',
        'billboards-advertising' => '/outdoor-advertising/billboards-advertising',
        'building-wraps' => '/outdoor-advertising/billboards-advertising/building-wraps',
        'hoarding' => '/outdoor-advertising/billboards-advertising/hoarding',
        'wallscapes' => '/outdoor-advertising/billboards-advertising/wallscapes',
        'bridge-banners' => '/outdoor-advertising/bridge-banners',
        'flyers-distribution' => '/outdoor-advertising/flyers-distribution',
    ];

    public function test_every_rewritten_page_is_covered(): void
    {
        $this->assertSame([], array_values(array_diff((array) config('v2.fast_pages'), array_keys(self::PAGES))), 'add the new page to PAGES');
    }

    public function test_hero_photo_has_a_small_phone_version(): void
    {
        foreach (self::PAGES as $path) {
            $this->assertSame(1, preg_match('#<link rel="preload" as="image" href="[^"]*/public/([^"?]+-w960\.webp)\?v=\d+" media="\(max-width: 767px\)"#', $this->html($path), $m), "$path: phone hero preload");
            $this->assertLessThan(90 * 1024, filesize(public_path($m[1])), "$path: the phone hero (LCP) photo stays under 90 KB: " . basename($m[1]));
        }
    }

    public function test_section_images_are_versioned_lazy_webp_within_the_size_budget(): void
    {
        foreach (self::PAGES as $path) {
            $imgs = $this->xpath($this->html($path))->query('//div[@data-sv2-legacy]//img[contains(@src,"/public/assets/img/")][not(ancestor::*[contains(@class,"hv2-clients") or contains(@class,"hv2-cta-bg")])]');
            foreach ($imgs as $img) {
                if (str_ends_with((string) parse_url($img->getAttribute('src'), PHP_URL_PATH), '.svg')) continue;
                $urls = [$img->getAttribute('src')];
                foreach (array_filter(explode(',', $img->getAttribute('srcset'))) as $c) $urls[] = explode(' ', trim($c))[0];
                $this->assertSame('lazy', $img->getAttribute('loading'), "$path lazy: {$urls[0]}");
                foreach ($urls as $u) {
                    $this->assertMatchesRegularExpression('#\.webp\?v=\d+$#', $u, "$path: WebP with a version (cached for a year)");
                    $file = public_path(preg_replace('#^.*?/public/|\?.*$#', '', $u));
                    $this->assertFileExists($file);
                    $limit = getimagesize($file)[0] >= 1440 ? 175 : 150;
                    $this->assertLessThan($limit * 1024, filesize($file), "$path: recompress " . basename($file));
                }
            }
        }
    }
}
