# DataMySite — codebase map (as of 2026-11-26, last commit 9ea5b48 + uncommitted service-v2 work)

Read this before exploring. Stack, performance rules, asset build and staging links are in `CLAUDE.md` (not repeated here).
**Update this file** when you add/move routes, controllers, views, assets, configs, tools or tests.

## Request pipeline (`bootstrap/app.php`)
1. `RewriteProductionUrls` (prepended, outermost): rewrites absolute live links and redirect `Location` to `APP_URL` off production; adds the sub-folder prefix locally.
2. Web group: `RedirectFirstPage`, `AttachLiteSpeedPurge`. Global append: `HandleDbRedirects` (redirects from DB table `redirects`), `StagingNoindex`.
3. CSRF exceptions: `api/chat`, `api/lead`, `api/track`. Alias `verify.api.token` → `VerifyApiToken` (for `/api/v1`).
- `routes/web.php` prepends the view path `resources/views/_v2-overrides` (lets a file there override a view of the same name).
- `routes/redirects.php` is an empty emergency fallback; redirects live in the DB (manage via API/MCP). `config/redirects.php` = settings.

## Routes
### `routes/web.php` (namespace `App\Http\Controllers\web` unless noted)
| URL | Handler → view |
|---|---|
| `/` | `WebController@indexNew` → `web.home-v2` |
| `/services` | `WebController@servicesNew` → `web.services-landing-v2` |
| `/faq`, `/about-us`, `/contact-us`, `/privacy-policy`, `/terms-conditions`, `/client-case-studies` | `WebController@{faq,about,contact,privacy,terms,caseStudies}New` → `web.<page>-v2` |
| `/blogs`, `/blogs/{slug}` (category), `/tag/{slug}` | `BlogController@indexNew/categoryNew/tagsNew` → `web.blogs-v2` |
| `/blogs/categories` | `BlogController@allCategoriesNew` → `web.blogs-categories-v2` |
| `/{blog_slug}` | `BlogController@detailsNew` → `web.blog-detail-v2` — **catch-all, must stay the last web route** |
| ~100 service URLs (`/outdoor-advertising/...`, `/transit-media/...`, `/seo-services/...`, `/radio-advertising/...` etc.) | `ServiceController@<camelName>` → `web.services-new.master-service` |
| `/v2/*`, `/*-new`, `/blogs-new/*`, `/home-new` | 301 to the original URL (old previews) |
| POST `/enquiry`, `/enquiryHelp`, `/enquiryLite` | `EnquiryController` |
| POST `/enquiry/planner` (`throttle:10,1`) | `EnquiryController@planner` + `EnquiryPlannerRequest` (extends `EnquiryRequest`): lead form of the service sections (adds budget and `dates` → `preferred_dates`; no file upload). `service` is fixed server-side ("Outdoor Advertising"). The DB section form gets its URL from `{{ route('enquiry.planner.submit') }}` + `@csrf` (marked `form.sv2-lead-form`, the only form the service template keeps). JSON answer: `{success, message, reference (enquiry id), emailed (visitor confirmation mail sent)}`. |
| POST `/subscribe`, GET `/unsubscribe` | `NewsletterController` |
| `/get-google-reviews` | `ReviewController` (`Services/GooglePlaces`) |
| `/get-aside` | `WebController@getAside` → `web.includes.elements.aside` |
| POST `/api/chat`, `/api/lead`, `/api/track` | `ChatWidgetController` (Anthropic proxy, HubSpot lead, `ChatEvent` log) |
| `/update-sitemap`, `/clear-cache`, `/route-list`, `/broker-growth-mailer` | inline closures (unauthenticated utilities) |
| `/admin/panel/*` (`admin` namespace, `adminAuth`) | login, dashboard, blogs, authors, chat-dashboard (`ChatDashboardController`), seo/meta, seo/snippet, seo/pagespeed, newsletter |

