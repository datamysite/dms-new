# DataMySite — project guide

## Stack
- **Backend:** PHP ^8.2, Laravel ^11.31 (Blade views, Eloquent, database cache/session drivers). Served by Apache (WAMP) at `http://localhost/datamysite`, not `php artisan serve`.
- **Frontend:** server-rendered Blade + static assets in `public/assets/{css,js,img,fonts,vendor}` (Bootstrap 5, bootstrap-icons, Swiper, intl-tel-input). `package.json` has Vite + Tailwind + `laravel-vite-plugin`, but **no view currently uses `@vite`**; pages link the static files directly (versioned with `?v=filemtime`, see `v2_asset()` in `resources/views/web/home-v2/helpers.php`).
- **Content:** service-page sections, meta tags and blogs live in the database (`service_sections`, `meta_tags`, `blogs`).

## Performance rules
Mobile (Slow 4G) PageSpeed/Lighthouse targets and implementation rules are in [`.claude/rules/performance.md`](.claude/rules/performance.md) — follow them for any change to views, CSS/JS or controllers.

Project-specific notes for applying them:
- Redesigned (V2) pages extend `web.home-v2.master`. A page opts in to the fast loading mode with `@section('v2lite', '1')` (third-party scripts wait for the first interaction), `@section('v2form', '1')` (keep the phone-input library eager) and `@section('v2keep', '1')` (keep Swiper eager).
- Minified `*.min.css` / `*.min.js` are served through `v2_asset()` and are rebuilt after editing a source asset.
- Images: keep WebP siblings (`name.webp`, `name-w480/960/1440.webp`) and use `v2_img()` for `<img>` attributes (width/height + srcset).
- Never animate layout properties (`left/top/height/width/background-position`); use `transform`/`opacity`. Avoid large `filter: blur()`/`backdrop-filter` layers.

## Conventions
- Redesigned page set: each page has its own view, CSS and JS (`<page>-v2.*`), shares the Home V2 design system (`home-v2.css`/`home-v2.js`), brand colours only, and keeps the original URLs and SEO text.
- Do not change backend logic, routes or the database when the task is a frontend/performance task unless asked.
