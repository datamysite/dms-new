<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/**
 * Audit item: Organization, WebSite, LocalBusiness, FAQPage and Service structured data.
 *
 * The site also keeps hand-made schema in the database (table snippet_code), keyed by the PRODUCTION url. Those snippets show on
 * https://datamysite.com but not on staging. The template adds its own schema only where no snippet has that type, so:
 *   - template-owned schema is asserted on the staging host (no snippets there), and
 *   - "never two blocks of one type" is asserted on both hosts.
 */
class StructuredDataTest extends TestCase
{
    use CrawlsPages;

    public function test_every_page_has_valid_json_ld_with_organization_and_website(): void
    {
        foreach (array_merge(self::TEMPLATE_PAGES, self::SERVICE_PAGES) as $path) {
            $types = $this->jsonLdTypes($this->html($path, self::STAGING)); // also fails on invalid JSON
            $this->assertContains('Organization', $types, "$path has Organization");
            $this->assertContains('WebSite', $types, "$path has WebSite");
        }
    }

    public function test_organization_facts_come_from_config_and_are_complete(): void
    {
        $org = collect($this->jsonLd($this->html('/', self::STAGING)))->firstWhere('@type', 'Organization');
        $this->assertSame('https://staging.datamysite.com/#organization', $org['@id']);
        $this->assertSame(config('business.name'), $org['name']);
        $this->assertSame(config('business.phone'), $org['telephone']);
        $this->assertSame('AE', $org['address']['addressCountry']);
        $this->assertSame(config('business.same_as'), $org['sameAs']);
        $this->assertStringEndsWith('/public/assets/img/logo.png', $org['logo']['url']);
    }

    public function test_local_business_only_on_home_and_contact(): void
    {
        foreach (['/', '/contact-us'] as $path) {
            $this->assertContains('ProfessionalService', $this->jsonLdTypes($this->html($path, self::STAGING)), $path);
        }
        foreach (['/about-us', '/faq', '/seo-services'] as $path) {
            $this->assertNotContains('ProfessionalService', $this->jsonLdTypes($this->html($path, self::STAGING)), $path);
        }
        $local = collect($this->jsonLd($this->html('/contact-us', self::STAGING)))->firstWhere('@type', 'ProfessionalService');
        $this->assertSame('Monday', $local['openingHoursSpecification'][0]['dayOfWeek'][0]);
        $this->assertSame('09:30', $local['openingHoursSpecification'][0]['opens']);
        $this->assertArrayNotHasKey('aggregateRating', $local, 'never invent ratings');
        $this->assertArrayNotHasKey('geo', $local, 'no coordinates until confirmed');
    }

    public function test_faq_pages_have_one_faqpage_matching_the_visible_questions(): void
    {
        $html = $this->html('/faq', self::STAGING);
        $faq = collect($this->jsonLd($html))->where('@type', 'FAQPage');
        $this->assertCount(1, $faq);
        $visible = substr_count($html, 'class="fqv-faq-item"');
        $this->assertGreaterThan(0, $visible);
        $this->assertCount($visible, $faq->first()['mainEntity']);

        $home = collect($this->jsonLd($this->html('/', self::STAGING)))->where('@type', 'FAQPage');
        $this->assertCount(1, $home);
        $this->assertGreaterThanOrEqual(10, count($home->first()['mainEntity']));
        $first = $home->first()['mainEntity'][0];
        $this->assertSame('Question', $first['@type']);
        $this->assertNotEmpty($first['acceptedAnswer']['text']);
        $this->assertStringNotContainsString('<', $first['acceptedAnswer']['text'], 'answers are plain text');
    }

    public function test_service_pages_have_a_service_and_a_faq_schema(): void
    {
        foreach (self::SERVICE_PAGES as $path) {
            $types = $this->jsonLdTypes($this->html($path, self::STAGING));
            $this->assertContains('Service', $types, "$path has Service");
            $this->assertContains('BreadcrumbList', $types, "$path has BreadcrumbList");
        }
    }

