# Performance playbook (what worked on the V2 pages, staging mobile 94 -> 76 -> far better)

Use this when checking or fixing any other page. Targets are in `performance.md`. Build/regenerate steps are in CLAUDE.md ("Asset build").

## Method
1. Read the PageSpeed report first and sort findings by metric: TBT (main-thread work), CLS (shifts), FCP/LCP (blocking CSS, images), then diagnostics.
2. Reproduce locally in headless Chrome over the DevTools protocol (4x CPU throttle): long tasks, `Performance.getMetrics`, a trace with layout / style-recalc events and invalidation reasons, a CPU profile by function (temporarily move the `.min.js` away to get real line numbers). Compare with the last commit using `git stash -u` (Git Bash only; restore with `git stash pop`). Local numbers are noisy: repeat runs and compare trends, not single values.
3. Change one group at a time, rebuild (`tools/build-assets.ps1`, then `tools/make-critical-all.ps1`), run PHPUnit, screenshot with stylesheets blocked, then ask for a new staging report.
4. Every change gets a test in `tests/Feature/Seo/AssetPipelineTest.php` (or the matching test file) that pins the code pattern, so a later edit cannot silently undo it.

## Rules learned (each one was a real regression or win here)
- **Third-party tags:** first click/key/form focus, or 8 s after load. Never earlier: a 3 s fallback landed inside the test window (TBT 40 -> 210 ms).
- **Heavy init goes late or on demand:** phone widget + 300 KB library (6 s / on form focus), Swiper (8.5 s / near view), marquee logos (IntersectionObserver ~1.5 screens). Provide a fallback (the form submits the typed number if the widget is not built).
- **No layout reads while scripts start:** no `scrollY`, `offsetHeight`, `getBoundingClientRect` at init. Run first effects in a frame after the loader curtain. Classify/measure with IntersectionObserver or ResizeObserver entries, not rect loops.
- **Avoid `toLocaleString`/`Intl` in hot paths** (builds a formatter per call, ~50 ms first use on phones).
- **Custom properties written every frame must be registered `@property ... inherits:false`**, otherwise every element is restyled.
- **Critical CSS:** inline only first-screen CSS, make every other stylesheet non-blocking, and switch them on together (`v2cs()` in master) - applying sheets one by one caused a full recalculation per sheet (TBT 650 ms). Collect rules down to ~1.6 screens, not 1: a box that starts in the first screen but ends below it changes height when the rest of its CSS arrives (CLS 0.134 on the hero form). Keep base rules of hidden-until-used UI (drawer, modals) and of `display:none` elements (judge them by their nearest visible ancestor).
- **Minify for real** (esbuild); a `.min` that is a copy of the source triggers the "Minify" audits.
- **Images:** serve the size actually displayed (x2 at most) as WebP; keep width/height attributes matching the real aspect ratio.
- **Contrast:** coral `#e04a43` fails 4.5:1 on light backgrounds; use `#b8302a` for small coral text on light sections.
- Inlined CSS makes the HTML bigger (45-60 KB): keep HTML compression on in `.htaccess`.

## Still open / ideas not done
Trimmed Bootstrap for non-service pages (26 KiB unused), Swiper JS size, client-logo/service image recompression (~7 KiB), style-recalc cost of the ~2,400-element home DOM.

