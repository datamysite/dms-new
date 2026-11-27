<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * /outdoor-advertising/flyers-distribution (service 17): rebuilt from the content brief in the hub design (DB rows).
 * Pins the brief's technical checklist so a later section edit cannot silently undo it. URL and canonical stay.
 */
class FlyersDistributionPageTest extends TestCase
{
    use CrawlsPages;

    private const PATH = '/outdoor-advertising/flyers-distribution';

    private function cells(\DOMXPath $x, string $query): array
    {
        return array_map(fn ($n) => trim($n->textContent), iterator_to_array($x->query($query)));
    }

    public function test_one_h1_and_the_briefs_headings(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $h1 = $x->query('//h1');
        $this->assertSame(1, $h1->length, 'exactly one H1');
        $this->assertSame('Flyer Distribution in Dubai', trim($h1->item(0)->textContent));
        $this->assertSame(0, $x->query('//main//h4|//main//h5')->length, 'no H4/H5 headings left');
        foreach (['Door to Door, Hand to Hand and Event Distribution', 'Brands we work with', 'Every Flyer Tracked by GPS', 'Areas We Cover in Dubai, Sharjah and Abu Dhabi',
                  'Flyer Distribution Cost in Dubai', 'Permits and the Consumer Protection Logo', 'How a Flyer Campaign Runs', 'What Response to Expect',
                  'Why Run Your Flyer Campaign With DMS', 'Case Studies', 'What our customers say', 'Flyer Distribution Guides', 'Flyer Distribution FAQs',
                  'Get Your Flyers Into the Right Hands'] as $h2) {
            $this->assertSame(1, $x->query('//main//h2[normalize-space(.)="' . $h2 . '"]')->length, "H2: $h2");
        }
        foreach (['Door to door', 'Hand to hand', 'Offices and business areas', 'Events and exhibitions', 'What We Will Not Do', 'What Makes a Flyer Work'] as $h3) {
            $this->assertSame(1, $x->query('//main//h3[normalize-space(.)="' . $h3 . '"]')->length, "H3: $h3");
        }
        $this->assertSame(['Tell us where you sell.', 'Design and print.', 'Permit and access.', 'Distribution.', 'GPS report.'],
            $this->cells($x, '//main//p[contains(@class,"sv2-step-title")]'));
        $this->assertSame(1, $x->query('//main//section[contains(@class,"sv2-process--five")]')->length, 'five-step timeline');
        $this->assertSame(5, $x->query('//main//section[contains(@class,"sv2-why")]//article')->length, 'five "why DMS" points');
    }

    public function test_section_order_follows_the_brief(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $ids = array_map(fn ($n) => $n->getAttribute('id'), iterator_to_array($x->query('//main//div[@data-sv2-legacy]')));
        $this->assertSame(['sv2-brands-we-work-with', 'sv2-flyer-distribution-methods', 'sv2-flyer-gps-tracking', 'sv2-flyer-distribution-areas',
            'sv2-flyer-distribution-cost-in-dubai', 'sv2-flyer-permits', 'sv2-how-a-flyer-campaign-runs', 'sv2-flyer-response', 'sv2-why-dms-for-flyers',
            'sv2-flyer-case-studies', 'sv2-what-our-clients-say', 'sv2-flyer-quote-cta'], $ids);
    }

    public function test_area_and_cost_tables(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $areas = '//section[contains(@class,"sv2-areas")]//table';
        $this->assertSame(['Area type', 'Examples', 'Works best for'], $this->cells($x, "$areas//thead//th"));
        $this->assertSame(['Villa communities', 'Apartment areas', 'Business districts', 'High footfall spots'], $this->cells($x, "$areas//tbody//th"));
        $this->assertSame(['AED 0.15 per flyer', 'AED 0.20 per flyer, or per promoter day', 'AED 0.10 per flyer', 'AED 1,500', 'Quoted per area or publication'],
            $this->cells($x, '//section[contains(@class,"sv2-prices--flyer-cost")]//tbody//td[2]'));
    }

    public function test_old_copy_and_brief_notes_are_gone(): void
    {
        $main = $this->xpath($this->html(self::PATH))->query('//main')->item(0)->textContent;
        foreach (['We Specialize in Strategic', 'Why Choose Flyer Distribution in Dubai', 'Our Flyer Distribution Services', 'Digital Flyers', 'Handout Flyer',
                  'Eco-Friendly', '30 fils', 'Bridge', '[Keep', '[Button', '[Show', '[Accordion', '[Link', '[DMS'] as $old) {
            $this->assertStringNotContainsString($old, $main, "old text: $old");
        }
    }

