<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Blogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * PageSpeed Checker (admin tool): lists the site's pages and runs Google PageSpeed Insights on them.
 * The API key stays on the server (config/services.php -> PAGESPEED_API_KEY); the browser only talks to run().
 */
class PageSpeedController extends Controller
{
    const PSI_ENDPOINT = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    /** base URLs the tool may test (host allow-list) */
    private function bases()
    {
        $staging = rtrim(config('services.pagespeed.default_base', 'https://staging.datamysite.com'), '/');
        return [
            ['key' => 'staging', 'label' => 'Staging', 'url' => $staging],
            ['key' => 'live', 'label' => 'Live', 'url' => 'https://datamysite.com'],
            ['key' => 'local', 'label' => 'This server', 'url' => rtrim(url('/'), '/')],
        ];
    }

    public function index()
    {
        $data['menu'] = 'seo.pagespeed';
        $data['bases'] = $this->bases();
        $data['groups'] = $this->pageGroups();
        $data['hasKey'] = (bool) config('services.pagespeed.key');

        return view('admin.seo.pagespeed.index')->with($data);
    }

    /** Pages grouped for the picker: [group => [[path, label], ...]] */
    private function pageGroups()
    {
        $groups = [
            'Main pages' => [
                ['/', 'Home'], ['/services', 'Services'], ['/about-us', 'About Us'], ['/contact-us', 'Contact Us'],
                ['/client-case-studies', 'Case Studies'], ['/faq', 'FAQ'], ['/privacy-policy', 'Privacy Policy'], ['/terms-conditions', 'Terms & Conditions'],
            ],
            'Blogs' => [['/blogs', 'Blogs (landing)'], ['/blogs/categories', 'Blog categories']],
        ];

        try {
            foreach (Blogs::where('status', '1')->orderBy('created_at', 'desc')->limit(5)->get(['slug', 'heading']) as $b) {
                $groups['Blogs'][] = ['/' . $b->slug, Str::limit($b->heading, 46)];
            }
        } catch (\Throwable $e) { /* blogs are optional in the list */ }

        /* service pages = every parameter-less GET route handled by ServiceController (the redesigned service template) */
        $service = [];
        foreach (app('router')->getRoutes()->getRoutes() as $r) {
            if (!in_array('GET', $r->methods(), true) || strpos($r->uri(), '{') !== false) continue;
            if (strpos($r->getActionName(), 'ServiceController@') === false) continue;
            $uri = '/' . trim($r->uri(), '/');
            $top = explode('/', trim($uri, '/'))[0];
            $service[Str::headline($top)][] = [$uri, Str::headline(basename($uri))];
        }
        ksort($service);
        foreach ($service as $name => $items) {
            usort($items, function ($a, $b) { return strcmp($a[0], $b[0]); });
            $groups['Service · ' . $name] = $items;
        }

        return $groups;
    }

