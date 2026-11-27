<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * /outdoor-advertising/unipoles (service 51): rebuilt from the content brief in the hub design (DB rows).
 * Pins the brief's technical checklist so a later section edit cannot silently undo it. URL and canonical stay.
 */
class UnipolesPageTest extends TestCase
{
    use CrawlsPages;

    private const PATH = '/outdoor-advertising/unipoles';

    private function cells(\DOMXPath $x, string $query): array
    {
        return array_map(fn ($n) => trim($n->textContent), iterator_to_array($x->query($query)));
    }

    public function test_one_h1_and_the_briefs_headings(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $h1 = $x->query('//h1');
        $this->assertSame(1, $h1->length, 'exactly one H1');
        $this->assertSame('Unipole Advertising in Dubai', trim($h1->item(0)->textContent));
        $this->assertSame(0, $x->query('//main//h4|//main//h5')->length, 'no H4/H5 headings left');
        foreach (['Brands we work with', 'Why Brands Book Unipoles', 'What You Get When You Book Unipoles with DMS', 'Unipole Formats',
                  'Digital Unipole Advertising in Dubai', 'Roads Where We Book Unipoles', 'Unipole Advertising Cost in Dubai', 'Unipole or Another Large Format?',
                  'How a Unipole Campaign Runs', 'Unipole Advertising Across the UAE', 'Case Studies', 'Unipole Advertising FAQs',
                  'Get a Shortlist of Available Unipoles'] as $h2) {
            $this->assertSame(1, $x->query('//main//h2[normalize-space(.)="' . $h2 . '"]')->length, "H2: $h2");
        }
        $this->assertSame(['Site photos before you commit.', 'Face direction checked.', 'A hold while you decide.', 'Print and install handled.',
            'Your digital slot in writing.', 'Proof that it ran.', "What We Won't Do"], $this->cells($x, '//main//section[contains(@class,"sv2-why")]//h3'));
        $this->assertSame(4, $x->query('//main//ul[contains(@class,"sv2-wontdo")]/li')->length);
        $this->assertSame(['Loop length and your seconds.', 'Plays per day.', 'Dayparting.'],
            $this->cells($x, '//main//section[contains(@class,"sv2-uni-digital")]//h3'));
        $this->assertSame(['Brief.', 'Shortlist with photos.', 'Hold and approvals.', 'Print and install.', 'Proof and checks.'],
            $this->cells($x, '//main//p[contains(@class,"sv2-step-title")]'));
    }

    public function test_section_order_follows_the_brief(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $ids = array_map(fn ($n) => $n->getAttribute('id'), iterator_to_array($x->query('//main//div[@data-sv2-legacy]')));
        $this->assertSame(['sv2-brands-we-work-with', 'sv2-why-brands-book-unipoles', 'sv2-why-dms-for-unipoles', 'sv2-unipole-formats',
            'sv2-digital-unipoles', 'sv2-unipole-roads', 'sv2-unipole-advertising-cost-in-dubai', 'sv2-unipole-or-another-format',
            'sv2-how-a-unipole-campaign-runs', 'sv2-unipole-uae-coverage', 'sv2-unipole-case-studies', 'sv2-unipole-quote-cta'], $ids);
    }

    public function test_the_four_tables(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $t = fn (string $cls) => '//section[contains(@class,"' . $cls . '")]//table';
        $this->assertSame(['Format', 'What it is', 'Good for'], $this->cells($x, $t('sv2-uni-formats') . '//thead//th'));
        $this->assertSame(['Single face', 'Double-sided', 'V-shape', 'Digital LED'], $this->cells($x, $t('sv2-uni-formats') . '//tbody//th'));
        $this->assertSame(['Road', 'Who passes', 'Good for'], $this->cells($x, $t('sv2-uni-roads') . '//thead//th'));
        $this->assertSame(7, $x->query($t('sv2-uni-roads') . '//tbody/tr')->length);
        $this->assertSame(['AED 45,000 a month', 'AED 175,000 a month', 'AED 20,000 per slot a month', 'AED 25,000 (one time)'],
            $this->cells($x, $t('sv2-uni-cost') . '//tbody//td[2]'));
        $this->assertSame(['Unipole', 'Bridge banner', 'Hoarding', 'Lamp post'], $this->cells($x, $t('sv2-uni-vs') . '//tbody//th'));
    }

