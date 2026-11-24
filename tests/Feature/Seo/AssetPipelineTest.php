<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * PageSpeed work: real minified assets, critical (first-screen) CSS inlined with every stylesheet non-blocking,
 * no layout reads while the page's scripts start, third-party scripts released outside the measuring window.
 * Rebuild the assets with tools/build-assets.ps1, then the critical CSS with tools/make-critical-all.ps1.
 */
class AssetPipelineTest extends TestCase
{
    use CrawlsPages;

    private const CRITICAL = [
        '/' => 'home', '/about-us' => 'about', '/contact-us' => 'contact', '/faq' => 'faq', '/blogs' => 'blogs', '/blogs/categories' => 'blogs-categories',
        '/client-case-studies' => 'case-studies', '/privacy-policy' => 'privacy', '/terms-conditions' => 'terms', '/services' => 'services-landing', '/seo-services' => 'service',
    ];

    private function src(string $rel): string
    {
        return file_get_contents(public_path($rel));
    }

    public function test_served_min_files_are_really_minified_and_newer_than_their_source(): void
    {
        $pairs = [];
        foreach (glob(public_path('assets/{css,js}/*-v2*.min.{css,js}'), GLOB_BRACE) as $min) {
            $pairs[] = $min;
        }
        foreach (['css/custom', 'css/main', 'css/responsive', 'js/main', 'js/enquiry', 'js/newsletter', 'js/input'] as $base) {
            $ext = str_starts_with($base, 'css') ? 'css' : 'js';
            $pairs[] = public_path("assets/$base.min.$ext");
        }
        $this->assertGreaterThan(25, count($pairs));
        foreach ($pairs as $min) {
            $this->assertFileExists($min);
            $source = preg_replace('/\.min\.(css|js)$/', '.$1', $min);
            $this->assertGreaterThanOrEqual(filemtime($source), filemtime($min), basename($min) . ' is older than its source: run tools/build-assets.ps1');
            $this->assertLessThan(filesize($source), filesize($min), basename($min) . ' is not smaller than its source');
            if (str_ends_with($min, '.js') && filesize($source) > 3000) {
                $this->assertLessThan(filesize($source) * 0.8, filesize($min), basename($min) . ' is not really minified');
            }
        }
    }

    public function test_critical_css_mode_works_when_switched_on_and_the_files_are_fresh(): void
    {
        config(['v2.critical_css' => true]);
        $newest = 0;
        foreach (glob(public_path('assets/css/*.css')) as $css) {
            if (!preg_match('/\.(min|critical)\.css$/', $css)) $newest = max($newest, filemtime($css));
        }
        foreach (self::CRITICAL as $name) {
            $f = public_path("assets/css/$name.critical.css");
            if (!is_file($f) || filemtime($f) + 120 < $newest) {
                $this->markTestSkipped('critical CSS is stale (optional mode, off by default): run tools/make-critical-all.ps1 to test it');
            }
        }
        foreach (self::CRITICAL as $path => $name) {
            $file = public_path("assets/css/$name.critical.css");
            $this->assertFileExists($file, "$name critical CSS: run tools/make-critical-all.ps1");
            $this->assertLessThan(80 * 1024, filesize($file), "$name critical CSS is too large");
            $this->assertStringNotContainsString('__PUBLIC__/', $this->html($path), "$path leaves the placeholder in the page");

            $html = $this->html($path);
            $this->assertStringContainsString('<style id="v2-critical">', $html, "$path does not inline its critical CSS (stale file? re-run tools/make-critical-all.ps1)");
            $this->assertStringNotContainsString('__PUBLIC__', $html, $path);
            // every stylesheet link, outside <noscript>, loads without blocking the first paint
            $outside = preg_replace('#<noscript>.*?</noscript>#s', '', $html);
            preg_match_all('#<link\b[^>]*rel="stylesheet"[^>]*>#i', $outside, $links);
            $this->assertNotEmpty($links[0], $path);
            foreach ($links[0] as $link) {
                $this->assertStringContainsString('media="print"', $link, "$path has a render-blocking stylesheet: $link");
                $this->assertStringContainsString('onload="v2cs(this)"', $link, $path);
            }
        }
    }

