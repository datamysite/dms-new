<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/** Audit items: links must follow the host (test site vs real site), "See all blogs", crawlable consultation links. */
class LinksTest extends TestCase
{
    use CrawlsPages;

    public function test_see_all_blogs_goes_to_the_blog_collection(): void
    {
        $html = $this->html('/');
        // the whole opening tag of the "See All Blogs" link
        $this->assertSame(1, preg_match('#<a\b([^>]*)><span>See All Blogs#', $html, $m), 'the "See All Blogs" link exists');
        $this->assertStringContainsString('href="https://datamysite.com/blogs"', $m[1], 'it points at the blog collection, not one category');
        $this->assertStringNotContainsString('pr-and-media-relations', $m[1]);
        $this->assertStringNotContainsString('target="_blank"', $m[1], 'it opens in the same tab');
    }

    public function test_see_all_blogs_on_every_service_page_goes_to_the_blog_collection(): void
    {
        // the stored section text links to one category (/blogs/pr-and-media-relations); the page must send visitors to /blogs
        $paths = array_merge(self::SERVICE_PAGES, ['/transit-media', '/digital-advertising/dooh-advertising', '/celebrity-marketing', '/public-relations-pr-coverage']);
        foreach ($paths as $path) {
            $html = $this->html($path);
            if (!preg_match('#<a\b([^>]*)><span>See All Blogs#', $html, $m)) {
                $this->fail("$path has no \"See All Blogs\" link");
            }
            $this->assertStringContainsString('href="https://datamysite.com/blogs"', $m[1], "$path: See All Blogs target");
            $this->assertStringNotContainsString('pr-and-media-relations', $m[1], $path);
            $this->assertStringNotContainsString('target="_blank"', $m[1], "$path: opens in the same tab");
        }
    }

    public function test_no_page_link_points_at_production_when_served_from_staging(): void
    {
        foreach (['/', '/about-us', '/contact-us', '/faq', '/services', '/blogs', '/client-case-studies'] as $path) {
            $html = $this->html($path, self::STAGING);
            preg_match_all('#\b(?:href|src|action)="(https?://(?:www\.)?datamysite\.com[^"]*)"#i', $html, $m);
            $this->assertSame([], array_values(array_unique($m[1])), "$path (staging) still links to the production host");
        }
    }

    public function test_chat_widget_posts_to_the_current_host(): void
    {
        // the widget file is @verbatim (Blade cannot fill it in), so it reads the address every layout prints in <meta name="home_url">
        foreach ([self::STAGING, self::PRODUCTION] as $host) {
            $html = $this->html('/', $host);
            $this->assertStringContainsString('<meta name="home_url" content="' . $host . '">', $html, "home_url meta on $host");
            $this->assertStringContainsString('document.querySelector(\'meta[name="home_url"]\')', $html);
            $this->assertStringNotContainsString('{{ url(', $html, 'no unprocessed Blade left in the page');
            $this->assertStringNotContainsString('apiBase: "https://datamysite.com/api"', $html, 'the production address is not hard-coded');
        }
    }

    public function test_consultation_links_are_crawlable(): void
    {
        foreach (['/', '/about-us', '/seo-services'] as $path) {
            $html = $this->html($path);
            $this->assertStringNotContainsString('href="javascript:', $html, "$path has javascript: links");
            $this->assertDoesNotMatchRegularExpression('#<a\b[^>]*\shref=""#', $html, "$path has empty links");
        }
        $this->assertMatchesRegularExpression('#<a href="https://datamysite.com/contact-us" class="hv2-btn hv2-btn--3d open-newsletter"#', $this->html('/'));
    }

    public function test_book_now_popup_opens_without_waiting_for_the_late_scripts(): void
    {
        $html = $this->html('/');
        $this->assertStringContainsString("getElementById('newsletter-modal-u')", $html);
        $this->assertStringContainsString("t.closest('.open-newsletter')", $html);
        $this->assertStringContainsString('id="newsletter-modal-u"', $html);
    }

    public function test_pages_without_a_hero_form_do_not_link_to_a_missing_anchor(): void
    {
        foreach (['/seo-services', '/website-development'] as $path) {
            $html = $this->html($path);
            if (strpos($html, 'id="hv2-form"') === false) {
                $this->assertStringNotContainsString('data-scrollto="#hv2-form"', $html, "$path: dead #hv2-form link");
            } else {
                $this->assertGreaterThan(0, substr_count($html, 'id="hv2-form"'));
            }
        }
    }
}
