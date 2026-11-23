<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/** Audit items: canonical = page URL, 1200x630 share image, no fake fb:app_id, noindex on staging only. */
class HeadTagsTest extends TestCase
{
    use CrawlsPages;

    private function canonical(string $html): ?string
    {
        return preg_match('#<link rel="canonical" href="([^"]*)"#', $html, $m) ? html_entity_decode($m[1]) : null;
    }

    public function test_canonical_equals_the_page_url_on_every_template_page(): void
    {
        foreach (array_merge(self::TEMPLATE_PAGES, self::SERVICE_PAGES) as $path) {
            $expected = $path === '/' ? 'https://datamysite.com' : 'https://datamysite.com' . $path;
            $this->assertSame($expected, $this->canonical($this->html($path)), "canonical of $path");
        }
    }

    public function test_canonical_follows_the_host_so_staging_points_at_staging(): void
    {
        $this->assertSame('https://staging.datamysite.com/about-us', $this->canonical($this->html('/about-us', self::STAGING)));
    }

    public function test_canonical_uses_https_and_no_www_on_production_and_drops_the_query_string(): void
    {
        $html = $this->get('http://www.datamysite.com/about-us?utm_source=x&a=1')->assertOk()->getContent();
        $this->assertSame('https://datamysite.com/about-us', $this->canonical($html));
        $this->assertStringContainsString('<meta property="og:url" content="https://datamysite.com/about-us"', $html);
    }

    public function test_canonical_keeps_the_page_number_of_paginated_lists_only_after_page_1(): void
    {
        $this->assertSame('https://datamysite.com/blogs?page=2', $this->canonical($this->get('https://datamysite.com/blogs?page=2')->getContent()));
        // page 1 has no ?page= of its own: the app 301-redirects ?page=1 to the clean URL, which is the canonical
        $this->get('https://datamysite.com/blogs?page=1')->assertStatus(301)->assertRedirect('https://datamysite.com/blogs');
    }

    public function test_share_image_is_1200x630_and_the_tags_say_so(): void
    {
        $file = public_path('og-image-1200x630.jpg');
        $this->assertFileExists($file);
        [$w, $h, $type] = getimagesize($file);
        $this->assertSame([1200, 630, IMAGETYPE_JPEG], [$w, $h, $type]);

        $html = $this->html('/about-us');
        $this->assertStringContainsString('og:image" content="https://datamysite.com/public/og-image-1200x630.jpg"', $html);
        $this->assertStringContainsString('twitter:image" content="https://datamysite.com/public/og-image-1200x630.jpg"', $html);
        $this->assertStringContainsString('<meta property="og:image:width" content="1200"', $html);
        $this->assertStringContainsString('<meta property="og:image:height" content="630"', $html);
        $this->assertStringContainsString('<meta property="og:image:type" content="image/jpeg"', $html);
    }

    public function test_the_fake_facebook_app_id_is_gone(): void
    {
        foreach (['/', '/contact-us', '/seo-services'] as $path) {
            $this->assertStringNotContainsString('fb:app_id', $this->html($path), $path);
            $this->assertStringNotContainsString('1234567890', $this->html($path), $path);
        }
    }

    public function test_staging_is_noindex_in_the_page_and_in_the_header(): void
    {
        foreach (['/', '/about-us', '/seo-services'] as $path) {
            $response = $this->page($path, self::STAGING)->assertOk();
            $this->assertStringContainsString('<meta name="robots" content="noindex, nofollow, noarchive">', $response->getContent(), $path);
            $this->assertStringContainsString('noindex', (string) $response->headers->get('X-Robots-Tag'), $path);
        }
    }

    public function test_production_is_never_noindex(): void
    {
        foreach (['https://datamysite.com', 'https://www.datamysite.com'] as $host) {
            foreach (['/', '/about-us', '/seo-services'] as $path) {
                $response = $this->get($host . $path)->assertOk();
                $this->assertStringNotContainsString('name="robots"', $response->getContent(), "$host$path");
                $this->assertNull($response->headers->get('X-Robots-Tag'), "$host$path");
            }
        }
    }

    public function test_staging_robots_txt_blocks_everything_and_production_robots_txt_does_not(): void
    {
        $this->assertStringContainsString('Disallow: /', file_get_contents(base_path('robots-staging.txt')));
        // the catch-all group ("User-agent: *") of the production file must not be a blanket "Disallow: /" (specific scraper bots may be)
        $prod = str_replace("\r", '', file_get_contents(base_path('robots.txt')));
        $this->assertSame(1, preg_match('/^User-agent: \*[ \t]*\n((?:(?!User-agent).*\n?)*)/mi', $prod, $m), 'production robots.txt has a "User-agent: *" group');
        $this->assertDoesNotMatchRegularExpression('/^Disallow:\s*\/\s*$/mi', $m[1], 'the production catch-all group must not block the whole site');
        // the host rule that swaps the file lives in .htaccess and must not apply to the production hosts
        $htaccess = file_get_contents(base_path('.htaccess'));
        $this->assertStringContainsString('RewriteRule ^robots\.txt$ robots-staging.txt', $htaccess);
        $this->assertStringContainsString('!^(www\.)?datamysite\.com$', $htaccess);
    }
}
