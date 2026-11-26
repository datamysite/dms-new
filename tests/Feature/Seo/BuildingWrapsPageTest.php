<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * /outdoor-advertising/billboards-advertising/building-wraps (service 48): rebuilt from the content brief in the hub design (DB rows).
 * Pins the brief's technical checklist so a later section edit cannot silently undo it.
 */
class BuildingWrapsPageTest extends TestCase
{
    use CrawlsPages;

    private const PATH = '/outdoor-advertising/billboards-advertising/building-wraps';

    public function test_one_h1_and_the_briefs_section_headings(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $h1 = $x->query('//h1');
        $this->assertSame(1, $h1->length, 'exactly one H1');
        $this->assertSame('Building Wrap Advertising Dubai', trim($h1->item(0)->textContent));
        $this->assertSame(0, $x->query('//main//h4')->length, 'no H4 section headings left (the old page used H4)');
        foreach (['Trusted by Brands Across the UAE', 'What Is Building Wrap Advertising?', 'Why Brands Choose Us for Building Wraps', 'Building Wrap Formats',
                  'Building Wrap Locations in Dubai', 'Building Wraps We Have Delivered', 'Building Wrap Advertising Cost in Dubai', 'From Brief to Wrapped Building',
                  'Talk to a Building Wrap Planner', 'What Clients Say About Working With Us', 'Building Wrap Advertising FAQs', 'Put Your Brand on a Dubai Building'] as $h2) {
            $this->assertSame(1, $x->query('//main//h2[normalize-space(.)="' . $h2 . '"]')->length, "H2: $h2");
        }
        $formats = $x->query('//section[contains(@class,"sv2-wrap-types")]//h3');
        $this->assertSame(['Full Building Wrap', 'Facade Banner', 'Scaffolding Mesh Wrap', 'Glass Facade Film'], array_map(fn ($n) => trim($n->textContent), iterator_to_array($formats)));
    }

    public function test_old_copy_is_gone_and_the_new_prices_are_shown(): void
    {
        $html = $this->html(self::PATH);
        foreach (['350,000', 'Facade &amp; Scaffolding Cover Rates', 'Facade & Scaffolding Cover Rates', 'Why Choose Building Wrap Advertising', 'Our Building Wrap Advertising Services', '[Button'] as $old) {
            $this->assertStringNotContainsString($old, $html, "old text: $old");
        }
        $cells = array_map(fn ($n) => trim($n->textContent), iterator_to_array($this->xpath($html)->query('//table[contains(@class,"sv2-price-table")]//tbody//td[3]')));
        $this->assertSame(['50,000', '100,000', '250,000', 'Quoted per site'], $cells);
    }

    public function test_structured_data_keeps_organization_service_breadcrumbs_and_ten_faqs(): void
    {
        $html = $this->html(self::PATH);
        $types = array_count_values($this->jsonLdTypes($html));
        $this->assertSame(1, $types['FAQPage'] ?? 0, 'one FAQPage block');
        $this->assertSame(1, $types['Service'] ?? 0, 'one Service block');
        $this->assertSame(1, $types['BreadcrumbList'] ?? 0, 'one BreadcrumbList block');
        $this->assertStringContainsString('"Organization"', $html);
        $faq = collect($this->jsonLd($html))->firstWhere('@type', 'FAQPage');
        $this->assertCount(10, $faq['mainEntity'], 'the 10 FAQs of the brief');
        $this->assertSame('How much does building wrap advertising cost in Dubai?', $faq['mainEntity'][0]['name']);
        $this->assertSame('Will a wrap damage the building?', $faq['mainEntity'][9]['name']);
        $crumbs = collect($this->jsonLd($html))->firstWhere('@type', 'BreadcrumbList');
        $this->assertSame(['Home', 'Outdoor Advertising', 'Billboard Advertising', 'Building Wraps'], array_column($crumbs['itemListElement'], 'name'));
        $this->assertStringEndsWith('/outdoor-advertising/billboards-advertising', $crumbs['itemListElement'][2]['item'], 'the breadcrumb links to the Billboard Advertising page');
    }

    public function test_canonical_quote_buttons_and_external_link(): void
    {
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('<link rel="canonical" href="https://datamysite.com' . self::PATH . '"', $html);
        $x = $this->xpath($html);
        $cta = $x->query('//section[@id="hv2-cta"]')->item(0);
        $this->assertSame(1, $x->query('.//button[@data-sv2-open="sv2-quote-modal"]', $cta)->length, '"Get a Quote" opens the quote form');
        $this->assertStringContainsString('WhatsApp +971 52 528 7259', preg_replace('/\s+/', ' ', $cta->textContent));
        $this->assertSame(1, $x->query('//a[contains(@href,"thenationalnews.com") and @target="_blank" and contains(@rel,"noopener")]')->length);
    }

    public function test_images_below_the_hero_are_lazy_webp_with_size_and_alt(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $imgs = $x->query('//main//section[contains(@class,"sv2-explain") or contains(@class,"sv2-agency") or contains(@class,"sv2-bb-locations")]//img');
        $this->assertSame(7, $imgs->length, 'explainer + agency photo + 5 location cards');
        foreach ($imgs as $img) {
            $src = $img->getAttribute('src');
            $this->assertStringContainsString('.webp', $src, "WebP: $src");
            $this->assertFileExists(public_path(preg_replace('#^.*?/public/|\?.*$#', '', $src)));
            $this->assertSame('lazy', $img->getAttribute('loading'), "lazy: $src");
            $this->assertNotSame('', $img->getAttribute('width'), "width: $src");
            $this->assertNotSame('', $img->getAttribute('height'), "height: $src");
            $this->assertNotSame('', trim($img->getAttribute('alt')), "alt: $src");
        }
    }

    public function test_locations_use_the_hub_map_block_with_five_cards(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $sec = $x->query('//section[contains(@class,"sv2-bb-locations") and @data-sv2-locations]');
        $this->assertSame(1, $sec->length);
        $cards = iterator_to_array($x->query('.//article[contains(@class,"sv2-loc-card") and @data-lat and @data-lng and @data-loc]', $sec->item(0)));
        $titles = array_map(fn ($n) => trim($x->query('.//h3', $n)->item(0)->textContent), $cards);
        $this->assertSame(['Sheikh Zayed Road', 'Downtown Dubai', 'Business Bay', 'Dubai Marina', 'Airport Road and Garhoud'], $titles);
    }

    public function test_fast_page_uses_only_subset_icons_and_has_its_styles(): void
    {
        $this->assertContains('building-wraps', config('v2.fast_pages'));
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('id="v2-icons"', $html);
        preg_match_all('/\bbi-[a-z0-9-]+/', $html, $m);
        preg_match_all('/\.(bi-[a-z0-9-]+)/', file_get_contents(public_path('assets/vendor/bootstrap-icons/bi-v2.css')), $mm);
        $missing = array_values(array_diff(array_unique($m[0]), array_unique($mm[1]), ['bi-v2', 'bi-subset', 'bi-v2-icons']));
        $this->assertSame([], $missing, 'every icon on this page must be in bi-v2.css');
        $css = file_get_contents(public_path('assets/css/service-v2.css'));
        foreach (['.sv2-wrap-grid', 'p.sv2-hero-sub'] as $rule) $this->assertStringContainsString($rule, $css);
    }
}