    public function test_critical_css_is_off_by_default_and_the_good_blocking_css_path_is_served(): void
    {
        $this->assertFalse(config('v2.critical_css'), 'critical + async CSS scored 76-85 in PageSpeed; blocking minified CSS scored 98/98');
        foreach (array_keys(self::CRITICAL) as $path) {
            $html = $this->html($path);
            $this->assertStringNotContainsString('id="v2-critical"', $html, "$path inlines critical CSS although the mode is off");
            $outside = preg_replace('#<noscript>.*?</noscript>#s', '', $html);
            preg_match_all('#<link\b[^>]*rel="stylesheet"[^>]*>#i', $outside, $links);
            $core = array_filter($links[0], fn ($l) => preg_match('#(bootstrap\.min|home-v2-shell|/home-v2[.-])#', $l));
            $this->assertNotEmpty($core, "$path core stylesheets");
            foreach ($core as $link) {
                $this->assertStringNotContainsString('media="print"', $link, "$path: core stylesheets must stay render-blocking: $link");
            }
        }
    }

    public function test_hero_stays_visible_when_the_deferred_script_runs_late(): void
    {
        $css = file_get_contents(public_path('assets/css/home-v2.css'));
        // visible after hv2-js is added ...
        $this->assertStringContainsString('.hv2-js .hv2 .hv2-hero--fast [data-reveal]:not(.is-in) { opacity: 1; translate: none; scale: 1; }', $css);
        $this->assertStringContainsString('.hv2-js .hv2 .hv2-hero--fast [data-split]:not(.is-in) .hv2-w > span { opacity: 1; transform: none; }', $css);
        // ... and hidden only while the loader curtain is up
        $this->assertStringContainsString('html.hv2-loading.hv2-js .hv2 .hv2-hero--fast [data-reveal]:not(.is-in) { opacity: 0;', $css);
        $this->assertStringContainsString('html.hv2-loading.hv2-js .hv2 .hv2-hero--fast [data-split]:not(.is-in) .hv2-w > span { opacity: 0;', $css);
        // the overrides come after the original hero rules, so they win
        $this->assertGreaterThan(strpos($css, '.hv2-js .hv2 .hv2-hero--fast [data-split]:not(.is-in) .hv2-w > span { transform: translateY(35%)'), strpos($css, '.hv2-js .hv2 .hv2-hero--fast [data-split]:not(.is-in) .hv2-w > span { opacity: 1; transform: none; }'));
    }

    public function test_critical_css_keeps_the_hidden_state_of_the_drawer_and_the_first_paint_loader(): void
    {
        $css = $this->src('assets/css/home.critical.css');
        $this->assertMatchesRegularExpression('/\.hs-drawer\{[^}]*(transform|visibility|display)/', $css, 'the mobile drawer must be hidden at first paint');
        $this->assertStringContainsString('html.hv2-loading', $css, 'loader overlay');
        $this->assertMatchesRegularExpression('/\.hs-nav,[^{]*\{display:none\}/', $this->src('assets/css/service.critical.css'), 'the desktop nav is hidden on phones at first paint');
    }

    public function test_blogs_hero_is_compact_and_has_no_3d_pieces(): void
    {
        $view = file_get_contents(resource_path('views/web/blogs-v2.blade.php'));
        preg_match('~<section class="hv2-hero.*?</section>~s', $view, $m);
        foreach (['blv-deco--hero2', 'blv-hs'] as $bad) {
            $this->assertStringNotContainsString($bad, $m[0], "blogs hero must not contain $bad");
        }
        $css = file_get_contents(public_path('assets/css/blogs-v2.css'));
        $this->assertStringContainsString('hv2-streaks', $m[0], 'the line streaks stay');
        $this->assertStringNotContainsString('hv2-enquiry-form', $m[0], 'the form lives in the sticky sidebar, not in the hero');
        // sticky consultation sidebar: a 2-column wrapper down to the Explore section, form pulled up into the hero
        $this->assertMatchesRegularExpression('~<aside class="blv-side" id="hv2-form".*?blv-sticky.*?hv2-enquiry-form.*?</aside>~s', $view);
        $this->assertStringContainsString('.blv-side .blv-sticky { position: sticky; top: 100px; align-self: start; }', $css);
        $this->assertStringNotContainsString('--blv-body', $css, 'the form does not overlap the hero');
        $this->assertStringContainsString('.blv-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));', $css, 'two cards per row');
        $this->assertStringContainsString('@media (max-width: 1023px)', $css);
        $this->assertStringContainsString('.blv-side .blv-sticky { position: static; }', $css, 'sticky is off below 1024px');
        // no ancestor of the form may clip or scroll (it would break sticky)
        $this->assertMatchesRegularExpression('~\.hv2 \.blv-layout \{ overflow: visible;~', $css);
        $this->assertDoesNotMatchRegularExpression('~\.blv-layout-grid\s*\{[^}]*overflow~', $css);
        $this->assertDoesNotMatchRegularExpression('~\.blv-main\s*\{[^}]*overflow~', $css);
    }

