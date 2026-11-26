{{--
|==========================================================================
| SERVICE PAGE V2 — redesigned template for every individual service page
|--------------------------------------------------------------------------
| One file renders all service / sub-service pages. Content is NOT duplicated:
| it still comes from the page's own `service_sections` rows (same as the live
| web.services-new.master-service), so each page keeps its dedicated text.
|
|   hero         -> rebuilt as the Home V2 hero (title / intro / enquiry form parsed from the row)
|   clients      -> Home V2 two-row client marquee
|   case-studies -> Home V2 case-study carousel
|   blogs        -> Home V2 blog cards ($blogs; "See all" link taken from the row)
|   faq          -> Home V2 accordion (questions/answers parsed from the row)
|   everything else (content / subservices / custom) -> rendered as-is inside .sv2-legacy and
|                   restyled + animated by service-v2.css / service-v2.js
|
| Layout : web.home-v2.master (new header + footer)
| Assets : home-v2.css + home-v2.js (shared design system / motion engine)
|          service-v2.css + service-v2.js (service-specific)
| Preview: /v2/<service-url>  (routes/web.php) — live pages are untouched.
|==========================================================================
--}}
@extends('web.home-v2.master')
@php require_once resource_path('views/web/home-v2/helpers.php'); @endphp

@php
	/* ---------- page + sections (same query as the live master-service) ---------- */
	$sv2Page = $subservice ?? $service;
	$sv2All = \App\Models\ServiceSection::where('service_id', $sv2Page->id)->where('is_active', 1)->orderBy('sort_order')->get();
	$sv2Areas = $sv2All->groupBy('area');
	$sv2Vars = \Illuminate\Support\Arr::only(get_defined_vars(), ['nav', 'service', 'subservice', 'mainservice', 'subservices', 'blogs', 'publications', 'web_drop', 'noPopup', 'errors']);
	/* Rendering the DB Blade rows is the slowest server step (~20 ms per row). The rendered HTML is cached per row and per request-scoped
	   memo; the key changes when the row is edited (updated_at) or its data changes (blogs / sub-services / publications / page).
	   The session CSRF token is stored as a placeholder and put back on every request. Rows that read request/session state
	   ($errors, old(), session(), request(), auth) are never cached. Any cache problem falls back to a normal render. */
	$sv2Sig = md5(json_encode([
		$sv2Page->id, optional($service ?? null)->id,
		collect($blogs ?? [])->map(fn ($b) => [$b->id, (string) $b->updated_at])->values(),
		collect($subservices ?? [])->map(fn ($s) => [$s->id, (string) $s->updated_at])->values(),
		isset($publications) ? [count($publications), (string) collect($publications)->max('updated_at')] : null,
	]));
	$sv2Memo = [];
	$sv2Render = function ($sec) use ($sv2Vars, $sv2Sig, &$sv2Memo) {
		if (isset($sv2Memo[$sec->id])) return $sv2Memo[$sec->id];
		$render = fn () => \Illuminate\Support\Facades\Blade::render($sec->content, $sv2Vars);
		if (preg_match('/\$errors|old\(|session\(|request\(|\bauth\b|@auth|@guest/i', (string) $sec->content)) return $sv2Memo[$sec->id] = $render();
		$tok = (string) csrf_token();
		if (strlen($tok) < 20) return $sv2Memo[$sec->id] = $render();
		try {
			$html = \Illuminate\Support\Facades\Cache::remember('sv2r_' . $sec->id . '_' . optional($sec->updated_at)->timestamp . '_' . $sv2Sig, 3600,
				fn () => str_replace($tok, '%%SV2CSRF%%', $render()));
			return $sv2Memo[$sec->id] = str_replace('%%SV2CSRF%%', $tok, $html);
		} catch (\Throwable $e) {
			return $sv2Memo[$sec->id] = $render();
		}
	};
	$sv2Area = function ($area) use ($sv2Areas, $sv2Render) { $h = ''; foreach ($sv2Areas->get($area, collect()) as $s) { $h .= $sv2Render($s) . "\n"; } return $h; };

	/* small DOM helper for reading the legacy markup */
	$sv2Dom = function ($html) {
		$d = new \DOMDocument();
		libxml_use_internal_errors(true);
		$d->loadHTML('<' . '?xml encoding="utf-8"?' . '><div id="sv2root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();
		return [$d, new \DOMXPath($d)];
	};
	$sv2Text = function ($n) { return trim(preg_replace('/\s+/u', ' ', $n ? $n->textContent : '')); };
	$sv2Inner = function ($n) { $h = ''; if ($n) { foreach ($n->childNodes as $c) { $h .= $n->ownerDocument->saveHTML($c); } } return trim($h); };

	/* ---------- hero: title, subtitle and intro from the page's own hero row ---------- */
	/* The hero's copy column is reused VERBATIM and in its original order — h1 (small coral label), h2 (big headline),
	   h4 (supporting line), p (intro), CTA span — with its bold keywords and links. SEO copy and structure are untouched;
	   service-v2.css keeps the live size hierarchy (16 / 56 / 24 / 18px on desktop). Each block only gets a reveal hook. */
	$sv2Hero = ['body' => '', 'photo' => null, 'sparse' => true, 'form' => true, 'title1' => false];
	$sv2HeroRow = $sv2All->first(fn ($s) => $s->area === 'main' && $s->type === 'hero');
	if ($sv2HeroRow) {
		[$d, $x] = $sv2Dom($sv2Render($sv2HeroRow));
		$h1 = $x->query('//h1')->item(0);
		$col = $x->query('//*[contains(concat(" ", normalize-space(@class), " "), " head-para-service ")]')->item(0) ?: ($h1 ? $h1->parentNode : null);
		if ($col && $col->getAttribute('id') !== 'sv2root') {
			/* the page's own breadcrumb (if any) is dropped: V2 shows one shared breadcrumb (web.home-v2.breadcrumbs) */
			foreach (iterator_to_array($x->query('.//*[contains(translate(@class, "BREADCUMS", "breadcums"), "breadcrumb")]', $col)) as $bc) {
				if ($bc->parentNode) $bc->parentNode->removeChild($bc);
			}
			$i = 0;
			foreach (iterator_to_array($col->childNodes) as $node) {
				if ($node->nodeType !== XML_ELEMENT_NODE || $node->nodeName === 'br') continue;
				$node->setAttribute('data-reveal', 'up');
				$node->setAttribute('data-delay', (string) min($i++, 5));
			}
			/* "sparse" hero = only an h1 (+ breadcrumb), no headline / intro copy yet (e.g. website-development pages):
			   the h1 is then shown as the big headline so the hero still reads well until copy is added */
			$sv2Hero['sparse'] = $x->query('.//h2|.//h3|.//h4|.//h5|.//p', $col)->length === 0;
			/* DB marker: class sv2-hero-title-first on the text column = the h1 is the large headline, the h2 a smaller sub line (no h4) */
			$sv2Hero['title1'] = in_array('sv2-hero-title-first', preg_split('/\s+/', trim($col->getAttribute('class'))), true);
			$sv2Hero['body'] = $sv2Inner($col);
		}
		/* the hero's own dedicated background image (from its inline style) */
		$bg = $x->query('//*[contains(@style,"background-image")]')->item(0);
		if ($bg && preg_match('/url\(\s*[\'"]?([^\'")]+)/i', $bg->getAttribute('style'), $m)) $sv2Hero['photo'] = trim($m[1]);
	}


	/* ---------- breadcrumb trail: Home > main service > service > sub-service (live URLs, not /v2) ---------- */
	$sv2CrumbUrl = function ($slugs) { return URL::to('/' . implode('/', array_filter($slugs))); };
	$sv2Crumbs = [['Home', URL::to('/')]];
	if (!empty($mainservice)) $sv2Crumbs[] = [$mainservice->name, $sv2CrumbUrl([$mainservice->slug])];
	if (!empty($subservice)) $sv2Crumbs[] = [$service->name, $sv2CrumbUrl([optional($mainservice ?? null)->slug, $service->slug])];
	$sv2Crumbs[] = [$sv2Page->name, null];
	$sv2CrumbSchema = !$sv2All->contains(fn ($x) => stripos((string) $x->content, 'BreadcrumbList') !== false);
	/* structured data this template adds itself — skipped when the page's own (database) sections already carry it */
	/* ... and by the database snippets (table snippet_code) that production shows for this URL ($headSnippet / $bodySnippet) */
	$sv2Snip = fn ($t) => v2_snippet_has($t, $headSnippet ?? null, $bodySnippet ?? null);
	$sv2ServiceSchema = config('seo.template_schema', true) && !$sv2All->contains(fn ($x) => preg_match('/"@type"\s*:\s*"(Service|LocalBusiness|ProfessionalService)"|local-schema|service-schema/i', (string) $x->content))
		&& !$sv2Snip('Service') && !$sv2Snip('LocalBusiness');
	$sv2FaqSchema = !$sv2All->contains(fn ($x) => stripos((string) $x->content, 'FAQPage') !== false) && !$sv2Snip('FAQPage');

	/* fallback photo when the hero row has no background image */
	$sv2Photo = $sv2Hero['photo'];
	if (!$sv2Photo) {
		foreach (['assets/img/service-new/' . $sv2Page->slug . '.jpg', 'assets/img/service-new/' . $sv2Page->slug . '.png'] as $cand) {
			if (is_file(public_path($cand))) { $sv2Photo = URL::to('/public/' . $cand); break; }
		}
	}
	/* the hero photo is the LCP element: serve its WebP sibling (e.g. digital-marketing.png 1.6 MB -> .webp 56 KB) when one exists */
	/* (the stored hero may already point at the .webp: its -w960 sibling is found the same way) */
	if ($sv2Photo && preg_match('#/public/(.+)\.(jpe?g|png|webp)$#i', parse_url($sv2Photo, PHP_URL_PATH) ?? '', $sv2Pm)) {
		if (is_file(public_path($sv2Pm[1] . '.webp'))) $sv2Photo = URL::to('/public/' . $sv2Pm[1] . '.webp');
		/* phones get the 960 px version (the photo is a faded backdrop there) */
		if (is_file(public_path($sv2Pm[1] . '-w960.webp'))) $sv2PhotoSm = URL::to('/public/' . $sv2Pm[1] . '-w960.webp');
	}
	$sv2PhotoSm = $sv2PhotoSm ?? null;
	/* images are cached for a year under the same URL (browser + CDN): a re-encoded hero must change URL, so add ?v=<file time> */
	$sv2Ver = function ($u) {
		if (!$u || !preg_match('#/public/(.+)$#', (string) parse_url($u, PHP_URL_PATH), $m) || !is_file(public_path($m[1]))) return $u;
		return $u . '?v=' . filemtime(public_path($m[1]));
	};
	$sv2Photo = $sv2Ver($sv2Photo); $sv2PhotoSm = $sv2Ver($sv2PhotoSm);

	/* ---------- FAQ rows -> question/answer pairs ---------- */
	$sv2FaqParse = function ($html) use ($sv2Dom, $sv2Text, $sv2Inner) {
		[$d, $x] = $sv2Dom($html); $out = [];
		foreach ($x->query('//*[contains(concat(" ", normalize-space(@class), " "), " card ")]') as $card) {
			$q = $x->query('.//*[contains(@class,"faq-title")]', $card)->item(0) ?: $x->query('.//h5', $card)->item(0);
			$a = $x->query('.//*[contains(@class,"card-body")]', $card)->item(0);
			if ($q && $a && $sv2Text($q) !== '') $out[] = [$sv2Text($q), $sv2Inner($a)];
		}
		return $out;
	};

	/* Media in the legacy (DB) sections — output only, the database content is not changed:
	   iframes (YouTube, maps) load lazily; <img> of local JPG/PNG uses the WebP sibling when it exists, gets intrinsic
	   aspect-ratio when it has no size (no layout shift) and loading="lazy" + decoding="async" (these sections are below the hero). */
	$sv2Media = function ($html) {
		$html = preg_replace('#<iframe\b(?![^>]*\bloading=)#i', '<iframe loading="lazy"', (string) $html);
		/* YouTube facade: the iframe keeps its classes/size but first shows the video thumbnail + play button (srcdoc);
		   the ~1.2 MB player only loads when the visitor presses play (then autoplays). */
		$html = preg_replace_callback('#<iframe\b[^>]*\ssrc\s*=\s*["\'](https?://(?:www\.)?youtube(?:-nocookie)?\.com/embed/([A-Za-z0-9_-]{6,}))([^"\']*)["\'][^>]*>#i', function ($m) {
			if (stripos($m[0], 'srcdoc') !== false) return $m[0];
			$play = $m[1] . (strpos($m[3], '?') === false ? '?' : $m[3] . '&') . 'autoplay=1';
			$thumb = 'https://i.ytimg.com/vi/' . $m[2] . '/hqdefault.jpg';
			$doc = '<style>*{margin:0;padding:0}html,body{height:100%;overflow:hidden;background:#000}a{position:relative;display:block;height:100%}'
				. 'img{width:100%;height:100%;object-fit:cover}span{position:absolute;left:50%;top:50%;width:68px;height:48px;margin:-24px 0 0 -34px;border-radius:12px;background:#f00;opacity:.9}'
				. 'span:after{content:"";position:absolute;left:27px;top:14px;border-style:solid;border-width:10px 0 10px 17px;border-color:transparent transparent transparent #fff}a:hover span{opacity:1}</style>'
				. '<a href="' . e($play) . '"><img src="' . e($thumb) . '" alt="Play video" loading="lazy"><span></span></a>';
			return preg_replace('#^<iframe\b#i', '<iframe srcdoc="' . htmlspecialchars($doc, ENT_QUOTES) . '"', $m[0]);
		}, $html);
		/* links the legacy text wrote as href="javascript:void(0)" are not crawlable (Lighthouse "Links are not crawlable"): give them a
		   real target and keep the behaviour — "scroll to top" links point at the hero, the copy-email link becomes a mailto that the
		   page's own click handler cancels (it copies the address instead of opening a mail program) */
		$html = preg_replace_callback('#<a\b[^>]*\shref\s*=\s*["\']javascript:(?:void\(0\)|;)?["\'][^>]*>#i', function ($m) {
			$tag = $m[0];
			if (preg_match('#copyEmail\(\s*[\'"]([^\'"]+@[^\'"]+)[\'"]#i', $tag, $e)) {
				$tag = preg_replace('#href\s*=\s*["\']javascript:[^"\']*["\']#i', 'href="mailto:' . e($e[1]) . '"', $tag);
				return preg_replace('#onclick\s*=\s*"([^"]*)"#i', 'onclick="$1 return false;"', $tag);
			}
			$target = stripos($tag, 'scroll-up') !== false ? '#hv2-hero' : '#';
			return preg_replace('#href\s*=\s*["\']javascript:[^"\']*["\']#i', 'href="' . $target . '"', $tag);
		}, (string) $html);
		return preg_replace_callback('#<img\b[^>]*>#i', function ($m) {
			$tag = $m[0];
			if (preg_match('#\ssrc\s*=\s*["\']([^"\']+)["\']#i', $tag, $s) && preg_match('#/public/([^?"\']+?)\.(jpe?g|png)(\?[^"\']*)?$#i', $s[1], $p)) {
				$rel = $p[1] . '.' . $p[2];
				if (is_file(public_path($p[1] . '.webp'))) $tag = str_replace($s[1], str_replace($rel, $p[1] . '.webp', $s[1]), $tag);
				/* legacy text writes width="100%" with no height: the browser cannot reserve space from that (PageSpeed "unsized images").
				   Use the file's real width/height as attributes (they give the aspect ratio) and keep the image fluid with CSS, as before */
				if (preg_match('#\swidth\s*=\s*["\']\s*\d+(?:\.\d+)?%\s*["\']#i', $tag) && !preg_match('#\sheight\s*=#i', $tag) && ($d = @getimagesize(public_path($rel))) && $d[0] && $d[1]) {
					$tag = preg_replace('#\swidth\s*=\s*["\'][^"\']*["\']#i', ' width="' . (int) $d[0] . '" height="' . (int) $d[1] . '"', $tag, 1);
					$tag = preg_match('#\sstyle\s*=\s*"#i', $tag) ? preg_replace('#(\sstyle\s*=\s*")#i', '$1width:100%;height:auto;', $tag, 1) : preg_replace('#^<img\b#i', '<img style="width:100%;height:auto"', $tag);
				}
				/* aspect-ratio (not width/height attributes): reserves space without overriding any size the legacy CSS sets */
				if (!preg_match('#\s(width|height)\s*=#i', $tag) && stripos($tag, 'aspect-ratio') === false && ($d = @getimagesize(public_path($rel))) && $d[0] && $d[1]) {
					$ar = 'aspect-ratio: ' . (int) $d[0] . ' / ' . (int) $d[1] . ';';
					$tag = preg_match('#\sstyle\s*=\s*"#i', $tag) ? preg_replace('#(\sstyle\s*=\s*")#i', '$1' . $ar . ' ', $tag, 1) : preg_replace('#^<img\b#i', '<img style="' . $ar . '"', $tag);
				}
			}
			/* no alt attribute at all -> derive a readable one from the file name (existing alt text, even empty/decorative, is kept) */
			if (!preg_match('#\salt\s*=#i', $tag)) {
				$altFile = preg_match('#([^/"\'?\s]+?)(?:-w\d+)?\.(?:jpe?g|png|webp|gif|svg)(?:\?[^"\']*)?["\']#i', $tag, $af) ? trim(preg_replace('#[-_\s]+#', ' ', $af[1])) : '';
				$tag = preg_replace('#^<img\b#i', '<img alt="' . e($altFile !== '' ? ucfirst($altFile) : 'DataMySite') . '"', $tag);
			}
			if (!preg_match('#\sloading\s*=#i', $tag)) $tag = preg_replace('#^<img\b#i', '<img loading="lazy"', $tag);
			if (!preg_match('#\sdecoding\s*=#i', $tag)) $tag = preg_replace('#^<img\b#i', '<img decoding="async"', $tag);
			return $tag;
		}, $html);
	};

	/* One enquiry form per page: the V2 hero form. Any extra in-content enquiry form (e.g. the "Want to know more?
	   Ask us anything" side form on website-development pages) is dropped from that section, and the text column
	   beside it is widened to full width. The database content is not changed. */
	$sv2StripForms = function ($html) use ($sv2Dom, $sv2Inner) {
		/* also the empty .aside-block column that the site's aside.js fills with a form via AJAX */
		if (stripos($html, '<form') === false && stripos($html, 'aside-block') === false) return $html;
		[$d, $x] = $sv2Dom($html);
		$hits = $x->query('//*[contains(concat(" ", normalize-space(@class), " "), " aside-form ")] | //*[contains(concat(" ", normalize-space(@class), " "), " aside-block ")] | //form[not(contains(concat(" ", normalize-space(@class), " "), " sv2-lead-form "))]');
		if (!$hits->length) return $html;
		foreach (iterator_to_array($hits) as $node) {
			if (!$node->parentNode) continue;                  /* already removed with an ancestor */
			$col = $node;                                      /* the Bootstrap column that holds the form */
			while ($col && $col->nodeType === XML_ELEMENT_NODE && !preg_match('/\bcol-/', (string) $col->getAttribute('class'))) $col = $col->parentNode;
			$target = ($col && $col->nodeType === XML_ELEMENT_NODE && $col->getAttribute('id') !== 'sv2root') ? $col : $node;
			$row = $target->parentNode;
			if (!$row) continue;                               /* already removed together with an ancestor */
			$row->removeChild($target);
			if ($row && $row->nodeType === XML_ELEMENT_NODE) {
				$left = [];
				foreach ($row->childNodes as $c) if ($c->nodeType === XML_ELEMENT_NODE && preg_match('/\bcol-/', (string) $c->getAttribute('class'))) $left[] = $c;
				if (count($left) === 1) $left[0]->setAttribute('class', preg_replace('/\bcol-lg-\d+\b/', 'col-lg-12', $left[0]->getAttribute('class')));
			}
		}
		return $sv2Inner($x->query('//*[@id="sv2root"]')->item(0));
	};

	/* the FAQ row's own section heading (e.g. "FAQs") */
	$sv2FaqTitle = function ($html) use ($sv2Dom, $sv2Text) {
		[$d, $x] = $sv2Dom($html);
		$h = $x->query('//h1|//h2|//h3|//h4')->item(0);
		return $h ? $sv2Text($h) : '';
	};

	/* ---------- blogs row -> its own heading, sub-heading and "See all" link ---------- */
	$sv2BlogMeta = function ($html) use ($sv2Dom, $sv2Text) {
		[$d, $x] = $sv2Dom($html);
		$h = $x->query('//h2|//h3|//h4')->item(0);
		$p = $x->query('//p[not(ancestor::*[contains(@class,"popular-card")]) and not(ancestor::a)]')->item(0);
		$a = $x->query('//a[contains(@class,"see-all-btn")]')->item(0);
		return [
			'title' => $h ? $sv2Text($h) : '',
			'sub' => $p ? $sv2Text($p) : '',
			/* always the blog collection: the stored "See All" links (111 sections) point at one category (/blogs/pr-and-media-relations) */
			'href' => route('blogs'),
			'label' => $a && $sv2Text($a) !== '' ? $sv2Text($a) : 'See All Blogs',
		];
	};

	$hv2Img = URL::to('/public/assets/img');
	$hv2Cases = v2_case_studies();   /* config/case_studies.php */
@endphp

{{-- fast loading mode (same as Home V2): third-party/legacy scripts wait for the first interaction,
     the hero enquiry form keeps the phone-input library, Swiper stays eager for the case-study carousel --}}
@section('v2lite', '1')
@section('v2crit', 'assets/css/service.critical.css')
@section('v2form', '1')
@section('v2keep', '1')
{{-- pages listed in config/v2.php (fast_pages) also use the icon subset (no 128 KB icon font) and load custom/responsive CSS without blocking the first paint;
     a test checks that every icon on those pages is in the subset --}}
@if(in_array($sv2Page->slug, (array) config('v2.fast_pages', []), true))
@section('v2icons', '1')
@section('v2cssasync', '1')
@endif

@section('metaAddition')
	<script>(function(){var d=document.documentElement;try{if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches)return;}catch(e){}d.classList.add('hv2-loading');setTimeout(function(){d.classList.remove('hv2-loading','hv2-leaving');},500);})();</script>
	<link rel="preload" as="image" href="{{ v2_asset('assets/img/logo-white.webp', false) }}">
	{{-- hero photo = LCP element: preload the right size per screen --}}
	@if($sv2Photo && $sv2PhotoSm)
		<link rel="preload" as="image" href="{{ $sv2PhotoSm }}" media="(max-width: 767px)" fetchpriority="high">
		<link rel="preload" as="image" href="{{ $sv2Photo }}" media="(min-width: 768px)" fetchpriority="high">
		<style>@media (max-width: 767px) { .sv2-hero .sv2-hero-photo { background-image: url('{{ $sv2PhotoSm }}') !important; } }</style>
	@elseif($sv2Photo)
		<link rel="preload" as="image" href="{{ $sv2Photo }}" fetchpriority="high">
	@endif
	{{-- self-hosted fonts: the two key files are preloaded, @font-face is inlined (font-display: swap) --}}
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/league-spartan-latin.woff2') }}">
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/nunito-sans-latin.woff2') }}">
	<style>{!! str_replace('../fonts/', URL::to('/public/assets/fonts') . '/', (string) @file_get_contents(public_path('assets/css/v2-fonts.css'))) !!}</style>
