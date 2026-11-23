<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * Smooth scrolling on the redesigned (V2) pages: the fixes for the "glitchy" scroll must not come back.
 * (Measured with headless Chrome + mouse-wheel scrolling: janky frames went from ~55 to 0-3 on the home page.)
 */
class ScrollPerformanceTest extends TestCase
{
    use CrawlsPages;

    private const V2_PAGES = ['/', '/about-us', '/contact-us', '/faq', '/blogs', '/blogs/categories', '/client-case-studies', '/privacy-policy', '/terms-conditions', '/services', '/seo-services'];

    private function asset(string $rel): string
    {
        return file_get_contents(public_path('assets/' . $rel));
    }

    public function test_no_v2_page_loads_the_lenis_smooth_scroll_library(): void
    {
        foreach (self::V2_PAGES as $path) {
            $html = $this->html($path);
            $this->assertDoesNotMatchRegularExpression('#<script[^>]+lenis#i', $html, "$path still loads Lenis");
        }
        foreach (['js/home-v2.js', 'js/home-v2-shell.js'] as $f) {
            $this->assertStringNotContainsString('new window.Lenis', $this->asset($f), $f);
            $this->assertStringNotContainsString('lenis.', $this->asset($f), $f);
        }
    }

    public function test_root_smooth_scroll_is_off_and_scrollbar_space_is_reserved(): void
    {
        $this->assertStringContainsString('html { scroll-behavior: auto !important; scrollbar-gutter: stable; }', $this->asset('css/home-v2.css'));
    }

    public function test_delayed_scripts_are_not_released_by_scrolling(): void
    {
        $master = file_get_contents(resource_path('views/web/home-v2/master.blade.php'));
        $this->assertStringContainsString("evs = ['pointerdown', 'keydown']", $master);
        $this->assertDoesNotMatchRegularExpression("#evs = \[[^\]]*'(wheel|touchstart|mousemove|scroll)'#", $master, 'a scroll gesture must not release the third-party scripts');
        $this->assertStringContainsString('function whenCalm(fn, idle)', $master, 'scripts wait until the visitor stops scrolling');
        // the page still loads them on its own (idle after load)
        $this->assertStringContainsString("whenCalm(start, true); }, 3000);", $master);
    }

    public function test_home_scroll_loop_reads_then_writes_and_never_changes_animation_speed(): void
    {
        $js = $this->asset('js/home-v2.js');
        $this->assertStringContainsString('if (f.on) f.v = f.read(); }', $js, 'read phase');
        $this->assertStringContainsString('if (f.on) f.write(f.v); }', $js, 'write phase after all reads');
        $this->assertStringNotContainsString('playbackRate', $js, 'sliders run at a steady speed');
        $this->assertStringNotContainsString("docEl.scrollHeight - window.innerHeight, p", $js, 'page height is not read every frame');
        $this->assertStringNotContainsString("cta.style.setProperty('--z'", $js, 'CTA zoom is written on the image, not as a variable on the whole section');
        $this->assertStringNotContainsString('void qBar.offsetWidth; qBar.style.setProperty', $js, 'no forced reflow on every testimonial slide');
    }

    public function test_page_parallax_loops_never_measure_inside_the_frame(): void
    {
        foreach (['about-v2', 'blogs-v2', 'blog-detail-v2', 'blogs-categories-v2', 'case-studies-v2', 'contact-v2', 'faq-v2', 'privacy-v2', 'terms-v2'] as $f) {
            $js = $this->asset("js/$f.js");
            $this->assertSame(1, preg_match('/function paint\(\) \{(.*?)\n  \}/s', $js, $m), "$f has a paint()");
            $this->assertStringNotContainsString('getBoundingClientRect', $m[1], "$f measures layout inside its scroll frame");
            $this->assertStringContainsString('function measure()', $js, "$f measures on resize / load instead");
        }
        $svc = $this->asset('js/service-v2.js');
        $this->assertStringContainsString('if (!m.host._live || m.top === undefined) return;', $svc, 'service image marks only move while visible');
        $this->assertStringNotContainsString("root.style.setProperty('--sv2-lx'", $svc, 'mouse lean is not set on the whole page');
    }

    public function test_no_blur_reveals_or_frosted_glass_on_scrolling_content(): void
    {
        $css = $this->asset('css/home-v2.css');
        foreach ([
            '.hv2-blob { filter: none; }',
            '.hv2-glass { -webkit-backdrop-filter: none; backdrop-filter: none; }',
            '.hv2-js .hv2 [data-reveal]:not(.is-in) { filter: none; }',
            '.hv2-js .hv2 [data-split]:not(.is-in) .hv2-w > span { filter: none; }',
            '.hv2-media img { filter: none; }',
            '@keyframes hv2-drip { from { translate: 0 0; } to { translate: 0 320%; } }',
            '@keyframes hv2-shimmer { from { translate: 0 0; } to { translate: -60% 0; } }',
        ] as $rule) {
            $this->assertStringContainsString($rule, $css);
        }
        // the overrides must come AFTER the original rules to win
        $this->assertGreaterThan(strpos($css, '.hv2-blob { position: absolute; border-radius: 50%; filter: blur(90px)'), strpos($css, '.hv2-blob { filter: none; }'));
    }

    public function test_home_upper_sections_render_normally_and_lower_ones_have_measured_placeholders(): void
    {
        $html = $this->html('/');
        foreach (['hv2-clients', 'hv2-stats', 'hv2-best-solutions', 'hv2-cases'] as $id) {
            $this->assertSame(1, preg_match('#<section class="([^"]*)" id="' . $id . '"#', $html, $m), $id);
            $this->assertStringNotContainsString('hv2-cv', $m[1], "$id renders normally (anchor jumps land correctly)");
        }
        $css = $this->asset('css/home-v2.css');
        foreach (['hv2-why', 'hv2-testimonials', 'hv2-who-we-are', 'hv2-blogs', 'hv2-faq'] as $id) {
            $this->assertStringContainsString(".hv2-cv#$id { contain-intrinsic-size: auto", $css, $id);
        }
    }

    public function test_served_minified_files_are_never_older_than_their_source(): void
    {
        foreach (glob(public_path('assets/{js,css}/*-v2*.{js,css}'), GLOB_BRACE) as $src) {
            if (str_contains($src, '.min.')) continue;
            $min = preg_replace('/\.(js|css)$/', '.min.$1', $src);
            if (!is_file($min)) continue;
            $this->assertGreaterThanOrEqual(filemtime($src), filemtime($min), basename($min) . ' is older than its source (an old version would be served after deploy)');
        }
    }
}
