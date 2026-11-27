# Service pages: database-driven content (how it works, how to change it)

Read this before changing the content of any service page (`/outdoor-advertising`, `/outdoor-advertising/billboards-advertising`, `/seo-services`, ...).
Code lives in git; **page content does not**. Each environment has its own database, and content is moved between them through the API.

## 1. Where the content lives
| Table | What | Notes |
|---|---|---|
| `services` | one row per page: `id`, `name`, `slug`, `parent_id` | `name` = breadcrumb last crumb, form `service` value, template Service schema. **No API to change it**: SQL (`UPDATE services SET name='...' WHERE id=N;`). |
| `service_sections` | the page body: one row per section | Columns: `service_id`, `area` (`head` / `main` / `after_main` / `scripts`), `section_key` (unique per page), `type` (`style`, `hero`, `content`, `include`, `subservices`, `blogs`, `faq`, `script`), `title`, `content` (Blade), `sort_order`, `is_active`. Every API change is saved in `service_section_revisions` (restorable). |
| `meta_tags` | title / description / keywords / H1 per URL | Keyed by the **live** URL (`https://datamysite.com/<path>`) in every environment. |
| `snippet_code` | extra head/body code per URL (admin panel > SEO > Snippets) | Shown **only when the request host is datamysite.com** (`ViewServiceProvider` matches `page_url` to the current URL), so local/staging never show them. They can carry JSON-LD (FAQPage/Service/LocalBusiness) that makes the template skip its own block. No API/MCP tool: edit in the admin panel (or tinker locally). |

