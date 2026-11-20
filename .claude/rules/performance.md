---
paths:
  - "resources/views/**/*.blade.php"
  - "resources/js/**/*"
  - "resources/css/**/*"
  - "app/Http/Controllers/**/*.php"
  - "vite.config.*"
---

# PageSpeed Insights & Lighthouse Mobile Targets (95–100 Score)

## Strict Metric Thresholds (Emulated Mobile / Slow 4G)
- **First Contentful Paint (FCP):** Must be **≤ 1.8 s** (ideal: **≤ 1.2 s**).
- **Largest Contentful Paint (LCP):** Must be **≤ 2.5 s** (ideal: **≤ 2.0 s**).
- **Total Blocking Time (TBT):** Must be **≤ 200 ms** (keep **< 100 ms**).
- **Cumulative Layout Shift (CLS):** Must be **0** (strictly **≤ 0.1**).
- **Speed Index (SI):** Must be **≤ 3.4 s** (ideal: **≤ 2.5 s**).

## Laravel & Frontend Implementation Rules
- **Fix FCP (≤ 1.8 s):** Eliminate render-blocking CSS/JS in `<head>`. Always load Vite assets via `@vite([...])` (module/deferred) or add `defer` to external scripts. Preload primary WOFF2 fonts with `<link rel="preload" as="font" type="font/woff2" crossorigin>` and `font-display: swap`.
- **Fix LCP (≤ 2.5 s):** Never use `loading="lazy"` on above-the-fold hero images. Add `fetchpriority="high"` and `decoding="sync"` to the LCP image, preload it in `<head>` when critical, and serve compressed WebP/AVIF formats with responsive `srcset`/`sizes`.
- **Preserve CLS (0):** Always declare explicit `width` and `height` attributes (or CSS `aspect-ratio`) on every `<img>`, `<video>`, `<iframe>`, and `<svg>`. Reserve container heights for dynamic banners or components.
- **Preserve TBT (< 100 ms):** Avoid heavy third-party JS libraries; use lightweight Alpine.js or native CSS transitions, and code-split non-critical scripts.
- **Keep TTFB Fast:** Eager-load all Eloquent relationships (`->with([...])`) to prevent N+1 queries, select only needed columns (`->select([...])`), and cache expensive read queries with `Cache::remember()`.
