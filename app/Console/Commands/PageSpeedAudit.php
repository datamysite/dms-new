<?php

namespace App\Console\Commands;

use App\Services\PageSpeedService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Runs Google PageSpeed Insights over a set of pages and checks the scores / Core Web Vitals against the targets in
 * .claude/rules/performance.md. The report is saved to storage/app/pagespeed/ so runs can be compared (before / after).
 *
 *   php artisan seo:pagespeed                                  staging, core pages, mobile + desktop
 *   php artisan seo:pagespeed --base=live --strategy=mobile    live site, mobile only
 *   php artisan seo:pagespeed --set=all --fresh                every page, ignore the 10-minute cache
 *   php artisan seo:pagespeed --fail-under=95                  exit code 1 when any category score is below 95
 */
class PageSpeedAudit extends Command
{
    protected $signature = 'seo:pagespeed
        {--base= : staging, live, local or a full URL (default: PAGESPEED_BASE_URL)}
        {--strategy=both : mobile, desktop or both}
        {--set=core : core (about 10 pages) or all (every page)}
        {--path=* : test only these paths, e.g. --path=/about-us}
        {--fail-under= : exit with an error when a category score is below this number (default: the "fail" thresholds in config/services.php)}
        {--fresh : ignore the 10-minute cache}
        {--json : print the report as JSON instead of a table}';

    protected $description = 'Run Google PageSpeed Insights on the site and check scores and Core Web Vitals against the targets';

    public function handle(PageSpeedService $psi): int
    {
        if (!$psi->hasKey()) {
            $this->error('PAGESPEED_API_KEY is not set in .env (then run: php artisan config:clear).');
            return self::FAILURE;
        }

        $base = $psi->resolveBase($this->option('base'));
        if (!$psi->isAllowed($base . '/')) {
            $this->error("Host not allowed: $base (use staging, live or local).");
            return self::FAILURE;
        }

        $strategies = $this->option('strategy') === 'both' ? ['mobile', 'desktop'] : [$this->option('strategy')];
        foreach ($strategies as $s) {
            if (!in_array($s, ['mobile', 'desktop'], true)) { $this->error('--strategy must be mobile, desktop or both.'); return self::FAILURE; }
        }
        $paths = $this->option('path') ?: $psi->pagePaths($this->option('set'));
        foreach ($paths as $p) {
            // Git Bash on Windows rewrites an argument like "/" into "C:/Program Files/Git/": refuse it instead of testing a wrong URL
            if (!preg_match('#^/[A-Za-z0-9/_\-.%]*$#', $p)) {
                $this->error("Invalid path \"$p\". Use a site path such as /about-us. (In Git Bash prefix the command with MSYS_NO_PATHCONV=1, or use PowerShell.)");
                return self::FAILURE;
            }
        }
        $failUnder = $this->option('fail-under') !== null ? (int) $this->option('fail-under') : null;
        $thresholds = config('services.pagespeed.thresholds', []);

        $total = count($paths) * count($strategies);
        $this->info("PageSpeed on $base: " . count($paths) . ' page(s) x ' . implode(' + ', $strategies) . " = $total run(s). This takes a while (about 20-60 s each).");

        $results = [];
        $failures = 0;
        $warnings = 0;
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        foreach ($paths as $path) {
            foreach ($strategies as $strategy) {
                $url = $base . '/' . ltrim($path, '/');
                if ($path === '/') $url = $base . '/';
                [$status, $r] = $psi->run($url, $strategy, (bool) $this->option('fresh'));
                $row = ['path' => $path, 'strategy' => $strategy, 'status' => $status, 'ok' => !empty($r['ok'])];
                if (!empty($r['ok']) && in_array('http-status-code', collect($r['issues'])->pluck('id')->all(), true)) {
                    // Lighthouse flags an HTTP error status: the scores would describe an error page, not the real page
                    $r = ['ok' => false, 'error' => 'The page answered with an HTTP error status (not deployed yet, or blocked?). Scores ignored.'];
                }
                if (!empty($r['ok'])) {
                    $row['scores'] = $r['scores'];
                    $row['metrics'] = collect($r['metrics'])->map(fn ($m) => ['value' => $m['value'], 'display' => $m['display']])->all();
                    $row['failedAudits'] = collect($r['issues'])->pluck('id')->all();
                    $row['cached'] = $r['cached'] ?? false;
                } else {
                    $row['error'] = $psi->scrubKey($r['error'] ?? 'unknown error');
                    $failures++;
                }
                $results[] = $row;
                $bar->advance();
            }
        }
        $bar->finish();
        $this->newLine(2);

        $table = [];
        foreach ($results as $row) {
            if (!$row['ok']) {
                $table[] = [$row['path'], $row['strategy'], 'ERROR', '', '', '', '', '', '', '', $row['error']];
                continue;
            }
            $cells = [];
            foreach (['performance', 'accessibility', 'best-practices', 'seo'] as $cat) {
                $score = $row['scores'][$cat];
                $limit = $failUnder ?? ($thresholds[$cat]['fail'] ?? 0);
                $target = $thresholds[$cat]['target'] ?? 100;
                if ($score === null) { $cells[] = '-'; continue; }
                if ($score < $limit) { $failures++; $cells[] = "$score FAIL"; }
                elseif ($score < $target) { $warnings++; $cells[] = "$score warn"; }
                else $cells[] = (string) $score;
            }
            // Core Web Vitals targets apply to the mobile (Slow 4G) run
            foreach (['FCP', 'LCP', 'TBT', 'CLS', 'SI'] as $m) {
                $v = $row['metrics'][$m]['value'] ?? null;
                $disp = $row['metrics'][$m]['display'] ?? '-';
                if ($v === null || $row['strategy'] !== 'mobile') { $cells[] = $disp; continue; }
                if ($v > PageSpeedService::TARGETS[$m]) { $warnings++; $cells[] = "$disp !"; } else $cells[] = $disp;
            }
            $table[] = array_merge([$row['path'], $row['strategy']], $cells, [count($row['failedAudits']) . ' audits']);
        }

        $dir = 'pagespeed';
        $file = $dir . '/' . parse_url($base, PHP_URL_HOST) . '-' . now()->format('Ymd-His') . '.json';
        Storage::disk('local')->put($file, json_encode(['base' => $base, 'generated' => now()->toIso8601String(), 'results' => $results], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if ($this->option('json')) {
            $this->line(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->table(['Page', 'Mode', 'Perf', 'A11y', 'BestPr', 'SEO', 'FCP', 'LCP', 'TBT', 'CLS', 'SI', 'Failed'], array_map(function ($r) {
                return array_pad($r, 12, '');
            }, $table));
            $this->line('"!" = misses the mobile target (FCP 1.8 s, LCP 2.5 s, TBT 200 ms, CLS 0.1, SI 3.4 s). "warn" = under the 95-100 goal, "FAIL" = under the minimum.');
        }
        $this->line('Saved: storage/app/private/' . $file . '  (or storage/app/' . $file . ')');
        $this->line("Result: $failures failure(s), $warnings warning(s).");

        return $failures > 0 ? self::FAILURE : self::SUCCESS;
    }
}