    /* The cards come from the blog table and public/storage/blogs (not in Git): a new or replaced post banner needs WebP copies too */
    public function test_blog_cards_meet_the_image_and_alt_rules(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $cards = '//section[@id="hv2-blogs"]//a[contains(@class,"hv2-blog")]';
        $this->assertSame(3, $x->query($cards)->length, 'three guide cards');
        $this->assertSame(3, $x->query("$cards/h3")->length, 'card titles are H3');
        foreach ($x->query("$cards//img") as $img) {
            $alt = trim($img->getAttribute('alt'));
            $this->assertStringContainsString(' ', $alt, "descriptive alt text, not a slug or file name: $alt");
            $this->assertSame('lazy', $img->getAttribute('loading'), $alt);
            $this->assertNotSame('', $img->getAttribute('width') . $img->getAttribute('height'), "width and height: $alt");
            $urls = [$img->getAttribute('src')];
            foreach (array_filter(explode(',', $img->getAttribute('srcset'))) as $c) $urls[] = explode(' ', trim($c))[0];
            foreach ($urls as $u) {
                $file = public_path(preg_replace('#^.*?/public/|\?.*$#', '', $u));
                $this->assertFileExists($file);
                $limit = getimagesize($file)[0] >= 1440 ? 175 : 150;
                $this->assertLessThan($limit * 1024, filesize($file), 'make smaller WebP copies (-w480/-w960) of ' . basename($file));
            }
        }
    }

    public function test_structured_data_and_eight_faqs(): void
    {
        $html = $this->html(self::PATH);
        $types = array_count_values($this->jsonLdTypes($html));
        $this->assertSame(1, $types['FAQPage'] ?? 0, 'one FAQPage block');
        $this->assertSame(1, $types['Service'] ?? 0, 'one Service block');
        $this->assertSame(1, $types['BreadcrumbList'] ?? 0, 'one BreadcrumbList block');
        $this->assertStringContainsString('"Organization"', $html);
        $ld = collect($this->jsonLd($html));
        $areas = $ld->firstWhere('@type', 'Service')['areaServed'];
        $this->assertSame(['City', 'City', 'City'], array_column($areas, '@type'));
        $this->assertSame(['Dubai', 'Sharjah', 'Abu Dhabi'], array_column($areas, 'name'));
        $faq = $ld->firstWhere('@type', 'FAQPage');
        $this->assertCount(8, $faq['mainEntity'], 'the 8 FAQs of the brief');
        $this->assertSame('How much does flyer distribution cost in Dubai?', $faq['mainEntity'][0]['name']);
        $this->assertSame('Do you distribute flyers in Sharjah and Abu Dhabi?', $faq['mainEntity'][7]['name']);
        $this->assertSame(['Home', 'Outdoor Advertising', 'Flyers Distribution'], array_column($ld->firstWhere('@type', 'BreadcrumbList')['itemListElement'], 'name'));
    }

    public function test_meta_canonical_links_and_quote_form(): void
    {
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('<link rel="canonical" href="https://datamysite.com' . self::PATH . '"', $html);
        $this->assertStringContainsString('<title>Flyer Distribution in Dubai | Door to Door and Hand to Hand</title>', $html);
        $this->assertStringContainsString('Flyer and leaflet distribution in Dubai, Sharjah and Abu Dhabi. Door to door and hand to hand, with print, permit and GPS tracked delivery in one quote.', $html);
        $x = $this->xpath($html);
        foreach (['lamp post advertising' => '/outdoor-advertising/lamp-post', 'outdoor advertising' => '/outdoor-advertising',
                  'consumerrights.ae' => 'consumerrights.ae', 'View all case studies' => '/client-case-studies'] as $text => $path) {
            $a = $x->query('//main//a[normalize-space(.)="' . $text . '"]');
            $this->assertSame(1, $a->length, "link: $text");
            $this->assertStringEndsWith($path, $a->item(0)->getAttribute('href'), "target: $text");
        }
        $this->assertSame(1, $x->query('//main//button[@data-sv2-open="sv2-quote-modal" and normalize-space(.)="Get a Flyer Quote"]')->length, 'final CTA button opens the quote form');
        $this->assertSame(1, $x->query('//*[@id="sv2-quote-modal"]')->length);
        $this->assertSame(1, $x->query('//input[@name="service" and @value="Flyers Distribution" and ancestor::form[@data-sv2-lead]]')->length, 'the form names this page, not the parent service');
        $this->assertSame(count(config('case_studies')), $x->query('//section[@id="hv2-cases"]//a[contains(@class,"hv2-case")]')->length, 'all case studies until the DMS team picks six');
    }

    public function test_fast_page_uses_only_subset_icons(): void
    {
        $this->assertContains('flyers-distribution', config('v2.fast_pages'));
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('id="v2-icons"', $html);
        preg_match_all('/\bbi-[a-z0-9-]+/', $html, $m);
        preg_match_all('/\.(bi-[a-z0-9-]+)/', file_get_contents(public_path('assets/vendor/bootstrap-icons/bi-v2.css')), $mm);
        $missing = array_values(array_diff(array_unique($m[0]), array_unique($mm[1]), ['bi-v2', 'bi-subset', 'bi-v2-icons']));
        $this->assertSame([], $missing, 'every icon on this page must be in bi-v2.css');
    }
}
