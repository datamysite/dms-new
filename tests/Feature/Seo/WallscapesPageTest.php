<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * /outdoor-advertising/billboards-advertising/wallscapes (service 50): rebuilt from the content brief in the hub design (DB rows).
 * Pins the brief's technical checklist so a later section edit cannot silently undo it. The page ranks first: URL and canonical stay.
 */
class WallscapesPageTest extends TestCase
{
    use CrawlsPages;

    private const PATH = '/outdoor-advertising/billboards-advertising/wallscapes';

    private const WRAPS = '/outdoor-advertising/billboards-advertising/building-wraps';

    public function test_one_h1_and_the_briefs_section_headings(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $h1 = $x->query('//h1');
        $this->assertSame(1, $h1->length, 'exactly one H1');
        $this->assertSame('Wallscape Advertising Dubai', trim($h1->item(0)->textContent));
        $this->assertSame(0, $x->query('//main//h4')->length, 'no H4 section headings left (the old page used H4)');
        foreach (['Who We Have Worked With', 'What Is Wallscape Advertising?', 'Why Brands Use Us for Wallscapes', 'Wallscape Formats',
                  'Where Wallscapes Work in Dubai', 'Wallscape Work We Have Delivered', 'Wallscape Advertising Cost in Dubai', 'From Blank Wall to Live Wallscape',
                  'Ask a Planner About a Wall', 'What Clients Say About Working With Us', 'Wallscape Advertising FAQs', 'Book a Wall in Dubai'] as $h2) {
            $this->assertSame(1, $x->query('//main//h2[normalize-space(.)="' . $h2 . '"]')->length, "H2: $h2");
        }
        $formats = $x->query('//section[contains(@class,"sv2-wrap-types")]//h3');
        $this->assertSame(['Vinyl Wallscape', 'Hand-Painted Mural', 'Lit Wallscape'], array_map(fn ($n) => trim($n->textContent), iterator_to_array($formats)));
    }

    public function test_comparison_and_price_tables(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $cells = fn ($q) => array_map(fn ($n) => trim($n->textContent), iterator_to_array($x->query($q)));
        $this->assertSame(['Feature', 'Wallscape', 'Building wrap', 'Billboard'], $cells('//table[contains(@class,"sv2-compare-table")]//thead//th'));
        $this->assertSame(['Where it goes', 'Size', 'Made from', 'Usual booking'], $cells('//table[contains(@class,"sv2-compare-table")]//tbody//th[@scope="row"]'));
        $this->assertSame(['40,000', '80,000', 'Quoted per project'], $cells('//section[contains(@class,"sv2-prices--wall")]//tbody//td[2]'));
        $css = file_get_contents(public_path('assets/css/service-v2.css'));
        foreach (['.sv2-compare-table', '.sv2-wrap-grid--3', '.sv2-wrap-card--link'] as $rule) $this->assertStringContainsString($rule, $css);
    }

    public function test_old_copy_and_brief_placeholders_are_gone(): void
    {
        $main = $this->xpath($this->html(self::PATH))->query('//main')->item(0)->textContent;
        foreach (['Transform Building Facades and Walls', 'Benefits of Wallscape Advertising', 'Why Wallscape Advertising Works', 'Call to Action', '[Button', '[Logo', 'Content to be added'] as $old) {
            $this->assertStringNotContainsString($old, $main, "old text: $old");
        }
    }

    public function test_structured_data_keeps_organization_service_breadcrumbs_and_eight_faqs(): void
    {
        $html = $this->html(self::PATH);
        $types = array_count_values($this->jsonLdTypes($html));
        $this->assertSame(1, $types['FAQPage'] ?? 0, 'one FAQPage block');
        $this->assertSame(1, $types['Service'] ?? 0, 'one Service block');
        $this->assertSame(1, $types['BreadcrumbList'] ?? 0, 'one BreadcrumbList block');
        $this->assertStringContainsString('"Organization"', $html);
        $faq = collect($this->jsonLd($html))->firstWhere('@type', 'FAQPage');
        $this->assertCount(8, $faq['mainEntity'], 'the 8 FAQs of the brief');
        $this->assertSame('What is the difference between a wallscape and a billboard?', $faq['mainEntity'][0]['name']);
        $this->assertSame('How long can a wallscape stay up?', $faq['mainEntity'][7]['name']);
        $crumbs = collect($this->jsonLd($html))->firstWhere('@type', 'BreadcrumbList');
        $this->assertSame(['Home', 'Outdoor Advertising', 'Billboard Advertising', 'Wallscapes'], array_column($crumbs['itemListElement'], 'name'));
    }

    public function test_canonical_quote_buttons_and_links_to_building_wraps(): void
    {
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('<link rel="canonical" href="https://datamysite.com' . self::PATH . '"', $html);
        $x = $this->xpath($html);
        $cta = $x->query('//section[@id="hv2-cta"]')->item(0);
        $this->assertSame(1, $x->query('.//button[@data-sv2-open="sv2-quote-modal"]', $cta)->length, '"Get a Quote" opens the quote form');
        $this->assertStringContainsString('WhatsApp +971 52 528 7259', preg_replace('/\s+/', ' ', $cta->textContent));
        foreach (['building wrap advertising page', 'building wrap page'] as $text) {
            $this->assertGreaterThan(0, $x->query('//main//a[normalize-space(.)="' . $text . '" and contains(@href,"' . self::WRAPS . '")]')->length, "link: $text");
        }
        $this->assertSame(1, $x->query('//main//a[normalize-space(.)="View all case studies" and contains(@href,"/client-case-studies")]')->length);
        $this->assertSame(1, $x->query('//input[@name="service" and @value="Wallscapes" and ancestor::form[@data-sv2-lead]]')->length);
    }

    public function test_images_below_the_hero_are_lazy_webp_with_size_and_alt(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $imgs = $x->query('//main//section[contains(@class,"sv2-agency") or contains(@class,"sv2-bb-locations")]//img');
        $this->assertSame(5, $imgs->length, 'agency photo + 4 location cards');
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

    public function test_locations_use_the_hub_map_block_with_four_cards(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $sec = $x->query('//section[contains(@class,"sv2-bb-locations") and @data-sv2-locations]');
        $this->assertSame(1, $sec->length);
        $cards = iterator_to_array($x->query('.//article[contains(@class,"sv2-loc-card") and @data-lat and @data-lng and @data-loc]', $sec->item(0)));
        $titles = array_map(fn ($n) => trim($x->query('.//h3', $n)->item(0)->textContent), $cards);
        $this->assertSame(['Downtown Dubai and Business Bay', 'Al Quoz and Alserkal Avenue', 'Jumeirah and City Walk', 'Deira and Bur Dubai'], $titles);
    }

    public function test_fast_page_uses_only_subset_icons(): void
    {
        $this->assertContains('wallscapes', config('v2.fast_pages'));
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('id="v2-icons"', $html);
        preg_match_all('/\bbi-[a-z0-9-]+/', $html, $m);
        preg_match_all('/\.(bi-[a-z0-9-]+)/', file_get_contents(public_path('assets/vendor/bootstrap-icons/bi-v2.css')), $mm);
        $missing = array_values(array_diff(array_unique($m[0]), array_unique($mm[1]), ['bi-v2', 'bi-subset', 'bi-v2-icons']));
        $this->assertSame([], $missing, 'every icon on this page must be in bi-v2.css');
    }
}