    /** POST: run PageSpeed Insights for one URL + strategy and return a trimmed report */
    public function run(Request $request)
    {
        $request->validate(['url' => 'required|url|max:300', 'strategy' => 'required|in:mobile,desktop']);
        $url = $request->input('url');
        $strategy = $request->input('strategy');

        $host = parse_url($url, PHP_URL_HOST);
        $allowed = array_map(function ($b) { return parse_url($b['url'], PHP_URL_HOST); }, $this->bases());
        if (!in_array($host, $allowed, true) && !in_array(preg_replace('/^www\./', '', (string) $host), $allowed, true)) {
            return response()->json(['ok' => false, 'error' => 'This host is not allowed. Use Staging, Live or This server.'], 422);
        }
        $key = config('services.pagespeed.key');
        if (!$key) {
            return response()->json(['ok' => false, 'error' => 'PAGESPEED_API_KEY is not set in .env. Add it and run: php artisan config:clear'], 500);
        }

        $cacheKey = 'psi2_' . sha1($url . '|' . $strategy);
        if (!$request->boolean('refresh') && ($hit = Cache::get($cacheKey))) {
            $hit['cached'] = true;
            return response()->json($hit);
        }

        try {
            // Google needs repeated "category=" params (not category[0]=...), so build the query by hand.
            $qs = http_build_query(['url' => $url, 'strategy' => $strategy, 'key' => $key])
                . '&category=PERFORMANCE&category=ACCESSIBILITY&category=BEST_PRACTICES&category=SEO';
            $res = Http::timeout(150)->retry(1, 1500, null, false)->get(self::PSI_ENDPOINT . '?' . $qs);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => 'Could not reach Google PageSpeed: ' . Str::limit($e->getMessage(), 160)], 502);
        }

        if ($res->failed()) {
            $msg = data_get($res->json(), 'error.message', 'Google returned HTTP ' . $res->status());
            if ($res->status() === 429 || stripos($msg, 'quota') !== false) $msg = 'Google PageSpeed quota exceeded. Try again later or use another API key. (' . Str::limit($msg, 120) . ')';
            elseif (stripos($msg, 'FAILED_DOCUMENT_REQUEST') !== false || stripos($msg, 'DNS') !== false) $msg = 'Google could not load this URL (is it public and online?). ' . Str::limit($msg, 140);
            return response()->json(['ok' => false, 'error' => $msg], $res->status() >= 400 && $res->status() < 600 ? $res->status() : 502);
        }

        $report = $this->trim($res->json(), $url, $strategy);
        Cache::put($cacheKey, $report, now()->addMinutes(10));

        return response()->json($report);
    }

    private function fmtItem($it)
    {
        $o = [];
        foreach (['url', 'label', 'groupLabel', 'entity', 'totalBytes', 'wastedBytes', 'wastedMs', 'transferSize', 'blockingTime', 'total', 'scripting', 'duration', 'score', 'cacheLifetimeMs', 'cacheHitProbability', 'reflowTime'] as $k) {
            if (isset($it[$k]) && !is_array($it[$k])) $o[$k] = $it[$k];
            elseif (isset($it[$k]['text'])) $o[$k] = $it[$k]['text'];
        }
        if (isset($it['node'])) { $o['node'] = Str::limit(data_get($it, 'node.snippet', ''), 220); $o['selector'] = data_get($it, 'node.selector'); }
        if (isset($it['source']['url'])) $o['url'] = $it['source']['url'] . (isset($it['source']['line']) ? ':' . ($it['source']['line'] + 1) : '');
        elseif (isset($it['source']['value'])) $o['url'] = $it['source']['value'];
        if (isset($it['url'])) $o['url'] = Str::limit(preg_replace('#^https?://[^/]+#', '', $it['url']) ?: $it['url'], 110);
        return $o;
    }

    private function trim($j, $url, $strategy)
    {
        $lh = $j['lighthouseResult'] ?? [];
        $audits = $lh['audits'] ?? [];
        $cats = $lh['categories'] ?? [];
        $score = function ($k) use ($cats) { return isset($cats[$k]['score']) ? (int) round($cats[$k]['score'] * 100) : null; };

        $metricIds = ['first-contentful-paint' => 'FCP', 'largest-contentful-paint' => 'LCP', 'total-blocking-time' => 'TBT', 'cumulative-layout-shift' => 'CLS', 'speed-index' => 'SI', 'interactive' => 'TTI', 'server-response-time' => 'TTFB'];
        $metrics = [];
        foreach ($metricIds as $id => $short) {
            if (!isset($audits[$id])) continue;
            $metrics[$short] = ['title' => $audits[$id]['title'] ?? $short, 'value' => $audits[$id]['numericValue'] ?? null, 'display' => $audits[$id]['displayValue'] ?? '', 'score' => $audits[$id]['score'] ?? null];
        }

        $mk = function ($id, $a, $cat) {
            $d = $a['details'] ?? [];
            $items = [];
            foreach (array_slice($d['items'] ?? [], 0, 8) as $it) { if (is_array($it)) $items[] = $this->fmtItem($it); }
            if (isset($d['items'][0]['items']) && !$items) { /* nested tables */ foreach (array_slice($d['items'][0]['items'], 0, 8) as $it) $items[] = $this->fmtItem($it); }
            return [
                'id' => $id, 'cat' => $cat, 'title' => $a['title'] ?? $id, 'description' => $a['description'] ?? '', 'display' => $a['displayValue'] ?? '',
                'score' => $a['score'] ?? null, 'mode' => $a['scoreDisplayMode'] ?? '',
                'savingsMs' => (int) ($d['overallSavingsMs'] ?? ($a['metricSavings']['LCP'] ?? 0)), 'savingsBytes' => (int) ($d['overallSavingsBytes'] ?? 0),
                'items' => $items,
            ];
        };

        $issues = [];
        foreach (['performance' => 'performance', 'accessibility' => 'accessibility', 'best-practices' => 'best-practices', 'seo' => 'seo'] as $catId => $cat) {
            foreach ($cats[$catId]['auditRefs'] ?? [] as $ref) {
                $id = $ref['id']; $a = $audits[$id] ?? null;
                if (!$a || isset($metricIds[$id]) || ($ref['group'] ?? '') === 'metrics' || ($ref['group'] ?? '') === 'hidden') continue;
                if (in_array($a['scoreDisplayMode'] ?? '', ['notApplicable', 'manual', 'error'], true)) continue;
                $s = $a['score'] ?? null;
                if ($catId === 'performance') {
                    if (($a['scoreDisplayMode'] ?? '') === 'informative' && empty($a['details']['items'])) continue;
                    if ($s !== null && $s >= 0.9) continue;
                } elseif ($s === null || $s >= 1) continue;
                $issues[] = $mk($id, $a, $cat) + ['weight' => $ref['weight'] ?? 0, 'group' => $ref['group'] ?? ''];
            }
        }

        $field = null;
        foreach (['loadingExperience', 'originLoadingExperience'] as $fk) {
            if (!empty($j[$fk]['metrics'])) {
                $field = ['scope' => $fk === 'loadingExperience' ? 'page' : 'origin', 'overall' => $j[$fk]['overall_category'] ?? null, 'metrics' => []];
                foreach ($j[$fk]['metrics'] as $mid => $m) $field['metrics'][$mid] = ['p75' => $m['percentile'] ?? null, 'category' => $m['category'] ?? null];
                break;
            }
        }

        return [
            'ok' => true, 'url' => $url, 'finalUrl' => $lh['finalUrl'] ?? $url, 'strategy' => $strategy, 'fetchedAt' => $lh['fetchTime'] ?? now()->toIso8601String(),
            'lighthouse' => $lh['lighthouseVersion'] ?? null,
            'scores' => ['performance' => $score('performance'), 'accessibility' => $score('accessibility'), 'best-practices' => $score('best-practices'), 'seo' => $score('seo')],
            'metrics' => $metrics, 'issues' => $issues, 'field' => $field,
            'screenshot' => data_get($audits, 'final-screenshot.details.data'),
            'filmstrip' => array_map(function ($f) { return ['t' => $f['timing'] ?? 0, 'data' => $f['data'] ?? '']; }, array_slice(data_get($audits, 'screenshot-thumbnails.details.items', []), 0, 8)),
            'weight' => data_get($audits, 'total-byte-weight.numericValue'), 'requests' => count(data_get($audits, 'network-requests.details.items', [])),
            'cached' => false,
        ];
    }
}