Legacy (non-`New`) methods in `WebController`/`BlogController` and `ServiceController-old.php` are kept but unrouted.

### `routes/api.php`
- `/api/v1/*` (`verify.api.token`): blogs (CRUD, publish, lookup), meta-tags (lookup/upsert/bulk-upsert/CRUD), services + sections (CRUD, reorder, deleted, revisions/restore), redirects (CRUD, revisions/undo, check, audit). Controllers: `app/Http/Controllers/Api/*`.
- `/api/mcp/{key?}` → `Mcp\McpServerController` (config `claude_mcp.php`).
- `routes/dms_admin.php` (token `DMS_ADMIN_API_TOKEN`, controllers `Api/DmsAdmin/{Template,FileRedirect,Ops}Controller`): template read/edit/revisions (allow-list in `config/dms_admin.php`), file redirects, cache purge (LiteSpeed), IndexNow, sitemap audit/rebuild, page inspect, duplicate meta, blogs hidden by redirect.

## Service pages (most of the site)
**Content workflow, environments, API access, snippets and per-page state: [`.claude/service-pages-db.md`](service-pages-db.md)** — read it before changing any service page content.
- `ServiceController`: one method per URL; loads `Services` by slug (`mainservice`/`service`/`subservice` + `subservices`) and 3 `Blogs`, returns `web.services-new.master-service`.
- That name resolves to `resources/views/_v2-overrides/web/services-new/master-service.blade.php` → `@include('web.services-v2.page')`.
- `web/services-v2/page.blade.php` (~560 lines) renders the DB rows `service_sections` (active, `sort_order`) inside the V2 design; sets `v2lite/v2crit/v2form/v2keep`.
- Section HTML is Blade stored in the DB (edit via MCP/API, revisions kept). Old per-service views remain in `web/services-new/<category>/`.
- `renderService()` (→ `web.services-new.db-page`) is used by one method only; that view file does not exist.

- **DB sections that reuse home blocks** (all inside `.sv2-legacy`, so `service-v2.css` re-applies the home sizes): `what-our-clients-say` (testimonial slider, same `.hv2-q-*` markup driven by `home-v2.js`), `outdoor-advertising-dubai-faqs` (type `faq`: cards `h5.faq-title` + `.card-body`; the template renders H2 + H3 accordion, one FAQPage schema per page, ids carry the row id), `outdoor-advertising-quote-cta` (home "Schedule Your Appointment Today" block, `#hv2-cta`, button `data-sv2-open` opens the quote modal). The same 10 outdoor FAQs are also rows of the home FAQ array (topic `outdoor`).
- **Quote modal** (DB section `speak-with-our-planning-team`): the section is centered copy + a `[data-sv2-open="sv2-quote-modal"]` button; the compact form (2 fields per row, placeholders, hero-form styling via `.hv2-field`) sits in `.sv2-modal`. `service-v2.js` moves the modal to the end of `#hv2` (out of the legacy wrapper), traps focus, closes on Esc/backdrop/`[data-sv2-close]`; after a successful send the modal switches to `.is-sent` (thank-you state with reference). Any DB section can reuse the modal pattern.

## Views (`resources/views`)
- `web/home-v2/`: `master.blade.php` (layout; sections `v2lite`, `v2crit`, `v2form`, `v2keep`, `metaAddition`, `addStyle`, `content`, `addScript`), `header`, `footer`, `popups`, `schema` (JSON-LD), `breadcrumbs`, `clients-marquee`, `contact-cards`, `helpers.php`.
- Pages: `web/{home,about,faq,contact,privacy,terms,case-studies,services-landing,blogs,blogs-categories,blog-detail}-v2.blade.php`.
- `partials/service-schema`; `web/emails/*`; `admin/*` (blogs, claude-chat, seo, newsletter, users, layout).
- Legacy, unused by routes: `web/index*.blade.php`, `web/{about,faq,contact,services,terms,privacy-policy,case-studies}.blade.php`, `web/includes/*` (old master/header/footer), `web/blogs/*`, `datamysite-revamp 5.html`.