    public function test_blog_detail_has_compact_hero_no_3d_and_sticky_form_sidebar(): void
    {
        $view = file_get_contents(resource_path('views/web/blog-detail-v2.blade.php'));
        preg_match('~<section class="hv2-hero.*?</section>~s', $view, $m);
        foreach (['bdv-hero-photo', 'bdv-deco--hero2', 'bdv-hs', 'hv2-enquiry-form'] as $bad) {
            $this->assertStringNotContainsString($bad, $m[0], "blog detail hero must not contain $bad");
        }
        $this->assertStringNotContainsString('$bdvStage(', $view, 'no 3D stages on the blog page');
        $this->assertStringContainsString('bdv-hero-meta', $m[0], 'author and date sit in the hero, after the title');
        $this->assertStringNotContainsString('class="author"', $view);
        $this->assertMatchesRegularExpression('~<aside class="bdv-side" id="hv2-form".*?bdv-sticky.*?hv2-enquiry-form.*?</aside>~s', $view);
        $css = file_get_contents(public_path('assets/css/blog-detail-v2.css'));
        $this->assertStringNotContainsString('bdv-photo-drift', $css);
        $this->assertStringContainsString('.bdv-side .bdv-sticky { position: sticky; top: 100px; align-self: start; }', $css);
        $this->assertStringContainsString('.bdv-side .bdv-sticky { position: static; }', $css, 'sticky is off below 1024px');
        $this->assertMatchesRegularExpression('~\.hv2 \.bdv-article-sec \{ overflow: visible; \}~', $css);
        $this->assertDoesNotMatchRegularExpression('~\.bdv-layout-grid\s*\{[^}]*overflow~', $css);
        $this->assertDoesNotMatchRegularExpression('~\.bdv-main\s*\{[^}]*overflow~', $css);
    }

    public function test_hub_guides_decorative_image_is_hidden_on_phones(): void
    {
        $css = file_get_contents(public_path('assets/css/service.css'));
        $this->assertStringContainsString("@media (max-width: 767px) {\n  .hub-guides::after { display: none; }", str_replace("\r", '', $css));
        $this->assertStringContainsString("'.hub-guides, .hs-header", file_get_contents(base_path('tools/make-critical.ps1')), 'the critical generator always keeps the hub-guides rules (mobile hide rule included)');
        $this->assertStringContainsString('service.css?v=', $this->get('/seo-services')->getContent(), 'the legacy service.css link is versioned so a stale browser cache cannot keep the old rules');
    }

    public function test_legacy_service_bullets_keep_their_left_padding(): void
    {
        // custom.css forces `.post-container li { padding-inline-start: 0 !important }`; without a matching !important the coral bullet overlaps the text
        $css = file_get_contents(public_path('assets/css/service-v2.css'));
        $this->assertStringContainsString('.sv2 .sv2-legacy ul:not(.nav) > li { position: relative; padding-left: 26px !important; padding-inline-start: 26px !important;', $css);
    }