## 2. How a page is rendered
`ServiceController@<method>` loads `Services` by slug → `web.services-new.master-service` → `_v2-overrides/.../master-service.blade.php` → `web/services-v2/page.blade.php`:
- loads the active rows ordered by `sort_order`, renders each row's Blade with the controller variables (`$service`, `$subservice`, `$mainservice`, `$blogs`, helpers like `v2_wa_url()`, `v2_biz()`, `v2_case_studies()`, `v2_reviews()`, `v2_google_score()`); rendered HTML is cached per row (`sv2r_<id>_<updated_at>`), so an update shows at once.
- **hero row** (`type=hero`): only its text column `.head-para-service` is reused (verbatim, in order); the template draws its own breadcrumb, enquiry form card and background (from the row's inline `background-image`, `-w960.webp` sibling for phones, `?v=filemtime`). Class `sv2-hero-title-first` = H1 is the big headline, H2 the sub line.
- **`faq` rows**: cards `h5.faq-title` + `.card-body` are parsed into an H2 + H3 accordion and ONE FAQPage schema per page (skipped when another row or a snippet already has FAQPage).
- **schema**: the template adds BreadcrumbList, Service (`partials/service-schema`) and FAQPage only when no row/snippet already carries that type (`StructuredDataTest` guards duplicates on both hosts).
- `config/v2.php` `fast_pages`: slugs that load only the icon subset (`bi-v2.css`): every `bi-*` icon in their rows must be in that file.
- Only one extra `<form>` survives in rows: `form.sv2-lead-form` (quote modal, posts to `/enquiry/planner`, needs the `enquiries.budget` / `preferred_dates` columns).

## 3. Reusable section blocks (copy the markup of an existing row, change the text)
Hub `/outdoor-advertising` (service 1) is the reference. Row keys → block:
`brands-we-work-with` (client marquee) · `our-successful-projects` (4 stat boxes) · `dubai-outdoor-advertising-explained` (`.sv2-explain`, dark, image + text) · `your-outdoor-advertising-agency-in-dubai` (`.sv2-agency`, text + image, optional `.sv2-agency-quote`) · `outdoor-advertising-formats-in-dubai` (`.sv2-formats [data-sv2-carousel]` card carousel) · `prime-outdoor-advertising-locations-in-dubai` (`[data-sv2-locations]` Leaflet map + cards) · `latest-ooh-campaigns` (case-study slider) · `outdoor-advertising-dubai-prices-2026` (`.sv2-price-table`) · `how-we-run-your-outdoor-campaign` (4-step process) · `speak-with-our-planning-team` (CTA + quote modal `#sv2-quote-modal`) · `what-our-clients-say` (reviews slider + Google score) · `outdoor-advertising-dubai-faqs` (`type=faq`) · `outdoor-advertising-quote-cta` (final CTA band, opens the modal).
Billboards page (service 16) = the same blocks; locations use the map block (`.sv2-bb-locations`; changed from the carousel on 2026-11-26), hero has `.sv2-hero-ctas` buttons + `.sv2-hero-chips`.

## 4. Environments and access
| | Site | Database | Write path |
|---|---|---|---|
| Local | `http://localhost/datamysite` | local MySQL (XAMPP) | `/api/v1` + tinker (`D:\xampp\php\php.exe artisan tinker`) |
| Staging | `https://staging.datamysite.com` | **own database** (separate since 2026-11-26) | `https://staging.datamysite.com/api/v1` |
| Production | `https://datamysite.com` | production MySQL | `/api/v1`, or the "DMS - Connector" MCP tools (they reach production only) |
- **The same bearer token works on all three**: `BLOG_API_TOKEN` from the local `.env` (`Authorization: Bearer ...`). Read it into a variable, never print it.
- Endpoints: `GET services/{slug}/sections`, `GET service-sections/{id}`, `POST services/{slug}/sections` (`section_key, title, type, area, sort_order, is_active, content`), `PATCH service-sections/{id}` (any field, or `find` + `replace` for a small edit), `POST services/{slug}/sections/reorder`, `GET service-sections/{id}/revisions`, `POST service-section-revisions/{rev}/restore`, `GET meta-tags/lookup?url=`, `POST meta-tags/upsert` (`url` + fields).
- Send JSON as UTF-8 bytes from PowerShell (`[Text.Encoding]::UTF8.GetBytes(... | ConvertTo-Json)`), otherwise "—", "→" etc. are garbled.
- Row ids can differ between databases: match rows by `section_key`, not id.
- Caches after a change: staging `GET https://staging.datamysite.com/clear-cache` (runs `optimize:clear`; meta tags are cached); production MCP `purge_cache` (LiteSpeed) — see the Release flow in `project-map.md`.

## 5. Workflow for a content change (proven on the billboards page, 2026-11-26)
1. Read the current rows (all environments you will touch) and save them as files (scratch), so you can compare/restore.
2. Write the new rows as local `.html` files (copy the hub row markup). Keep Blade expressions (`{{ }}`, `@csrf`, `@include`) intact.
3. Apply locally via the API (create new rows, `PATCH` the hero, hide old rows with `is_active=false` — never delete), meta via `meta-tags/upsert`, `services.name` via tinker.
4. Check: curl the page as **both hosts** (local host hides snippets: render with the production host in a test, `CrawlsPages::html()`), run `D:\xampp\php\php.exe artisan test`, look with `/browse` at 1440 and 390 px.
5. Commit the code (CSS/config/tests) → push `staging` → run the same script with `-Base https://staging.datamysite.com` (guard: stop if the new keys already exist) → `clear-cache` → check staging. Then, in the same task, update [`production-rollout.md`](production-rollout.md) (required after every staging DB change, small edits included).
6. Production only on the user's go-ahead, after the code is merged to `main`: same API calls (or MCP tools), SQL for `services.name`, snippet edits in the admin panel, `purge_cache`.
A page's rows depend on code: never put rows on a database whose site runs older code (production `main` lacked the hub CSS/JS/`/enquiry/planner` on 2026-11-26).

## 6. State per page (update when it changes; the production push list is `production-rollout.md`, update both)
| Page | Local | Staging | Production |
|---|---|---|---|
| `/outdoor-advertising` (service 1) | redesigned rows 1344–1356 | same | **old rows** (redesign not live) |
| `/outdoor-advertising/billboards-advertising` (16) | new rows 1357–1368, hero 13, old rows hidden, name "Billboard Advertising", new meta description; snippet #63 deleted, #24 name updated | same rows/meta (2026-11-26); name "Billboard Advertising" (breadcrumb checked on staging 2026-11-26) | **old rows**; snippets #24 (Service+LocalBusiness, keep, rename "Billboard Advertising Dubai") and #63 (old FAQPage, remove) |
| `.../billboards-advertising/building-wraps` (48) | new rows (13 new keys + hero 25 rewritten), rows 26–35 hidden (35 = old FAQPage), new meta title/description | same rows/meta (2026-11-26) | **old rows**; needs the hub + Billboards code first; check snippets for this URL with `inspect_page` |
| `.../billboards-advertising/hoarding` (49) | new rows (13 new keys + hero 37 rewritten), rows 38–47 hidden (47 = old FAQ), new meta title/description | same rows/meta (2026-11-26); code `ee3db48` live on staging; the cost guide post is not in the staging DB (link 404s there) | **old rows**; needs the hub + Billboards + Building Wraps code first; check snippets for this URL with `inspect_page` |
| `.../billboards-advertising/wallscapes` (50) | new rows (11 new keys + hero 49 rewritten), rows 50–59 hidden (59 = old FAQ), new meta title/description; Building Wraps row `building-wrap-formats` links its Facade Banner card here | same rows/meta, location photos and Facade Banner link (2026-11-26, identical to local; code `3d86166`) | **old rows**; needs the hub + Billboards + Building Wraps code and rows first; check snippets for this URL with `inspect_page` |
| `/outdoor-advertising/flyers-distribution` (17) | new rows (14 new keys + hero 61 and blogs 69 rewritten), old rows hidden incl. `faq` + `schema`, new meta title/description, snippet #25 deleted (2026-11-27); 7 blog banner WebP copies + alt text of post 759 (git-ignored uploads, see project-map) | same rows/meta as local (2026-11-27), page checked; blog WebP copies and alt text of post 759 not there yet | **old rows**; needs the hub + Bridge Banners code plus this page's CSS first; delete snippet #25 (Service schema) in /admin/panel/seo/snippet |
| `/outdoor-advertising/human-billboards` (18) | new rows (12 new keys + hero 72 rewritten), rows 73–81 hidden, new meta title/description (2026-11-27) | same rows/meta (2026-11-27); code `b2f9ccd` live, page checked; WebP copies of the two blog banners not on staging (cards use the JPGs) | **old rows**; needs the hub code plus this page's CSS and mockup images first; copy the blog WebP files; check snippets for this URL with `inspect_page` |
| `/outdoor-advertising/bridge-banners` (126) | new rows (14 new keys + hero rewritten), old rows 110–121 and 1324 hidden, new meta title/description (2026-11-27) | same rows/meta (2026-11-27); code `00c053a` live on staging, page checked | **old rows** (newer than local before the rebuild: row `where-the-bridges-are` has the road link); needs the hub + Billboards code first; check snippets for this URL with `inspect_page` |