## Helpers (`resources/views/web/home-v2/helpers.php`)
`v2_url` (internal link → local URL), `v2_links` (rewrite links in stored HTML), `v2_blog_url`, `v2_asset` (serves `.min` via md5 `public/assets/min-manifest.json`, adds `?v=`), `v2_img` (WebP + width/height + srcset + `?v=`), `v2_clients`, `seo_canonical`, `v2_faq_schema`, `v2_snippet_has` (avoid duplicate snippet schema), `v2_biz` (reads `config/business.php`), `v2_wa_url` (WhatsApp link), `v2_case_studies`, `v2_reviews` (client reviews shared by the home slider and the service "What Our Clients Say" section), `v2_google_score` (rating/total/stars from the cached Google data, never calls Google).

## Assets (`public/assets`)
- Per page: `css/<page>-v2.css` + `js/<page>-v2.js` and `.min` siblings (pages: about, blog-detail, blogs, blogs-categories, case-studies, contact, faq, privacy, service, services-landing, terms).
- Shared: `home-v2.css/js` (design system), `home-v2-shell.css/js`, `v2-fonts.css`, `enquiry.js`, `input.js` (phone), `newsletter.js`, `aside.js`.
- `css/*.critical.css` (only used when `V2_CRITICAL_CSS=true`). Legacy: `main`, `custom`, `responsive`, `header`, `service.css`.
- Images: `img/` (WebP siblings `-w480/960/1440`), `vendor/` (Bootstrap, Swiper, intl-tel-input).

## Data (models in `app/Models` → table)
`Services`→services, `ServiceSection`→service_sections, `ServiceSectionRevision`, `Blogs`→blogs, `Author`→authors, `Categories`, `BlogTags`→blogs_tags, `TagData`→blog_tags_data, `MetaTags`→meta_tags, `SnippetCode`→snippet_code, `Redirect`/`RedirectRevision`, `DmsAdminRevision`, `Enquiry`→enquiries (columns beyond the migration, added directly in the DB: `website_link`, `budget`, `preferred_dates`; `photo_path` exists but is unused), `Newsletter`, `Publications`→media_publications, `ChatEvent`, `Admin`, `User`. (`Blogs-old.php` is dead.)

## Backend services
- `app/Services/PageSpeedService.php` (PSI client, `TARGETS_MOBILE/DESKTOP`), `GooglePlaces.php` (reviews, cached).
- `app/Services/DmsAdmin/`: `IndexNow`, `LiteSpeedPurge`, `PageInspector`, `SitemapBuilder`, `TemplateGuard`.
- `app/Support/Redirects/`: `RedirectMap`, `RedirectRules`. `app/Helpers/Mailer.php` (`sendMail(subject, to, name, view, data, attachments = [])`, never throws). `app/Observers/DmsAdminPublishObserver.php`.
- `app/Mcp/ApiBridge.php` + `WebsiteTools.php`: the 47 tools of the "DMS - Connector" MCP (meta, services/sections, blogs, redirects, templates, file redirects, cache, IndexNow, sitemap, inspect).
- Commands: `app:generate-sitemap`, `seo:pagespeed` (`--base=staging --strategy=both --path=/ --fresh`).

## Config worth knowing
`v2.php` (critical CSS switch; `fast_pages` = service slugs that use the icon subset + non-blocking custom/responsive CSS, icons verified by a test), `business.php` (name/phone/WhatsApp/social for V2 + schema), `services.php` (anthropic, hubspot, `chatbot.enabled` = `CHATBOT_ENABLED`, default off; blog_api), `clients.php`/`clients_featured.php`, `case_studies.php`, `seo.php`, `sitemap.php`, `redirects.php`, `claude_mcp.php`, `dms_admin.php`.
Env key names (values in `.env`, never copy): `APP_URL`, `DB_*`, `GOOGLE_PLACES_*`, `ANTHROPIC_API_KEY`, `HUBSPOT_TOKEN`, `BLOG_API_*`, `CLAUDE_MCP_*`, `DMS_ADMIN_*`, `DMS_SITE_URL`, `INDEXNOW_KEY`, `PAGESPEED_*`, `V2_CRITICAL_CSS`, `CHATBOT_ENABLED`.

