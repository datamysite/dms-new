<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * /outdoor-advertising/lamp-post (service 52): rebuilt from the content brief in the hub design (DB rows).
 * Pins the brief's technical checklist so a later section edit cannot silently undo it. URL and canonical stay.
 */
class LampPostPageTest extends TestCase
{
    use CrawlsPages;

    private const PATH = '/outdoor-advertising/lamp-post';

    private function cells(\DOMXPath $x, string $query): array
    {
        return array_map(fn ($n) => trim($n->textContent), iterator_to_array($x->query($query)));
    }

    public function test_one_h1_and_the_briefs_headings(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $h1 = $x->query('//h1');
        $this->assertSame(1, $h1->length, 'exactly one H1');
        $this->assertSame('Lamppost Advertising in Dubai', trim($h1->item(0)->textContent));
        $this->assertSame(0, $x->query('//main//h4|//main//h5')->length, 'no H4/H5 headings left');
        foreach (['Brands we work with', 'Why Brands Book Lamp Posts in Dubai', 'What You Get When You Book Lamp Posts with DMS', 'Lamppost Advertising Formats',
                  'Roads and Areas We Book', 'How Many Poles Your Campaign Needs', 'Lamp Post Advertising Cost in Dubai', 'Lamp Posts or Billboards?',
                  'Permits, Lead Times and How a Campaign Runs', 'Artwork That Works on a Lamp Post', 'Lamppost Advertising Across the UAE', 'Case Studies',
                  'Lamppost Advertising FAQs', 'Get a Route Plan for Your Lamp Post Campaign'] as $h2) {
            $this->assertSame(1, $x->query('//main//h2[normalize-space(.)="' . $h2 . '"]')->length, "H2: $h2");
        }
        $this->assertSame(['A route planned pole by pole.', 'A quote you can compare.', 'The permit, handled.', 'Print made for Dubai.',
            'Installation and weekly checks.', 'Removal and a photo report.'], $this->cells($x, '//main//section[contains(@class,"sv2-why")]//h3'));
        $this->assertSame(['Signals, Junctions and Slower Roads', 'Faster Roads', 'Traffic Direction and Faces'],
            $this->cells($x, '//main//section[contains(@class,"sv2-lamp-count")]//h3'));
        $this->assertSame(3, $x->query('//main//div[contains(@class,"sv2-route-plan")]//li')->length);
        $this->assertSame(['Brief.', 'Route plan and quote.', 'Permit.', 'Print and install.', 'Checks and report.'],
            $this->cells($x, '//main//p[contains(@class,"sv2-step-title")]'));
    }

    public function test_section_order_follows_the_brief(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $ids = array_map(fn ($n) => $n->getAttribute('id'), iterator_to_array($x->query('//main//div[@data-sv2-legacy]')));
        $this->assertSame(['sv2-brands-we-work-with', 'sv2-why-brands-book-lamp-posts', 'sv2-why-dms-for-lamp-posts', 'sv2-lamp-post-formats',
            'sv2-lamp-post-roads-and-areas', 'sv2-how-many-lamp-posts', 'sv2-lamp-post-advertising-cost-in-dubai', 'sv2-lamp-posts-or-billboards',
            'sv2-how-a-lamp-post-campaign-runs', 'sv2-lamp-post-artwork-and-uae-coverage', 'sv2-lamp-post-case-studies', 'sv2-lamp-post-quote-cta'], $ids);
    }

    public function test_the_four_tables(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $t = fn (string $cls) => '//section[contains(@class,"' . $cls . '")]//table';
        $this->assertSame(['Format', 'How it works', 'Good for'], $this->cells($x, $t('sv2-lamp-formats') . '//thead//th'));
        $this->assertSame(['Static banner (single or double-sided)', 'Backlit lamp post', 'Digital LED lamp post'], $this->cells($x, $t('sv2-lamp-formats') . '//tbody//th'));
        $this->assertSame(6, $x->query($t('sv2-spots') . '//tbody/tr')->length);
        $this->assertSame(['AED 2,000 per pole', 'AED 3,500 per pole', 'AED 10,000 per pole', 'AED 4,000 per unit', 'Quoted per route', 'AED 120 per banner (one time)'],
            $this->cells($x, $t('sv2-lamp-cost') . '//tbody//td[2]'));
        $this->assertSame(['Feature', 'Lamp posts', 'Billboards'], $this->cells($x, $t('sv2-lamp-vs') . '//thead//th'));
        $this->assertSame(['How people see it', 'Targeting', 'Budget to start', 'Best for'], $this->cells($x, $t('sv2-lamp-vs') . '//tbody//th'));
    }

