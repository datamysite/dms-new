<?php

namespace Tests\Feature;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * The "Ava" chat widget was redesigned (editorial bento + liquid glass). Only markup, styles and UI animation changed:
 * these tests pin everything the conversation logic, tracking and conversion links depend on, and the design rules.
 * (The behaviour itself, e.g. clicking a topic card sends the original text, was also proven in headless Chrome with a stubbed network.)
 */
class ChatWidgetTest extends TestCase
{
    use CrawlsPages;

    private function widget(): string
    {
        return file_get_contents(resource_path('views/partials/chat-widget.blade.php'));
    }

    private function css(): string
    {
        preg_match('#<style>(.*?)</style>#s', $this->widget(), $m);
        return $m[1];
    }

    private function script(): string
    {
        preg_match('#<script>(.*)</script>#s', $this->widget(), $m);
        return $m[1];
    }

    public function test_every_hook_the_conversation_logic_depends_on_is_still_there(): void
    {
        $w = $this->widget();
        foreach (['dms-root', 'dms-launcher', 'dms-launch-label', 'dms-panel', 'dms-close', 'dms-avatar', 'dms-botname', 'dms-scroll', 'dms-quickbar', 'dms-input', 'dms-send'] as $id) {
            $this->assertStringContainsString('id="' . $id . '"', $w, "#$id");
        }
        $js = $this->script();
        foreach (['dms-chips', 'dms-msg', 'dms-typing', 'dms-leadcard', 'dms-wa-btn', 'dms-call-btn', 'data-submit', 'data-err', 'data-f='] as $hook) {
            $this->assertStringContainsString($hook, $js, $hook);
        }
        $this->assertStringStartsWith('{{-- DataMySite AI chat widget', ltrim($w));
        $this->assertStringContainsString('@verbatim', $w);
        $this->assertStringContainsString('@endverbatim', $w);
    }

    public function test_the_conversation_engine_is_untouched(): void
    {
        $js = $this->script();
        foreach ([
            // chat request + payload shape
            'body:JSON.stringify({system:buildSystemPrompt(),messages:history})',
            'fetch(CONFIG.apiBase.replace(/\/+$/,"")+"/chat",{',
            // tracking
            'const body = { sid:SID, event:event, meta:meta||null,messages:history };',
            // lead + conversion
            'window.dataLayer.push({event:"chatLeadEvent"})',
            'fetch(CONFIG.apiBase.replace(/\/+$/,"")+"/lead"',
            // the connect token flow
            'const tokenIdx = reply.indexOf("<<<CONNECT>>>");',
            'if(CONFIG.connectMode === "whatsapp" && CONFIG.whatsappNumber){ showWhatsApp(); }',
            // numbers and copy
            'whatsappNumber: "971525287259"',
            'supportPhone: "+971 52 528 7259"',
            'connectAfterMessages: 2',
            'whatsappGreeting: "Hi DataMySite team, I\'d like to talk about my marketing."',
            "apiBase: (function(){ var m=document.querySelector('meta[name=\"home_url\"]')",
            'Powered by',
        ] as $needle) {
            $this->assertStringContainsString($needle, $js . $this->widget(), $needle);
        }
        // the original greeting and the 5 quick replies, byte for byte
        $this->assertStringContainsString('const GREETING = `Hi 👋 I\'m ${CONFIG.botName} from ${CONFIG.brandName}. What are you trying to grow right now? Tap one below — or WhatsApp / call our team directly using the buttons underneath.`;', $js);
        $this->assertStringContainsString('const INTENT_CHIPS = ["🚀 I want more leads & sales","📣 Brand visibility / advertising","💰 What does it cost?","🏆 Show me client results",{label:"💬 WhatsApp our team", action:"whatsapp"}];', $js);
        foreach (['"loaded"', '"open"', '"message"', '"connect_shown"', '"converted"', '"teaser"'] as $event) {
            $this->assertStringContainsString("track($event", $js, "track($event");
        }
    }

    public function test_the_topic_cards_send_the_original_text_not_the_shortened_display_text(): void
    {
        $js = $this->script();
        $this->assertStringContainsString('else { send(label); }', $js, 'the payload is the full original label');
        $this->assertStringContainsString('if(isObj && t.action === "whatsapp"){ addUser(label); showWhatsApp(); }', $js);
        $this->assertStringContainsString('scroll-snap-type:x mandatory', $this->css(), 'swipeable cards');
        $this->assertStringContainsString('animation-delay:calc(.22s + var(--i,0) * 40ms)', $this->css(), '40 ms stagger per card');
    }