    public function test_old_copy_old_prices_and_brief_notes_are_gone(): void
    {
        $html = $this->html(self::PATH);
        $main = $this->xpath($html)->query('//main')->item(0)->textContent;
        foreach (['Command Attention', 'What is Unipole Advertising', 'Advantages of Partnering', 'Tower Above', 'AED 40,000', 'AED 120,000', 'AED 15,000',
                  'Cinema Advertising', '[Keep', '[Button', '[Link', '[Six', '[Only', '[Every', '[Show', '[Five'] as $old) {
            $this->assertStringNotContainsString($old, $main, "old text: $old");
        }
        $this->assertStringNotContainsString('Billboard Rates', $html, 'the old H1 and meta are gone');
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
        $this->assertSame('How much does unipole advertising cost in Dubai?', $faq['mainEntity'][0]['name']);
        $this->assertSame('Do you book unipoles outside Dubai?', $faq['mainEntity'][6]['name']);
        $this->assertSame(['Home', 'Outdoor Advertising', 'Unipoles'], array_column($ld->firstWhere('@type', 'BreadcrumbList')['itemListElement'], 'name'));
    }

    public function test_meta_canonical_links_and_quote_form(): void
    {
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('<link rel="canonical" href="https://datamysite.com' . self::PATH . '"', $html);
        $this->assertStringContainsString('<title>Unipole Advertising in Dubai | Static and Digital Unipoles</title>', $html);
        $this->assertStringContainsString('Book static and digital unipole advertising in Dubai on Sheikh Zayed Road and the main highways. We check the site, print, install and send proof photos.', $html);
        $x = $this->xpath($html);
        foreach (['Bridge banner' => '/outdoor-advertising/bridge-banners', 'Hoarding' => '/outdoor-advertising/billboards-advertising/hoarding',
                  'Lamp post' => '/outdoor-advertising/lamp-post',
                  'How Much Does Unipole Advertising Cost in Dubai in 2026?' => '/how-much-does-unipole-advertising-cost-in-dubai-in-2026',
                  'View all case studies' => '/client-case-studies'] as $text => $path) {
            $a = $x->query('//main//div[@data-sv2-legacy]//a[normalize-space(.)="' . $text . '"]');
            $this->assertSame(1, $a->length, "link: $text");
            $this->assertStringEndsWith($path, $a->item(0)->getAttribute('href'), "target: $text");
        }
        $this->assertSame(1, $x->query('//main//button[@data-sv2-open="sv2-quote-modal" and normalize-space(.)="Get My Shortlist"]')->length, 'final CTA button opens the quote form');
        $this->assertSame(1, $x->query('//*[@id="sv2-quote-modal"]')->length);
        $this->assertSame(1, $x->query('//input[@name="service" and @value="Unipoles" and ancestor::form[@data-sv2-lead]]')->length, 'the form names this page, not the parent service');
        $this->assertSame(count(config('case_studies')), $x->query('//section[@id="hv2-cases"]//a[contains(@class,"hv2-case")]')->length, 'all case studies until the DMS team picks six');
    }

    public function test_fast_page_uses_only_subset_icons(): void
    {
        $this->assertContains('unipoles', config('v2.fast_pages'));
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('id="v2-icons"', $html);
        preg_match_all('/\bbi-[a-z0-9-]+/', $html, $m);
        preg_match_all('/\.(bi-[a-z0-9-]+)/', file_get_contents(public_path('assets/vendor/bootstrap-icons/bi-v2.css')), $mm);
        $missing = array_values(array_diff(array_unique($m[0]), array_unique($mm[1]), ['bi-v2', 'bi-subset', 'bi-v2-icons']));
        $this->assertSame([], $missing, 'every icon on this page must be in bi-v2.css');
    }
}
