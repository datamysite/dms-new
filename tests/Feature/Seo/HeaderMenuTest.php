<?php

namespace Tests\Feature\Seo;

use App\Models\Services;
use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * Desktop header: "Services" is a plain link to /services; a fixed second row lists 10 short service names with their
 * sub-services in hover dropdowns; the other 6 services live in the footer.
 */
class HeaderMenuTest extends TestCase
{
    use CrawlsPages;

    private const BAR = ['digital-marketing', 'outdoor-advertising', 'digital-advertising', 'influencer-marketing', 'seo-services', 'transit-media', 'celebrity-marketing', 'public-relations-pr-coverage', 'lead-generation', 'radio-advertising'];
    private const FOOT = ['website-development', 'event-marketing', 'sponsorships', 'creative-and-cgi-advertising', 'experiential-marketing', 'global-marketing'];

    private function bar(string $html): string
    {
        $this->assertSame(1, preg_match('#<div class="hs-subbar".*?<div class="hs-scrim"#s', $html, $m), 'service bar present');
        return $m[0];
    }

    public function test_services_is_a_plain_link_and_the_mega_panel_is_gone(): void
    {
        foreach (['/', '/services', '/seo-services'] as $path) {
            $html = $this->html($path);
            $this->assertStringNotContainsString('hs-mega', $html, $path);
            $this->assertStringNotContainsString('data-hs-mega', $html, $path);
            $this->assertMatchesRegularExpression('#<li><a href="[^"]*/services" class="hs-link[^"]*">Services</a></li>#', $html, $path);
        }
    }

    public function test_the_service_bar_lists_the_ten_short_names_in_order_with_their_sub_services(): void
    {
        $html = $this->html('/services');
        $bar = $this->bar($html);
        preg_match_all('#class="hs-svc-link"[^>]*>\s*([^<]+?)\s*(?:<i|</a>)#', $bar, $names);
        $this->assertSame(['Digital', 'Outdoor', 'DOOH', 'Influencer', 'SEO', 'Transit', 'Celebrity', 'PR', 'Leads', 'Radio'], $names[1]);

        foreach (self::BAR as $slug) {
            $svc = Services::where('slug', $slug)->where('parent_id', 0)->with('subServices')->firstOrFail();
            $this->assertMatchesRegularExpression('#href="[^"]*/' . preg_quote($slug, '#') . '" class="hs-svc-link"#', $bar, "$slug main link");
            foreach ($svc->subServices as $sub) {
                $this->assertMatchesRegularExpression('#href="[^"]*/' . preg_quote($slug . '/' . $sub->slug, '#') . '"#', $bar, "$slug/{$sub->slug} in its dropdown");
            }
            if (count($svc->subServices)) {
                $this->assertMatchesRegularExpression('#<a href="[^"]*/' . preg_quote($slug, '#') . '" class="hs-svc-head"#', $bar, "$slug dropdown links to its main page");
            }
        }
        $this->assertStringContainsString('hs-svc--end', $bar, 'the right-most dropdowns open towards the left');
    }

    public function test_header_bar_and_footer_together_reach_every_top_level_service(): void
    {
        $html = $this->html('/services');
        $footer = substr($html, strpos($html, 'hs-foot-services'));
        $footer = substr($footer, 0, strpos($footer, '</nav>'));
        foreach (self::FOOT as $slug) {
            $this->assertStringContainsString('/' . $slug . '"', $footer, "$slug is in the footer");
        }
        $all = Services::where('parent_id', 0)->pluck('slug')->all();
        $this->assertEqualsCanonicalizing($all, array_merge(self::BAR, self::FOOT), 'every top-level service is in the header bar or the footer');
    }

    public function test_the_mobile_drawer_still_lists_all_services(): void
    {
        $html = $this->html('/');
        $drawer = substr($html, strpos($html, 'class="hs-drawer"'));
        foreach (array_merge(self::BAR, self::FOOT) as $slug) {
            $this->assertStringContainsString('/' . $slug . '"', $drawer, "$slug in the drawer");
        }
    }

    public function test_css_and_js_follow_the_performance_rules(): void
    {
        $css = file_get_contents(public_path('assets/css/home-v2-shell.css'));
        $this->assertStringContainsString('.hs-subbar { display: none; }', $css, 'service bar is desktop only');
        $this->assertStringContainsString('@media (min-width: 1200px)', $css);
        $this->assertStringNotContainsString('.hs-mega', $css);
        $svc = substr($css, strpos($css, '/* ---- service bar'), 6000);
        $svc = substr($svc, 0, strpos($svc, '/* ---- burger'));
        $this->assertDoesNotMatchRegularExpression('#transition:[^;]*\b(?:left|top|height|width|padding)\b#', $svc, 'only transform/opacity/colour transitions');
        $this->assertStringNotContainsString('backdrop-filter', $svc);

        $home = file_get_contents(public_path('assets/css/home-v2.css'));
        $this->assertMatchesRegularExpression('#@media \(min-width: 1200px\) \{ :root \{ --hv2-header: 120px; \} \}#', $home, 'CSS default equals the real two-row header height (no layout shift)');

        $js = file_get_contents(public_path('assets/js/home-v2-shell.js'));
        $svcJs = substr($js, strpos($js, 'service bar dropdowns'), 2600);
        $this->assertDoesNotMatchRegularExpression('#getBoundingClientRect|offsetHeight|offsetWidth|scrollY|innerHeight#', $svcJs, 'no layout reads');
    }

    public function test_whatsapp_button_animation_uses_only_compositor_properties_and_respects_reduced_motion(): void
    {
        $css = file_get_contents(public_path('assets/css/home-v2.css'));
        $blk = substr($css, strpos($css, '/* == WhatsApp button: pop-in'));
        foreach (['wa-pop', 'wa-ripple', 'wa-ring'] as $name) {
            $this->assertSame(1, preg_match('#@keyframes ' . $name . ' \{(.*?)\}\s*(?=@keyframes|@media|$)#s', $blk, $m), $name);
            $this->assertDoesNotMatchRegularExpression('#\b(left|right|top|bottom|width|height|margin|padding|box-shadow|filter)\s*:#', $m[1], "$name animates only transform/opacity");
        }
        $this->assertMatchesRegularExpression('#prefers-reduced-motion: reduce\) \{[^}]*whatsapp-icon-position img \{ animation: none; \}#s', $blk, 'reduced motion switches it off');
        $this->assertStringContainsString('.hs-body .whatsapp-chat { animation: wa-pop', $blk);
    }
}