    public function test_copy_labels_and_zones_of_the_new_design(): void
    {
        $w = $this->widget();
        foreach (['ASK AVA AI', 'GREETING', 'TOPICS', 'GET IN TOUCH', 'ONLINE', 'Usually replies instantly', 'Ava · datamysite', 'Type your message…', 'Powered by <b>Claude</b> · DataMySite', 'Chat on WhatsApp', 'WhatsApp us', 'Call us'] as $text) {
            $this->assertTrue(str_contains($w, $text) || str_contains($w, str_replace('<b>', '', $text)), "missing: $text");
        }
        foreach (['dms-h-wa', 'dms-h-call', 'dms-reset', 'dms-strip'] as $id) {
            $this->assertStringContainsString('id="' . $id . '"', $w, $id);
        }
    }

    public function test_icons_are_inline_lucide_svgs_with_no_framework_or_extra_download(): void
    {
        $w = $this->widget();
        foreach (['sparkles', 'message-circle', 'phone', 'x', 'arrow-up', 'rotate-ccw', 'trending-up', 'megaphone', 'wallet', 'trophy'] as $name) {
            $this->assertMatchesRegularExpression('/\b"?' . preg_quote($name, '/') . '"?: "<(path|circle)/', $w, "icon $name is inlined");
        }
        $this->assertStringContainsString('function icon(name,size)', $w);
        $this->assertDoesNotMatchRegularExpression('#<script[^>]+src=#i', $w, 'the widget loads no external script');
        $this->assertDoesNotMatchRegularExpression('#cdn\.tailwindcss|alpinejs|unpkg\.com/lucide|lucide\.createIcons#i', $w, 'no Tailwind / Alpine / Lucide runtime');
        $this->assertStringNotContainsString('x-data', $w);
    }

    public function test_design_rules_cheap_to_draw_and_accessible(): void
    {
        $css = $this->css();
        // the launcher is always on screen: it must not use a backdrop blur
        preg_match('/#dms-launcher\{(.*?)\n  \}/s', $css, $m);
        $this->assertNotEmpty($m, 'launcher rule');
        $this->assertStringNotContainsString('backdrop-filter', $m[1]);
        // glass is only on the panel, which is hidden until opened
        $this->assertSame(1, preg_match('/#dms-panel\{[^}]*display:none[^}]*backdrop-filter:blur\(30px\)/s', $css), 'blur lives on the (initially hidden) panel');
        $this->assertStringContainsString('@media (prefers-reduced-motion:reduce)', $css);
        $this->assertStringContainsString('@media (prefers-reduced-transparency:reduce)', $css);
        $this->assertStringContainsString('@supports not ((backdrop-filter:blur(1px))', $css);
        $this->assertStringContainsString('cubic-bezier(.16,1,.3,1)', $css, 'spring easing');
        // only compositor-friendly properties are animated: no left/top/width/height keyframes
        foreach (array_slice(explode('@keyframes', $css), 1) as $block) {   // each keyframes rule sits on one line
            $this->assertDoesNotMatchRegularExpression('/\b(left|top|right|bottom|width|height|margin|padding)\s*:/', substr($block, 0, strpos($block, "\n") ?: 220), 'keyframes animate only transform / opacity / shadow rings');
        }
        // brand colours, not the old copper
        $this->assertStringNotContainsString('#B06A48', $css);
        $this->assertStringContainsString('var(--coral, var(--accent-color, #e04a43))', $css);
        $this->assertStringContainsString('var(--green, #0b3d2e)', $css);
        // tap targets and focus
        $this->assertStringContainsString(':focus-visible', $css);
        $this->assertStringContainsString('min-height:44px', $css);
        $this->assertStringContainsString('env(safe-area-inset-bottom)', $css);
    }

    public function test_script_is_balanced(): void
    {
        $js = $this->script();
        // template strings contain {} too, so compare against the original engine's own imbalance instead of zero
        $this->assertSame(substr_count($js, '('), substr_count($js, ')'), 'parentheses balance');
        $this->assertSame(substr_count($js, '{'), substr_count($js, '}'), 'braces balance');
        $this->assertSame(substr_count($js, '['), substr_count($js, ']'), 'brackets balance');
    }

    public function test_widget_is_on_every_v2_page_with_the_new_launcher_and_the_site_address_for_its_api(): void
    {
        foreach (['/', '/about-us', '/contact-us', '/seo-services', '/blogs'] as $path) {
            $html = $this->html($path);
            $this->assertStringContainsString('id="dms-launcher"', $html, $path);
            $this->assertStringContainsString('ASK AVA AI', $html, $path);
            $this->assertStringContainsString('<meta name="home_url"', $html, $path);
            $this->assertStringNotContainsString('Chat with us', $html, "$path still shows the old launcher text");
        }
    }
}
