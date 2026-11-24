# DataMySite — project guide

## Stack
- **Backend:** PHP ^8.2, Laravel ^11.31 (Blade views, Eloquent, database cache/session drivers). Served by Apache (WAMP) at `http://localhost/datamysite`, not `php artisan serve`.
- **Frontend:** server-rendered Blade + static assets in `public/assets/{css,js,img,fonts,vendor}` (Bootstrap 5, bootstrap-icons, Swiper, intl-tel-input). **Vite is not used** (no view uses `@vite`, even though `package.json` lists it); pages link the static files directly, versioned with `?v=filemtime` (see `v2_asset()` in `resources/views/web/home-v2/helpers.php`). Ignore any Vite/`@vite` instructions.
- **Content:** service-page sections, meta tags and blogs live in the database (`service_sections`, `meta_tags`, `blogs`).

## Performance rules
Mobile (Slow 4G) PageSpeed/Lighthouse targets and implementation rules are in [`.claude/rules/performance.md`](.claude/rules/performance.md) — follow them for any change to views, CSS/JS or controllers.

Project-specific notes for applying them (no Vite: use `defer` and the static-asset approach below):
- Redesigned (V2) pages extend `web.home-v2.master`. A page opts in to the fast loading mode with `@section('v2lite', '1')` (third-party scripts wait for the first interaction), `@section('v2form', '1')` (keep the phone-input library eager) and `@section('v2keep', '1')` (keep Swiper eager).
- Minified `*.min.css` / `*.min.js` are served through `v2_asset()` and are rebuilt after editing a source asset.
- Images: keep WebP siblings (`name.webp`, `name-w480/960/1440.webp`) and use `v2_img()` for `<img>` attributes (width/height + srcset).
- Never animate layout properties (`left/top/height/width/background-position`); use `transform`/`opacity`. Avoid large `filter: blur()`/`backdrop-filter` layers.

### Asset build (no Node on this PC)
- **Minified assets:** after editing any source in `public/assets/{css,js}` run `powershell -ExecutionPolicy Bypass -File tools/build-assets.ps1`. It uses a standalone esbuild (`tools/esbuild/`, git-ignored; download link and sha512 are in the script header) and writes `*.min.css/js`. `v2_asset()` serves a `.min` only while it is not older than its source (60 s slack for deploys).
- **Critical CSS:** each V2 page declares `@section('v2crit', 'assets/css/<name>.critical.css')`; the file is inlined and every stylesheet loads non-blocking. Regenerate after any CSS change (and after the build above): `powershell -ExecutionPolicy Bypass -File tools/make-critical-all.ps1` (headless Chrome, needs the local site running). A critical file older than a stylesheet is ignored (normal blocking CSS is used), so a stale file is slow but never broken. Hidden-until-used UI (drawer, popups) is kept by the generator's `KEEP` list.
- **Images:** `tools/png-to-webp.ps1` converts/resizes a PNG to WebP with Chrome (e.g. `logo-white.webp`).
- Loading policy (do not change without re-measuring): third-party tags start on the first click/key/form focus or 8 s after load; the phone widget (+ its 300 KB library) is built 6 s after load or when the form is touched; Swiper at 8.5 s or when near view; no layout reads while scripts start (first effect run happens after the loader curtain).
- Measured before/after on this PC (4x CPU throttle, home, mobile) is noisy; use `php artisan seo:pagespeed` against staging for real numbers.

## Conventions
- Redesigned page set: each page has its own view, CSS and JS (`<page>-v2.*`), shares the Home V2 design system (`home-v2.css`/`home-v2.js`), brand colours only, and keeps the original URLs and SEO text.
- Do not change backend logic, routes or the database when the task is a frontend/performance task unless asked.
