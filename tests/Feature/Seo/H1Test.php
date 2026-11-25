<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * SEO rule: every page has exactly one h1 and it is the main title (the biggest heading of the first screen).
 * Covers the Blade-template pages and the blog pages; service pages (stored section content) are out of scope.
 */
class H1Test extends TestCase
{
    use CrawlsPages;

    private function main(string $html): string
    {
        $clean = preg_replace(['#<!--.*?-->#s', '#<script\b.*?</script>#is', '#<noscript\b.*?</noscript>#is'], '', $html);
        $this->assertSame(1, preg_match('#<main\b.*?</main>#s', $clean, $m), 'page has a <main>');
        return $m[0];
    }

    private function assertOneH1(string $path, string $html): void
    {
        $main = $this->main($html);
        $clean = preg_replace(['#<!--.*?-->#s', '#<script\b.*?</script>#is', '#<noscript\b.*?</noscript>#is'], '', $html);
        $this->assertSame(1, preg_match_all('#<h1\b#i', $clean), "$path: exactly one h1 in the document");
        $this->assertSame(1, preg_match_all('#<h1\b#i', $main), "$path: the h1 is inside <main>");
        preg_match_all('#<h([1-6])\b#i', $main, $levels);
        $this->assertSame('1', $levels[1][0], "$path: the h1 is the first heading of <main>");
        preg_match('#<h1\b[^>]*>(.*?)</h1>#is', $main, $h);
        $this->assertNotSame('', trim(strip_tags($h[1])), "$path: the h1 is not empty");
    }

    public function test_every_template_page_has_exactly_one_h1(): void
    {
        foreach (self::TEMPLATE_PAGES as $path) {
            $this->assertOneH1($path, $this->html($path));
        }
    }

    public function test_blog_posts_and_pagination_have_exactly_one_h1(): void
    {
        $list = $this->html('/blogs');
        preg_match_all('#href="https://datamysite\.com/(?:blogs/)?([a-z0-9-]{25,})"#', $list, $m);
        $slugs = array_slice(array_unique($m[1]), 0, 4);
        $this->assertNotEmpty($slugs, 'blog posts are listed');
        foreach ($slugs as $slug) {
            $this->assertOneH1("/$slug", $this->html("/$slug"));
        }
        $this->assertOneH1('/blogs?page=2', $this->html('/blogs?page=2'));
    }

    public function test_services_h1_is_the_big_tagline_and_our_services_is_only_a_label(): void
    {
        $html = $this->html('/services');
        $main = $this->main($html);
        $this->assertMatchesRegularExpression('#<h1\b[^>]*>\s*Elevate Your Brand Visibility and Boost Sales\s*</h1>#', $main, 'the main title is the h1');
        $this->assertDoesNotMatchRegularExpression('#<h[1-6]\b[^>]*>\s*Our Services\s*</h[1-6]>#', $main, '"Our Services" is a label, not a heading');
        $this->assertMatchesRegularExpression('#<p class="svl-eyebrow"[^>]*>Our Services</p>#', $main);

        $css = file_get_contents(public_path('assets/css/services-landing-v2.css'));
        $this->assertSame(1, preg_match('#\.svl-hero-body h1 \{[^}]*font: \d+ clamp\((\d+)px, [\d.]+vw, (\d+)px\)#', $css, $h1), 'h1 size rule');
        $this->assertSame(1, preg_match('#\.svl-hero-body \.svl-eyebrow \{[^}]*font: \d+ clamp\((\d+)px, [\d.]+vw, (\d+)px\)#', $css, $eb), 'label size rule');
        $this->assertGreaterThan((int) $eb[2] * 3, (int) $h1[2], 'the h1 is far larger than the label');
    }
}
