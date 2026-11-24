<?php

namespace Tests\Feature\Seo;

use App\Services\PageSpeedService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Live checks with Google PageSpeed Insights. Skipped in the normal run (it calls Google, about 20-60 s per page); run with
 *     php vendor/bin/phpunit --group pagespeed
 * It tests PAGESPEED_BASE_URL (default https://staging.datamysite.com) and needs PAGESPEED_API_KEY. Set PAGESPEED_TEST_BASE=live
 * to test the live site instead. Staging only shows the audit fixes after they are deployed.
 */
#[Group('pagespeed')]
class PageSpeedTest extends TestCase
{
    /** audits that belong to the SEO audit items and must pass (they are listed in "issues" only when they fail) */
    private const MUST_PASS = ['image-alt', 'canonical', 'heading-order', 'document-title', 'meta-description', 'link-text', 'crawlable-anchors', 'http-status-code'];

    private static array $reports = [];

    private function base(PageSpeedService $psi): string
    {
        return $psi->resolveBase(env('PAGESPEED_TEST_BASE') ?: null);
    }

    private function report(string $path, string $strategy): array
    {
        $psi = app(PageSpeedService::class);
        if (!$psi->hasKey()) $this->markTestSkipped('PAGESPEED_API_KEY is not set');
        $url = $this->base($psi) . ($path === '/' ? '/' : $path);
        $key = $url . '|' . $strategy;
        if (!isset(self::$reports[$key])) {
            [$status, $r] = $psi->run($url, $strategy);
            $this->assertTrue($r['ok'] ?? false, "PageSpeed failed for $url: " . ($r['error'] ?? "HTTP $status"));
            self::$reports[$key] = $r;
        }
        return self::$reports[$key];
    }

    private function failedIds(array $report): array
    {
        return array_column($report['issues'], 'id');
    }

    public static function pages(): array
    {
        return [['/'], ['/about-us'], ['/contact-us'], ['/faq'], ['/blogs'], ['/seo-services']];
    }

    #[DataProvider('pages')]
    public function test_audit_items_pass_on_mobile(string $path): void
    {
        $failed = $this->failedIds($this->report($path, 'mobile'));
        foreach (self::MUST_PASS as $audit) {
            $this->assertNotContains($audit, $failed, "$path: Lighthouse audit \"$audit\" fails");
        }
    }

    public function test_the_page_is_indexable_on_staging_and_on_live(): void
    {
        // staging is served exactly like production (config seo.block_non_production is off), so "Page is blocked from indexing" must not appear
        $failed = $this->failedIds($this->report('/', 'mobile'));
        $this->assertNotContains('is-crawlable', $failed, 'the page must not be blocked from indexing');
    }

    public function test_home_scores_stay_above_the_minimums(): void
    {
        $thresholds = config('services.pagespeed.thresholds');
        $scores = $this->report('/', 'mobile')['scores'];
        foreach ($thresholds as $category => $limit) {
            $this->assertGreaterThanOrEqual($limit['fail'], $scores[$category], "home mobile $category score");
        }
    }

    public function test_home_core_web_vitals_meet_the_mobile_and_desktop_targets(): void
    {
        foreach (['mobile', 'desktop'] as $strategy) {
            $metrics = $this->report('/', $strategy)['metrics'];
            foreach (PageSpeedService::targets($strategy) as $name => $limit) {
                $this->assertArrayHasKey($name, $metrics);
                $this->assertLessThanOrEqual($limit, $metrics[$name]['value'], "home $strategy $name (" . $metrics[$name]['display'] . ')');
            }
        }
    }

    public function test_the_api_key_never_appears_in_error_messages(): void
    {
        $psi = app(PageSpeedService::class);
        $key = (string) config('services.pagespeed.key');
        if ($key === '') $this->markTestSkipped('PAGESPEED_API_KEY is not set');
        $text = $psi->scrubKey("cURL error 28 for https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=x&strategy=mobile&key=$key&category=SEO");
        $this->assertStringNotContainsString($key, $text);
        $this->assertStringContainsString('key=[hidden]', $text);
    }
}