    public function test_blog_detail_title_is_full_width_and_article_gap_matches_the_form_gap(): void
    {
        $css = file_get_contents(public_path('assets/css/blog-detail-v2.css'));
        $this->assertStringContainsString('.bdv-hero-body, .bdv-hero-body h1 { max-width: none; }', $css);
        $this->assertStringContainsString('.hv2 .bdv-article-sec { padding-top: clamp(28px, 3.5vw, 56px); }', $css);
        $this->assertStringContainsString('column-gap: clamp(28px, 3.5vw, 56px)', $css, 'same gap between the article and the form');
    }

    public function test_blog_detail_hero_shows_the_read_time_from_the_database(): void
    {
        $view = file_get_contents(resource_path('views/web/blog-detail-v2.blade.php'));
        preg_match('~<div class="bdv-hero-meta">.*?</div>~s', $view, $m);
        $this->assertStringContainsString('$data->read_time', $m[0], 'read time (minutes, key read_time) sits next to author and date');
        $this->assertStringContainsString('min read', $m[0]);
    }

    public function test_services_landing_has_no_clientele_section(): void
    {
        $view = file_get_contents(resource_path('views/web/services-landing-v2.blade.php'));
        $this->assertStringNotContainsString('svl-clientele', $view);
        $this->assertStringContainsString('svl-clients', $view, 'the brands section stays');
    }

    public function test_third_party_tags_wait_for_an_interaction_or_8_seconds(): void
    {
        $master = file_get_contents(resource_path('views/web/home-v2/master.blade.php'));
        $this->assertStringContainsString("whenCalm(start, true); }, 8000);", $master);
        $this->assertStringContainsString("window.dataLayer = window.dataLayer || [];", $master);
        $this->assertStringNotContainsString('}, 3000); });', $master);
    }

    public function test_no_layout_reads_while_the_scripts_start(): void
    {
        $shell = file_get_contents(public_path('assets/js/home-v2-shell.js'));
        $this->assertStringContainsString('new IntersectionObserver(function (en) { header.classList.toggle(\'is-scrolled\'', $shell, 'scrolled state without scroll events or scrollY reads');
        $this->assertDoesNotMatchRegularExpression('/var onScroll = function \(\) \{ header\.classList\.toggle\(\'is-scrolled\', window\.scrollY/', $shell, 'scrollY must not be read synchronously at start-up');

        $js = file_get_contents(public_path('assets/js/home-v2.js'));
        $this->assertStringContainsString('var scrollState = { y: 0, velocity: 0 };', $js);
        $this->assertStringContainsString('var lastY = 0, ticking = false;', $js);
        $this->assertStringNotContainsString('f.v = read(); write(f.v);', $js, 'addFx must not read and write while registering');
        $this->assertStringContainsString('afterIntro(function () { requestAnimationFrame(runFx); });', $js, 'effects run for the first time in a frame after the loader curtain is gone');
        $this->assertStringContainsString('requestAnimationFrame(function tick()', $js, 'the loader starts painting in a frame');
        $this->assertStringContainsString('var probe = new IntersectionObserver', $js, 'headings are classified without rect reads');
        $this->assertStringContainsString('requestIdleCallback(initPhone, { timeout: 2000 })', $js, 'the phone widget is built late, when idle');
        $this->assertStringContainsString("form.addEventListener('focusin', function () {", $js, 'and at once when the visitor touches the form');
        $this->assertStringContainsString("document.querySelector('script[src*=\"intlTelInputWithUtils\"]')", $js, 'a tap fetches the phone library immediately, not through the delayed-script queue');
        $this->assertStringContainsString('if (!qBar.__started) qBar.__started = true;', $js);
    }

    public function test_marquee_logos_load_near_the_viewport_not_all_at_idle(): void
    {
        $js = file_get_contents(public_path('assets/js/home-v2.js'));
        $this->assertStringContainsString("rootMargin: '150% 0px 150% 0px'", $js);
        $this->assertStringNotContainsString("requestIdleCallback(start, { timeout: 3000 })", $js);
    }

