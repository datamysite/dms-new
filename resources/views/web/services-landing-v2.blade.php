{{--
|==========================================================================
| SERVICES LANDING V2 — redesigned /services (independent copy of web.services)
|--------------------------------------------------------------------------
| Self-contained page: this view + public/assets/css/services-landing-v2.css
|                                + public/assets/js/services-landing-v2.js
| Uses the shared V2 shell (web.home-v2.master) and the Home V2 design system
| (home-v2.css / home-v2.js). Does not include or depend on the live services
| view, service-v2.css or service-v2.js. Same text + images as web.services.
| Preview: /services-new   Switch: WebController@services -> view('web.services-landing-v2')
|==========================================================================
--}}
@extends('web.home-v2.master')
@php require_once resource_path('views/web/home-v2/helpers.php'); @endphp

@section('v2lite', '1')

@section('metaAddition')
	<meta name="robots" content="noindex, follow">
	<script>(function(){var d=document.documentElement;try{if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches)return;}catch(e){}d.classList.add('hv2-loading');setTimeout(function(){d.classList.remove('hv2-loading','hv2-leaving');},2500);})();</script>
	<link rel="preload" as="image" href="{{ URL::to('/public/assets/img/logo-white.png') }}">
	{{-- self-hosted fonts: the two key files are preloaded, @font-face is inlined (font-display: swap) --}}
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/league-spartan-latin.woff2') }}">
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/nunito-sans-latin.woff2') }}">
	<style>{!! str_replace('../fonts/', URL::to('/public/assets/fonts') . '/', (string) @file_get_contents(public_path('assets/css/v2-fonts.css'))) !!}</style>
@endsection

@section('addStyle')
	<link href="{{ v2_asset('assets/css/home-v2.css') }}" rel="stylesheet">
	<link href="{{ v2_asset('assets/css/services-landing-v2.css') }}" rel="stylesheet">
@endsection

@php
	$hv2Img = URL::to('/public/assets/img');
	$svlPhoto = is_file(public_path('assets/img/title/services.webp')) ? $hv2Img . '/title/services.webp' : (is_file(public_path('assets/img/title/services.jpg')) ? $hv2Img . '/title/services.jpg' : null);

	/* ---------- Client logos (same as Home V2 marquee) ---------- */
	$hv2Clients = [
		38 => 'Nike', 12 => 'Intel', 35 => 'Whole Foods', 31 => 'e&', 1 => 'Dubai Duty Free', 4 => 'DAMAC', 17 => 'MakeMyTrip',
		6 => 'Lenskart', 16 => 'Cerruti', 3 => 'Danube Properties', 2 => 'Jetour', 39 => 'Géant', 36 => "Haldiram's", 37 => 'GMG',
		25 => 'Al Rawabi', 28 => 'Al Ain Farms', 20 => 'SIVVI', 40 => 'Aswaaq', 7 => 'Logicom Group', 5 => 'ILG', 24 => 'MARQ Dubai',
		26 => 'Hayata', 27 => 'Cavo', 13 => 'Elite Group Holding', 14 => 'BNW Developments', 8 => 'Arabian Hills', 19 => 'Tranquil Developers', 21 => 'Prime Estate',
		18 => 'Oscar Luxury Auto', 15 => 'AYS Developers', 32 => 'USI Consultancy', 34 => 'First Super Car Rental', 33 => 'Royalty Tourism', 29 => 'Darcey Flowers', 30 => 'Peace Homes Development',
		9 => "D's Damat", 10 => 'Metamind Inspire', 11 => 'Ask Kubeir', 22 => 'Lasa Ona', 23 => 'Just Love UAE', 41 => 'Royal',
	];
	$hv2Rows = [[], []];
	foreach (array_keys($hv2Clients) as $pos => $logoNo) { $hv2Rows[$pos % 2][$logoNo] = $hv2Clients[$logoNo]; }
@endphp

