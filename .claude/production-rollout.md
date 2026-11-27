# Production rollout: database-driven service pages

**Purpose:** everything needed to push the rebuilt service pages to production (datamysite.com). Page content lives in each environment's database, **not in Git**, so deploying the code alone does not publish these pages.

**Keep this file updated (standing rule):** whenever a database-driven service page is rebuilt or its rows, meta, snippets, files or SQL change, add or update its section here in the same task. When a page goes live, set its status to "Live" with the date. **After every staging database change** (rows, meta, snippets, names, SQL), update the page's section and its status in the "Pages" table before reporting the task as done.

How rows are rendered and the API: [`service-pages-db.md`](service-pages-db.md). Change history: [`project-map.md`](project-map.md).

## Pages

| # | Page | Service id / slug | Local | Staging | Production |
|---|---|---|---|---|---|
| 1 | [`/outdoor-advertising`](https://datamysite.com/outdoor-advertising) | 1 / `outdoor-advertising` | Done | Done | **Pending** |
| 2 | [`/outdoor-advertising/billboards-advertising`](https://datamysite.com/outdoor-advertising/billboards-advertising) | 16 / `billboards-advertising` | Done | Done | **Pending** |
| 3 | [`/outdoor-advertising/billboards-advertising/building-wraps`](https://datamysite.com/outdoor-advertising/billboards-advertising/building-wraps) | 48 / `building-wraps` | Done | Done | **Pending** |
| 4 | [`/outdoor-advertising/billboards-advertising/hoarding`](https://datamysite.com/outdoor-advertising/billboards-advertising/hoarding) | 49 / `hoarding` | Done | Done | **Pending** |
| 5 | [`/outdoor-advertising/billboards-advertising/wallscapes`](https://datamysite.com/outdoor-advertising/billboards-advertising/wallscapes) | 50 / `wallscapes` | Done | Done | **Pending** |
| 6 | [`/outdoor-advertising/bridge-banners`](https://datamysite.com/outdoor-advertising/bridge-banners) | 126 / `bridge-banners` | Done | Done | **Pending** |
| 7 | [`/outdoor-advertising/flyers-distribution`](https://datamysite.com/outdoor-advertising/flyers-distribution) | 17 / `flyers-distribution` | Done | Done (blog image files + post 759 alt text still missing) | **Pending** |
| 8 | [`/outdoor-advertising/human-billboards`](https://datamysite.com/outdoor-advertising/human-billboards) | 18 / `human-billboards` | Done | Done (4 blog WebP files still missing) | **Pending** |
| 9 | [`/outdoor-advertising/lamp-post`](https://datamysite.com/outdoor-advertising/lamp-post) | 52 / `lamp-post` | Done | Done | **Pending** |
| 10 | [`/outdoor-advertising/unipoles`](https://datamysite.com/outdoor-advertising/unipoles) | 51 / `unipoles` | Done | Done | **Pending** |

State read from the three environments' APIs on 2026-11-27: the active rows and the meta title/description of pages 1-7 are identical on local and staging; page 8 was applied to staging later that day from the same row files and checked there; production still has the old rows.

## Release order

1. **Go-ahead** from the owner. Resolve the open items in the page sections that must not go live.
2. **Back up** on production: for each page `GET /api/v1/services/{slug}/sections`, then each row (`GET /api/v1/service-sections/{id}`), plus the meta (`GET /api/v1/meta-tags/lookup?url=`) and the snippets listed below. Compare "newest production edit" in each page section: if a row changed after that date, someone edited live; check before overwriting.
3. **SQL** (before the code; check with `SHOW COLUMNS FROM enquiries;` first, the quote form needs both columns):
   `ALTER TABLE enquiries ADD COLUMN budget VARCHAR(60) NULL, ADD COLUMN preferred_dates VARCHAR(120) NULL;`
4. **Code:** merge `staging` into `main` and deploy. On 2026-11-27 `main` was at `9ea5b48`, without the new CSS, templates, images or the `/enquiry/planner` route. Never add the new rows to a site running older code.
5. **Rows, straight after the deploy** (the new code already treats these pages as rebuilt, for example it loads only the icon subset). Source = **staging** rows. Match by `section_key`, never by id. For each key in a page's table: "Update" = PATCH the existing row, "Create" = POST it (copy `content`, `title`, `type`, `area`, `sort_order`, `is_active=true`). Then set `is_active=false` on every key in the "Hide" list. **Never delete rows.** Use `/api/v1` (bearer `BLOG_API_TOKEN`, never print it; send JSON as UTF-8) or the "DMS - Connector" tools (`create_service_section`, `update_service_section`).
6. **Meta:** upsert each page's title and description (`upsert_meta_tags`), keyed by the live URL.
7. **SQL for names, snippets, files:** see the page sections. Snippets are edited only in `/admin/panel/seo/snippet` (no API).
8. **Purge** the cache **after** the code and rows are live (`purge_cache`). An earlier purge caches the old page again.
9. **Check each page** (`inspect_page`): new title and description, one H1, **one** FAQPage with the same questions as the page, one Service block, canonical unchanged, no `[` placeholders, images load, quote popup opens. Do not submit a test enquiry without telling the team (it emails them). Check "Read reviews" in the footer.
10. **PageSpeed:** `php artisan seo:pagespeed --base=<live> --strategy=both --path=/... --fresh`, 3 runs about 70 s apart (limits: `.claude/rules/performance.md`).
11. Update this file (status "Live" + date) and the state table in `service-pages-db.md`.

**Roll back a page:** restore from the backup or the row revisions (`GET /api/v1/service-sections/{id}/revisions`, then restore), hide the newly created rows, re-activate the hidden ones, restore meta and snippets, purge.

## 1. Outdoor Advertising (hub): `/outdoor-advertising`

Service 1 (`outdoor-advertising`). URL and canonical unchanged. Newest production row edit on 2026-11-27: `2026-11-25T10:31:41+00:00`.

**Meta title:** Outdoor Advertising Dubai: Billboards, Hoardings & Rates
**Meta description:** Outdoor advertising agency in Dubai since 2010. Billboards, unipoles, hoardings and building wraps on Sheikh Zayed Road and beyond. Quote in 60 minutes.

**Rows to publish**

| Area | Sort | section_key | Type | On production |
|---|---|---|---|---|
| main | 20 | `hero` | hero | Update |
| main | 22 | `brands-we-work-with` | content | Create |
| main | 24 | `our-successful-projects` | content | Create |
| main | 26 | `dubai-outdoor-advertising-explained` | content | Create |
| main | 28 | `your-outdoor-advertising-agency-in-dubai` | content | Create |
| main | 30 | `outdoor-advertising-formats-in-dubai` | content | Create |
| main | 32 | `prime-outdoor-advertising-locations-in-dubai` | content | Create |
| main | 34 | `latest-ooh-campaigns` | content | Create |
| main | 36 | `outdoor-advertising-dubai-prices-2026` | content | Create |
| main | 38 | `how-we-run-your-outdoor-campaign` | content | Create |
| main | 39 | `speak-with-our-planning-team` | content | Create |
| main | 40 | `what-our-clients-say` | content | Create |
| main | 41 | `outdoor-advertising-dubai-faqs` | faq | Create |
| main | 42 | `outdoor-advertising-quote-cta` | content | Create |

Already identical on production (leave as is): `styles`, `scripts`.

**Hide** (`is_active=false`): `outdoor-advertising-in-practice`, `why-outdoor-advertising-works-in-dubai`, `clients`, `case-studies`, `our-outdoor-advertising-services`, `prime-locations-we-cover`, `blogs`, `faq`.

**Page notes**
- **Snippet #12** (Head, "Rating Schema - Outdoor Advertising"): **delete its FAQPage `<script>` block.** It holds the 5 old questions; while it exists the template skips its own FAQPage, so Google would get 5 old questions while the page shows 10 new ones. The same snippet also has a Service block with an OfferCatalog: keep it, but check its prices and text against the new page.
- The live hero row was edited on 2026-11-25 10:31 UTC and its wording differs from the new hero. The rollout replaces it: confirm nothing from that edit must be kept.
- Other pages are linked from here and copy blocks from here: do this page first.

## 2. Billboard Advertising: `/outdoor-advertising/billboards-advertising`

Service 16 (`billboards-advertising`). URL and canonical unchanged. Newest production row edit on 2026-11-27: `2026-11-13T10:37:39+00:00`.

**Meta title:** Billboard Advertising Dubai: 2026 Rates, Locations & Formats
**Meta description:** Billboard advertising in Dubai since 2010. Unipoles, digital, rooftop and bridge billboards on Sheikh Zayed Road and beyond. Quote in 60 minutes.

**Rows to publish**

| Area | Sort | section_key | Type | On production |
|---|---|---|---|---|
| main | 20 | `hero` | hero | Update |
| main | 22 | `brands-we-work-with` | content | Create |
| main | 24 | `our-successful-projects` | content | Create |
| main | 26 | `how-billboard-advertising-in-dubai-works` | content | Create |
| main | 28 | `your-billboard-advertising-agency-in-dubai` | content | Create |
| main | 32 | `billboard-advertising-locations-in-dubai` | content | Create |
| main | 34 | `latest-billboard-campaigns` | content | Create |
| main | 36 | `billboard-sizes-and-prices-in-dubai` | content | Create |
| main | 38 | `how-we-run-your-billboard-campaign` | content | Create |
| main | 39 | `speak-with-our-planning-team` | content | Create |
| main | 40 | `what-our-clients-say` | content | Create |
| main | 41 | `billboard-advertising-dubai-faqs` | faq | Create |
| main | 42 | `billboard-advertising-quote-cta` | content | Create |

Already identical on production (leave as is): `styles`.

**Hide** (`is_active=false`): `schema`, `why-billboard-advertising-still-wins-in-dubai`, `clients`, `case-studies`, `our-billboard-advertising-formats`, `dubai-s-highest-trafficbillboard-spots`, `how-much-does-billboard-advertising-cost-in-dubai`, `billboard-sizes-downtown-business-bay`, `our-process`, `2-million-eyes-on-yourbrand-every-single-day`, `blogs`, `faq`.

**Page notes**
- **SQL (no API):** `UPDATE services SET name='Billboard Advertising' WHERE id=16;` (breadcrumb, form value, schema).
- **Snippet #63** (Body, old 4-question FAQPage): **delete.** Live currently prints two FAQPage blocks (8 and 4 questions).
- **Snippet #24** (Head, Service + LocalBusiness): keep, rename the service in it to "Billboard Advertising Dubai".
- Local also has an inactive spare row `service-schema`: do not create it.

## 3. Building Wraps: `/outdoor-advertising/billboards-advertising/building-wraps`

Service 48 (`building-wraps`). URL and canonical unchanged. Newest production row edit on 2026-11-27: `2026-11-12T14:57:40+00:00`.

**Meta title:** Building Wrap Advertising Dubai: Formats, Locations & Costs
**Meta description:** Building wrap advertising in Dubai on towers, facades and scaffolding. We find the building, get approvals, print and install. Quote in 60 minutes.

**Rows to publish**

| Area | Sort | section_key | Type | On production |
|---|---|---|---|---|
| main | 20 | `hero` | hero | Update |
| main | 22 | `brands-we-work-with` | content | Create |
| main | 24 | `our-successful-projects` | content | Create |
| main | 26 | `what-is-building-wrap-advertising-in-dubai` | content | Create |
| main | 28 | `why-brands-choose-us-for-building-wraps` | content | Create |
| main | 30 | `building-wrap-formats` | content | Create |
| main | 32 | `building-wrap-locations-in-dubai` | content | Create |
| main | 34 | `building-wraps-we-have-delivered` | content | Create |
| main | 36 | `building-wrap-advertising-cost-in-dubai` | content | Create |
| main | 38 | `how-we-run-your-building-wrap-campaign` | content | Create |
| main | 39 | `speak-with-our-planning-team` | content | Create |
| main | 40 | `what-our-clients-say` | content | Create |
| main | 41 | `building-wrap-advertising-faqs` | faq | Create |
| main | 42 | `building-wrap-quote-cta` | content | Create |

Already identical on production (leave as is): `styles`.

**Hide** (`is_active=false`): `what-is-building-wrap-advertising`, `clients`, `case-studies`, `why-choose-building-wrap-advertising-in-dubai`, `ideal-locations-for-building-wraps-in-dubai`, `our-building-wrap-advertising-services`, `advantages-of-choosing-datamysite`, `blogs`, `faq`, `scripts`.

**Page notes**
- Row `building-wrap-formats` links its "Facade Banner" card to the Wallscapes page.
- No snippet schema found on the live page (2026-11-27); recheck with `inspect_page`.
- Location photos are stand-ins; the case-study slider shows all case studies and testimonials are the shared review slider, until the DMS team supplies its picks.

## 4. Hoarding: `/outdoor-advertising/billboards-advertising/hoarding`

Service 49 (`hoarding`). URL and canonical unchanged. Newest production row edit on 2026-11-27: `2026-11-12T14:57:40+00:00`.

**Meta title:** Hoarding Advertising Dubai: Sites, Sizes & Costs
**Meta description:** Hoarding advertising in Dubai on construction sites and main roads. We find the site, handle permits, print and install. Reply in 60 minutes.

**Rows to publish**

| Area | Sort | section_key | Type | On production |
|---|---|---|---|---|
| main | 20 | `hero` | hero | Update |
| main | 22 | `brands-we-work-with` | content | Create |
| main | 24 | `our-successful-projects` | content | Create |
| main | 26 | `what-is-hoarding-advertising-in-dubai` | content | Create |
| main | 28 | `choosing-among-hoarding-companies` | content | Create |
| main | 30 | `types-of-hoarding-we-book` | content | Create |
| main | 32 | `hoarding-advertising-locations` | content | Create |
| main | 34 | `hoarding-campaigns-portfolio` | content | Create |
| main | 36 | `hoarding-sizes-and-costs` | content | Create |
| main | 38 | `how-we-run-your-hoarding-campaign` | content | Create |
| main | 39 | `speak-with-our-planning-team` | content | Create |
| main | 40 | `what-our-clients-say` | content | Create |
| main | 41 | `hoarding-advertising-faqs` | faq | Create |
| main | 42 | `hoarding-quote-cta` | content | Create |

Already identical on production (leave as is): `styles`.

**Hide** (`is_active=false`): `what-is-hoarding-advertising`, `clients`, `case-studies`, `why-hoarding-advertising-works-so-well-in-dubai`, `prime-locations-for-hoarding-advertising-in-dubai`, `our-hoarding-advertising-services`, `benefits-of-hoarding-advertising`, `why-choose-datamysite-for-hoarding-advertising-in-dubai`, `blogs`, `faq`.

**Page notes**
- No snippet schema found on the live page (2026-11-27); recheck with `inspect_page`.
- The cost guide post it links to exists on live only (404 on local/staging).
- Photos are stand-ins except Dubai Media City.

## 5. Wallscapes: `/outdoor-advertising/billboards-advertising/wallscapes`

Service 50 (`wallscapes`). URL and canonical unchanged. Newest production row edit on 2026-11-27: `2026-11-12T14:57:40+00:00`.

**Meta title:** Wallscape Advertising Dubai: Vinyl and Hand-Painted Walls
**Meta description:** Wallscape advertising in Dubai on building walls, printed in vinyl or painted by hand. We find the wall, get approval and install. Reply in 60 minutes.

**Rows to publish**

| Area | Sort | section_key | Type | On production |
|---|---|---|---|---|
| main | 20 | `hero` | hero | Update |
| main | 22 | `brands-we-work-with` | content | Create |
| main | 26 | `what-is-wallscape-advertising-in-dubai` | content | Create |
| main | 28 | `why-brands-use-us-for-wallscapes` | content | Create |
| main | 30 | `wallscape-formats` | content | Create |
| main | 32 | `wallscape-locations-in-dubai` | content | Create |
| main | 34 | `wallscape-work-we-have-delivered` | content | Create |
| main | 36 | `wallscape-advertising-cost-in-dubai` | content | Create |
| main | 38 | `how-we-run-your-wallscape-campaign` | content | Create |
| main | 39 | `speak-with-our-planning-team` | content | Create |
| main | 40 | `what-our-clients-say` | content | Create |
| main | 41 | `wallscape-advertising-faqs` | faq | Create |
| main | 42 | `wallscape-quote-cta` | content | Create |

Already identical on production (leave as is): `styles`.

**Hide** (`is_active=false`): `what-is-wallscape-advertising`, `clients`, `case-studies`, `why-wallscape-advertising-works-in-dubai`, `ideal-locations-for-wallscape-advertising-in-dubai`, `datamysite-s-wallscape-advertising-services`, `benefits-of-wallscape-advertising`, `why-choose-datamysite-for-wallscape-advertising`, `blogs`, `faq`.

**Page notes**
- No snippet schema found on the live page (2026-11-27); recheck with `inspect_page`.
- Agency photo is a stand-in; the case-study sentence needs DMS team confirmation.

## 6. Bridge Banners: `/outdoor-advertising/bridge-banners`

Service 126 (`bridge-banners`). URL and canonical unchanged. Newest production row edit on 2026-11-27: `2026-11-16T12:29:49+00:00`.

**Meta title:** Bridge Banner Advertising in Dubai | Locations and Costs
**Meta description:** Advertise on bridge banners in Dubai over Sheikh Zayed Road, Al Khail Road and more. Bridge locations, daily traffic, sizes, approx. costs and lead times.

**Rows to publish**

| Area | Sort | section_key | Type | On production |
|---|---|---|---|---|
| main | 20 | `hero` | hero | Update |
| main | 22 | `brands-we-work-with` | content | Create |
| main | 24 | `bridge-banner-locations-in-dubai` | content | Create |
| main | 26 | `bridge-banner-sizes-and-formats` | content | Create |
| main | 28 | `how-to-choose-the-right-bridge` | content | Create |
| main | 32 | `bridge-banner-advertising-cost-in-dubai` | content | Create |
| main | 34 | `bridge-banner-permits-and-lead-times` | content | Create |
| main | 36 | `designing-a-bridge-banner` | content | Create |
| main | 38 | `bridge-banner-vs-billboard-vs-unipole` | content | Create |
| main | 39 | `why-book-your-bridge-banner-with-dms` | content | Create |
| main | 41 | `bridge-banner-case-studies` | content | Create |
| main | 42 | `what-our-clients-say` | content | Create |
| main | 44 | `bridge-banner-faqs` | faq | Create |
| main | 46 | `bridge-banner-quote-cta` | content | Create |
| after_main | 48 | `bridge-banner-schema` | script | Create |

Already identical on production (leave as is): `styles`.

**Hide** (`is_active=false`): `mobile-hero-clamp`, `outdoor-advertising-guides`, `where-the-bridges-are`, `clients`, `sizes-and-formats`, `rates-and-lead-times`, `bridge-banner-or-unipole`, `case-studies`, `who-you-ll-work-with`, `blogs`, `faq`, `put-your-brand-over-dubai-s-busiest-roads`, `schema`.

**Page notes**
- No snippet found: the Service/LocalBusiness block on live today comes from the old `schema` row, which is hidden here and replaced by `bridge-banner-schema`.
- The road link `/al-khail-road-e311-and-al-ittihad-road-advertising-2026-rates` is live on production.
- Hero photo is a stand-in and there is no photo of Aan yet.

## 7. Flyer Distribution: `/outdoor-advertising/flyers-distribution`

Service 17 (`flyers-distribution`). URL and canonical unchanged. Newest production row edit on 2026-11-27: `2026-11-12T14:57:40+00:00`.

**Meta title:** Flyer Distribution in Dubai | Door to Door and Hand to Hand
**Meta description:** Flyer and leaflet distribution in Dubai, Sharjah and Abu Dhabi. Door to door and hand to hand, with print, permit and GPS tracked delivery in one quote.

**Rows to publish**

| Area | Sort | section_key | Type | On production |
|---|---|---|---|---|
| main | 20 | `hero` | hero | Update |
| main | 22 | `brands-we-work-with` | content | Create |
| main | 24 | `flyer-distribution-methods` | content | Create |
| main | 26 | `flyer-gps-tracking` | content | Create |
| main | 28 | `flyer-distribution-areas` | content | Create |
| main | 30 | `flyer-distribution-cost-in-dubai` | content | Create |
| main | 32 | `flyer-permits` | content | Create |
| main | 34 | `how-a-flyer-campaign-runs` | content | Create |
| main | 36 | `flyer-response` | content | Create |
| main | 38 | `why-dms-for-flyers` | content | Create |
| main | 40 | `flyer-case-studies` | content | Create |
| main | 42 | `what-our-clients-say` | content | Create |
| main | 44 | `blogs` | blogs | Update |
| main | 46 | `flyer-distribution-faqs` | faq | Create |
| main | 48 | `flyer-quote-cta` | content | Create |
| after_main | 50 | `flyer-distribution-schema` | script | Create |

Already identical on production (leave as is): `styles`.

**Hide** (`is_active=false`): `schema`, `flyer-distribution-guides`, `why-choose-flyer-distribution-in-dubai`, `clients`, `case-studies`, `our-flyer-distribution-services`, `why-choose-datamysite-for-flyer-distribution-in-dubai`, `industries-that-benefit-from-flyer-marketing`, `flyer-rates-types-areas-2026`, `faq`.

**Page notes**
- **Snippet #25** (Head, Dubai-only Service schema): **delete.** It would duplicate the new Service block (row `flyer-distribution-schema`).
- The quote modal on this page has no budget field (the fixed budget options start at "Under AED 20,000").
- **Blog banner files (git-ignored, copy by hand to `public/storage/blogs/`):** `759190226053806.webp`, `759190226053806-w480.webp`, `759190226053806-w960.webp`, `532031125112100.webp`, `532031125112100-w480.webp`, `532031125112100-w960.webp`, `532031125112100-w1440.webp`. Staging does not have them yet either.
- **Blog 759 alt text** (live still shows the slug): set to "Dubai Marina towers, a traditional souk and a villa community, areas for flyer distribution in Dubai" (connector `update_blog`; the IndexNow submit it triggers is expected on production).
- Open items: real GPS report screenshot and team photos; 6 case studies to be chosen; the live cost guide post quotes different per-flyer prices (AED 0.15-0.60 door to door, 0.25-0.80 hand to hand) from the page (0.15 / 0.20): confirm before release. The Best Areas banner looks AI-generated (sparkle mark).

## 8. Human Billboards: `/outdoor-advertising/human-billboards`

Service 18 (`human-billboards`). URL, canonical and `services.name` unchanged (no SQL beyond the shared `enquiries` columns). Newest production row edit on 2026-11-27: `2026-11-12T14:57:40+00:00`.

**Meta title:** Human Billboard Advertising Dubai | Walking LED Billboards
**Meta description:** Human billboard advertising in Dubai and across the UAE: walking and backpack LED billboards, sign spinners and mascots, with approx. cost per promoter day.

**Rows to publish**

| Area | Sort | section_key | Type | On production |
|---|---|---|---|---|
| main | 20 | `hero` | hero | Update |
| main | 22 | `brands-we-work-with` | content | Create |
| main | 24 | `human-billboard-formats` | content | Create |
| main | 26 | `where-human-billboards-work` | content | Create |
| main | 28 | `human-billboard-cost-in-dubai` | content | Create |
| main | 30 | `human-billboard-permits-and-proof` | content | Create |
| main | 32 | `why-dms-for-human-billboards` | content | Create |
| main | 34 | `human-billboard-case-studies` | content | Create |
| main | 36 | `what-our-clients-say` | content | Create |
| main | 38 | `human-billboard-guides` | content | Create |
| main | 40 | `human-billboard-faqs` | faq | Create |
| main | 42 | `human-billboard-quote-cta` | content | Create |
| after_main | 50 | `human-billboard-schema` | script | Create |

Already identical on production (leave as is): `styles`.

**Hide** (`is_active=false`): `what-is-human-billboard-advertising`, `clients`, `case-studies`, `types-of-human-billboard-advertising`, `why-choose-human-billboard-advertising-in-dubai`, `our-human-billboard-advertising-process`, `why-choose-datamysite-advertising`, `blogs`, `faq`.

**Page notes**
- **Code it depends on:** the hub, Bridge Banners and Flyer Distribution code, plus commit `b2f9ccd` (format card CSS, mockups in `public/assets/img/human-billboards/` (in Git), `fast_pages`).
- The live page's JSON-LD (2026-11-27) has one Service, one BreadcrumbList and one FAQPage (5 old questions); no snippet was found locally. Check with `inspect_page` before and after: the new page must show 6 questions and one Service (United Arab Emirates).
- The `human-billboard-guides` row links two live posts and `/experiential-marketing/mall-activations-dubai`.
- **Blog banner WebP files (git-ignored, copy by hand to `public/storage/blogs/`):** `8290125011943.webp`, `8290125011943-w480.webp`, `924070826044454.webp`, `924070826044454-w480.webp`. Staging and production both have the JPGs but not these (cards fall back to the JPGs).
- Open items: the hero photo and the human billboards blog banner show other brands' ads (replace with DMS campaign photos; hero kept by the owner's choice); the format images are drawn mockups with no visible "mockup" label (owner asked to remove it; the alt text says mockup); 6 case studies to be chosen; confirm the permit, supervisor, GPS route log and AED 300 claims.

## 9. Lamp Post: `/outdoor-advertising/lamp-post`

Service 52 (`lamp-post`). URL, canonical and `services.name` ("Lamp Post") unchanged (no SQL beyond the shared `enquiries` columns). Newest production row edit on 2026-11-27: `2026-11-10T13:42:10+00:00`. **Local and staging DB have the same rows and meta (2026-11-27); code `674add3` is live on staging.** Staging page checked: one H1, one FAQPage with 7 questions, one Service for the United Arab Emirates, no sideways scroll at 1440 px. PageSpeed not measured yet.

**Meta title:** Lamppost Advertising in Dubai | Routes, Permits and Cost
**Meta description:** Book lamppost advertising in Dubai on the roads your customers use. We plan the route, handle the permit, print, install and check every pole.

**Rows to publish**

| Area | Sort | section_key | Type | On production |
|---|---|---|---|---|
| main | 20 | `hero` | hero | Update |
| main | 22 | `brands-we-work-with` | content | Create |
| main | 24 | `why-brands-book-lamp-posts` | content | Create |
| main | 26 | `why-dms-for-lamp-posts` | content | Create |
| main | 28 | `lamp-post-formats` | content | Create |
| main | 30 | `lamp-post-roads-and-areas` | content | Create |
| main | 32 | `how-many-lamp-posts` | content | Create |
| main | 34 | `lamp-post-advertising-cost-in-dubai` | content | Create |
| main | 36 | `lamp-posts-or-billboards` | content | Create |
| main | 38 | `how-a-lamp-post-campaign-runs` | content | Create |
| main | 40 | `lamp-post-artwork-and-uae-coverage` | content | Create |
| main | 42 | `lamp-post-case-studies` | content | Create |
| main | 44 | `lamp-post-faqs` | faq | Create |
| main | 46 | `lamp-post-quote-cta` | content | Create |
| after_main | 48 | `lamp-post-schema` | script | Create |

Already identical on production (leave as is): `styles`.

**Hide** (`is_active=false`): `schema` (old FAQPage), `mobile-hero-clamp`, `what-is-lamp-post-advertising`, `clients`, `case-studies`, `why-choose-lamp-post-advertising-in-dubai`, `types-of-lamp-post-advertising-in-dubai`, `our-lamp-post-advertising-services-in-dubai`, `cost-of-lamp-post-advertising-in-dubai`, `advantages-of-choosing-datamysite`, `light-up-dubai-with-your-brand`, `blogs`, `faq`.

**Page notes**
- **Code it depends on:** the hub, Bridge Banners and Flyer Distribution code, plus commit `674add3` (`.sv2-route-plan` CSS, `fast_pages`).
- No snippet found locally; check with `inspect_page`: after release the page needs one Service (United Arab Emirates) and one FAQPage with 7 questions.
- The brief has no testimonials or blog block, so neither is on the page.
- **Must fix before release, the blogs contradict the page's prices:** post 921 (the linked guide) says AED 15,000-50,000 per route package plus AED 5,000-12,000+ permits; post 942 says AED 300-800 per unit and RTA approval (the page says AED 2,000 / 3,500 / 10,000 per pole and a municipal permit). Both have their own FAQPage with these figures.
- **Prices change a lot:** live today says from AED 500 per pole; the new page says approx. avg. AED 2,000. Confirm before release.
- The Al Barsha example says approx. avg. AED 40,000 for media plus print (owner's choice, brief wording). The page's own rates give AED 40,000 media + AED 3,360 print (28 x 120) = AED 43,360.
- Hero photo (`service-new/lamp-post`) shows another brand's ads (Rivoli Shop), kept by the owner's choice: replace with a DMS campaign photo. No other images were added.
- Open items: 6 case studies to be chosen (the slider shows all); DMS team to confirm the route drive before quoting, weekly checks, free banner replacement, dated photo report and overnight installation.

## 10. Unipoles: `/outdoor-advertising/unipoles`

Service 51 (`unipoles`). URL, canonical and `services.name` ("Unipoles") unchanged (no SQL beyond the shared `enquiries` columns). Newest production row edit on 2026-11-27: `2026-11-12T14:57:40+00:00`. **Local and staging DB have the same rows and meta (2026-11-27); code `4d6e904` is live on staging.** Staging page checked: all 15 rows identical to local, one H1, one FAQPage with 7 questions, one Service for the United Arab Emirates, the five linked pages load, no sideways scroll, broken images or console errors at 1440 and 390 px. PageSpeed on staging, 3 runs: mobile 98 each (FCP 1.4, LCP 2.1-2.3, TBT 0-40 ms, CLS 0, SI 2.6-3.2), desktop 96-100 (SI 1.0-2.0, two runs got a Speed Index warning), accessibility, best practices and SEO 100.

**Meta title:** Unipole Advertising in Dubai | Static and Digital Unipoles
**Meta description:** Book static and digital unipole advertising in Dubai on Sheikh Zayed Road and the main highways. We check the site, print, install and send proof photos.

**Rows to publish**

| Area | Sort | section_key | Type | On production |
|---|---|---|---|---|
| main | 20 | `hero` | hero | Update |
| main | 22 | `brands-we-work-with` | content | Create |
| main | 24 | `why-brands-book-unipoles` | content | Create |
| main | 26 | `why-dms-for-unipoles` | content | Create |
| main | 28 | `unipole-formats` | content | Create |
| main | 30 | `digital-unipoles` | content | Create |
| main | 32 | `unipole-roads` | content | Create |
| main | 34 | `unipole-advertising-cost-in-dubai` | content | Create |
| main | 36 | `unipole-or-another-format` | content | Create |
| main | 38 | `how-a-unipole-campaign-runs` | content | Create |
| main | 40 | `unipole-uae-coverage` | content | Create |
| main | 42 | `unipole-case-studies` | content | Create |
| main | 44 | `unipole-faqs` | faq | Create |
| main | 46 | `unipole-quote-cta` | content | Create |
| after_main | 48 | `unipole-schema` | script | Create |

Already identical on production (leave as is): `styles`.

**Hide** (`is_active=false`): `what-is-unipole-advertising`, `clients`, `case-studies`, `why-choose-unipole-advertising-in-dubai`, `types-of-unipole-advertising-in-dubai`, `our-unipole-advertising-services-in-dubai`, `advantages-of-partnering-with-datamysite`, `make-your-brand-tower-above-the-rest`, `blogs`, `faq`, `scripts` (this one holds the old FAQPage).

**Page notes**
- **Code it depends on:** the hub, Bridge Banners, Flyer Distribution and Lamp Post code, plus this page's `.sv2-uni-*` CSS and `fast_pages` entry.
- No snippet found locally; check with `inspect_page`: after release the page needs one Service (United Arab Emirates) and one FAQPage with 7 questions.
- The brief has no testimonials or blog block, so neither is on the page. The road list is a table (the brief's fallback until there are real site photos).
- **Price conflicts, fix before release** (owner chose to change this page only). The page says approx. avg. AED 45,000 secondary highways, 175,000 prime Sheikh Zayed Road, 20,000 per digital slot, 25,000 print and install:
  - Hub rows `outdoor-advertising-dubai-prices-2026`, `outdoor-advertising-formats-in-dubai`, `outdoor-advertising-dubai-faqs` and Billboards rows `billboard-sizes-and-prices-in-dubai`, `billboard-advertising-dubai-faqs`: secondary AED 40,000, main highway AED 100,000. Billboards also lists unipole sizes of 12 x 4 m or 14 x 5 m (this page's FAQ: 8 x 4 m to 14 x 6 m).
  - Home FAQ (`home-v2.blade.php`): highway unipole AED 100,000.
  - `/digital-advertising` row `dubai-digital-advertising-what-each-format-does`: digital unipoles AED 60,000.
  - **The linked cost guide** (blog 899, also live): secondary 35,000-50,000, prime Sheikh Zayed Road from about 225,000, production 15,000-40,000, plus its own FAQPage.
  - Local DB blogs 531 (from 35,000), 817 (from 40,000, SZR 100,000-350,000, digital 15,000-25,000), 952 (from 45,000, SZR 75,000-220,000). Meta descriptions of those posts, `/unipole-advertising-in-dubai-stand-tall-stay-visible` and `/digital-advertising/dooh-advertising/digital-unipole` (AED 60,000) quote prices too.
- Hero photo is the Outdoor Advertising hub photo, kept by the owner's choice: there is no real DMS unipole photo yet. No images were added.
- Open items: real unipole photos (hero and road cards); 6 case studies to be chosen (the slider shows all); DMS team to confirm the site photos, hold, overnight install, next-morning check, mid-campaign check, lighting from dusk, face sizes and lead times.

## Adding a new page to this file

Copy a section above and fill in: URL, service id and slug, meta title and description, rows to publish (area, sort, key, type, Create/Update), rows to hide, `services.name` or other SQL, snippets to change (check the live page's JSON-LD, local copies can differ), files that Git does not carry, code it depends on, and open items. Add its line to the "Pages" table.