    public function test_swiper_is_a_delayed_script_and_page_height_is_read_inside_a_resize_observer(): void
    {
        foreach (['/', '/seo-services'] as $path) {
            $html = $this->html($path);
            $this->assertMatchesRegularExpression('#<script type="text/v2-delayed"[^>]*swiper-bundle#', $html, "$path loads Swiper before the first interaction");
        }
        $js = file_get_contents(public_path('assets/js/home-v2.js'));
        $this->assertStringContainsString('lastDocH = boxH(en)', $js, 'page height comes from the ResizeObserver entry');
        $this->assertStringContainsString('var vpMark = document.createElement', $js, 'viewport height comes from an observed marker');
        $this->assertStringNotContainsString('docEl.scrollHeight - ', $js);
        $this->assertStringNotContainsString('requestAnimationFrame(syncHeader)', $js, 'header size comes from a ResizeObserver');
        $this->assertStringContainsString('heroH = boxH(en)', $js);
        $this->assertStringNotContainsString('window.innerHeight * 0.92', $js);
        $this->assertStringContainsString('f.el.__fxOn === true', $js, 'off-screen effects wait for the observer');
        $this->assertStringNotContainsString('Math.max(1, hero.offsetHeight)', $js);
        $shell = file_get_contents(public_path('assets/js/home-v2-shell.js'));
        $this->assertStringContainsString('keep trying for up to 15 s', $shell, 'the reviews panel waits for the late Swiper');
    }

    public function test_phone_input_css_is_not_render_blocking_and_is_added_when_the_widget_is_built(): void
    {
        foreach (['/', '/about-us', '/contact-us', '/seo-services'] as $path) {
            $html = $this->html($path);
            $outside = preg_replace('#<noscript>.*?</noscript>#s', '', $html);
            $this->assertDoesNotMatchRegularExpression('#<link\b[^>]*intlTelInput#i', $outside, "$path still has a stylesheet link for the phone input in the head");
            $this->assertStringContainsString('<meta name="v2_intl_css" content=', $html, $path);
            $this->assertMatchesRegularExpression('#<noscript><link rel="stylesheet" href="[^"]*intlTelInput\.min\.css"></noscript>#', $html, "$path keeps a no-JS fallback");
            $this->assertStringContainsString('function v2IntlCss(cb)', $html, $path);
            $this->assertStringContainsString('v2IntlCss();', $html, "$path: the delayed-script release adds the CSS for the popup forms");
        }
        $js = file_get_contents(public_path('assets/js/home-v2.js'));
        $this->assertStringContainsString('window.v2IntlCss(build)', $js, 'the widget is built once its stylesheet is in');
    }

    public function test_minified_files_are_chosen_by_content_not_by_timestamp(): void
    {
        require_once resource_path('views/web/home-v2/helpers.php');
        $manifestFile = public_path('assets/min-manifest.json');
        $this->assertFileExists($manifestFile, 'run tools/build-assets.ps1');
        $manifest = json_decode(file_get_contents($manifestFile), true);
        $this->assertArrayHasKey('assets/css/home-v2.css', $manifest);
        foreach (['assets/css/home-v2.css', 'assets/js/home-v2.js', 'assets/css/home-v2-shell.css'] as $src) {
            $this->assertSame($manifest[$src], md5(str_replace("\r", '', file_get_contents(public_path($src)))), "$src changed after the last build: run tools/build-assets.ps1");
        }
        // a deploy that leaves the sources newer than the .min files must not switch minification off
        $src = public_path('assets/css/home-v2.css');
        $old = filemtime($src);
        touch($src, time() + 3600);
        clearstatcache();
        try {
            $this->assertStringContainsString('home-v2.min.css', $this->html('/'), 'minified CSS must still be served when the source file is newer than the .min');
        } finally {
            touch($src, $old);
            clearstatcache();
        }
    }

    public function test_shared_scripts_are_served_minified_on_v2_pages(): void
    {
        $html = $this->html('/about-us');
        foreach (['main', 'enquiry', 'newsletter', 'input'] as $n) {
            $this->assertMatchesRegularExpression("#assets/js/$n\\.min\\.js\\?v=\\d+#", $html, "$n.js is not served minified");
        }
        $this->assertMatchesRegularExpression('#assets/css/custom\.min\.css\?v=\d+#', $html);
        $this->assertMatchesRegularExpression('#assets/input-master/css/intlTelInput\.min\.css#', $html);
    }
}