## Tests (`tests/Feature`)
Run: `D:\xampp\php\php.exe artisan test` (PHP is not on PATH).
`Seo/`: `AssetPipelineTest` (minify, critical CSS, loading policy, pins perf fixes), `ScrollPerformanceTest`, `PageSpeedTest` (live PSI, skipped by default), `H1Test`, `HeadTagsTest`, `StructuredDataTest`, `AltTextAndHeadingsTest`, `LinksTest`, `HostLinksTest` (staging/local link rewriting), `HeaderMenuTest`, `SharedSourcesTest`, `CaseStudiesTest`, `EnquiryValidationTest`. Plus `ChatWidgetTest`.

## Tools (`tools/`, PowerShell)
`build-assets.ps1` (esbuild → `.min` + manifest), `make-critical-all.ps1` / `make-critical.ps1`, `png-to-webp.ps1`, `webp-batch.ps1`; `esbuild/` is git-ignored.

## Other top-level items
- `beta/`: a separate older copy of the Laravel app (not used by the main site). `clients/`: static client microsites (`dgnova`, `wholefoods`). `perf-report/pages.txt`: page list for perf runs.
- Root: `index.php` + `.htaccess` (Apache serves from the project root), `sitemap.xml`, `robots.txt`/`robots-staging.txt`, `llms.txt` (must stay lowercase: Linux hosting is case-sensitive; `/LLMs.txt` is rewritten to it in `.htaccess`; `LlmsTxtTest`), `llms-full.txt`, `indexnow_submit.php`.
- Ignore: `*-old.php`, `error_log`, `bash.exe.stackdump`, `.phpunit.result.cache`, README (Laravel stock).

## Gotchas
- `git` is not on the PowerShell PATH: use the Bash tool (Git Bash).
- Stored DB content has absolute `https://datamysite.com` links; the middleware rewrites them — don't "fix" the data.
- **No migration files for schema changes**: tables are altered directly in the DB (local and production), so `database/migrations` is out of date (e.g. `enquiries`). Give the SQL to the user; deploy code only after the columns exist.
- Local `.env` uses the real SMTP: a valid enquiry submit emails the actual team. Test with `Mail::fake()` (feature tests) or send invalid input.
- New web routes must go above the `/{blog_slug}` catch-all.
- After any CSS/JS edit: run `tools/build-assets.ps1` (else the manifest serves the old `.min`).

## Release flow
Work locally → commit on branch `staging` → push `origin staging` (staging server pulls it, `APP_URL=https://staging.datamysite.com`) → user verifies → user merges/pushes to `main` (production). Never push `main` without the user's go-ahead. **Database content (service sections, meta tags, blogs) is NOT in git**: it is changed locally through the local API (`/api/v1`, bearer `BLOG_API_TOKEN` from `.env`, never print it) and on production/staging through the "DMS - Connector" MCP tools (`update_service_section`, `create_service_section`, `purge_cache`, `inspect_page`). Order for a release that needs new DB structure: SQL on the server DB → deploy code → create/update DB sections → `purge_cache` → test.