    public function test_old_copy_old_prices_and_brief_notes_are_gone(): void
    {
        $main = $this->xpath($this->html(self::PATH))->query('//main')->item(0)->textContent;
        foreach (['Light Up the Streets', 'What is Lamp Post Advertising', 'Advantages of Choosing DataMySite', 'AED 500', 'RTA', 'Cinema Advertising',
                  'Unipole Advertising Cost', '[Keep', '[Button', '[Link', '[Six', '[Only', '[Every'] as $old) {
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
        $ld = collect($this->jsonLd($html));
        $this->assertSame(['@type' => 'Country', 'name' => 'United Arab Emirates'], $ld->firstWhere('@type', 'Service')['areaServed']);
        $faq = $ld->firstWhere('@type', 'FAQPage');
        $this->assertCount(7, $faq['mainEntity'], 'the 7 FAQs of the brief');
        $this->assertSame('How much does lamp post advertising cost in Dubai?', $faq['mainEntity'][0]['name']);
        $this->assertSame('Do you offer lamppost advertising in Abu Dhabi and Sharjah?', $faq['mainEntity'][6]['name']);
        $this->assertSame(['Home', 'Outdoor Advertising', 'Lamp Post'], array_column($ld->firstWhere('@type', 'BreadcrumbList')['itemListElement'], 'name'));
    }

    public function test_meta_canonical_links_and_quote_form(): void
    {
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('<link rel="canonical" href="https://datamysite.com' . self::PATH . '"', $html);
        $this->assertStringContainsString('<title>Lamppost Advertising in Dubai | Routes, Permits and Cost</title>', $html);
        $this->assertStringContainsString('Book lamppost advertising in Dubai on the roads your customers use. We plan the route, handle the permit, print, install and check every pole.', $html);
        $x = $this->xpath($html);
        foreach (['billboard' => '/outdoor-advertising/billboards-advertising', 'unipole' => '/outdoor-advertising/unipoles',
                  'How Much Does Lamp Post Advertising Cost in Dubai? 2026' => '/how-much-does-lamp-post-advertising-cost-in-dubai-2026',
                  'View all case studies' => '/client-case-studies'] as $text => $path) {
            $a = $x->query('//main//a[normalize-space(.)="' . $text . '"]');
            $this->assertSame(1, $a->length, "link: $text");
            $this->assertStringEndsWith($path, $a->item(0)->getAttribute('href'), "target: $text");
        }
        $this->assertSame(1, $x->query('//main//button[@data-sv2-open="sv2-quote-modal" and normalize-space(.)="Get My Route Plan"]')->length, 'final CTA button opens the quote form');
        $this->assertSame(1, $x->query('//*[@id="sv2-quote-modal"]')->length);
        $this->assertSame(1, $x->query('//input[@name="service" and @value="Lamp Post" and ancestor::form[@data-sv2-lead]]')->length, 'the form names this page, not the parent service');
        $this->assertSame(count(config('case_studies')), $x->query('//section[@id="hv2-cases"]//a[contains(@class,"hv2-case")]')->length, 'all case studies until the DMS team picks six');
    }

    public function test_fast_page_uses_only_subset_icons(): void
    {
        $this->assertContains('lamp-post', config('v2.fast_pages'));
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('id="v2-icons"', $html);
        preg_match_all('/\bbi-[a-z0-9-]+/', $html, $m);
        preg_match_all('/\.(bi-[a-z0-9-]+)/', file_get_contents(public_path('assets/vendor/bootstrap-icons/bi-v2.css')), $mm);
        $missing = array_values(array_diff(array_unique($m[0]), array_unique($mm[1]), ['bi-v2', 'bi-subset', 'bi-v2-icons']));
        $this->assertSame([], $missing, 'every icon on this page must be in bi-v2.css');
    }
}