## Round 3 additions (mobile 96)
- Swiper (38 KB, unused at first paint) is a delayed script on every page; the home carousel loads it itself near view / at 8.5 s, and the reviews panel retries until it arrives.
- Read page/hero height inside a `ResizeObserver` callback (layout is clean there); effects for off-screen elements wait for their IntersectionObserver instead of reading rects at start-up.
- Re-encoding already-optimised WebP (client logos) at a smaller size made them BIGGER; check the byte size before replacing. Recompress from the PNG source instead (service/1-3.webp: 17 -> 13 KB).
- Local measurements are poisoned by leftover headless Chrome processes (58 once!): the helper scripts must always stop their own Chrome; check `remote-debugging-port` processes before trusting numbers. Never kill all `chrome.exe` (it is the user's browser too).
- Remaining PageSpeed items that are not worth the risk: unused Bootstrap CSS (non-blocking, byte-only), client logo "oversized" notice (DPR makes 300 px right for phones), Speed Index (~3.4 s: loader + hero).

## Round 4 (the same code scored 96 then 79: PSI is noisy, so remove main-thread work, not just chase one report)
- The trace showed ~2,600 "Animation" style recalculations on `<img>` and ~2,100 on `::before`: image reveals used a `blur()` + scale transition and every unloaded image had an endless shimmer. Now: opacity+scale reveal only, static placeholder, client logos (88 tiles) have no reveal at all. Local blocking-time estimate fell from ~400 ms to ~250 ms.
- Header "scrolled" state uses an IntersectionObserver marker, not a scroll listener + `scrollY` (forced-reflow source). Page height / hero height come from ResizeObserver entries; cached `geo.vh` instead of `window.innerHeight` in effects.
- Muted text colours raised (light .76, dark .78) so axe/Lighthouse never reports contrast when elements are mid-fade.
- Phone input has a plain placeholder until the widget is built late.

## Round 5 (mobile 85, desktop 91; PSI still shows service images at the OLD size)
- **Images are cached for a year (browser + Hostinger CDN) under the same URL**, so a re-encoded file is never fetched again: the report kept showing service/2.webp at 17.8 KiB after it was already 11 KB locally. `v2_img()`, the client logos and the header/footer logo now append `?v=<filemtime>`. Always version re-encoded images this way.
- `window.innerHeight`, `documentElement.scrollHeight` and `offsetHeight` each forced a layout (131 ms / 44 ms / 187 ms in the report) - even at start-up. Viewport height now comes from a ResizeObserver on an invisible fixed 100lvh marker, page height from a body observer, header height from a header observer (first report skipped: it equals the CSS default).
- (Tried and reverted: content-visibility on clients/stats/solutions - those sections stay normal so anchor jumps land right; see ScrollPerformanceTest.)
- Local A/B runs drift with machine load across the day (same code: TBT estimate 250 -> 700): compare variants back to back, never against an earlier session.

## Speed Index (staging 98/98 but SI 3.7 s while LCP was 1.5 s)
- A gap between LCP and Speed Index means the first screen changes after first paint. Cause here: the hero was visible in the static HTML, then
  `home-v2.js` (deferred, 2-3 s on a slow phone) added `.hv2-js`, which hid the hero (opacity 0) and faded it back in ~0.5 s later: visible -> blank -> visible.
  **Never hide above-the-fold content behind a class that deferred JS adds.** The pre-reveal state now lives only under `html.hv2-loading`
  (set by the inline head script before first paint, removed after 0.5 s).
- Reproduce with `late_js`-style test: block the script, inject it at 3 s, log opacity of the hero pieces every frame (before: 1 -> 0 -> 1; after: 1 throughout).
- **Critical CSS + async stylesheets are OFF** (`config/v2.php`, `V2_CRITICAL_CSS`): PageSpeed scored 76-85 with it and 98/98 without. The mode used to be chosen by
  file timestamps, so a deploy could flip it; it is now an explicit switch. Never depend on mtimes for behaviour.

## Round 6 (mobile 97, SI 4.3 in one UI run vs 3.1-3.4 in five API runs of the same code)
- **Judge changes with several runs.** `php artisan seo:pagespeed --base=staging --strategy=both --path=/ --fresh` (run it 3x, ~100 s apart; Google caches
  identical requests for a short while) gave mobile 96-99, SI 3.1-3.4 s, TBT 0-190 ms, desktop 97-99 on the same deployed code. The PageSpeed web UI showed 4.3 s once
  (its server response took 530 ms instead of ~190 ms). A single report cannot show that a change helped or hurt Speed Index.
- Lighthouse's mobile Speed Index is simulated from layout/paint work after FCP: a smaller render-blocking CSS payload and no late hide/show of the hero are the safe levers.
- `v2_asset()` now picks the `.min` file by content (md5 manifest `public/assets/min-manifest.json`, written by `tools/build-assets.ps1`), never by timestamps;
  the phone-input CSS is added by `v2IntlCss()` when the widget is built (or when the delayed scripts are released); on a tap in the form the phone
  library is fetched immediately (widget in ~0.5 s) instead of waiting for the delayed-script queue; the loader logo is a 8.7 KB WebP.
- Local Speed Index meter: `speedindex.ps1` (Slow-4G + CPU throttle + screencast) is only useful for before/after on the same machine; it shows the home page is
  visually complete at first paint (SI = FCP) with the hero fix.
