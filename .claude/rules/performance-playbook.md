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
