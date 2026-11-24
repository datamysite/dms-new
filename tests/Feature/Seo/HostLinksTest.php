<?php

namespace Tests\Feature\Seo;

use App\Http\Middleware\RewriteProductionUrls;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * Stored page content links to https://datamysite.com/...; on a copy of the site (staging) those links must stay on that copy.
 * The switch is APP_URL of the server's .env (production value = nothing is rewritten).
 */
class HostLinksTest extends TestCase
{
    use CrawlsPages;

    private const PAGES = ['/', '/about-us', '/contact-us', '/services', '/seo-services', '/website-development', '/radio-advertising', '/lead-generation', '/influencer-marketing'];

    private function leaks(string $html): array
    {
        preg_match_all('~(?:https?:)?[\\\\]?/[\\\\]?/(?:www\.)?datamysite\.com(?![\w-]|\.[\w])[^"\s<\']*~i', $html, $m);
        return $m[0];
    }

    public function test_on_staging_every_link_in_stored_content_follows_the_staging_address(): void
    {
        config(['app.url' => 'https://staging.datamysite.com']);
        foreach (self::PAGES as $path) {
            $html = $this->html($path, self::STAGING);
            $this->assertSame([], $this->leaks($html), "$path still links to the live site");
        }
        $wd = $this->html('/website-development', self::STAGING);
        $this->assertStringContainsString('href="https://staging.datamysite.com/website-development/e-commerce-website-development"', $wd, 'the E-commerce development link stays on staging');
        $this->assertStringContainsString('mailto:contact@datamysite.com', $wd, 'e-mail addresses are not URLs');
    }

    public function test_json_ld_in_stored_content_follows_the_staging_address_too(): void
    {
        config(['app.url' => 'https://staging.datamysite.com']);
        $html = $this->html('/influencer-marketing', self::STAGING);
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $this->assertNotEmpty($m[1]);
        foreach ($m[1] as $json) {
            $this->assertNotNull(json_decode($json, true), 'JSON-LD is still valid JSON');
            $this->assertSame([], $this->leaks($json));
        }
    }

    public function test_blog_posts_follow_the_staging_address(): void
    {
        config(['app.url' => 'https://staging.datamysite.com']);
        $list = $this->html('/blogs', self::STAGING);
        preg_match_all('#href="https://staging\.datamysite\.com/(?:blogs/)?([a-z0-9-]{25,})"#', $list, $m);
        $slugs = array_slice(array_unique($m[1]), 0, 4);
        $this->assertNotEmpty($slugs, 'blog posts are listed');
        foreach ($slugs as $slug) {
            $this->assertSame([], $this->leaks($this->html("/$slug", self::STAGING)), "/$slug still links to the live site");
        }
    }

    public function test_production_output_is_untouched(): void
    {
        foreach (['https://datamysite.com', 'https://www.datamysite.com'] as $appUrl) {
            config(['app.url' => $appUrl]);
            $html = $this->get('https://datamysite.com/website-development')->assertOk()->getContent();
            $this->assertStringContainsString('href="https://datamysite.com/website-development/e-commerce-website-development"', $html, "APP_URL=$appUrl");
            $this->assertStringNotContainsString('staging.datamysite.com', $html);
        }
    }

    public function test_rewrite_handles_the_url_shapes_and_leaves_everything_else_alone(): void
    {
        $base = 'https://staging.datamysite.com';
        $cases = [
            '<a href="https://datamysite.com/x">' => '<a href="https://staging.datamysite.com/x">',
            '<a href="http://www.datamysite.com/x?a=1#b">' => '<a href="https://staging.datamysite.com/x?a=1#b">',
            '<a href="HTTPS://DataMySite.com/x">' => '<a href="https://staging.datamysite.com/x">',
            '<script src="//datamysite.com/a.js">' => '<script src="//staging.datamysite.com/a.js">',
            '{"url":"https:\/\/datamysite.com\/seo-services"}' => '{"url":"https:\/\/staging.datamysite.com\/seo-services"}',
            'https://datamysite.com' => 'https://staging.datamysite.com',
            'mailto:contact@datamysite.com' => 'mailto:contact@datamysite.com',
            'Write to info@datamysite.com or visit datamysite.com' => 'Write to info@datamysite.com or visit datamysite.com',
            'https://datamysite.com.evil.example/x' => 'https://datamysite.com.evil.example/x',
            'https://notdatamysite.com/x' => 'https://notdatamysite.com/x',
            'https://staging.datamysite.com/x' => 'https://staging.datamysite.com/x',
            'https://www.facebook.com/datamysite' => 'https://www.facebook.com/datamysite',
        ];
        foreach ($cases as $in => $out) {
            $this->assertSame($out, RewriteProductionUrls::rewrite($in, $base), $in);
        }
        // a local copy served from a sub-folder
        $this->assertSame('<a href="http://localhost/datamysite/x">', RewriteProductionUrls::rewrite('<a href="https://datamysite.com/x">', 'http://localhost/datamysite'));
    }

    public function test_base_is_only_used_for_a_non_production_app_url(): void
    {
        $this->assertNull(RewriteProductionUrls::baseFor('https://datamysite.com'));
        $this->assertNull(RewriteProductionUrls::baseFor('https://datamysite.com/'));
        $this->assertNull(RewriteProductionUrls::baseFor('https://www.datamysite.com'));
        $this->assertNull(RewriteProductionUrls::baseFor(''));
        $this->assertSame('https://staging.datamysite.com', RewriteProductionUrls::baseFor('https://staging.datamysite.com/'));
        $this->assertSame('http://localhost/datamysite', RewriteProductionUrls::baseFor('http://localhost/datamysite'));
    }

    public function test_redirect_locations_follow_the_staging_address(): void
    {
        config(['app.url' => 'https://staging.datamysite.com']);
        $mw = new RewriteProductionUrls();
        $res = $mw->handle(Request::create('https://staging.datamysite.com/old'), fn () => new RedirectResponse('https://datamysite.com/new-page'));
        $this->assertSame('https://staging.datamysite.com/new-page', $res->headers->get('Location'));

        config(['app.url' => 'https://datamysite.com']);
        $res = $mw->handle(Request::create('https://datamysite.com/old'), fn () => new RedirectResponse('https://datamysite.com/new-page'));
        $this->assertSame('https://datamysite.com/new-page', $res->headers->get('Location'), 'production keeps its redirects');
    }

    public function test_only_html_is_rewritten(): void
    {
        config(['app.url' => 'https://staging.datamysite.com']);
        $mw = new RewriteProductionUrls();
        $json = $mw->handle(Request::create('/x'), fn () => new Response('{"u":"https://datamysite.com/a"}', 200, ['Content-Type' => 'application/json']));
        $this->assertStringContainsString('https://datamysite.com/a', $json->getContent());
        $html = $mw->handle(Request::create('/x'), fn () => new Response('<a href="https://datamysite.com/a">', 200, ['Content-Type' => 'text/html; charset=UTF-8']));
        $this->assertStringContainsString('https://staging.datamysite.com/a', $html->getContent());
    }
}
