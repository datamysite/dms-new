# dgnova — Website Frontend

Multi-page static frontend for **dgnova** (UAE multi-sector company: Real Estate, Car Rental, Car Import & Export, Travel & Tourism, General Trading).

Built with **HTML5 + CSS3 + Bootstrap 5 + jQuery** only. No build step, no backend.

---

## Run it

**Option A — just open it**
Double-click `index.html`. The site works directly from the file system (listing data is embedded via `assets/data/data.js` as a fallback).

**Option B — local server (recommended)**
- VS Code: right-click `index.html` → *Open with Live Server*, or
- Terminal: `python -m http.server 8080` inside this folder, then open `http://localhost:8080`

---

## Before going live — replace placeholders

All contact details live in **one file**: `assets/js/config.js`

| Key | Currently | Replace with |
|---|---|---|
| `phone` / `phoneHref` | `+971 X XXX XXXX` | Real display number / tel: digits |
| `whatsapp` | `9710000000000` | Real WhatsApp number (digits only, no `+`) |
| `email` | `info@dgnova.com` | Confirmed email |
| `address`, `businessHours` | placeholders | Real details |
| `socialLinks` | `#` | Real profile URLs |

Also:
- **Legal pages** (`privacy-policy.html`, `terms.html`) contain drafted placeholder copy — have a UAE legal adviser review before launch.
- **Form submission** is simulated (front-end only). On submit, forms validate, show a loading state, then redirect to `thank-you.html` (or show inline success). Wire a real endpoint in `assets/js/enquiry.js` → `handleSubmit()` where marked.

---

## Content management

Listings are data-driven from JSON in `assets/data/`:

- `properties.json` — real estate (purpose: sale/rent, price, beds, area, gallery…)
- `cars.json` — rental fleet (daily/weekly/monthly rates, specs, conditions, documents)
- `tours.json` — travel packages (itinerary, included/excluded; all price-on-request)
- `products.json` — trading catalogue (categories are generated from this file)

**After editing any JSON**, regenerate the embedded fallback so `file://` opening keeps working:

```bash
python3 - <<'EOF'
import json
out=['/* Generated from assets/data/*.json */','window.dgnova_DATA = {']
parts=[]
for k in ['properties','cars','tours','products']:
    parts.append(f'  "{k}": '+json.dumps(json.load(open(f'assets/data/{k}.json')), ensure_ascii=False))
out.append(',\n'.join(parts)); out.append('};')
open('assets/data/data.js','w').write('\n'.join(out))
EOF
```

(If the site only ever runs on a server, you can skip this — JSON is fetched first.)

Detail pages read the item via query string, e.g. `property-details.html?id=property-001`. Invalid IDs get a styled "Listing Not Found" state.

Images use Unsplash URLs in the JSON; any failed image automatically falls back to branded SVG placeholders in `assets/images/`.

---

## Structure

```
├── index.html … 404.html        (17 pages)
├── assets/
│   ├── css/  style.css          design system + components
│   │         responsive.css     breakpoints, mobile behaviours
│   ├── js/   config.js          ← site config (edit this)
│   │         main.js            header, nav, data loader, utilities
│   │         listings.js        card renderers + related items
│   │         filters.js         listing filter/sort engine
│   │         enquiry.js         enquiry modal/forms/WhatsApp context
│   │         details.js         detail pages + gallery/lightbox
│   ├── data/ *.json + data.js
│   └── images/                  favicon + branded fallbacks
```

## Design tokens

Brand colors and spacing are CSS variables at the top of `assets/css/style.css`:
`--primary: #0c2464` (navy), `--secondary: #41b121` (green, conversion only), radius/shadow/type scales. Changing the brand = editing `:root`.