@section('content')
<main class="main hv2 svl" id="hv2">

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
	     HERO — same design as Home V2 (title + tagline of the live page)
	==================================================================== --}}
	<section class="hv2-hero hv2-dark svl-hero" id="hv2-hero">
		<div class="hv2-hero-bg" aria-hidden="true">
			@if($svlPhoto)<span class="svl-hero-photo" style="background-image: url('{{ $svlPhoto }}')"></span>@endif
			<span class="hv2-blob hv2-blob--1"></span><span class="hv2-blob hv2-blob--2"></span><span class="hv2-blob hv2-blob--3"></span>
			<span class="hv2-grid-lines"></span>
		</div>
		<div class="hv2-streaks" aria-hidden="true">
			@for($s = 0; $s < 8; $s++)<i style="top: {{ 10 + $s * 11 }}%; --t: {{ 2.8 + ($s % 4) * .7 }}s; --dl: {{ $s * .5 }}s"></i>@endfor
		</div>
		<div class="svl-deco svl-deco--dark svl-deco--hero" aria-hidden="true"><div class="svl-stage2 svl-stage2--h1" data-svl-depth="0.05"><div class="svl-mega svl-sp"><span class="svl-mg-cone"></span><span class="svl-mg-grip"></span><span class="svl-mg-wave w1"></span><span class="svl-mg-wave w2"></span><span class="svl-mg-wave w3"></span></div></div><div class="svl-stage2 svl-stage2--h2" data-svl-depth="-0.08"><div class="svl-dooh svl-sp"><span class="svl-dh-frame"><i></i><i></i><i></i><i></i><i></i><i></i></span><span class="svl-dh-stand"></span></div></div><div class="svl-stage2 svl-stage2--h3" data-svl-depth="0.1"><div class="svl-phone svl-sp"><span class="svl-ph-body"><i></i></span><span class="svl-ph-heart h1">♥</span><span class="svl-ph-heart h2">♥</span><span class="svl-ph-heart h3">♥</span></div></div></div>
		<div class="hv2-container hv2-hero-inner">
			<div class="hv2-hero-copy">
				@include('web.home-v2.breadcrumbs', ['crumbs' => [['Home', URL::to('/')], ['Services', null]]])
				<div class="svl-hero-body">
					<h1 data-reveal="up">Our Services</h1>
					<h2 data-reveal="up" data-delay="1">Elevate Your Brand Visibility and Boost Sales</h2>
				</div>
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     SERVICES — Home V2 service cards (all shown)
	==================================================================== --}}
	<section class="hv2-section hv2-tint hv2-services svl-services" id="hv2-services">
			<div class="svl-deco" aria-hidden="true"><div class="svl-stage2 svl-stage2--s1" data-svl-depth="0.08"><div class="svl-taxi svl-sp"><span class="svl-tx-lamp"></span><span class="svl-tx-body"><i></i><i></i></span><span class="svl-tx-wheel a"></span><span class="svl-tx-wheel b"></span><span class="svl-tx-beam"></span><span class="svl-tx-road"></span></div></div><div class="svl-stage2 svl-stage2--s2" data-svl-depth="-0.1"><div class="svl-radio svl-sp"><span class="svl-rd-ant"></span><span class="svl-rd-body"><i class="grille"></i><i class="knob"></i></span><span class="svl-rd-wave w1"></span><span class="svl-rd-wave w2"></span></div></div><div class="svl-stage2 svl-stage2--s3" data-svl-depth="0.06"><div class="svl-spot svl-sp"><span class="svl-sp-beam b1"></span><span class="svl-sp-beam b2"></span><span class="svl-sp-disc"></span><span class="svl-sp-star">★</span></div></div><div class="svl-stage2 svl-stage2--s4" data-svl-depth="-0.06"><div class="svl-mega svl-sp"><span class="svl-mg-cone"></span><span class="svl-mg-grip"></span><span class="svl-mg-wave w1"></span><span class="svl-mg-wave w2"></span><span class="svl-mg-wave w3"></span></div></div><div class="svl-stage2 svl-stage2--s5" data-svl-depth="0.1"><div class="svl-phone svl-sp"><span class="svl-ph-body"><i></i></span><span class="svl-ph-heart h1">♥</span><span class="svl-ph-heart h2">♥</span><span class="svl-ph-heart h3">♥</span></div></div>
				<div class="svl-stage" data-svl-depth="0.10">
					<div class="svl-cube"><i class="f1"></i><i class="f2"></i><i class="f3"></i><i class="f4"></i><i class="f5"></i><i class="f6"></i></div>
					<span class="svl-orb"></span>
					<span class="svl-ring r1"></span><span class="svl-ring r2"></span>
					<span class="svl-floor"></span>
				</div>
				<span class="svl-dots" data-svl-depth="-0.06"></span>
				<span class="svl-plus p1" data-svl-depth="0.18">+</span>
				<span class="svl-plus p2" data-svl-depth="-0.14">+</span>
				<span class="svl-plus p3" data-svl-depth="0.12">+</span>
				<span class="svl-tri t1" data-svl-depth="-0.2"></span>
				<span class="svl-tri t2" data-svl-depth="0.16"></span>
				<span class="svl-glow g1" data-svl-depth="0.08"></span>
				<span class="svl-glow g2" data-svl-depth="-0.1"></span>
				<span class="svl-bar b1" data-svl-depth="0.2"></span>
				<span class="svl-bar b2" data-svl-depth="-0.18"></span>
			</div>
		<div class="hv2-container">
			<div class="hv2-head">
				<div>
					<span class="hv2-eyebrow" data-reveal="up">Our Services</span>
					<h2 class="hv2-display hv2-h2" data-split>Elevate Your Brand Visibility <span class="hv2-outline">and Boost Sales</span></h2>
				</div>
			</div>
						<div class="svl-grid" id="svl-grid">
				@foreach($services as $val)
					<a href="{{ v2_url('/'.$val->slug) }}" class="svl-card" style="--i: {{ $loop->index % 3 }}" data-tilt data-tilt-max="5" data-reveal="up" data-delay="{{ ($loop->index % 3) + 1 }}">
						<span class="svl-card-media hv2-media">
							@if(is_file(public_path('assets/img/service-new/'.trim($val->slug, '/').'.jpg')))
								@php
									$svSlug = trim($val->slug, '/'); $svBase = 'assets/img/service-new/' . $svSlug;
									$svWebp = is_file(public_path($svBase . '.webp'));
									$svSize = @getimagesize(public_path($svBase . '.jpg')) ?: [1200, 750];
									$svSrcset = [];
									foreach ([480, 960, 1440] as $svW) { if (is_file(public_path($svBase . '-w' . $svW . '.webp'))) $svSrcset[] = $hv2Img . '/service-new/' . $svSlug . '-w' . $svW . '.webp ' . $svW . 'w'; }
								@endphp
								<img src="{{ $hv2Img }}/service-new/{{ $svSlug }}.{{ $svWebp ? 'webp' : 'jpg' }}" @if($svWebp && $svSrcset) srcset="{{ implode(', ', $svSrcset) }}" sizes="(max-width: 640px) 92vw, (max-width: 1100px) 46vw, 31vw" @endif alt="{{ $val->name }}" width="{{ $svSize[0] }}" height="{{ $svSize[1] }}" loading="lazy" decoding="async">
							@else
								<span class="svl-card-fallback" aria-hidden="true"><i class="{{ $val->icon }}"></i></span>
							@endif
							<span class="svl-card-no hv2-display" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
							<span class="svl-card-icon"><i class="{{ $val->icon }}"></i></span>
						</span>
						<span class="svl-card-body">
							<h3>{{ $val->name }}</h3>
							<p>{{ $val->description }}</p>
							<span class="svl-card-more" aria-label="Explore {{ $val->name }}"><span>Explore</span><i class="bi bi-arrow-right"></i></span>
						</span>
					</a>
				@endforeach
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     CLIENTELE — live banner section (image + heading + text)
	==================================================================== --}}
	<section class="hv2-section hv2-dark svl-clientele" id="svl-clientele">
		<span class="svl-clientele-bg" aria-hidden="true" style="background-image: url('{{ $hv2Img }}/clientele.jpg')"></span>
		<span class="hv2-blob hv2-blob--2" aria-hidden="true"></span>
		<span class="svl-ring svl-ring--l" data-svl-depth="0.12" aria-hidden="true"></span><span class="svl-ring svl-ring--r" data-svl-depth="-0.1" aria-hidden="true"></span>
		<div class="svl-deco svl-deco--dark" aria-hidden="true"><div class="svl-stage2 svl-stage2--c1" data-svl-depth="0.08"><div class="svl-dooh svl-sp"><span class="svl-dh-frame"><i></i><i></i><i></i><i></i><i></i><i></i></span><span class="svl-dh-stand"></span></div></div><div class="svl-stage2 svl-stage2--c2" data-svl-depth="-0.08"><div class="svl-spot svl-sp"><span class="svl-sp-beam b1"></span><span class="svl-sp-beam b2"></span><span class="svl-sp-disc"></span><span class="svl-sp-star">★</span></div></div><div class="svl-stage svl-stage--a" data-svl-depth="0.08"><div class="svl-pc"><div class="svl-orbit"><span class="svl-core"></span><span class="svl-path p1"><i></i></span><span class="svl-path p2"><i></i></span><span class="svl-path p3"><i></i></span></div></div></div><div class="svl-stage svl-stage--b" data-svl-depth="-0.1"><div class="svl-pc"><div class="svl-board"><span class="svl-panel"><i></i><i></i><i></i></span><span class="svl-pole l"></span><span class="svl-pole r"></span></div></div></div><span class="svl-dots svl-dots--l"></span><span class="svl-dots svl-dots--r"></span><span class="svl-plus p1" data-svl-depth="0.18">+</span><span class="svl-plus p2" data-svl-depth="-0.14">+</span></div>
		<div class="hv2-container">
			<div class="svl-clientele-copy">
				<span class="hv2-eyebrow" data-reveal="up">Clientele</span>
				<h2 class="hv2-display hv2-h2" data-split>Clientele</h2>
				<p class="hv2-lead" data-reveal="up" data-delay="2">We’ve helped these brands achieve their goals and see how we can do the same for you.</p>
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     OUR CLIENTS — live "Brands we work with" section, logos in the Home V2 marquee
	==================================================================== --}}
	<section class="hv2-section hv2-dark hv2-clients svl-clients" id="hv2-clients">
		<span class="hv2-watermark" data-parallax="0.12" aria-hidden="true">Clients</span>
		<div class="svl-deco svl-deco--dark svl-deco--clients" aria-hidden="true"><div class="svl-stage2 svl-stage2--k1" data-svl-depth="0.08"><div class="svl-taxi svl-sp"><span class="svl-tx-lamp"></span><span class="svl-tx-body"><i></i><i></i></span><span class="svl-tx-wheel a"></span><span class="svl-tx-wheel b"></span><span class="svl-tx-beam"></span><span class="svl-tx-road"></span></div></div><div class="svl-stage2 svl-stage2--k2" data-svl-depth="-0.08"><div class="svl-radio svl-sp"><span class="svl-rd-ant"></span><span class="svl-rd-body"><i class="grille"></i><i class="knob"></i></span><span class="svl-rd-wave w1"></span><span class="svl-rd-wave w2"></span></div></div><div class="svl-stage svl-stage--c" data-svl-depth="0.1"><div class="svl-pc"><div class="svl-columns"><i class="svl-tw" style="--h:70px;--w:30px;--dl:0s"></i><i class="svl-tw" style="--h:120px;--w:30px;--dl:.4s"></i><i class="svl-tw" style="--h:170px;--w:30px;--dl:.8s"></i><i class="svl-tw" style="--h:230px;--w:30px;--dl:1.2s"></i></div></div></div><span class="svl-dots svl-dots--r"></span><span class="svl-glow g1" data-svl-depth="0.08"></span><span class="svl-glow g2" data-svl-depth="-0.1"></span><span class="svl-plus p1" data-svl-depth="0.18">+</span><span class="svl-plus p2" data-svl-depth="-0.14">+</span><span class="svl-tri t1" data-svl-depth="-0.2"></span><span class="svl-bar b1" data-svl-depth="0.2"></span></div>
		<div class="hv2-container">
			<div class="hv2-head hv2-head--split">
				<div>
					<span class="hv2-eyebrow" data-reveal="up">Our Clients</span>
					<h2 class="hv2-display hv2-h2" data-split>Brands we <span class="hv2-outline">work with</span></h2>
				</div>
				<p class="hv2-lead" data-reveal="up" data-delay="2">At DataMySite, we are proud to partner with a diverse range of clients across various industries, from startups to established global brands.</p>
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
</main>
@endsection

@section('addScript')
	<script src="{{ URL::to('/public/assets/vendor/lenis/lenis-1.1.13.min.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/home-v2.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/services-landing-v2.js') }}" defer></script>
@endsection
