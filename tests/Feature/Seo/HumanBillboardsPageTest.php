<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * /outdoor-advertising/human-billboards (service 18): rebuilt from the content brief in the hub design (DB rows).
 * Pins the brief's technical checklist so a later section edit cannot silently undo it. URL and canonical stay.
 */
class HumanBillboardsPageTest extends TestCase
{
    use CrawlsPages;

    private const PATH = '/outdoor-advertising/human-billboards';

    private function cells(\DOMXPath $x, string $query): array
    {
        return array_map(fn ($n) => trim($n->textContent), iterator_to_array($x->query($query)));
    }

    public function test_one_h1_and_the_briefs_headings(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $h1 = $x->query('//h1');
        $this->assertSame(1, $h1->length, 'exactly one H1');
        $this->assertSame('Human Billboard Advertising in Dubai', trim($h1->item(0)->textContent));
        $this->assertSame(0, $x->query('//main//h4|//main//h5')->length, 'no H4/H5 headings left');
        foreach (['Brands we work with', 'Walking, Backpack LED and Standing Billboards', 'Where Human Billboards Work in Dubai', 'Human Billboard Cost in Dubai',
                  'Permits, Supervision and Proof', 'Why Book Human Billboards With DMS', 'Case Studies', 'What our customers say', 'Related Guides',
                  'Human Billboard FAQs', 'Put Your Brand Where People Walk'] as $h2) {
            $this->assertSame(1, $x->query('//main//h2[normalize-space(.)="' . $h2 . '"]')->length, "H2: $h2");
        }
        $this->assertSame(['Backpack LED Billboards', 'Walking Boards', 'Standing Displays', 'Sign Spinners, Mascots and Balloon Carriers'],
            $this->cells($x, '//main//section[contains(@class,"sv2-formats-hb")]//h3'));
        $this->assertSame(4, $x->query('//main//section[contains(@class,"sv2-running")]//article')->length);
        $this->assertSame(4, $x->query('//main//section[contains(@class,"sv2-why")]//article')->length);
    }

    public function test_section_order_follows_the_brief(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $ids = array_map(fn ($n) => $n->getAttribute('id'), iterator_to_array($x->query('//main//div[@data-sv2-legacy]')));
        $this->assertSame(['sv2-brands-we-work-with', 'sv2-human-billboard-formats', 'sv2-where-human-billboards-work', 'sv2-human-billboard-cost-in-dubai',
            'sv2-human-billboard-permits-and-proof', 'sv2-why-dms-for-human-billboards', 'sv2-human-billboard-case-studies', 'sv2-what-our-clients-say',
            'sv2-human-billboard-guides', 'sv2-human-billboard-quote-cta'], $ids);
    }

    public function test_spot_and_cost_tables(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $spots = '//section[contains(@class,"sv2-spots")]//table';
        $this->assertSame(['Spot', 'When it works', 'Good for'], $this->cells($x, "$spots//thead//th"));
        $this->assertSame(['Mall entrances', 'Metro and bus exits', 'Events and exhibitions', 'JBR, Marina and Kite Beach walks', 'Outside a new shop'],
            $this->cells($x, "$spots//tbody//th"));
        $this->assertSame(['AED 300 per promoter per day', 'Quoted per campaign', 'Quoted per campaign', '4 to 8 promoters for one day'],
            $this->cells($x, '//section[contains(@class,"sv2-prices--hb-cost")]//tbody//td[2]'));
    }

    public function test_format_mockups_are_our_own_lazy_webp_with_alt_text(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $imgs = iterator_to_array($x->query('//main//section[contains(@class,"sv2-formats-hb")]//img'));
        $this->assertSame(['Backpack LED Billboards mockup, Dubai', 'Walking Boards mockup, Dubai', 'Standing Displays mockup, Dubai',
            'Sign Spinners, Mascots and Balloon Carriers mockup, Dubai'], array_map(fn ($i) => $i->getAttribute('alt'), $imgs));
        foreach ($imgs as $img) {
            $this->assertSame('lazy', $img->getAttribute('loading'));
            $this->assertSame(['720', '900'], [$img->getAttribute('width'), $img->getAttribute('height')]);
            foreach (explode(',', $img->getAttribute('srcset')) as $c) {
                $file = public_path(preg_replace('#^.*?/public/|\?.*$#', '', explode(' ', trim($c))[0]));
                $this->assertStringContainsString('assets' . '/img/human-billboards/', str_replace('\\', '/', $file));
                $this->assertStringEndsWith('.webp', $file);
                $this->assertFileExists($file);
                $this->assertLessThan(100 * 1024, filesize($file));
            }
        }
    }