    /** the real guarantee: whatever the database adds, a page never carries two blocks of the same type */
    public function test_no_page_has_two_blocks_of_the_same_schema_type_on_either_host(): void
    {
        // Some pages already carry two blocks of a type from the DATABASE alone (a page's stored section text AND a snippet both hold a
        // Service or FAQPage block: pending database work). So the check compares each page with the template schema switched off
        // (config seo.template_schema = false) and on: the template may add a type only when the database provides none.
        $types = ['FAQPage', 'BreadcrumbList', 'Service', 'WebSite', 'Organization', 'ProfessionalService'];
        $pages = array_merge(self::TEMPLATE_PAGES, self::SERVICE_PAGES, ['/outdoor-advertising/billboards-advertising', '/radio-advertising', '/transit-media', '/influencer-marketing', '/digital-advertising/dooh-advertising']);
        $problems = [];
        foreach ([self::PRODUCTION, self::STAGING] as $host) {
            foreach ($pages as $path) {
                config(['seo.template_schema' => false]);
                $base = array_count_values($this->jsonLdTypes($this->html($path, $host)));
                config(['seo.template_schema' => true]);
                $with = array_count_values($this->jsonLdTypes($this->html($path, $host)));
                foreach ($types as $t) {
                    $b = $base[$t] ?? 0; $w = $with[$t] ?? 0;
                    $allowed = $b >= 1 ? $b : 1;      // never more than the database has; if it has none, at most one
                    if ($w > $allowed) $problems[] = "$host$path: $t database=$b, with template=$w";
                }
            }
        }
        config(['seo.template_schema' => true]);
        $this->assertSame([], $problems, "the template added a duplicate schema block:\n" . implode("\n", $problems));
    }

    public function test_the_template_schema_can_be_switched_off(): void
    {
        config(['seo.template_schema' => false]);
        $types = $this->jsonLdTypes($this->html('/about-us', self::STAGING));
        $this->assertNotContains('Organization', $types);
        $this->assertNotContains('WebSite', $types);
        config(['seo.template_schema' => true]);
    }

    public function test_template_schema_steps_aside_when_a_database_snippet_already_has_the_type(): void
    {
        // snippets exist for the production URL of /faq (FAQPage + BreadcrumbList): production must show them once, not twice
        $prod = $this->jsonLdTypes($this->html('/faq', self::PRODUCTION));
        $this->assertSame(1, count(array_keys($prod, 'FAQPage', true)));
        $this->assertSame(1, count(array_keys($prod, 'BreadcrumbList', true)));
        // and staging, where no snippet matches, still gets the template's own
        $stg = $this->jsonLdTypes($this->html('/faq', self::STAGING));
        $this->assertSame(1, count(array_keys($stg, 'FAQPage', true)));
        $this->assertSame(1, count(array_keys($stg, 'BreadcrumbList', true)));
    }

    public function test_snippet_detector_understands_json_ld_types(): void
    {
        $snip = fn (string $code) => (object) ['snippet_code' => $code];
        $this->assertTrue(v2_snippet_has('FAQPage', [$snip('<script type="application/ld+json">{"@type":"FAQPage"}</script>')]));
        $this->assertTrue(v2_snippet_has('Service', null, [$snip('{ "@context": "x", "@type": "Service" }')]));
        $this->assertTrue(v2_snippet_has('LocalBusiness', [$snip('{"@type": ["Organization","LocalBusiness"]}')]));
        $this->assertFalse(v2_snippet_has('FAQPage', [$snip('{"@type":"FAQPageX"}'), $snip('<script>gtag()</script>')]));
        $this->assertFalse(v2_snippet_has('Service', null, null));
    }

    public function test_blog_detail_schema_uses_the_current_host(): void
    {
        $slug = \App\Models\Blogs::where('status', '1')->orderBy('created_at', 'desc')->value('slug');
        $this->assertNotEmpty($slug, 'a published blog is needed for this test');
        $html = $this->html('/' . $slug, self::STAGING);
        $post = collect($this->jsonLd($html))->firstWhere('@type', 'BlogPosting');
        $this->assertStringStartsWith('https://staging.datamysite.com', $post['isPartOf']['@id']);
        $this->assertStringContainsString('"https://staging.datamysite.com/#organization"', $html);
    }
}
