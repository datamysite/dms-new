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
	$sv2Render = function ($sec) use ($sv2Vars) { return \Illuminate\Support\Facades\Blade::render($sec->content, $sv2Vars); };
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
	$sv2Hero = ['body' => '', 'photo' => null, 'sparse' => true, 'form' => true];
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

	/* fallback photo when the hero row has no background image */
	$sv2Photo = $sv2Hero['photo'];
	if (!$sv2Photo) {
		foreach (['assets/img/service-new/' . $sv2Page->slug . '.jpg', 'assets/img/service-new/' . $sv2Page->slug . '.png'] as $cand) {
			if (is_file(public_path($cand))) { $sv2Photo = URL::to('/public/' . $cand); break; }
		}
	}

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

	/* One enquiry form per page: the V2 hero form. Any extra in-content enquiry form (e.g. the "Want to know more?
	   Ask us anything" side form on website-development pages) is dropped from that section, and the text column
	   beside it is widened to full width. The database content is not changed. */
	$sv2StripForms = function ($html) use ($sv2Dom, $sv2Inner) {
		/* also the empty .aside-block column that the site's aside.js fills with a form via AJAX */
		if (stripos($html, '<form') === false && stripos($html, 'aside-block') === false) return $html;
		[$d, $x] = $sv2Dom($html);
		$hits = $x->query('//*[contains(concat(" ", normalize-space(@class), " "), " aside-form ")] | //*[contains(concat(" ", normalize-space(@class), " "), " aside-block ")] | //form');
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
			'href' => $a ? $a->getAttribute('href') : URL::to('/blogs'),
			'label' => $a && $sv2Text($a) !== '' ? $sv2Text($a) : 'See All Blogs',
		];
	};

	$hv2Img = URL::to('/public/assets/img');
/* ---------- copied from Home V2: client logos + case studies ---------- */
	$hv2Clients = [
		38 => 'Nike', 12 => 'Intel', 35 => 'Whole Foods', 31 => 'e&', 1 => 'Dubai Duty Free', 4 => 'DAMAC', 17 => 'MakeMyTrip',
		6 => 'Lenskart', 16 => 'Cerruti', 3 => 'Danube Properties', 2 => 'Jetour', 39 => 'Géant', 36 => "Haldiram's", 37 => 'GMG',
		25 => 'Al Rawabi', 28 => 'Al Ain Farms', 20 => 'SIVVI', 40 => 'Aswaaq', 7 => 'Logicom Group', 5 => 'ILG', 24 => 'MARQ Dubai',
		26 => 'Hayata', 27 => 'Cavo', 13 => 'Elite Group Holding', 14 => 'BNW Developments', 8 => 'Arabian Hills', 19 => 'Tranquil Developers', 21 => 'Prime Estate',
		18 => 'Oscar Luxury Auto', 15 => 'AYS Developers', 32 => 'USI Consultancy', 34 => 'First Super Car Rental', 33 => 'Royalty Tourism', 29 => 'Darcey Flowers', 30 => 'Peace Homes Development',
		9 => "D's Damat", 10 => 'Metamind Inspire', 11 => 'Ask Kubeir', 22 => 'Lasa Ona', 23 => 'Just Love UAE', 41 => 'Royal',
	];
	$hv2Cases = [
		['bnw', 'bnw.jpg', 'BNW Developers'],
		['ays', 'ays.jpg', 'AYS Developers'],
		['jetour', 'jetour.jpg', 'Jetour'],
		['tranquil', 'tranquil.jpg', 'Tranquil Developers'],
		['ilg', 'ilg.jpg', 'ILG Luxury Group'],
		['dubai-duty-free', 'dubai-duty-free.jpg', 'Dubai Duty Free'],
		['danube', 'danube.jpg', 'Danube'],
		['ksa', 'lenskart-p.png', 'KSA Influencers'],
	];
@endphp