## Structural changes log (newest first; read this when setting up a new session or machine)
**2026-11-27 · /outdoor-advertising/bridge-banners rebuilt in the hub design (local + staging DB; code `00c053a`)**
- Service 126 (name stays "Bridge Banners", URL/canonical unchanged: the page ranks). Hero row rewritten; 14 new rows (matched by `section_key`); old rows and `mobile-hero-clamp` hidden; new meta title/description. All mega banner copy removed; no blog block.
- The quote modal `#sv2-quote-modal` lives in the final CTA row `bridge-banner-quote-cta` (no planner section). Service JSON-LD (LocalBusiness provider, areaServed Dubai/Abu Dhabi/Sharjah) is the static row `bridge-banner-schema` (the section API refuses new `@php` blocks).
- Code: `service-v2.css` (`.sv2-wrap-ico`, `.sv2-process-sub`, `p.sv2-step-title`, `.sv2-process-foot`, `.sv2-cta-more`), `fast_pages` += bridge-banners, test `tests/Feature/Seo/BridgeBannersPageTest.php`, LinksTest `$withoutBlogs`, `RewrittenPagesPerfTest`.
- User decisions: stand-in hero (Sheikh Zayed Road unipole photo) and no photo of Aan until real ones arrive (the hero is a CSS background: the brief's alt text needs a small template change with the real photo); case-study slider shows all until the DMS team picks 6; no logo links; testimonials = `v2_reviews()` headed "What our customers say".
- The road link `/al-khail-road-e311-and-al-ittihad-road-advertising-2026-rates` is live on production; the post is not in the local DB.

**2026-11-26 · /outdoor-advertising/billboards-advertising/wallscapes rebuilt in the hub design (local + staging DB)**
- Service 50 (name stays "Wallscapes", URL/canonical unchanged: the page ranked first before the rebuild). Rows cloned from the Building Wraps rows; hero 49 rewritten, rows 50–59 hidden; new meta title/description. Building Wraps row `building-wrap-formats`: the "Facade Banner" card links to this page.
- New CSS in `service-v2.css`: `.sv2-wrap-grid--3`, `.sv2-wrap-card--link` + `.sv2-wrap-more` (whole card clickable; the legacy h3 must be `position: static`), `.sv2-compare` / `.sv2-compare-table` (comparison table). `fast_pages` += wallscapes; hero WebP re-encoded; test `tests/Feature/Seo/WallscapesPageTest.php`; LinksTest `$withoutBlogs`; `RewrittenPagesPerfTest`.
- Code is in commits `44fca4d` to `3d86166`; the staging DB got the rows, meta, location photos and Facade Banner link on 2026-11-26.
- User decisions: testimonials = the shared review slider (`v2_reviews()`) until the DMS team supplies three; case-study slider shows all case studies with the brief's "walls we have put brands on" sentence (DMS team to confirm). Agency photo is a stand-in. The location cards use the user's photos (`wallscape-location-*-w*.webp`, local DB row `wallscape-locations-in-dubai`).

**2026-11-26 · /outdoor-advertising/billboards-advertising/hoarding rebuilt in the hub design (local + staging DB; code `ee3db48`)**
- Service 49 (`hoarding`, name stays "Hoarding"). Rows cloned from the Building Wraps rows; no new CSS or template change (`.sv2-wrap-types` reused for the 4 types). `fast_pages` += hoarding; test `tests/Feature/Seo/HoardingPageTest.php`; LinksTest `$withoutBlogs`; also listed in `RewrittenPagesPerfTest`.
- User decisions: logo marquee has no case-study links; the slider shows all case studies; cost guide link = `/how-much-does-hoarding-advertising-cost-in-dubai-in-2026` (post exists on live, not in the local DB). Stats: "2010" has no `data-count` (the counter would print "2,010").
- Photos are stand-ins, except Dubai Media City (`hoarding-location-dubai-media-city-w*.webp`, added later; local + staging DB). Testimonials = `v2_reviews()` until the DMS team supplies three.

**2026-11-26 · redesigned case-study covers** (list: `config/case_studies.php`)
- `public/case-studies/v2/cover/`: per cover `{name}.jpg` + `{name}.webp` (1080x605) + `{name}-w480.webp` + `{name}-w960.webp`; both widths are needed because `v2_img()` builds the `srcset` from them. Lenskart KSA and UAE share `lenskart`. The `*-dms-cover*.png` files there are the 1920x1080 design sources, not used by any page; old covers are in `v2/cover-old/`.
- New cover: centre-crop the 1920x1080 source to the 1080x605 ratio, export the four files, add the line in the config (`CaseStudiesTest` checks them).

**2026-11-26 · /outdoor-advertising/billboards-advertising/building-wraps rebuilt in the hub design (local + staging DB)**
- Service 48. Rows cloned from the Billboards rows (see `service-pages-db.md` §6). New block `.sv2-wrap-types` (4 text-only format cards) and hero `p.sv2-hero-sub` in `service-v2.css`; `fast_pages` += building-wraps; test `tests/Feature/Seo/BuildingWrapsPageTest.php`; LinksTest `$withoutBlogs`.
- Photos are stand-ins (area views): no real wrap photos yet. 2026-11-27: the agency block shows the user's wrap image (`building-wrap-dubai-tower-w480/960/1408.webp`, made from the upload `public/storage/blogs/951121026124715.webp`; crop rule in `service-v2.css`; local DB row `why-brands-choose-us-for-building-wraps` only so far). Case-study slider shows all case studies (user's choice); testimonials = `v2_reviews()`.

