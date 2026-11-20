/* PageSpeed Checker (admin) — vanilla JS, no dependencies. Data comes from window.PSC (see index.blade.php). */
(function () {
  'use strict';
  var C = window.PSC, root = document.getElementById('psc');
  if (!C || !root) return;

  /* ---------- helpers ---------- */
  var $ = function (s, el) { return (el || root).querySelector(s); };
  var esc = function (s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };
  var cls = function (s) { return s == null ? '' : s >= 90 ? 'g' : s >= 50 ? 'o' : 'r'; };
  var col = function (c) { return c === 'g' ? '#0cce6b' : c === 'o' ? '#ffa400' : c === 'r' ? '#ff4e42' : '#c5ced8'; };
  var store = {
    get: function (k, d) { try { var v = localStorage.getItem(k); return v ? JSON.parse(v) : d; } catch (e) { return d; } },
    set: function (k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} }
  };
  var fmtMs = function (v) { return v == null ? '–' : v >= 1000 ? (v / 1000).toFixed(1) + ' s' : Math.round(v) + ' ms'; };
  var fmtKb = function (b) { return b >= 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.round(b / 1024) + ' KB'; };
  var tagBytes = function (v) { return typeof v === 'number' ? fmtKb(v) : v; };
  function toast(msg, err) {
    var h = $('.psc-toasts', document.body) || (function () { var d = document.createElement('div'); d.className = 'psc-toasts'; document.body.appendChild(d); return d; })();
    var t = document.createElement('div'); t.className = 'psc-toast' + (err ? ' err' : ''); t.textContent = msg; h.appendChild(t);
    setTimeout(function () { t.remove(); }, 4500);
  }
  function ago(iso) { var m = Math.round((Date.now() - new Date(iso)) / 60000); return m < 1 ? 'just now' : m < 60 ? m + ' min ago' : m < 1440 ? Math.round(m / 60) + ' h ago' : Math.round(m / 1440) + ' d ago'; }

  /* ---------- metric targets (mobile thresholds from Google) ---------- */
  var METRICS = [
    { k: 'FCP', name: 'First Contentful Paint', good: 1800, poor: 3000, target: '≤ 1.8 s' },
    { k: 'LCP', name: 'Largest Contentful Paint', good: 2500, poor: 4000, target: '≤ 2.5 s' },
    { k: 'TBT', name: 'Total Blocking Time', good: 200, poor: 600, target: '≤ 200 ms' },
    { k: 'CLS', name: 'Cumulative Layout Shift', good: 0.1, poor: 0.25, target: '≤ 0.1', unit: '' },
    { k: 'SI', name: 'Speed Index', good: 3400, poor: 5800, target: '≤ 3.4 s' },
    { k: 'TTFB', name: 'Server response (TTFB)', good: 800, poor: 1800, target: '≤ 0.8 s' }
  ];
  function rate(m, v) { return v == null ? '' : v <= m.good ? 'g' : v <= m.poor ? 'o' : 'r'; }
  function fmtMetric(m, v) { return v == null ? '–' : m.k === 'CLS' ? v.toFixed(3) : fmtMs(v); }

  /* ---------- "How to fix" knowledge base (audit id -> steps) ---------- */
  var FIX = {
    'render-blocking-resources': { steps: ['Add <code>defer</code> to non-critical <code>&lt;script&gt;</code> tags and move them to the end of <code>&lt;body&gt;</code>.', 'Load non-critical CSS with <code>media="print" onload="this.media=\'all\'"</code> or inline only the above-the-fold CSS.', 'Minify and merge CSS/JS files; serve with gzip/brotli.'], tip: 'Blade: put page CSS in <code>@push(\'styles\')</code> and scripts in <code>@push(\'scripts\')</code> at the end of the layout; use <code>v2_asset()</code> for the minified files.' },
    'unused-css-rules': { steps: ['Split CSS per page (each V2 page already has its own file) and remove rules for sections that no longer exist.', 'Do not load vendor CSS (Swiper, intl-tel-input) on pages that do not use it.', 'Use PurgeCSS/<code>npx purgecss</code> on a copy to find dead rules.'], tip: 'The <code>v2lite / v2form / v2keep</code> sections decide which vendor files load.' },
    'unused-javascript': { steps: ['Load libraries only on pages that use them.', 'Delay third-party scripts until first user interaction.', 'Replace heavy libraries with native CSS/JS where possible.'], tip: 'Use <code>@section(\'v2lite\', \'1\')</code> so GTM/analytics wait for the first interaction.' },
    'unminified-css': { steps: ['Serve the <code>.min.css</code> file through <code>v2_asset()</code> and rebuild it after editing the source.'] },
    'unminified-javascript': { steps: ['Serve the <code>.min.js</code> file through <code>v2_asset()</code> and rebuild it after editing the source.'] },
    'uses-text-compression': { steps: ['Enable gzip/brotli on the web server for HTML, CSS, JS, SVG and JSON.', 'Apache: enable <code>mod_deflate</code> and add <code>AddOutputFilterByType DEFLATE text/html text/css application/javascript image/svg+xml</code>.', 'Nginx: <code>gzip on; gzip_types text/css application/javascript image/svg+xml;</code> (or brotli).'], tip: 'This is a server setting — nothing to change in Laravel.' },
    'uses-long-cache-ttl': { steps: ['Send <code>Cache-Control: public, max-age=31536000, immutable</code> for versioned static files (CSS, JS, images, fonts).', 'Apache: enable <code>mod_expires</code> / <code>mod_headers</code> for <code>/public/assets</code>.', 'Keep <code>?v=filemtime</code> so a change busts the cache.'], tip: 'Short TTL on third-party scripts (GTM, chat widgets) cannot be fixed by you — delay them instead.' },
    'offscreen-images': { steps: ['Add <code>loading="lazy"</code> and <code>decoding="async"</code> to every image below the fold.', 'Never lazy-load the hero/LCP image.'] },
    'modern-image-formats': { steps: ['Convert JPG/PNG to WebP (or AVIF) and keep the old file as fallback in <code>&lt;picture&gt;</code>.', 'Project helper: <code>v2_img()</code> outputs WebP + srcset when <code>name.webp</code> exists next to the original.'] },
    'uses-optimized-images': { steps: ['Compress images (quality 75–82 for WebP/JPG) and strip metadata.'] },
    'uses-responsive-images': { steps: ['Provide <code>srcset</code> + <code>sizes</code> so phones download the small file.', 'Generate <code>-w480 / -w960 / -w1440</code> WebP siblings and use <code>v2_img()</code>.'] },
    'unsized-images': { steps: ['Add explicit <code>width</code> and <code>height</code> (or CSS <code>aspect-ratio</code>) to every image.'] },
    'prioritize-lcp-image': { steps: ['Add <code>fetchpriority="high"</code> and <code>decoding="sync"</code> to the LCP image.', 'Preload it: <code>&lt;link rel="preload" as="image" href="…" fetchpriority="high"&gt;</code>.'] },
    'lcp-lazy-loaded': { steps: ['Remove <code>loading="lazy"</code> from the hero image.'] },
    'largest-contentful-paint-element': { steps: ['Identify the LCP element below, make it small, preload it and avoid animating it in with a delay.'] },
    'font-display': { steps: ['Add <code>font-display: swap</code> to every <code>@font-face</code>.', 'Self-host WOFF2 fonts and preload the main one.'] },
    'third-party-summary': { steps: ['Remove tags you no longer use (GTM, gtag, chat, pixels).', 'Load the rest after the first interaction or via a delayed loader.', 'Consolidate duplicate analytics tags.'] },
    'third-party-facades': { steps: ['Replace heavy embeds (YouTube, chat, maps) with a click-to-load placeholder.'] },
    'bootup-time': { steps: ['Reduce/defer JavaScript execution; split large bundles.', 'Remove unused libraries; delay third-party scripts.'] },
    'mainthread-work-breakdown': { steps: ['Reduce JS and style/layout work: remove heavy animations, avoid large DOM, debounce scroll/resize handlers.'] },
    'dom-size': { steps: ['Keep the DOM under ~1,500 nodes: render long lists on demand, remove wrapper divs, avoid duplicated mobile/desktop markup.'] },
    'layout-shifts': { steps: ['Reserve space (width/height/aspect-ratio/min-height) for images, banners, fonts and injected widgets.', 'Animate with <code>transform</code>/<code>opacity</code> only — never <code>top/left/height/width</code>.'] },
    'cls-culprits-insight': { steps: ['See "layout-shifts": reserve space for the elements listed below.'] },
    'long-tasks': { steps: ['Break long JS tasks (>50 ms) into smaller chunks; defer non-critical work with <code>requestIdleCallback</code>.'] },
    'forced-reflow-insight': { steps: ['Batch DOM reads before writes; avoid reading <code>offsetHeight</code>/<code>getBoundingClientRect</code> right after changing styles.'] },
    'non-composited-animations': { steps: ['Animate only <code>transform</code> and <code>opacity</code>; avoid animating <code>filter</code>, <code>box-shadow</code> or layout properties.'] },
    'total-byte-weight': { steps: ['Compress/resize images, enable gzip/brotli, remove unused libraries and fonts.'] },
    'server-response-time': { steps: ['Cache expensive queries with <code>Cache::remember()</code>; eager-load relations (<code>->with()</code>) to avoid N+1.', 'Run <code>php artisan config:cache route:cache view:cache</code>; enable OPcache.'], tip: 'On local WAMP it is normally slower than production.' },
    'redirects': { steps: ['Link directly to the final URL (https + correct host + trailing-slash rule) to avoid redirect chains.'] },
    'uses-rel-preconnect': { steps: ['Add <code>&lt;link rel="preconnect" href="https://origin"&gt;</code> for the 1–2 most important third-party origins.'] },
    'network-dependency-tree-insight': { steps: ['Shorten request chains: preload critical resources and avoid CSS <code>@import</code>.'] },
    'color-contrast': { steps: ['Raise text/background contrast to at least 4.5:1 (3:1 for large text).'] },
    'image-alt': { steps: ['Add a meaningful <code>alt</code> to every <code>&lt;img&gt;</code> (use <code>alt=""</code> for decorative).'] },
    'button-name': { steps: ['Give every button an accessible name: visible text or <code>aria-label</code>.'] },
    'link-name': { steps: ['Give every link descriptive text or <code>aria-label</code>.'] },
    'label': { steps: ['Associate every form input with a <code>&lt;label for&gt;</code> or <code>aria-label</code>.'] },
    'heading-order': { steps: ['Do not skip heading levels (h1 → h2 → h3). Use CSS to change size, not heading tags.'] },
    'aria-allowed-attr': { steps: ['Remove ARIA attributes that are not valid for that element role.'] },
    'tabindex': { steps: ['Avoid <code>tabindex</code> greater than 0.'] },
    'meta-description': { steps: ['Add a unique 140–160 character meta description (admin → SEO Tools → Meta Tags).'] },
    'document-title': { steps: ['Add a unique <code>&lt;title&gt;</code> of 50–60 characters (SEO Tools → Meta Tags).'] },
    'link-text': { steps: ['Replace "click here / read more" with descriptive link text.'] },
    'crawlable-anchors': { steps: ['Use real <code>&lt;a href&gt;</code> links, not JS-only click handlers.'] },
    'is-crawlable': { steps: ['Remove <code>noindex</code> from meta robots / X-Robots-Tag and check robots.txt.'], tip: 'Staging sites are often noindex on purpose — that is expected there.' },
    'canonical': { steps: ['Add a single valid absolute <code>rel="canonical"</code> pointing to the preferred URL.'] },
    'robots-txt': { steps: ['Fix syntax errors in <code>/robots.txt</code>.'] },
    'viewport': { steps: ['Add <code>&lt;meta name="viewport" content="width=device-width, initial-scale=1"&gt;</code>.'] },
    'errors-in-console': { steps: ['Open DevTools → Console, fix the listed errors (broken scripts, 404 files, CORS).'] },
    'inspector-issues': { steps: ['Open DevTools → Issues tab and resolve each listed problem.'] },
    'is-on-https': { steps: ['Serve every resource over HTTPS and redirect HTTP → HTTPS.'] },
    'csp-xss': { steps: ['Add a Content-Security-Policy header (optional hardening).'] },
    'deprecations': { steps: ['Replace the deprecated browser APIs shown below.'] },
    'bf-cache': { steps: ['Avoid <code>unload</code> handlers and <code>Cache-Control: no-store</code> on pages to allow instant back/forward navigation.'] },
    'valid-source-maps': { steps: ['Ship valid source maps or ignore (informational only).'] }
  };
  var CAT = { performance: 'Performance', accessibility: 'Accessibility', 'best-practices': 'Best practices', seo: 'SEO' };

  /* ---------- state ---------- */
  var S = {
    base: store.get('psc.base', C.bases[0].url),
    sel: new Set(), active: null, strategy: store.get('psc.strategy', 'mobile'),
    res: {}, hist: store.get('psc.hist', {}), busy: {}, err: {}, q: '', filter: 'all'
  };
  if (!C.bases.some(function (b) { return b.url === S.base; })) S.base = C.bases[0].url;
  var urlOf = function (p) { return S.base.replace(/\/$/, '') + p; };
  var key = function (p, st) { return urlOf(p) + '|' + (st || S.strategy); };
  var last = function (p, st) { var h = S.hist[key(p, st)]; return h && h.length ? h[h.length - 1] : null; };
  var labelOf = {};
  Object.keys(C.groups).forEach(function (g) { C.groups[g].forEach(function (r) { labelOf[r[0]] = r[1]; }); });

  /* ---------- running ---------- */
  var queue = [], running = 0, MAXPAR = 2;
  function enqueue(p, st, refresh) {
    var k = key(p, st); if (S.busy[k]) return;
    S.busy[k] = true; delete S.err[k];
    queue.push({ p: p, st: st, k: k, refresh: refresh }); pump(); renderAll();
  }
  function pump() {
    while (running < MAXPAR && queue.length) { (function (j) { running++; exec(j).then(function () { running--; pump(); }); })(queue.shift()); }
  }
  function exec(j) {
    var body = new FormData(); body.append('url', urlOf(j.p)); body.append('strategy', j.st); if (j.refresh) body.append('refresh', '1');
    return fetch(C.runUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': C.csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: body, credentials: 'same-origin' })
      .then(function (r) { return r.json().catch(function () { return { ok: false, message: 'Unexpected response (' + r.status + ')' + (r.status === 419 ? ' — session expired, reload the page.' : '') }; }); })
      .then(function (d) {
        if (!d.ok) { S.err[j.k] = d.message || d.error || 'Request failed'; toast((labelOf[j.p] || j.p) + ' (' + j.st + '): ' + S.err[j.k], true); return; }
        S.res[j.k] = d; saveHist(j.k, d);
      })
      .catch(function (e) { S.err[j.k] = 'Network error: ' + e.message; toast(S.err[j.k], true); })
      .then(function () { delete S.busy[j.k]; renderAll(); });
  }
  function saveHist(k, d) {
    var h = S.hist[k] || [], m = {};
    Object.keys(d.metrics || {}).forEach(function (n) { m[n] = d.metrics[n].value; });
    h.push({ t: d.fetchedAt, s: d.scores, m: m }); S.hist[k] = h.slice(-10); store.set('psc.hist', S.hist);
  }

  /* ---------- rendering ---------- */
  function renderAll() { renderRail(); renderMain(); }

  function chip(p, st) {
    var k = key(p, st), l = last(p, st), t = st === 'mobile' ? 'M' : 'D';
    if (S.busy[k]) return '<span class="psc-chip run">…<small>' + t + '</small></span>';
    if (!l) return '<span class="psc-chip">–<small>' + t + '</small></span>';
    return '<span class="psc-chip ' + cls(l.s.performance) + '">' + l.s.performance + '<small>' + t + '</small></span>';
  }
  function renderRail() {
    var list = $('.psc-list'), scroll = list ? list.scrollTop : 0, openState = {};
    if (list) list.querySelectorAll('details').forEach(function (d) { openState[d.dataset.g] = d.open; });
    var q = S.q.toLowerCase(), html = '';
    Object.keys(C.groups).forEach(function (g, gi) {
      var rows = C.groups[g].filter(function (r) { return !q || (r[0] + ' ' + r[1]).toLowerCase().indexOf(q) > -1; });
      if (!rows.length) return;
      var open = q ? true : (g in openState ? openState[g] : gi === 0);
      html += '<details class="psc-group" data-g="' + esc(g) + '"' + (open ? ' open' : '') + '><summary>' + esc(g) + '<span class="psc-count">' + rows.length + '</span><button type="button" class="psc-grun" data-grun="' + esc(g) + '" title="Run all pages in this group">Run group</button></summary>';
      rows.forEach(function (r) {
        html += '<div class="psc-row' + (S.active === r[0] ? ' is-on' : '') + '" data-p="' + esc(r[0]) + '"><input type="checkbox" data-sel="' + esc(r[0]) + '"' + (S.sel.has(r[0]) ? ' checked' : '') + ' aria-label="Select ' + esc(r[1]) + '"><span class="psc-row-name">' + esc(r[1]) + '<span class="psc-row-path">' + esc(r[0]) + '</span></span><span class="psc-chips">' + chip(r[0], 'mobile') + chip(r[0], 'desktop') + '</span></div>';
      });
      html += '</details>';
    });
    $('.psc-list').innerHTML = html || '<div class="psc-empty">No page matches “' + esc(S.q) + '”.</div>';
    $('.psc-list').scrollTop = scroll;
    var n = S.sel.size; var b = $('[data-act=runsel]'); b.disabled = !n || !C.hasKey; b.textContent = n ? 'Run ' + n + ' selected' : 'Run selected';
    $('.psc-url').textContent = 'Testing: ' + S.base;
  }

  function ring(label, v, prev) {
    var c = cls(v), r = 42, len = 2 * Math.PI * r, off = len * (1 - (v || 0) / 100);
    var d = prev != null && v != null && prev !== v ? '<span class="psc-delta ' + (v > prev ? 'up' : 'down') + '">' + (v > prev ? '▲ +' : '▼ ') + (v - prev) + '</span>' : '<span>&nbsp;</span>';
    return '<div class="psc-ring"><svg viewBox="0 0 100 100" role="img" aria-label="' + label + ' ' + v + '"><circle class="trk" cx="50" cy="50" r="' + r + '"/><circle class="bar" cx="50" cy="50" r="' + r + '" stroke="' + col(c) + '" stroke-dasharray="' + len + '" stroke-dashoffset="' + off + '"/><text x="50" y="50" fill="' + col(c) + '">' + (v == null ? '–' : v) + '</text></svg><b>' + label + '</b><small>' + d + '</small></div>';
  }
  function sparkline(arr) {
    if (arr.length < 2) return '<span class="psc-meta">Run again to build a trend.</span>';
    var w = 220, h = 54, pts = arr.map(function (v, i) { return [8 + i * (w - 16) / (arr.length - 1), h - 6 - (v / 100) * (h - 12)]; });
    return '<svg class="psc-spark" viewBox="0 0 ' + w + ' ' + h + '"><polyline points="' + pts.map(function (p) { return p[0].toFixed(1) + ',' + p[1].toFixed(1); }).join(' ') + '"/>' + pts.map(function (p) { return '<circle cx="' + p[0].toFixed(1) + '" cy="' + p[1].toFixed(1) + '" r="3"/>'; }).join('') + '</svg>';
  }

  function issueHtml(i) {
    var kb = FIX[i.id], dot = i.score != null && i.score < 0.5 ? '' : 'o', tags = '';
    if (i.display) tags += '<span class="psc-tag">' + esc(i.display) + '</span>';
    if (i.savingsMs) tags += '<span class="psc-tag sav">save ' + fmtMs(i.savingsMs) + '</span>';
    if (i.savingsBytes) tags += '<span class="psc-tag sav">save ' + fmtKb(i.savingsBytes) + '</span>';
    var desc = esc(i.description).replace(/\[([^\]]+)\]\((https?:[^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener">$1</a>');
    var fix = kb
      ? '<div class="psc-fix"><h4>✔ How to fix</h4><ol>' + kb.steps.map(function (s) { return '<li>' + s + '</li>'; }).join('') + '</ol>' + (kb.tip ? '<div class="tip"><b>In this project:</b> ' + kb.tip + '</div>' : '') + '</div>'
      : '<div class="psc-fix psc-fix--gen"><h4>How to fix</h4><div style="font-size:13px">Follow Google’s guidance in the description above (the “Learn more” link) and fix the affected items listed below.</div></div>';
    var items = '';
    if (i.items && i.items.length) {
      items = '<table class="psc-items"><thead><tr><th>Affected resource / element</th><th>Details</th></tr></thead><tbody>' + i.items.map(function (it) {
        var name = it.url || it.node || it.label || it.groupLabel || it.entity || it.selector || '';
        var det = [];
        if (it.wastedBytes) det.push('waste ' + tagBytes(it.wastedBytes)); if (it.totalBytes) det.push('size ' + tagBytes(it.totalBytes)); if (it.transferSize) det.push('transfer ' + tagBytes(it.transferSize));
        if (it.wastedMs) det.push('save ' + fmtMs(it.wastedMs)); if (it.blockingTime) det.push('blocking ' + fmtMs(it.blockingTime)); if (it.total) det.push('total ' + fmtMs(it.total)); if (it.duration) det.push(fmtMs(it.duration)); if (it.score != null && typeof it.score === 'number') det.push('score ' + it.score.toFixed(3)); if (it.entity && it.url) det.push(esc(it.entity));
        return '<tr><td><code>' + esc(name) + '</code>' + (it.selector && it.node ? '<br><small>' + esc(it.selector) + '</small>' : '') + '</td><td class="n">' + det.join(' · ') + '</td></tr>';
      }).join('') + '</tbody></table>';
    }
    return '<details class="psc-issue" data-cat="' + i.cat + '"><summary><span class="psc-dot ' + dot + '"></span><span class="name">' + esc(i.title) + '</span>' + tags + '<span class="psc-tag">' + CAT[i.cat] + '</span></summary><div class="psc-body"><p class="psc-why">' + desc + '</p>' + fix + items + '</div></details>';
  }

  function priority(d) {
    var perf = d.issues.filter(function (i) { return i.cat === 'performance' && (i.savingsMs || i.savingsBytes); })
      .sort(function (a, b) { return (b.savingsMs - a.savingsMs) || (b.savingsBytes - a.savingsBytes); }).slice(0, 3);
    var other = d.issues.filter(function (i) { return i.cat !== 'performance' && i.score === 0; }).slice(0, 2);
    var list = perf.concat(other);
    if (!list.length) return '<div class="psc-card psc-prio psc-good"><h3>🎉 Nothing big to fix</h3><div>No high-impact issues were found for this run.</div></div>';
    return '<div class="psc-card psc-prio"><h3>Fix these first</h3><ol>' + list.map(function (i) {
      var s = i.savingsMs ? ' — saves about <b>' + fmtMs(i.savingsMs) + '</b>' : i.savingsBytes ? ' — saves about <b>' + fmtKb(i.savingsBytes) + '</b>' : '';
      return '<li>' + esc(i.title) + s + '</li>';
    }).join('') + '</ol></div>';
  }

  function renderMain() {
    var el = $('.psc-main');
    if (!S.active) { el.innerHTML = '<div class="psc-card psc-empty"><h3>Pick a page to test</h3><p>Choose a page on the left and press <b>Run</b>. You can also tick several pages (or press “Run group”) to test them all at once.</p><p><button class="psc-btn psc-btn--accent" data-act="home">Test the home page</button></p></div>'; return; }
    var p = S.active, k = key(p), d = S.res[k], h = S.hist[k] || [];
    var head = '<div class="psc-card psc-head"><div><h2>' + esc(labelOf[p] || p) + '</h2><a href="' + esc(urlOf(p)) + '" target="_blank" rel="noopener">' + esc(urlOf(p)) + ' ↗</a><div class="psc-meta">' + (d ? 'Tested ' + ago(d.fetchedAt) + ' · Lighthouse ' + esc(d.lighthouse) + (d.cached ? ' · cached result' : '') : 'Not tested yet') + '</div></div>' +
      '<div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center"><div class="psc-tabs" role="tablist">' +
      ['mobile', 'desktop'].map(function (st) { var l = last(p, st); return '<button class="psc-tab' + (S.strategy === st ? ' is-on' : '') + '" data-st="' + st + '" role="tab">' + (st === 'mobile' ? '📱 Mobile' : '🖥 Desktop') + (l ? ' <span class="psc-chip ' + cls(l.s.performance) + '">' + l.s.performance + '</span>' : '') + '</button>'; }).join('') +
      '</div><button class="psc-btn psc-btn--accent" data-act="run"' + (S.busy[k] || !C.hasKey ? ' disabled' : '') + '>' + (d ? '↻ Re-run' : '▶ Run test') + '</button><button class="psc-btn" data-act="both"' + (!C.hasKey ? ' disabled' : '') + '>Run mobile + desktop</button></div></div>';
    var body = '';
    if (S.busy[k]) body = '<div class="psc-card psc-progress"><div class="psc-spin"></div><b>Analysing ' + esc(S.strategy) + ' …</b><div class="psc-meta">Google needs 20–60 seconds per test. You can start other pages meanwhile.</div></div>';
    else if (S.err[k]) body = '<div class="psc-error"><b>Could not run the test.</b><div>' + esc(S.err[k]) + '</div></div>';
    else if (!d) body = '<div class="psc-card psc-empty"><h3>No result for ' + esc(S.strategy) + ' yet</h3><p>Press <b>Run test</b> to get scores, issues and how to fix them.</p></div>';
    if (d && !S.busy[k]) body += S.err[k] ? '' : resultHtml(d, h);
    el.innerHTML = head + body;
    if (d) requestAnimationFrame(function () { el.querySelectorAll('.psc-ring .bar').forEach(function (b) { b.style.strokeDashoffset = b.getAttribute('stroke-dashoffset'); }); });
  }

  function resultHtml(d, h) {
    var prev = h.length > 1 ? h[h.length - 2] : null, s = d.scores, out = '';
    out += priority(d);
    out += '<div class="psc-card"><div class="psc-scores">' + ring('Performance', s.performance, prev && prev.s.performance) + ring('Accessibility', s.accessibility, prev && prev.s.accessibility) + ring('Best practices', s['best-practices'], prev && prev.s['best-practices']) + ring('SEO', s.seo, prev && prev.s.seo) + '</div></div>';
    out += '<div class="psc-card"><h3 class="psc-sec-title">Core Web Vitals &amp; speed metrics</h3><div class="psc-vitals">' + METRICS.map(function (m) {
      var x = d.metrics[m.k]; if (!x) return ''; var v = x.value, c = rate(m, v), pct = v == null ? 0 : Math.min(100, v / (m.poor * 1.4) * 100);
      return '<div class="psc-vital psc-' + c + '"><div class="t">' + m.k + ' · ' + m.name + '</div><div class="v">' + fmtMetric(m, v) + '</div><div class="g">Target ' + m.target + '</div><div class="bar"><i style="width:' + pct + '%"></i></div></div>';
    }).join('') + '</div>' +
      '<div class="psc-field-data"><span class="psc-pill">Page weight ' + (d.weight ? fmtKb(d.weight) : '–') + '</span><span class="psc-pill">' + (d.requests || 0) + ' requests</span>' + (d.finalUrl && d.finalUrl !== d.url ? '<span class="psc-pill o">Redirected to ' + esc(d.finalUrl) + '</span>' : '') + '</div></div>';
    if (d.field) {
      var map = { LARGEST_CONTENTFUL_PAINT_MS: 'LCP', FIRST_CONTENTFUL_PAINT_MS: 'FCP', CUMULATIVE_LAYOUT_SHIFT_SCORE: 'CLS', INTERACTION_TO_NEXT_PAINT: 'INP' };
      out += '<div class="psc-card"><h3 class="psc-sec-title">Real-user data (Chrome UX Report, ' + (d.field.scope === 'page' ? 'this page' : 'whole site') + ')</h3><div class="psc-field-data">' + Object.keys(d.field.metrics).map(function (k) {
        var m = d.field.metrics[k], c = m.category === 'FAST' ? 'g' : m.category === 'AVERAGE' ? 'o' : 'r', v = m.p75;
        var txt = k.indexOf('LAYOUT') > -1 ? (v / 100).toFixed(2) : fmtMs(v);
        return '<span class="psc-pill ' + c + '">' + (map[k] || k) + ' p75 ' + txt + '</span>';
      }).join('') + '</div></div>';
    } else out += '<div class="psc-card"><div class="psc-field-data" style="padding-top:14px">No real-user (CrUX) data for this URL — Google only has it for pages with enough traffic.</div></div>';
    if (d.filmstrip && d.filmstrip.length) out += '<div class="psc-card"><h3 class="psc-sec-title">How the page loads</h3><div class="psc-film">' + d.filmstrip.map(function (f) { return '<figure><img src="' + esc(f.data) + '" alt=""><figcaption>' + fmtMs(f.t) + '</figcaption></figure>'; }).join('') + '</div></div>';

    var counts = { all: d.issues.length }; d.issues.forEach(function (i) { counts[i.cat] = (counts[i.cat] || 0) + 1; });
    var order = { performance: 0, accessibility: 1, 'best-practices': 2, seo: 3 };
    var issues = d.issues.filter(function (i) { return S.filter === 'all' || i.cat === S.filter; }).sort(function (a, b) { return (order[a.cat] - order[b.cat]) || (b.savingsMs - a.savingsMs) || (b.savingsBytes - a.savingsBytes) || ((a.score || 0) - (b.score || 0)); });
    out += '<div class="psc-card"><h3 class="psc-sec-title">Issues &amp; how to fix (' + d.issues.length + ')</h3><div class="psc-filters">' + ['all', 'performance', 'accessibility', 'best-practices', 'seo'].map(function (f) { return '<button class="psc-f' + (S.filter === f ? ' is-on' : '') + '" data-f="' + f + '">' + (f === 'all' ? 'All' : CAT[f]) + ' ' + (counts[f] || 0) + '</button>'; }).join('') + '</div><div class="psc-issues">' + (issues.length ? issues.map(issueHtml).join('') : '<div class="psc-ok">✔ No issues in this category.</div>') + '</div></div>';

    out += '<div class="psc-card"><h3 class="psc-sec-title">History (' + S.strategy + ')</h3><div class="psc-hist">' + sparkline(h.map(function (x) { return x.s.performance; })) + '<table><thead><tr><th>When</th><th>Perf</th><th>A11y</th><th>BP</th><th>SEO</th><th>LCP</th></tr></thead><tbody>' + h.slice().reverse().slice(0, 6).map(function (x) { return '<tr><td>' + ago(x.t) + '</td><td>' + x.s.performance + '</td><td>' + x.s.accessibility + '</td><td>' + x.s['best-practices'] + '</td><td>' + x.s.seo + '</td><td>' + fmtMs(x.m.LCP) + '</td></tr>'; }).join('') + '</tbody></table></div></div>';
    return out;
  }

  /* ---------- exports ---------- */
  function download(name, text, type) { var a = document.createElement('a'); a.href = URL.createObjectURL(new Blob([text], { type: type })); a.download = name; a.click(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 1000); }
  function rows() {
    var out = [];
    Object.keys(S.hist).forEach(function (k) { var l = S.hist[k][S.hist[k].length - 1], pr = k.split('|'); if (l && pr[0].indexOf(S.base) === 0) out.push([pr[0], pr[1], l.t, l.s.performance, l.s.accessibility, l.s['best-practices'], l.s.seo, l.m.FCP, l.m.LCP, l.m.TBT, l.m.CLS, l.m.SI]); });
    return out;
  }
  function reportText() {
    var r = rows(); if (!r.length) return 'No results yet.';
    return 'PageSpeed report — ' + S.base + '\n' + r.map(function (x) { return x[1][0].toUpperCase() + ' ' + x[0] + ' → Perf ' + x[3] + ' · A11y ' + x[4] + ' · BP ' + x[5] + ' · SEO ' + x[6] + ' · LCP ' + fmtMs(x[8]) + ' · CLS ' + (x[10] == null ? '–' : (+x[10]).toFixed(3)); }).join('\n');
  }

  /* ---------- skeleton + events ---------- */
  root.innerHTML = '<aside class="psc-card psc-rail"><div class="psc-rail-top">' +
    '<div class="psc-field"><label for="psc-base">Site to test</label><select id="psc-base">' + C.bases.map(function (b) { return '<option value="' + esc(b.url) + '"' + (b.url === S.base ? ' selected' : '') + '>' + esc(b.label) + ' — ' + esc(b.url) + '</option>'; }).join('') + '</select><div class="psc-url"></div></div>' +
    '<div class="psc-field"><label for="psc-q">Find a page</label><input id="psc-q" type="search" placeholder="Search pages…" autocomplete="off"></div>' +
    '<div class="psc-actions"><button class="psc-btn psc-btn--primary" data-act="runsel" disabled>Run selected</button><button class="psc-btn" data-act="mobsel" title="Check/uncheck all visible">Select all</button><button class="psc-btn" data-act="failed" title="Re-run pages scoring below 90">Re-run &lt; 90</button></div>' +
    '<div class="psc-actions"><button class="psc-btn" data-act="csv">Export CSV</button><button class="psc-btn" data-act="json">JSON</button><button class="psc-btn" data-act="copy">Copy report</button><button class="psc-btn" data-act="clear">Clear history</button></div>' +
    '</div><div class="psc-list"></div></aside><main class="psc-main"></main>';

  root.addEventListener('click', function (e) {
    var t = e.target, a = t.closest('[data-act]'), row = t.closest('.psc-row');
    if (t.matches('[data-sel]')) { var p = t.dataset.sel; t.checked ? S.sel.add(p) : S.sel.delete(p); renderRail(); return; }
    var g = t.closest('[data-grun]');
    if (g) { e.preventDefault(); e.stopPropagation(); var rowsG = C.groups[g.dataset.grun] || []; rowsG.forEach(function (r) { enqueue(r[0], S.strategy); }); toast('Started ' + rowsG.length + ' ' + S.strategy + ' tests'); return; }
    if (row) { S.active = row.dataset.p; S.filter = 'all'; renderAll(); return; }
    var st = t.closest('[data-st]'); if (st) { S.strategy = st.dataset.st; store.set('psc.strategy', S.strategy); renderAll(); return; }
    var f = t.closest('[data-f]'); if (f) { S.filter = f.dataset.f; renderMain(); return; }
    if (!a) return;
    var act = a.dataset.act;
    if (act === 'home') { S.active = '/'; S.sel.add('/'); enqueue('/', S.strategy); }
    else if (act === 'run') enqueue(S.active, S.strategy, true);
    else if (act === 'both') { enqueue(S.active, 'mobile', true); enqueue(S.active, 'desktop', true); }
    else if (act === 'runsel') { S.sel.forEach(function (p) { enqueue(p, S.strategy); }); toast('Started ' + S.sel.size + ' ' + S.strategy + ' tests'); }
    else if (act === 'mobsel') { var all = [].concat.apply([], Object.keys(C.groups).map(function (g2) { return C.groups[g2].map(function (r) { return r[0]; }); })); var vis = all.filter(function (p) { return !S.q || (p + ' ' + labelOf[p]).toLowerCase().indexOf(S.q.toLowerCase()) > -1; }); var allOn = vis.every(function (p) { return S.sel.has(p); }); vis.forEach(function (p) { allOn ? S.sel.delete(p) : S.sel.add(p); }); renderRail(); }
    else if (act === 'failed') { var n = 0; Object.keys(labelOf).forEach(function (p) { var l = last(p, S.strategy); if (l && l.s.performance < 90) { enqueue(p, S.strategy, true); n++; } }); toast(n ? 'Re-running ' + n + ' pages below 90' : 'No tested page is below 90'); }
    else if (act === 'csv') { var r = rows(); if (!r.length) return toast('Nothing to export yet', true); download('pagespeed-' + new Date().toISOString().slice(0, 10) + '.csv', 'url,device,tested_at,performance,accessibility,best_practices,seo,fcp_ms,lcp_ms,tbt_ms,cls,si_ms\n' + r.map(function (x) { return x.map(function (v) { return '"' + (v == null ? '' : v) + '"'; }).join(','); }).join('\n'), 'text/csv'); }
    else if (act === 'json') { download('pagespeed-history.json', JSON.stringify(S.hist, null, 2), 'application/json'); }
    else if (act === 'copy') { var txt = reportText(); (navigator.clipboard ? navigator.clipboard.writeText(txt) : Promise.reject()).then(function () { toast('Report copied'); }, function () { toast('Copy failed — select manually', true); }); }
    else if (act === 'clear') { if (confirm('Delete saved PageSpeed history from this browser?')) { S.hist = {}; S.res = {}; store.set('psc.hist', {}); renderAll(); } }
  });
  $('#psc-base').addEventListener('change', function (e) { S.base = e.target.value; store.set('psc.base', S.base); renderAll(); });
  $('#psc-q').addEventListener('input', function (e) { S.q = e.target.value; renderRail(); });
  if (!C.hasKey) toast('Add PAGESPEED_API_KEY to .env to enable tests', true);
  renderAll();
})();
