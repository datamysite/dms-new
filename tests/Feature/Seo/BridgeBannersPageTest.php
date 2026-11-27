<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * /outdoor-advertising/bridge-banners (service 126): rebuilt from the content brief in the hub design (DB rows).
 * Pins the brief's technical checklist so a later section edit cannot silently undo it. The page ranks: URL and canonical stay.
 */
class BridgeBannersPageTest extends TestCase
{
    use CrawlsPages;

    private const PATH = '/outdoor-advertising/bridge-banners';

    private function cells(\DOMXPath $x, string $query): array
    {
        return array_map(fn ($n) => trim($n->textContent), iterator_to_array($x->query($query)));
    }

    public function test_one_h1_and_the_briefs_headings(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $h1 = $x->query('//h1');
        $this->assertSame(1, $h1->length, 'exactly one H1');
        $this->assertSame('Bridge Banner Advertising in Dubai', trim($h1->item(0)->textContent));
        $this->assertSame(0, $x->query('//main//h4')->length, 'no H4 headings left');
        foreach (['Bridge Banner Locations in Dubai', 'Brands we work with', 'Sizes and Formats', 'How to Choose the Right Bridge', 'Bridge Banner Advertising Cost in Dubai',
                  'Permits and Lead Times', 'Designing a Bridge Banner That Gets Read', 'Bridge Banner vs Billboard vs Unipole', 'Why Book Your Bridge Banner With DMS',
                  'Case Studies', 'What our customers say', 'Bridge Banner FAQs', "Get Your Brand Above Dubai's Busiest Roads"] as $h2) {
            $this->assertSame(1, $x->query('//main//h2[normalize-space(.)="' . $h2 . '"]')->length, "H2: $h2");
        }
        $this->assertSame(1, $x->query('//main//h3[normalize-space(.)="From Booking to Going Live"]')->length);
        $this->assertSame(['Book your bridge', 'Permit', 'Printing', 'Installation'], $this->cells($x, '//main//p[contains(@class,"sv2-step-title")]'));
        $this->assertSame(6, $x->query('//main//section[contains(@class,"sv2-why")]//article//h3')->length, 'six "why DMS" cards');
    }

    public function test_locations_table_has_ten_bridges_and_no_prices(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $table = '//section[contains(@class,"sv2-prices--bridges")]//table';
        $this->assertSame(['Road', 'Bridge', 'Daily traffic (est.)'], $this->cells($x, "$table//thead//th"));
        $this->assertSame(10, $x->query("$table//tbody/tr")->length);
        $this->assertStringNotContainsString('AED', $x->query($table)->item(0)->textContent);
        $this->assertSame(['AED 120,000', 'AED 200,000', 'AED 55,000', 'Quoted per site', 'Quoted per site', 'AED 2,500 per banner (one time)'],
            $this->cells($x, '//section[contains(@class,"sv2-prices--bridge-cost")]//tbody//td[2]'));
    }

    public function test_mega_banners_old_prices_and_brief_notes_are_gone(): void
    {
        $html = $this->html(self::PATH);
        $this->assertStringNotContainsStringIgnoringCase('mega banner', $html, 'removed from the page, meta and structured data');
        $main = $this->xpath($html)->query('//main')->item(0)->textContent;
        foreach (['AED 30,000', 'AED 85,000', '150,000 a month', 'Rate band', 'Rates and Lead Times', 'Outdoor advertising guides', '2026 Rates in AED',
                  'Dubai Marina', 'Need Something Bigger?', 'Bridge Banner or Unipole?', '[Button', '[Keep', '[Remove', '[Short'] as $old) {
            $this->assertStringNotContainsString($old, $main, "old text: $old");
        }
    }

    public function test_structured_data_and_seven_faqs(): void
    {
        $html = $this->html(self::PATH);
        $types = array_count_values($this->jsonLdTypes($html));
        $this->assertSame(1, $types['FAQPage'] ?? 0, 'one FAQPage block');
        $this->assertSame(1, $types['Service'] ?? 0, 'one Service block');
        $this->assertSame(1, $types['BreadcrumbList'] ?? 0, 'one BreadcrumbList block');
        $this->assertStringContainsString('"Organization"', $html);
        $this->assertStringContainsString('"LocalBusiness"', $html);
        $ld = collect($this->jsonLd($html));
        $this->assertSame(['Dubai', 'Abu Dhabi', 'Sharjah'], $ld->firstWhere('@type', 'Service')['areaServed']);
        $faq = $ld->firstWhere('@type', 'FAQPage');
        $this->assertCount(7, $faq['mainEntity'], 'the 7 FAQs of the brief');
        $this->assertSame('How much does it cost to advertise on a bridge banner in Dubai?', $faq['mainEntity'][0]['name']);
        $this->assertSame('Do you offer bridge banners outside Dubai?', $faq['mainEntity'][6]['name']);
        $this->assertSame(['Home', 'Outdoor Advertising', 'Bridge Banners'], array_column($ld->firstWhere('@type', 'BreadcrumbList')['itemListElement'], 'name'));
    }

    public function test_meta_canonical_links_and_quote_buttons(): void
    {
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('<link rel="canonical" href="https://datamysite.com' . self::PATH . '"', $html);
        $this->assertStringContainsString('<title>Bridge Banner Advertising in Dubai | Locations and Costs</title>', $html);
        $x = $this->xpath($html);
        foreach ([
            'billboard advertising' => '/outdoor-advertising/billboards-advertising',
            'unipole advertising' => '/outdoor-advertising/unipoles',
            'building wraps' => '/outdoor-advertising/billboards-advertising/building-wraps',
            'hoarding advertising' => '/outdoor-advertising/billboards-advertising/hoarding',
            'Al Khail Road, E311 and Al Ittihad Road' => '/al-khail-road-e311-and-al-ittihad-road-advertising-2026-rates',
            'View all case studies' => '/client-case-studies',
        ] as $text => $path) {
            $a = $x->query('//main//a[normalize-space(.)="' . $text . '"]');
            $this->assertSame(1, $a->length, "link: $text");
            $this->assertStringEndsWith($path, $a->item(0)->getAttribute('href'), "target: $text");
        }
        $this->assertSame(1, $x->query('//a[@data-scrollto="#hv2-form" and normalize-space(.)="Check Bridge Availability"]')->length, 'hero button scrolls to the form');
        $this->assertSame(2, $x->query('//main//button[@data-sv2-open="sv2-quote-modal"]')->length, 'cost + final CTA buttons open the quote form');
        $this->assertSame(1, $x->query('//*[@id="sv2-quote-modal"]')->length);
        $this->assertSame(1, $x->query('//input[@name="service" and @value="Bridge Banners" and ancestor::form[@data-sv2-lead]]')->length);
        $this->assertSame(count(config('case_studies')), $x->query('//section[@id="hv2-cases"]//a[contains(@class,"hv2-case")]')->length, 'all case studies until the DMS team picks six');
    }

    public function test_fast_page_uses_only_subset_icons(): void
    {
        $this->assertContains('bridge-banners', config('v2.fast_pages'));
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('id="v2-icons"', $html);
        preg_match_all('/\bbi-[a-z0-9-]+/', $html, $m);
        preg_match_all('/\.(bi-[a-z0-9-]+)/', file_get_contents(public_path('assets/vendor/bootstrap-icons/bi-v2.css')), $mm);
        $missing = array_values(array_diff(array_unique($m[0]), array_unique($mm[1]), ['bi-v2', 'bi-subset', 'bi-v2-icons']));
        $this->assertSame([], $missing, 'every icon on this page must be in bi-v2.css');
    }
}