**2026-11-26 · /outdoor-advertising/billboards-advertising rebuilt in the hub design (LOCAL DB only so far)**
- Service 16 renamed "Billboard Advertising" (breadcrumb/schema). New rows cloned from the hub rows: 1357 brands(22) → 1358 stats(24) → 1359 how-billboard-advertising-in-dubai-works(26) → 1360 agency(28) → 1361 locations, hub map block `[data-sv2-locations]` with 5 markers/cards(32) → 1362 latest-billboard-campaigns(34) → 1363 sizes-and-prices, 3-col table(36) → 1364 process(38) → 1365 speak-with-our-planning-team + Abhishek quote + quote modal(39) → 1366 what-our-clients-say(40) → 1367 FAQs, 10 Q, type faq(41) → 1368 quote CTA(42). Hero row 13 rewritten (h1 "Billboard Advertising Dubai", `sv2-hero-title-first`, `.sv2-hero-ctas` buttons + `.sv2-hero-chips`). Hidden: 14–23, 1306, 1307 (old FAQPage), 1369 (spare Service schema row, unused).
- **Snippets (table `snippet_code`, shown only when the host is datamysite.com):** #24 Head = Service + LocalBusiness for this URL (kept, name updated); #63 Body = old 4-question FAQPage → deleted locally so the template builds FAQPage from row 1367. Snippets can hide template schema and old copy from local curl checks: test with the production host (`CrawlsPages::html()`).
- Code: `service-v2.css` (hero CTA/chips, planner quote), `config/v2.php` fast_pages += billboards-advertising, `tests/Feature/Seo/BillboardsPageTest.php`, LinksTest `$withoutBlogs`.
- Production rollout needs: hub rows first, then these rows via MCP, meta description, services.name, snippet #24 edit + #63 removal in /admin/panel/seo/snippet (no MCP tool for snippets), `purge_cache`.