@endsection

@section('addStyle')
	{{-- the page's own legacy styles first (service.css + any page-specific CSS), then the v2 layers win --}}
	{!! preg_replace("~(/assets/css/service\.css)([\"'])~", "$1?v=" . @filemtime(public_path("assets/css/service.css")) . "$2", $sv2Area("head")) !!}
	<link href="{{ v2_asset('assets/css/home-v2.css') }}" rel="stylesheet">
	<link href="{{ v2_asset('assets/css/service-v2.css') }}" rel="stylesheet">
@endsection

@section('content')
@if($sv2ServiceSchema)@include('partials.service-schema', ['page' => $sv2Page])@endif
<main class="main hv2 sv2" id="hv2" data-sv2-group="{{ ($mainservice ?? $service)->slug }}" data-sv2-page="{{ $sv2Page->slug }}" data-sv2-name="{{ $sv2Page->name }}">

	<div class="hv2-progress" aria-hidden="true"><span></span></div>
	<div class="hv2-cursor" aria-hidden="true"><span class="hv2-cursor-dot"></span><span class="hv2-cursor-ring"></span></div>
	<div class="hv2-grain" aria-hidden="true"></div>

	<button type="button" class="hv2-top" id="hv2-top" aria-label="Back to top">
		<svg class="hv2-top-ring" viewBox="0 0 48 48" aria-hidden="true"><circle class="trk" cx="24" cy="24" r="21"/><circle class="bar" cx="24" cy="24" r="21"/></svg>
		<i class="bi bi-arrow-up hv2-top-arrow" aria-hidden="true"></i>
		<span class="hv2-top-label" aria-hidden="true">Top</span>
	</button>

	<svg class="hv2-defs" width="0" height="0" aria-hidden="true" focusable="false">
		<filter id="hv2-distort" x="-10%" y="-10%" width="120%" height="120%">
			<feTurbulence type="fractalNoise" baseFrequency="0.012 0.02" numOctaves="2" seed="3" result="noise"/>
			<feDisplacementMap in="SourceGraphic" in2="noise" scale="0" xChannelSelector="R" yChannelSelector="G"/>
		</filter>
	</svg>

	{{-- ===================================================================
	     HERO — same design as Home V2; text + form come from this page
	==================================================================== --}}
	<section class="hv2-hero hv2-hero--fast hv2-dark sv2-hero{{ $sv2Hero['form'] ? '' : ' sv2-hero--noform' }}" id="hv2-hero">
		<div class="hv2-hero-bg" aria-hidden="true">
			@if($sv2Photo)<span class="sv2-hero-photo" style="background-image: url('{{ $sv2Photo }}')"></span>@endif
			<span class="hv2-blob hv2-blob--1"></span>
			<span class="hv2-blob hv2-blob--2"></span>
			<span class="hv2-blob hv2-blob--3"></span>
			<span class="hv2-grid-lines"></span>
		</div>
		<div class="hv2-streaks" aria-hidden="true">
			@for($s = 0; $s < 8; $s++)
				<i style="top: {{ 10 + $s * 11 }}%; --t: {{ 2.8 + ($s % 4) * .7 }}s; --dl: {{ $s * .5 }}s"></i>
			@endfor
		</div>
		@php
			$sv2hPieces = [
		'mega' => '<div class="sv2h-mega sv2h-sp"><span class="sv2h-mg-cone"></span><span class="sv2h-mg-grip"></span><span class="sv2h-mg-wave w1"></span><span class="sv2h-mg-wave w2"></span><span class="sv2h-mg-wave w3"></span></div>',
		'dooh' => '<div class="sv2h-dooh sv2h-sp"><span class="sv2h-dh-frame"><i></i><i></i><i></i><i></i><i></i><i></i></span><span class="sv2h-dh-stand"></span></div>',
		'taxi' => '<div class="sv2h-taxi sv2h-sp"><span class="sv2h-tx-lamp"></span><span class="sv2h-tx-body"><i></i><i></i></span><span class="sv2h-tx-wheel a"></span><span class="sv2h-tx-wheel b"></span><span class="sv2h-tx-beam"></span><span class="sv2h-tx-road"></span></div>',
		'radio' => '<div class="sv2h-radio sv2h-sp"><span class="sv2h-rd-ant"></span><span class="sv2h-rd-body"><i class="grille"></i><i class="knob"></i></span><span class="sv2h-rd-wave w1"></span><span class="sv2h-rd-wave w2"></span></div>',
		'phone' => '<div class="sv2h-phone sv2h-sp"><span class="sv2h-ph-body"><i></i></span><span class="sv2h-ph-heart h1">♥</span><span class="sv2h-ph-heart h2">♥</span><span class="sv2h-ph-heart h3">♥</span></div>',
		'stage' => '<div class="sv2h-spot sv2h-sp"><span class="sv2h-sp-beam b1"></span><span class="sv2h-sp-beam b2"></span><span class="sv2h-sp-disc"></span><span class="sv2h-sp-star">★</span></div>',
			];
			$sv2hSets = ['outdoor-advertising' => ['mega', 'dooh'], 'digital-advertising' => ['dooh', 'phone'], 'public-relations-pr-coverage' => ['mega', 'radio'], 'celebrity-marketing' => ['stage', 'phone'], 'influencer-marketing' => ['phone', 'stage'], 'event-marketing' => ['stage', 'mega'], 'transit-media' => ['taxi', 'dooh'], 'radio-advertising' => ['radio', 'mega'], 'seo-services' => ['dooh', 'phone'], 'website-development' => ['dooh', 'phone'], 'lead-generation' => ['phone', 'dooh'], 'sponsorships' => ['stage', 'mega']];
			$sv2hSet = $sv2hSets[($mainservice ?? $service)->slug] ?? ['mega', 'dooh'];
		@endphp
		<div class="sv2h-deco" aria-hidden="true">
			@foreach($sv2hSet as $sv2hI => $sv2hK)
				<div class="sv2h-hs sv2h-hs--n{{ $sv2hI + 1 }}">{!! $sv2hPieces[$sv2hK] !!}</div>
			@endforeach
		</div>


		<div class="hv2-container hv2-hero-inner">
			<div class="hv2-hero-copy">
				@include('web.home-v2.breadcrumbs', ['crumbs' => $sv2Crumbs, 'crumbSchema' => $sv2CrumbSchema])
				{{-- the live hero copy column, verbatim and in order (h1 label, h2 headline, h4, p, CTA line) --}}
				@if($sv2Hero['body'])
					<div class="sv2-hero-body{{ $sv2Hero['sparse'] ? ' sv2-hero-body--sparse' : '' }}{{ $sv2Hero['title1'] ? ' sv2-hero-body--title' : '' }}">{!! v2_links($sv2Hero['body']) !!}</div>
				@else
					<div class="sv2-hero-body sv2-hero-body--sparse"><h1 data-reveal="up">{{ $sv2Page->name }}</h1></div>
				@endif
			</div>

			{{-- enquiry form only where the live hero had one (website-development pages keep their own in-page form) --}}
			@if($sv2Hero['form'])
			<div class="hv2-hero-form-col" id="hv2-form">
				<div class="hv2-form-card hv2-glass" data-tilt data-tilt-max="4" data-reveal="scale" data-delay="2">
					<span class="hv2-glare" aria-hidden="true"></span>
					<form action="{{ route('enquiry.submit') }}" id="hv2-enquiry-form">
						@csrf
						<input type="hidden" name="service" value="{{ $sv2Page->name }}">
						<span class="hv2-form-kicker"><i class="hv2-live-dot"></i> Experts online · reply in 60 min</span>
						<div class="hv2-form-title">Ready to Get <span class="hv2-hot">Results?</span></div>
						<p class="hv2-form-sub">Drop us your details — our experts respond within 60 minutes.</p>
						<label class="hv2-field">
							<input type="text" name="name" placeholder=" " required autocomplete="name">
							<span>Your Full Name</span>
						</label>
						<div class="hv2-field hv2-field--tel">
							<input type="tel" name="mainphone" id="hv2-phone" required autocomplete="tel" placeholder="Your phone number" aria-label="Phone number">
							<input type="hidden" name="phone" id="hv2-fullphone">
						</div>
						<label class="hv2-field">
							<input type="email" name="email" id="hv2-email" placeholder=" " required autocomplete="email">
							<span>Your Email Address</span>
						</label>
						<label class="hv2-field">
							<textarea name="description" rows="3" placeholder=" " required></textarea>
							<span>Tell us what you need</span>
						</label>
						<button class="hv2-btn hv2-btn--primary hv2-btn--block" type="submit"><span>Get Free Consultation <i class="bi bi-arrow-right"></i></span></button>
						<ul class="hv2-form-trust">
							<li><i class="bi bi-star-fill"></i> No spam, ever</li>
							<li><i class="bi bi-check-circle-fill"></i> Reply in 60 min</li>
							<li><i class="bi bi-lock-fill"></i> 100% Private</li>
						</ul>
					</form>
					<div class="hv2-form-loader" aria-hidden="true"><span class="hv2-spinner"></span></div>
				</div>
			</div>
			@endif
		</div>

		<a href="#sv2-start" class="hv2-scroll-cue" data-scrollto="#sv2-start" aria-label="Scroll to content"></a>
	</section>

	<span id="sv2-start" aria-hidden="true"></span>
	{{-- hooks some pages' own scripts expect from their old hero (e.g. the particles counter) --}}
	<span class="js-count-particles" hidden></span>

	{{-- ===================================================================
	     PAGE SECTIONS — same order as the live page
	==================================================================== --}}
	@php $sv2Tone = 0; @endphp
	@foreach(($sv2Areas->get('main') ?? collect()) as $sec)
		@continue($sec->type === 'hero')

		@if($sec->section_key === 'clients')
	<section class="hv2-section hv2-dark hv2-clients" id="hv2-clients">
		<span class="hv2-watermark" data-parallax="0.12" aria-hidden="true">Clients</span>
		<div class="hv2-container">
			<div class="hv2-head hv2-head--split">
				<div>
					<span class="hv2-eyebrow" data-reveal="up">Our Clients</span>
					<h2 class="hv2-display hv2-h2" data-split>Brands we <span class="hv2-outline">work with</span></h2>
				</div>
				<div class="hv2-head-side" data-reveal="up" data-delay="2">
					<p class="hv2-lead">At DataMySite, we are proud to partner with a diverse range of clients across various industries, from startups to established global brands.</p>
				</div>
			</div>
		</div>

		@include('web.home-v2.clients-marquee')
	</section>

		@elseif($sec->section_key === 'case-studies')
	<section class="hv2-section hv2-dark hv2-cases" id="hv2-cases">
		<div class="hv2-container">
			<div class="hv2-head hv2-head--split hv2-head--tight">
				<div>
					<span class="hv2-eyebrow" data-reveal="up">Case Studies</span>
					<h2 class="hv2-display hv2-h2" data-split>Case <span class="hv2-outline">Studies</span></h2>
				</div>
				<div class="hv2-head-side" data-reveal="up" data-delay="2">
					<p class="hv2-lead">At <strong>DataMySite</strong>, we believe in transparency and trust. Sharing case studies allows us to demonstrate the tangible impact of our marketing and advertising solutions.</p>
					<div class="hv2-cs-controls">
						<div class="hv2-cs-nav">
							<button type="button" class="hv2-nav-btn hv2-cs-prev" aria-label="Previous case study"><i class="bi bi-arrow-left" aria-hidden="true"></i></button>
							<button type="button" class="hv2-nav-btn hv2-cs-next" aria-label="Next case study"><i class="bi bi-arrow-right" aria-hidden="true"></i></button>
							<button type="button" class="hv2-nav-btn hv2-cs-toggle" aria-label="Pause auto-play" aria-pressed="false"><i class="bi bi-pause-fill" aria-hidden="true"></i></button>
						</div>
						<span class="hv2-cs-count" aria-live="off"><b class="hv2-cs-cur">01</b> / {{ str_pad(count($hv2Cases), 2, '0', STR_PAD_LEFT) }}</span>
					</div>
				</div>
			</div>
		</div>

		<div class="hv2-cs-viewport" data-autoplay="1500" role="region" aria-roledescription="carousel" aria-label="Case studies">
			<div class="hv2-cs-track">
				@foreach($hv2Cases as $k => $case)
					<a href="{{ $case['pdf_url'] }}" target="_blank" rel="noopener" class="hv2-case" draggable="false" data-distort>
						<span class="hv2-case-num hv2-display" data-num="{{ str_pad($k + 1, 2, '0', STR_PAD_LEFT) }}" aria-hidden="true"></span>
						<div class="hv2-media hv2-case-pic">
							<img {!! v2_img($case['cover'], '(max-width: 640px) 78vw, 480px') !!} alt="{{ $case['alt'] }}" loading="lazy" draggable="false">
							<span class="hv2-case-panel" data-label="View case study" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
						</div>
						{{-- same card heading text as the live page (the name is already in the cover image, so it is visually hidden) --}}
						<h3 class="sv2-sr"><span>{{ $case['name'] }}</span> x <span>DMS</span> |  Case Study</h3>
					</a>
				@endforeach
			</div>
		</div>

		<div class="hv2-container">
			<div class="hv2-cs-footer">
				<div class="hv2-cs-progress" aria-hidden="true"><i></i></div>
				<a href="{{ route('caseStudies') }}" class="hv2-btn hv2-btn--outline" data-magnetic><span>View all case studies <i class="bi bi-arrow-right"></i></span></a>
			</div>
		</div>
	</section>

		@elseif($sec->type === 'blogs')
			@php $sv2B = $sv2BlogMeta($sv2Render($sec)); @endphp
			{{-- always rendered (like the live page): heading, sub-heading and "See all" stay even when there are no posts --}}
			<section class="hv2-section hv2-light hv2-blogs" id="hv2-blogs">
				<div class="hv2-container">
					<div class="hv2-head hv2-head--split">
						<div>
							{{-- heading + sub-heading text exactly as on the live page --}}
							<h2 class="hv2-display hv2-h2" data-split>{{ $sv2B['title'] ?: 'Blogs' }}</h2>
							@if($sv2B['sub'])<p class="hv2-lead sv2-blogs-sub" data-reveal="up" data-delay="1">{{ $sv2B['sub'] }}</p>@endif
						</div>
						<a href="{{ $sv2B['href'] }}" class="hv2-btn hv2-btn--dark" data-magnetic data-reveal="up" data-delay="2"><span>{{ $sv2B['label'] }} <i class="bi bi-arrow-right"></i></span></a>
					</div>
					@if(!empty($blogs) && count($blogs))
					<div class="hv2-blog-grid">
						@foreach($blogs as $val)
							<a href="{{ route('blogs.detail', [$val->slug]) }}" class="hv2-blog" data-reveal="up" data-delay="{{ $loop->index + 1 }}" data-distort>
								<div class="hv2-media hv2-media--blog">
									<img {!! v2_img('public/storage/blogs/' . $val->banner, '(max-width: 640px) 92vw, 31vw', URL::to('public/storage/blogs/' . $val->banner)) !!} alt="{{ $val->banner_alt ?: $val->heading }}" loading="lazy">
								</div>
								{{-- same tags/text as the live card: h6 title, short description, "by Author - Date" --}}
								<h3 class="sv2-blog-h" title="{{ $val->heading }}">{{ $val->heading }}</h3>
								<p>{{ $val->short_description }}</p>
								<small class="hv2-blog-meta">by <b>{{ optional($val->author)->name }}</b> - {{ date('F d, Y', strtotime($val->created_at)) }}</small>
							</a>
						@endforeach
					</div>
					@endif
				</div>
			</section>

		@elseif($sec->type === 'faq')
			@php $sv2FaqHtml = $sv2Render($sec); $sv2Faqs = $sv2FaqParse($sv2FaqHtml); $sv2FaqH = $sv2FaqTitle($sv2FaqHtml); @endphp
			@if(count($sv2Faqs))
			{{-- one FAQPage block per page, even when two FAQ rows exist (the new one and the old one the editor has not removed yet); ids carry the row id so they stay unique --}}
			@if($sv2FaqSchema && empty($sv2FaqSchemaDone)){!! v2_faq_schema($sv2Faqs) !!}@php $sv2FaqSchemaDone = true; @endphp@endif
			<section class="hv2-section hv2-tint sv2-faq" id="sv2-faq{{ empty($sv2FaqSeen) ? '' : '-' . $sec->id }}">
			@php $sv2FaqSeen = true; @endphp
				<div class="hv2-container sv2-faq-grid">
					<div class="sv2-faq-side">
						{{-- same heading tag + text as the live FAQ block --}}
						<h2 class="hv2-display hv2-h2 sv2-faq-title" data-split>{{ $sv2FaqH ?: 'FAQs' }}</h2>
						{{-- the hero form is the target; pages without a hero form send the visitor to the contact page instead of a dead anchor --}}
						@if($sv2Hero['form'])
						<a href="#hv2-form" class="hv2-btn hv2-btn--primary hv2-btn--3d" data-scrollto="#hv2-form" data-magnetic data-reveal="up" data-delay="2"><span>Ask our team <i class="bi bi-arrow-right"></i></span></a>
						@else
						<a href="{{ route('contact') }}" class="hv2-btn hv2-btn--primary hv2-btn--3d" data-magnetic data-reveal="up" data-delay="2"><span>Ask our team <i class="bi bi-arrow-right"></i></span></a>
						@endif
					</div>
					<div class="sv2-faq-list" data-sv2-faq>
						@foreach($sv2Faqs as $k => $f)
							<div class="hv2-faq-item {{ $k === 0 ? 'is-open' : '' }}" data-reveal="up" data-delay="{{ min($k, 4) }}">
								<h3 class="hv2-faq-h">
									<button type="button" class="hv2-faq-q" id="sv2-faq{{ $sec->id }}-q{{ $k }}" aria-expanded="{{ $k === 0 ? 'true' : 'false' }}" aria-controls="sv2-faq{{ $sec->id }}-a{{ $k }}">
										<span class="hv2-faq-num" data-num="{{ str_pad($k + 1, 2, '0', STR_PAD_LEFT) }}" aria-hidden="true"></span>
										<span class="hv2-faq-text">{{ $f[0] }}</span>
										<span class="hv2-faq-tog" aria-hidden="true"></span>
									</button>
								</h3>
								<div class="hv2-faq-a" id="sv2-faq{{ $sec->id }}-a{{ $k }}" role="region" aria-labelledby="sv2-faq{{ $sec->id }}-q{{ $k }}">
									<div>{!! $f[1] !!}</div>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			</section>
			@endif

		@elseif(in_array($sec->type, ['style', 'script']))
			{{-- inline <style>/<script> rows placed in the body: output as-is, no section wrapper --}}
			{!! $sv2Render($sec) !!}

		@else
			{{-- this page's own content, rendered exactly as before, restyled + animated --}}
			@php $sv2Tone++; @endphp
			<div class="sv2-legacy sv2-tone-{{ $sv2Tone % 2 ? 'light' : 'tint' }}" id="sv2-{{ $sec->section_key ?: 'section-' . $sec->id }}" data-sv2-legacy>
				{!! $sv2Media(v2_links($sv2StripForms($sv2Render($sec)))) !!}
			</div>
		@endif
	@endforeach

</main>
{!! $sv2Area('after_main') !!}
@endsection

@section('addScript')
	{{-- page scripts, minus aside.js (it injects a second enquiry form; V2 keeps only the hero form) --}}
	{!! preg_replace('#<script[^>]+/aside\.js[^>]*>\s*</script>#i', '', $sv2Area('scripts')) !!}
	{{-- service-v2.js must run before home-v2.js: it tags the legacy content for the reveal engine --}}
	<script src="{{ v2_asset('assets/js/service-v2.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/home-v2.js') }}" defer></script>
@endsection
