<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * /outdoor-advertising/billboards-advertising (service 16): SEO rebuild in the hub design (DB rows 1357-1369 + hero row 13).
 * Pins the brief's technical checklist so a later section edit cannot silently undo it.
 */
class BillboardsPageTest extends TestCase
{
    use CrawlsPages;

    private const PATH = '/outdoor-advertising/billboards-advertising';

    public function test_one_h1_and_only_h2_h3_section_headings(): void
    {
        $html = $this->html(self::PATH);
        $x = $this->xpath($html);
        $h1 = $x->query('//h1');
        $this->assertSame(1, $h1->length, 'exactly one H1');
        $this->assertSame('Billboard Advertising Dubai', trim($h1->item(0)->textContent));
        $this->assertSame(0, $x->query('//main//h4')->length, 'no H4 section headings left (the old page used H4)');
        foreach (['How Billboard Advertising in Dubai Works', 'Your Billboard Advertising Agency in Dubai', 'Billboard Advertising Locations in Dubai',
                  'Billboard Sizes and Prices in Dubai', 'Speak With Our Planning Team', 'Billboard Advertising Dubai FAQs'] as $h2) {
            $this->assertSame(1, $x->query('//main//h2[normalize-space(.)="' . $h2 . '"]')->length, "H2: $h2");
        }
    }

    public function test_old_copy_and_prices_are_gone(): void
    {
        $html = $this->html(self::PATH);
        foreach (['30,000', 'Transform Your Brand Presence', '2 Million Eyes', 'Billboard Advertising Company in Dubai'] as $old) {
            $this->assertStringNotContainsString($old, $html, "old text: $old");
        }
        // prices match the hub table
        foreach (['40,000', '100,000', '500,000', '55,000', 'Quoted per site'] as $p) $this->assertStringContainsString($p, $html);
    }

    public function test_structured_data_keeps_service_local_business_breadcrumbs_and_ten_faqs(): void
    {
        $html = $this->html(self::PATH);
        $types = array_count_values($this->jsonLdTypes($html));
        $this->assertSame(1, $types['FAQPage'] ?? 0, 'one FAQPage block');
        $this->assertSame(1, $types['Service'] ?? 0, 'one Service block');
        $this->assertSame(1, $types['BreadcrumbList'] ?? 0, 'one BreadcrumbList block');
        $faq = collect($this->jsonLd($html))->firstWhere('@type', 'FAQPage');
        $this->assertCount(10, $faq['mainEntity'], 'the 10 FAQs of the brief');
        $this->assertSame('How much does billboard advertising cost in Dubai?', $faq['mainEntity'][0]['name']);
        $service = collect($this->jsonLd($html))->firstWhere('@type', 'Service');
        $this->assertSame('LocalBusiness', $service['provider']['@type'] ?? null, 'LocalBusiness is kept (as the Service provider)');
        $crumbs = collect($this->jsonLd($html))->firstWhere('@type', 'BreadcrumbList');
        $this->assertSame(['Home', 'Outdoor Advertising', 'Billboard Advertising'], array_column($crumbs['itemListElement'], 'name'));
    }

    public function test_hero_buttons_chips_and_canonical(): void
    {
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('<link rel="canonical" href="https://datamysite.com/outdoor-advertising/billboards-advertising"', $html);
        $this->assertMatchesRegularExpression('#class="sv2-hero-ctas".*?href="\#hv2-form"[^>]*data-scrollto="\#hv2-form".*?api\.whatsapp\.com#s', $html, 'quote button scrolls to the form, second button is WhatsApp');
        foreach (['Since 2010', '5.0 Google rating', 'Permits handled'] as $chip) $this->assertStringContainsString($chip, $html);
        $css = file_get_contents(public_path('assets/css/service-v2.css'));
        $this->assertStringContainsString('.sv2-hero-chips', $css);
    }

    public function test_images_below_the_hero_are_lazy_webp_with_size_and_alt(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        foreach ($x->query('//main//section[contains(@class,"sv2-explain") or contains(@class,"sv2-agency") or contains(@class,"sv2-bb-locations")]//img') as $img) {
            $src = $img->getAttribute('src');
            $this->assertStringContainsString('.webp', $src, "WebP: $src");
            $this->assertSame('lazy', $img->getAttribute('loading'), "lazy: $src");
            $this->assertNotSame('', $img->getAttribute('width') . $img->getAttribute('height'), "size: $src");
            $this->assertNotSame('', trim($img->getAttribute('alt')), "alt: $src");
        }
    }

    public function test_locations_use_the_hub_formats_carousel_with_five_cards(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $sec = $x->query('//section[contains(@class,"sv2-bb-locations") and @data-sv2-carousel]');
        $this->assertSame(1, $sec->length, 'locations use the same carousel block as "Outdoor Advertising Formats in Dubai"');
        $titles = array_map(fn ($n) => trim($n->textContent), iterator_to_array($x->query('.//article[contains(@class,"sv2-format-card")]//h3', $sec->item(0))));
        $this->assertSame(['Sheikh Zayed Road', 'Downtown Dubai', 'Business Bay', 'Airport Road and DXB', 'Dubai Marina Rooftops'], $titles);
        $this->assertSame(0, $x->query('//*[@data-sv2-locations]')->length, 'no map block on this page');
    }

    public function test_fast_page_uses_only_subset_icons(): void
    {
        $this->assertContains('billboards-advertising', config('v2.fast_pages'));
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('id="v2-icons"', $html);
        preg_match_all('/\bbi-[a-z0-9-]+/', $html, $m);
        preg_match_all('/\.(bi-[a-z0-9-]+)/', file_get_contents(public_path('assets/vendor/bootstrap-icons/bi-v2.css')), $mm);
        $missing = array_values(array_diff(array_unique($m[0]), array_unique($mm[1]), ['bi-v2', 'bi-subset', 'bi-v2-icons']));
        $this->assertSame([], $missing, 'every icon on this page must be in bi-v2.css');
    }
}