**2026-11 · /outdoor-advertising redesign (DB-driven page) + quote form**
- **Page = DB rows of service 1** (`service_sections`, ordered by `sort_order`, Blade stored in the DB). Visible order: `hero`(20) → brands-we-work-with(22) → our-successful-projects(24, 4 stat boxes) → dubai-outdoor-advertising-explained(26) → your-outdoor-advertising-agency-in-dubai(28) → outdoor-advertising-formats-in-dubai(30, carousel) → prime-outdoor-advertising-locations-in-dubai(32, Leaflet map) → latest-ooh-campaigns(34, home case-study slider) → outdoor-advertising-dubai-prices-2026(36, table) → how-we-run-your-outdoor-campaign(38, home process) → speak-with-our-planning-team(39, CTA + quote modal) → what-our-clients-say(40, home testimonials) → outdoor-advertising-dubai-faqs(41, type `faq`) → outdoor-advertising-quote-cta(42, home "Schedule Your Appointment Today"). Old rows kept but hidden (`is_active=false`): why-outdoor-advertising-works-in-dubai, practice, clients, case-studies, our-outdoor-advertising-services, prime-locations-we-cover, blogs, old faq. `styles`(1) and `scripts`(11) stay active.
- **Template** `web/services-v2/page.blade.php`: hero flag class `sv2-hero-title-first`; keeps ONE extra form (`form.sv2-lead-form`, all other forms in DB content are stripped); FAQ rows: unique ids per row (`sv2-faq<rowId>-q<k>`), one FAQPage schema per page; legacy `service.css` is versioned.
- **Reusable blocks in DB sections** (`.sv2-legacy` wrapper re-skinned by scoped rules in `service-v2.css`): carousel `[data-sv2-carousel]`, locations map (vendored Leaflet 1.9.4 in `public/assets/vendor/leaflet/`, lazy init), price table, home slider/process/stat/testimonial/CTA markup reused as-is so `home-v2.js` drives them, modal `.sv2-modal` opened by `[data-sv2-open="<id>"]` (focus trap, Esc, page behind `inert`, z-index 10090). New helpers: `v2_reviews()`, `v2_google_score()` (`web/home-v2/helpers.php`; home now reads reviews from there).
- **Quote form**: `POST /enquiry/planner` → `EnquiryController@planner` + `EnquiryPlannerRequest`; saves `enquiries` (+ `budget`, `preferred_dates`, set by hand in the DB; `photo_path` exists but unused, the photo upload was dropped), mails visitor + team (`web/emails/enquiry.blade.php` shows budget/dates), JSON `{success,message,reference,emailed}`, throttled 10/min. Production SQL: `ALTER TABLE enquiries ADD COLUMN budget VARCHAR(60) NULL, ADD COLUMN preferred_dates VARCHAR(120) NULL;`.
- **PageSpeed round (staging mobile 85 → fixes)**: hero photo re-encoded (1440 px 162 KB, `-w960` 91 KB for phones; the template now finds the `-w960` sibling of a `.webp` hero and adds `?v=filemtime` because images are cached a year), skyline recompressed, CTA background has width/height, `config/v2.php` `fast_pages` = [outdoor-advertising] → `@section(v2icons, v2cssasync)` (icon subset `bi-v2.css`: only use icons that are in it — process steps use `bi-calendar2-week`, `bi-clipboard-data` for that reason), decoration measuring pass in `service-v2.js` (forced reflow) now waits for first scroll/touch/key or 8 s. Not changed on purpose: client logos 300 px notice, unused Bootstrap CSS (see performance playbook).
- **Home FAQ**: the 10 outdoor FAQs are also rows of the home FAQ array (topic `outdoor`) — keep both copies in sync.
- Earlier in the same stretch: blogs listing/detail redesign (sticky consultation form), chatbot hidden by default (`CHATBOT_ENABLED`), global-marketing images hidden on phones, hub-guides pseudo-element hidden on mobile, bullets fix in legacy sections.
- Tests: `AssetPipelineTest` pins every one of these (modal, carousels, map, FAQ ids, shared helpers); `EnquiryValidationTest` covers `/enquiry/planner`; `LinksTest` exempts `/outdoor-advertising` from the "See All Blogs" rule.
- **Purge AFTER the deploy**: staging/production cache the rendered HTML (LiteSpeed). A purge sent before the new code is live re-caches the OLD page (PageSpeed then reports the old numbers). Order: push → wait until the new file is served (check a `?v=` URL) → `POST /api/v1/admin/cache/purge` (or connector `purge_cache`) → run `MSYS_NO_PATHCONV=1 php artisan seo:pagespeed --base=staging --path=/x --fresh` (Git Bash needs MSYS_NO_PATHCONV) 2-3 times, ~60 s apart.
