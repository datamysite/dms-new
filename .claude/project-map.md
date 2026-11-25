# DataMySite — codebase map (as of 2026-11-25, last commit b5623e1)

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
- `ServiceController`: one method per URL; loads `Services` by slug (`mainservice`/`service`/`subservice` + `subservices`) and 3 `Blogs`, returns `web.services-new.master-service`.
- That name resolves to `resources/views/_v2-overrides/web/services-new/master-service.blade.php` → `@include('web.services-v2.page')`.
- `web/services-v2/page.blade.php` (~560 lines) renders the DB rows `service_sections` (active, `sort_order`) inside the V2 design; sets `v2lite/v2crit/v2form/v2keep`.
- Section HTML is Blade stored in the DB (edit via MCP/API, revisions kept). Old per-service views remain in `web/services-new/<category>/`.
- `renderService()` (→ `web.services-new.db-page`) is used by one method only; that view file does not exist.

## Views (`resources/views`)
- `web/home-v2/`: `master.blade.php` (layout; sections `v2lite`, `v2crit`, `v2form`, `v2keep`, `metaAddition`, `addStyle`, `content`, `addScript`), `header`, `footer`, `popups`, `schema` (JSON-LD), `breadcrumbs`, `clients-marquee`, `contact-cards`, `helpers.php`.
- Pages: `web/{home,about,faq,contact,privacy,terms,case-studies,services-landing,blogs,blogs-categories,blog-detail}-v2.blade.php`.
- `partials/service-schema`; `web/emails/*`; `admin/*` (blogs, claude-chat, seo, newsletter, users, layout).
- Legacy, unused by routes: `web/index*.blade.php`, `web/{about,faq,contact,services,terms,privacy-policy,case-studies}.blade.php`, `web/includes/*` (old master/header/footer), `web/blogs/*`, `datamysite-revamp 5.html`.

## Helpers (`resources/views/web/home-v2/helpers.php`)
`v2_url` (internal link → local URL), `v2_links` (rewrite links in stored HTML), `v2_blog_url`, `v2_asset` (serves `.min` via md5 `public/assets/min-manifest.json`, adds `?v=`), `v2_img` (WebP + width/height + srcset + `?v=`), `v2_clients`, `seo_canonical`, `v2_faq_schema`, `v2_snippet_has` (avoid duplicate snippet schema), `v2_biz` (reads `config/business.php`), `v2_wa_url` (WhatsApp link), `v2_case_studies`.

## Assets (`public/assets`)
- Per page: `css/<page>-v2.css` + `js/<page>-v2.js` and `.min` siblings (pages: about, blog-detail, blogs, blogs-categories, case-studies, contact, faq, privacy, service, services-landing, terms).
- Shared: `home-v2.css/js` (design system), `home-v2-shell.css/js`, `v2-fonts.css`, `enquiry.js`, `input.js` (phone), `newsletter.js`, `aside.js`.
- `css/*.critical.css` (only used when `V2_CRITICAL_CSS=true`). Legacy: `main`, `custom`, `responsive`, `header`, `service.css`.
- Images: `img/` (WebP siblings `-w480/960/1440`), `vendor/` (Bootstrap, Swiper, intl-tel-input).

## Data (models in `app/Models` → table)
`Services`→services, `ServiceSection`→service_sections, `ServiceSectionRevision`, `Blogs`→blogs, `Author`→authors, `Categories`, `BlogTags`→blogs_tags, `TagData`→blog_tags_data, `MetaTags`→meta_tags, `SnippetCode`→snippet_code, `Redirect`/`RedirectRevision`, `DmsAdminRevision`, `Enquiry`→enquiries, `Newsletter`, `Publications`→media_publications, `ChatEvent`, `Admin`, `User`. (`Blogs-old.php` is dead.)

## Backend services
- `app/Services/PageSpeedService.php` (PSI client, `TARGETS_MOBILE/DESKTOP`), `GooglePlaces.php` (reviews, cached).
- `app/Services/DmsAdmin/`: `IndexNow`, `LiteSpeedPurge`, `PageInspector`, `SitemapBuilder`, `TemplateGuard`.
- `app/Support/Redirects/`: `RedirectMap`, `RedirectRules`. `app/Helpers/Mailer.php`. `app/Observers/DmsAdminPublishObserver.php`.
- `app/Mcp/ApiBridge.php` + `WebsiteTools.php`: the 47 tools of the "DMS - Connector" MCP (meta, services/sections, blogs, redirects, templates, file redirects, cache, IndexNow, sitemap, inspect).
- Commands: `app:generate-sitemap`, `seo:pagespeed` (`--base=staging --strategy=both --path=/ --fresh`).

## Config worth knowing
`v2.php` (critical CSS switch), `business.php` (name/phone/WhatsApp/social for V2 + schema), `services.php` (anthropic, hubspot, `chatbot.enabled` = `CHATBOT_ENABLED`, default off; blog_api), `clients.php`/`clients_featured.php`, `case_studies.php`, `seo.php`, `sitemap.php`, `redirects.php`, `claude_mcp.php`, `dms_admin.php`.
Env key names (values in `.env`, never copy): `APP_URL`, `DB_*`, `GOOGLE_PLACES_*`, `ANTHROPIC_API_KEY`, `HUBSPOT_TOKEN`, `BLOG_API_*`, `CLAUDE_MCP_*`, `DMS_ADMIN_*`, `DMS_SITE_URL`, `INDEXNOW_KEY`, `PAGESPEED_*`, `V2_CRITICAL_CSS`, `CHATBOT_ENABLED`.

## Tests (`tests/Feature`)
Run: `D:\xampp\php\php.exe artisan test` (PHP is not on PATH).
`Seo/`: `AssetPipelineTest` (minify, critical CSS, loading policy, pins perf fixes), `ScrollPerformanceTest`, `PageSpeedTest` (live PSI, skipped by default), `H1Test`, `HeadTagsTest`, `StructuredDataTest`, `AltTextAndHeadingsTest`, `LinksTest`, `HostLinksTest` (staging/local link rewriting), `HeaderMenuTest`, `SharedSourcesTest`, `CaseStudiesTest`, `EnquiryValidationTest`. Plus `ChatWidgetTest`.

## Tools (`tools/`, PowerShell)
`build-assets.ps1` (esbuild → `.min` + manifest), `make-critical-all.ps1` / `make-critical.ps1`, `png-to-webp.ps1`, `webp-batch.ps1`; `esbuild/` is git-ignored.

## Other top-level items
- `beta/`: a separate older copy of the Laravel app (not used by the main site). `clients/`: static client microsites (`dgnova`, `wholefoods`). `perf-report/pages.txt`: page list for perf runs.
- Root: `index.php` + `.htaccess` (Apache serves from the project root), `sitemap.xml`, `robots.txt`/`robots-staging.txt`, `LLMs.txt`, `llms-full.txt`, `indexnow_submit.php`.
- Ignore: `*-old.php`, `error_log`, `bash.exe.stackdump`, `.phpunit.result.cache`, README (Laravel stock).

## Gotchas
- `git` is not on the PowerShell PATH: use the Bash tool (Git Bash).
- Stored DB content has absolute `https://datamysite.com` links; the middleware rewrites them — don't "fix" the data.
- New web routes must go above the `/{blog_slug}` catch-all.
- After any CSS/JS edit: run `tools/build-assets.ps1` (else the manifest serves the old `.min`).