@section('metaAddition')
	<meta name="robots" content="noindex, follow">
	<script>(function(){var d=document.documentElement;try{if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches)return;}catch(e){}d.classList.add('hv2-loading');setTimeout(function(){d.classList.remove('hv2-loading','hv2-leaving');},8000);})();</script>
	<link rel="preload" as="image" href="{{ URL::to('/public/assets/img/logo-white.png') }}">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400..900&family=Nunito+Sans:opsz,wght@6..12,400;6..12,600;6..12,700;6..12,800&display=swap" rel="stylesheet">
@endsection

@section('addStyle')
	{{-- the page's own legacy styles first (service.css + any page-specific CSS), then the v2 layers win --}}
	{!! $sv2Area('head') !!}
	<link href="{{ URL::to('/public/assets/css/home-v2.css') }}?v={{ @filemtime(public_path('assets/css/home-v2.css')) }}" rel="stylesheet">
	<link href="{{ URL::to('/public/assets/css/service-v2.css') }}?v={{ @filemtime(public_path('assets/css/service-v2.css')) }}" rel="stylesheet">
@endsection

@section('content')
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
	<section class="hv2-hero hv2-dark sv2-hero{{ $sv2Hero['form'] ? '' : ' sv2-hero--noform' }}" id="hv2-hero">
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
					<div class="sv2-hero-body{{ $sv2Hero['sparse'] ? ' sv2-hero-body--sparse' : '' }}">{!! v2_links($sv2Hero['body']) !!}</div>
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
						<h2 class="hv2-form-title">Ready to Get <span class="hv2-hot">Results?</span></h2>
						<p class="hv2-form-sub">Drop us your details — our experts respond within 60 minutes.</p>
						<label class="hv2-field">
							<input type="text" name="name" placeholder=" " required autocomplete="name">
							<span>Your Full Name</span>
						</label>
						<div class="hv2-field hv2-field--tel">
							<input type="tel" name="mainphone" id="hv2-phone" required autocomplete="tel" aria-label="Phone number">
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
	@php
		// split the size-ordered list into two rows (alternating) so both rows mix big and small brands
		$hv2Rows = [[], []];
		foreach (array_keys($hv2Clients) as $pos => $logoNo) { $hv2Rows[$pos % 2][$logoNo] = $hv2Clients[$logoNo]; }
	@endphp
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

		<div class="hv2-marquee-wrap" role="group" aria-label="Our clients — {{ count($hv2Clients) }} brands" data-reveal="up" data-spotlight>
			@foreach($hv2Rows as $r => $row)
				<div class="hv2-marquee {{ $r === 1 ? 'hv2-marquee--reverse' : '' }}">
					<div class="hv2-marquee-track">
						@for($dup = 0; $dup < 2; $dup++)
							@foreach($row as $i => $brand)
								<div class="hv2-brand hv2-brand--lg" style="--n: {{ $loop->index }}" @if($dup) aria-hidden="true" @endif>
									<div class="hv2-brand-logo hv2-media hv2-media--logo">
										<img src="{{ $hv2Img }}/clients-final/{{ $i }}.png" alt="{{ $dup ? '' : $brand.' logo' }}" loading="lazy" width="300" height="300">
									</div>
								</div>
							@endforeach
						@endfor
					</div>
				</div>
			@endforeach
		</div>
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
					<a href="{{ URL::to('/public/case-studies/'.$case[0].'.pdf') }}" target="_blank" rel="noopener" class="hv2-case" draggable="false" data-distort>
						<span class="hv2-case-num hv2-display" data-num="{{ str_pad($k + 1, 2, '0', STR_PAD_LEFT) }}" aria-hidden="true"></span>
						<div class="hv2-media hv2-case-pic">
							<img src="{{ URL::to('/public/case-studies/cover/'.$case[1]) }}" alt="Case Studies" loading="lazy" draggable="false">
							<span class="hv2-case-panel" data-label="View case study" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
						</div>
						{{-- same card heading text as the live page (the name is already in the cover image, so it is visually hidden) --}}
						<h5 class="sv2-sr"><span>{{ $case[2] }}</span> x <span>DMS</span> |  Case Study</h5>
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
						<a href="{{ $sv2B['href'] }}" class="hv2-btn hv2-btn--dark" target="_blank" data-magnetic data-reveal="up" data-delay="2"><span>{{ $sv2B['label'] }} <i class="bi bi-arrow-right"></i></span></a>
					</div>
					@if(!empty($blogs) && count($blogs))
					<div class="hv2-blog-grid">
						@foreach($blogs as $val)
							<a href="{{ route('blogs.detail', [$val->slug]) }}" class="hv2-blog" data-reveal="up" data-delay="{{ $loop->index + 1 }}" data-distort>
								<div class="hv2-media hv2-media--blog">
									<img src="{{ URL::to('public/storage/blogs/'.$val->banner) }}" alt="{{ $val->banner_alt ?: $val->heading }}" loading="lazy">
								</div>
								{{-- same tags/text as the live card: h6 title, short description, "by Author - Date" --}}
								<h6 class="sv2-blog-h" title="{{ $val->heading }}">{{ $val->heading }}</h6>
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
			<section class="hv2-section hv2-tint sv2-faq" id="sv2-faq">
				<div class="hv2-container sv2-faq-grid">
					<div class="sv2-faq-side">
						{{-- same heading tag + text as the live FAQ block --}}
						<h4 class="hv2-display hv2-h2 sv2-faq-title" data-split>{{ $sv2FaqH ?: 'FAQs' }}</h4>
						<a href="#hv2-form" class="hv2-btn hv2-btn--primary hv2-btn--3d" data-scrollto="#hv2-form" data-magnetic data-reveal="up" data-delay="2"><span>Ask our team <i class="bi bi-arrow-right"></i></span></a>
					</div>
					<div class="sv2-faq-list" data-sv2-faq>
						@foreach($sv2Faqs as $k => $f)
							<div class="hv2-faq-item {{ $k === 0 ? 'is-open' : '' }}" data-reveal="up" data-delay="{{ min($k, 4) }}">
								<h5 class="hv2-faq-h">
									<button type="button" class="hv2-faq-q" id="sv2-faq-q{{ $k }}" aria-expanded="{{ $k === 0 ? 'true' : 'false' }}" aria-controls="sv2-faq-a{{ $k }}">
										<span class="hv2-faq-num" data-num="{{ str_pad($k + 1, 2, '0', STR_PAD_LEFT) }}" aria-hidden="true"></span>
										<span class="hv2-faq-text">{{ $f[0] }}</span>
										<span class="hv2-faq-tog" aria-hidden="true"></span>
									</button>
								</h5>
								<div class="hv2-faq-a" id="sv2-faq-a{{ $k }}" role="region" aria-labelledby="sv2-faq-q{{ $k }}">
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
				{!! v2_links($sv2StripForms($sv2Render($sec))) !!}
			</div>
		@endif
	@endforeach

</main>
{!! $sv2Area('after_main') !!}
@endsection

@section('addScript')
	{{-- page scripts, minus aside.js (it injects a second enquiry form; V2 keeps only the hero form) --}}
	{!! preg_replace('#<script[^>]+/aside\.js[^>]*>\s*</script>#i', '', $sv2Area('scripts')) !!}
	<script src="https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js" defer></script>
	{{-- service-v2.js must run before home-v2.js: it tags the legacy content for the reveal engine --}}
	<script src="{{ URL::to('/public/assets/js/service-v2.js') }}?v={{ @filemtime(public_path('assets/js/service-v2.js')) }}" defer></script>
	<script src="{{ URL::to('/public/assets/js/home-v2.js') }}?v={{ @filemtime(public_path('assets/js/home-v2.js')) }}" defer></script>
@endsection
