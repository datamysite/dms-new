<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/** Audit items: alt text on client logos / all images, and a clean heading structure. */
class AltTextAndHeadingsTest extends TestCase
{
    use CrawlsPages;

    public function test_no_image_is_missing_its_alt_attribute(): void
    {
        foreach (array_merge(self::TEMPLATE_PAGES, self::SERVICE_PAGES) as $path) {
            foreach ($this->images($this->html($path)) as $img) {
                $this->assertMatchesRegularExpression('/\salt=/i', $img, "$path: image without alt: " . substr($img, 0, 120));
            }
        }
    }

    public function test_client_logos_have_a_descriptive_alt_text_in_both_marquee_passes(): void
    {
        $brands = config('clients');
        $this->assertGreaterThanOrEqual(44, count($brands));
        foreach (['/', '/about-us', '/services', '/seo-services'] as $path) {
            $html = $this->html($path);
            preg_match_all('#<img src="[^"]*clients-final/(\d+)\.(webp|png)" alt="([^"]*)"#', $html, $m, PREG_SET_ORDER);
            $this->assertCount(count($brands) * 2, $m, "$path shows every logo twice (endless loop)");
            foreach ($m as [$all, $no, $ext, $alt]) {
                $this->assertStringContainsString('DataMySite client', $alt, "$path logo $no");
                $this->assertStringContainsString(html_entity_decode($brands[(int) $no], ENT_QUOTES), html_entity_decode($alt, ENT_QUOTES), "$path logo $no");
            }
        }
    }

    public function test_first_slider_shows_the_priority_brands_in_the_given_order_and_the_rest_are_in_the_second(): void
    {
        $expected = ['Nike', 'e&', 'Dubai Duty Free', 'Dubai Holding', 'SKIN111', 'JD Sports', 'Aswaaq', 'Géant', "Haldiram's", 'Lenskart', 'Jetour', 'GMG', 'Elite Group Holding', 'BNW Developments', 'Tranquil Developers', 'AYS Developers'];
        $this->assertSame($expected, array_map(fn ($no) => config('clients')[$no], config('clients_featured')), 'config lists the 16 priority brands in order');

        foreach (['/', '/about-us', '/services', '/seo-services'] as $path) {
            $html = $this->html($path);
            // the two sliders are the two .hv2-marquee blocks; only the first pass of each row (tiles without aria-hidden) is read
            $xp = $this->xpath($html);
            $sliders = $xp->query('//div[contains(concat(" ", normalize-space(@class), " "), " hv2-marquee ")]');
            $this->assertSame(2, $sliders->length, "$path has two sliders");
            $rowAlts = [];
            foreach ($sliders as $slider) {
                $alts = [];
                foreach ($xp->query('.//div[contains(@class, "hv2-brand--lg") and not(@aria-hidden)]//img/@alt', $slider) as $a) {
                    $alts[] = str_replace(' logo – DataMySite client', '', $a->nodeValue);
                }
                $rowAlts[] = $alts;
            }
            $this->assertSame($expected, $rowAlts[0], "$path: first slider = priority brands, in order");
            $this->assertCount(count(config('clients')) - 16, $rowAlts[1], "$path: second slider = all the other brands");
            $this->assertSame([], array_intersect($rowAlts[0], $rowAlts[1]), "$path: no brand in both sliders");
            $this->assertSame(1, $xp->query('//div[contains(@class, "hv2-marquee--reverse")]')->length, "$path: only the second slider moves the other way");
        }
    }

    public function test_slider_speed_follows_the_number_of_logos_in_each_row(): void
    {
        $html = $this->html('/');
        preg_match_all('#class="hv2-marquee-track" style="animation-duration: (\d+)s"#', $html, $m);
        $this->assertCount(2, $m[1]);
        $this->assertSame((int) round(16 * 3.6), (int) $m[1][0], 'first row: 16 logos');
        $this->assertSame((int) round((count(config('clients')) - 16) * 4.0), (int) $m[1][1], 'second row: the rest');
    }

    public function test_logos_without_a_webp_file_fall_back_to_png(): void
    {
        foreach (array_keys(config('clients')) as $no) {
            $expected = is_file(public_path("assets/img/clients-final/$no.webp")) ? 'webp' : 'png';
            $this->assertFileExists(public_path("assets/img/clients-final/$no.$expected"), "logo $no has an image file");
            $this->assertStringContainsString("clients-final/$no.$expected", v2_clients()[$no]['src']);
        }
    }

    public function test_template_pages_have_one_h1_and_no_skipped_heading_levels(): void
    {
        foreach (array_merge(self::TEMPLATE_PAGES, ['/blogs']) as $path) {
            $levels = $this->headingLevels($this->html($path));
            $this->assertSame(1, count(array_keys($levels, 1, true)), "$path has exactly one h1");
            $prev = 0;
            foreach ($levels as $l) {
                $this->assertLessThanOrEqual($prev + 1, $l, "$path skips from h$prev to h$l");
                $prev = $l;
            }
        }
    }

    public function test_blog_detail_has_a_clean_outline_too(): void
    {
        $slug = \App\Models\Blogs::where('status', '1')->orderBy('created_at', 'desc')->value('slug');
        $levels = $this->headingLevels($this->html('/' . $slug));
        $this->assertSame(1, count(array_keys($levels, 1, true)));
        $prev = 0;
        foreach ($levels as $l) { $this->assertLessThanOrEqual($prev + 1, $l, "blog skips h$prev -> h$l"); $prev = $l; }
    }

    public function test_service_pages_have_one_h1(): void
    {
        // their section text comes from the database and may still skip levels (listed as pending), but the page must have one h1
        foreach (self::SERVICE_PAGES as $path) {
            $this->assertSame(1, count(array_keys($this->headingLevels($this->html($path)), 1, true)), $path);
        }
    }

    public function test_hero_form_title_footer_and_popup_titles_are_not_headings(): void
    {
        $html = $this->html('/about-us');
        $this->assertDoesNotMatchRegularExpression('#<h[1-6][^>]*class="[^"]*hv2-form-title#', $this->html('/'));
        $this->assertStringContainsString('class="hs-foot-h"', $html);
        $this->assertDoesNotMatchRegularExpression('#<h[1-6][^>]*>\s*(Services|Company|Stay in the loop)\s*</h[1-6]>#', $html);
        $this->assertDoesNotMatchRegularExpression('#<h[1-6][^>]*>\s*Need a Help\?\s*</h[1-6]>#', $html);
    }
}