    /* the two blog banners come from public/storage/blogs (not in Git): their WebP copies must be on every server */
    public function test_three_related_guides_replace_the_latest_posts_block(): void
    {
        $x = $this->xpath($this->html(self::PATH));
        $cards = '//section[@id="hv2-blogs"]//a[contains(@class,"hv2-blog")]';
        $hrefs = array_map(fn ($n) => preg_replace('#^.*?(?=/(human-billboards-a|flyer-distribution-cost|experiential-marketing))#', '', $n->getAttribute('href')), iterator_to_array($x->query($cards)));
        $this->assertSame(['/human-billboards-a-unique-advertising-strategy-in-dubai-uae', '/flyer-distribution-cost-in-dubai-2026-rates-and-response-guide',
            '/experiential-marketing/mall-activations-dubai'], $hrefs);
        $this->assertSame(['Human Billboards in Dubai', 'Flyer Distribution Cost in Dubai', 'Mall Activations in Dubai'], $this->cells($x, "$cards/h3"));
        foreach ($x->query("$cards//img") as $img) {
            $alt = trim($img->getAttribute('alt'));
            $this->assertStringContainsString(' ', $alt);
            $this->assertSame('lazy', $img->getAttribute('loading'), $alt);
            $this->assertStringContainsString('.webp', $img->getAttribute('src'), $alt);
            $this->assertNotSame('', $img->getAttribute('width'), $alt);
        }
    }

    public function test_old_copy_and_brief_notes_are_gone(): void
    {
        $main = $this->xpath($this->html(self::PATH))->query('//main')->item(0)->textContent;
        foreach (['Maximise Brand Impact', 'What is Human Billboard Advertising', 'Our Human Billboard Advertising Process', 'Why Choose DataMySite',
                  'Lamp Post Advertising Cost', 'Cinema Advertising', 'Unipole Advertising Cost', '[Keep', '[Button', '[Show', '[Accordion', '[Link', '[DMS', '[Four'] as $old) {
            $this->assertStringNotContainsStringIgnoringCase($old, $main, "old text: $old");
        }
    }

    public function test_structured_data_and_six_faqs(): void
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
        $this->assertCount(6, $faq['mainEntity'], 'the 6 FAQs of the brief');
        $this->assertSame('How much does a human billboard cost in Dubai?', $faq['mainEntity'][0]['name']);
        $this->assertSame('Do you run human billboards outside Dubai?', $faq['mainEntity'][5]['name']);
        $this->assertSame(['Home', 'Outdoor Advertising', 'Human Billboards'], array_column($ld->firstWhere('@type', 'BreadcrumbList')['itemListElement'], 'name'));
    }

    public function test_meta_canonical_links_and_quote_form(): void
    {
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('<link rel="canonical" href="https://datamysite.com' . self::PATH . '"', $html);
        $this->assertStringContainsString('<title>Human Billboard Advertising Dubai | Walking LED Billboards</title>', $html);
        $this->assertStringContainsString('Human billboard advertising in Dubai and across the UAE: walking and backpack LED billboards, sign spinners and mascots, with approx. cost per promoter day.', $html);
        $x = $this->xpath($html);
        foreach (['flyer distribution' => '/outdoor-advertising/flyers-distribution', 'View all case studies' => '/client-case-studies'] as $text => $path) {
            $a = $x->query('//main//a[normalize-space(.)="' . $text . '"]');
            $this->assertSame(1, $a->length, "link: $text");
            $this->assertStringEndsWith($path, $a->item(0)->getAttribute('href'), "target: $text");
        }
        /* the brief's other internal links: hub (breadcrumb); mall activations and the blog are guide cards (tested above) */
        $this->assertGreaterThan(0, $x->query('//main//a[normalize-space(.)="Outdoor Advertising" and substring(@href, string-length(@href) - 19) = "/outdoor-advertising"]')->length, 'link to the outdoor advertising hub');
        $this->assertSame('Handing out flyers as well? See our flyer distribution service.',
            trim($x->query('//section[@id="hv2-cta"]//p[contains(@class,"sv2-cta-more")]')->item(0)->textContent), 'the brief\'s wording, nothing added');
        $this->assertSame(1, $x->query('//main//button[@data-sv2-open="sv2-quote-modal" and normalize-space(.)="Plan My Campaign"]')->length, 'final CTA button opens the quote form');
        $this->assertSame(1, $x->query('//*[@id="sv2-quote-modal"]')->length);
        $this->assertSame(1, $x->query('//input[@name="service" and @value="Human Billboards" and ancestor::form[@data-sv2-lead]]')->length, 'the form names this page, not the parent service');
        $this->assertSame(count(config('case_studies')), $x->query('//section[@id="hv2-cases"]//a[contains(@class,"hv2-case")]')->length, 'all case studies until the DMS team picks six');
    }

    public function test_fast_page_uses_only_subset_icons(): void
    {
        $this->assertContains('human-billboards', config('v2.fast_pages'));
        $html = $this->html(self::PATH);
        $this->assertStringContainsString('id="v2-icons"', $html);
        preg_match_all('/\bbi-[a-z0-9-]+/', $html, $m);
        preg_match_all('/\.(bi-[a-z0-9-]+)/', file_get_contents(public_path('assets/vendor/bootstrap-icons/bi-v2.css')), $mm);
        $missing = array_values(array_diff(array_unique($m[0]), array_unique($mm[1]), ['bi-v2', 'bi-subset', 'bi-v2-icons']));
        $this->assertSame([], $missing, 'every icon on this page must be in bi-v2.css');
    }
}
