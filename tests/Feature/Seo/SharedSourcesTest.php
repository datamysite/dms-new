<?php

namespace Tests\Feature\Seo;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\Support\CrawlsPages;
use Tests\TestCase;

/** The V2 pages read business details, brands, popups and reviews from one place each (config/business.php, config/clients.php, partials). */
class SharedSourcesTest extends TestCase
{
    use CrawlsPages;

    public function test_contact_and_about_show_the_same_four_cards_from_config(): void
    {
        $b = config('business');
        foreach (['/contact-us' => 'ctv-card', '/about-us' => 'abv-cc'] as $path => $class) {
            $html = $this->html($path);
            $this->assertSame(4, substr_count($html, 'class="' . $class . '"'), "$path has four cards");
            $this->assertStringContainsString($b['address_line'], $html, $path);
            $this->assertStringContainsString('href="tel:' . $b['phone'] . '"', $html, $path);
            $this->assertStringContainsString('href="tel:' . $b['telephone'] . '"', $html, $path);
            $this->assertStringContainsString($b['telephone_display'], $html, $path);
            $this->assertStringContainsString($b['hours_text'][0], html_entity_decode($html), $path);
            $this->assertStringContainsString('data-copy="' . $b['email'] . '"', $html, $path);
        }
    }

    public function test_footer_social_links_come_from_config_and_match_the_schema(): void
    {
        $html = $this->html('/about-us', self::STAGING);
        foreach (config('business.social') as $s) {
            $this->assertStringContainsString('<a href="' . $s['url'] . '" aria-label="' . $s['label'] . '"', $html, $s['label']);
        }
        $org = collect($this->jsonLd($html))->firstWhere('@type', 'Organization');
        $this->assertSame(array_column(config('business.social'), 'url'), $org['sameAs'], 'footer and structured data use the same profiles');
    }

    public function test_phone_whatsapp_and_email_are_the_configured_ones_on_every_v2_page(): void
    {
        $b = config('business');
        foreach (['/', '/about-us', '/contact-us', '/faq', '/privacy-policy', '/seo-services'] as $path) {
            $html = $this->html($path);
            $this->assertStringContainsString('href="tel:' . $b['phone'] . '"', $html, "$path header/footer phone");
            $this->assertStringContainsString('href="' . v2_wa_url() . '"', $html, "$path WhatsApp link");
            $this->assertStringContainsString('mailto:' . $b['email'], $html, "$path email");
            // the number is never typed into the page a second way (service pages are skipped: their stored database text and snippets
            // may still carry the old local-format links)
            if (!in_array($path, self::SERVICE_PAGES, true)) {
                $this->assertStringNotContainsString('tel:0525287259', $html, $path);
                $this->assertStringNotContainsString('tel:042957001', $html, $path);
            }
        }
    }

    public function test_whatsapp_helper_builds_the_link_and_encodes_a_message(): void
    {
        $this->assertSame('https://api.whatsapp.com/send/?phone=' . config('business.whatsapp'), v2_wa_url());
        $this->assertSame('https://api.whatsapp.com/send/?phone=' . config('business.whatsapp') . '&text=Hi%20there%20%26%20hello', v2_wa_url('Hi there & hello'));
    }

    public function test_enquiry_popups_come_from_one_partial_and_appear_once_per_page(): void
    {
        foreach (['/', '/about-us', '/contact-us', '/seo-services'] as $path) {
            $html = $this->html($path);
            foreach (['newsletter-modal-u', 'seo-cta-modal-u', 'seo-ads-cta-modal-u', 'ov'] as $id) {
                $this->assertSame(1, substr_count($html, 'id="' . $id . '"'), "$path popup #$id");
            }
            $this->assertStringContainsString('id="enquiry-form-exit"', $html);
        }
        $this->assertFileExists(resource_path('views/web/home-v2/popups.blade.php'));
        $this->assertStringNotContainsString('id="newsletter-modal-u"', file_get_contents(resource_path('views/web/home-v2/master.blade.php')), 'the master no longer holds the popup markup');
    }

    public function test_every_page_footer_has_read_reviews_without_a_home_page_visit(): void
    {
        Storage::fake('local');
        $google = ['status' => 'OK', 'result' => ['rating' => 5, 'user_ratings_total' => 45,
            'reviews' => [['author_name' => 'Test Reviewer', 'rating' => 5, 'text' => 'Great team.', 'relative_time_description' => 'a month ago']]]];
        $up = true;
        Http::fake(['maps.googleapis.com/*' => function () use (&$up, $google) {
            return $up ? Http::response($google) : Http::response(['status' => 'OVER_QUERY_LIMIT']);
        }]);

        $button = fn (string $path) => $this->xpath($this->html($path))->query('//footer//button[contains(@class,"hs-rating--btn")]')->length;
        Cache::flush();
        $this->assertSame(1, $button('/about-us'), 'empty cache, home page never opened');

        Cache::flush();
        $up = false;
        $this->assertSame(1, $button('/contact-us'), 'cache cleared and Google failing: the last good copy is used');

        Cache::flush();
        Storage::fake('local');
        $x = $this->xpath($this->html('/faq'));
        $this->assertSame(1, $x->query('//footer//a[contains(@class,"hs-rating")]//span[contains(@class,"hs-rating-go")]')->length,
            'no review texts at all: a link to Google with the same label');
    }

    public function test_reviews_link_and_fallback_numbers_come_from_config(): void
    {
        $this->assertStringContainsString('0xad7a49fd99d3061', config('business.reviews.url'));
        $this->assertSame(46, config('business.reviews.fallback_total'));
        $html = $this->html('/');
        $this->assertStringContainsString(e(config('business.reviews.url')), $html);
        $this->assertStringNotContainsString('hv2Hubs', file_get_contents(resource_path('views/web/home-v2.blade.php')), 'dead array is gone');
    }

    public function test_the_address_wording_is_the_same_everywhere(): void
    {
        foreach (['/', '/about-us', '/contact-us'] as $path) {
            $this->assertStringContainsString(config('business.address_line'), $this->html($path), $path);
        }
        $this->assertStringNotContainsString('Dubai Investment Park, Dubai', $this->html('/'), 'the home page no longer words the address differently');
    }
}
