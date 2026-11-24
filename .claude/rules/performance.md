---
paths:
  - "resources/views/**/*.blade.php"
  - "resources/js/**/*"
  - "resources/css/**/*"
  - "app/Http/Controllers/**/*.php"
  - "vite.config.*"
---

# PageSpeed Insights & Lighthouse Targets — Mobile and Desktop (95–100 Score)

The limits below are the project's accepted results (the levels the redesigned pages reach on staging). Anything above a limit is a
regression to fix. They are enforced by `PageSpeedService::TARGETS_MOBILE` / `TARGETS_DESKTOP` (used by `php artisan seo:pagespeed`
and `tests/Feature/Seo/PageSpeedTest.php`): change both places together.

## Strict Metric Thresholds
| Metric | Mobile (Slow 4G) | Desktop |
|---|---|---|
| First Contentful Paint (FCP) | **≤ 1.7 s** | **≤ 0.8 s** |
| Largest Contentful Paint (LCP) | **≤ 2.4 s** | **≤ 1.1 s** |
| Total Blocking Time (TBT) | **≤ 190 ms** | **≤ 140 ms** |
| Cumulative Layout Shift (CLS) | **≤ 0.1** (aim for 0) | **≤ 0.1** (aim for 0) |
| Speed Index (SI) | **≤ 3.3 s** | **≤ 1.2 s** |

## Laravel & Frontend Implementation Rules
- **Fix FCP (limits in the table above):** Eliminate render-blocking CSS/JS in `<head>`. Add `defer` to external scripts (this project does not use Vite, so ignore any `@vite` guidance). Preload primary WOFF2 fonts with `<link rel="preload" as="font" type="font/woff2" crossorigin>` and `font-display: swap`.
- **Fix LCP (limits in the table above):** Never use `loading="lazy"` on above-the-fold hero images. Add `fetchpriority="high"` and `decoding="sync"` to the LCP image, preload it in `<head>` when critical, and serve compressed WebP/AVIF formats with responsive `srcset`/`sizes`.
- **Preserve CLS (0):** Always declare explicit `width` and `height` attributes (or CSS `aspect-ratio`) on every `<img>`, `<video>`, `<iframe>`, and `<svg>`. Reserve container heights for dynamic banners or components.
- **Preserve TBT (limits in the table above; lower is better):** Avoid heavy third-party JS libraries; use lightweight Alpine.js or native CSS transitions, and code-split non-critical scripts.
- **Keep TTFB Fast:** Eager-load all Eloquent relationships (`->with([...])`) to prevent N+1 queries, select only needed columns (`->select([...])`), and cache expensive read queries with `Cache::remember()`.
