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
@section('v2crit', 'assets/css/services-landing.critical.css')

@section('metaAddition')
	<script>(function(){var d=document.documentElement;try{if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches)return;}catch(e){}d.classList.add('hv2-loading');setTimeout(function(){d.classList.remove('hv2-loading','hv2-leaving');},500);})();</script>
	<link rel="preload" as="image" href="{{ v2_asset('assets/img/logo-white.webp', false) }}">
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
	$svl3d = URL::to('/public/assets/img/3d/services');
	$svlPhoto = is_file(public_path('assets/img/title/services.webp')) ? $hv2Img . '/title/services.webp' : (is_file(public_path('assets/img/title/services.jpg')) ? $hv2Img . '/title/services.jpg' : null);

	/* ---------- Client logos (same as Home V2 marquee) ---------- */
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
		<div class="svl-deco svl-deco--dark svl-deco--hero" aria-hidden="true"><img class="svl-3d svl-3d--h1" src="{{ $svl3d }}/megaphone-480.webp" srcset="{{ $svl3d }}/megaphone-240.webp 240w, {{ $svl3d }}/megaphone-480.webp 480w" sizes="(max-width: 991px) 140px, 240px" width="480" height="360" alt="" decoding="async" fetchpriority="high" data-svl-depth="0.05"><img class="svl-3d svl-3d--h2" src="{{ $svl3d }}/screen-480.webp" srcset="{{ $svl3d }}/screen-240.webp 240w, {{ $svl3d }}/screen-480.webp 480w" sizes="(max-width: 991px) 140px, 240px" width="444" height="480" alt="" decoding="async" fetchpriority="high" data-svl-depth="-0.06"></div>
		<div class="hv2-container hv2-hero-inner">
			<div class="hv2-hero-copy">
				@include('web.home-v2.breadcrumbs', ['crumbs' => [['Home', URL::to('/')], ['Services', null]]])
				<div class="svl-hero-body">
					<p class="svl-eyebrow" data-reveal="up">Our Services</p>
					<h1 data-reveal="up" data-delay="1">Elevate Your Brand Visibility and Boost Sales</h1>
				</div>
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     SERVICES — Home V2 service cards (all shown)
	==================================================================== --}}
	<section class="hv2-section hv2-tint hv2-services svl-services" id="hv2-services">
			<div class="svl-deco" aria-hidden="true"><img class="svl-3d svl-3d--s1" src="{{ $svl3d }}/taxi-480.webp" srcset="{{ $svl3d }}/taxi-240.webp 240w, {{ $svl3d }}/taxi-480.webp 480w" sizes="(max-width: 991px) 140px, 240px" width="480" height="303" alt="" decoding="async" loading="lazy" data-svl-depth="0.08"><img class="svl-3d svl-3d--s2" src="{{ $svl3d }}/phone-480.webp" srcset="{{ $svl3d }}/phone-240.webp 240w, {{ $svl3d }}/phone-480.webp 480w" sizes="(max-width: 991px) 140px, 240px" width="323" height="480" alt="" decoding="async" loading="lazy" data-svl-depth="-0.08"></div>
		<div class="hv2-container">
			{{-- (no section heading here: the hero above already says "Our Services - Elevate Your Brand Visibility and Boost Sales") --}}
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
							<h2>{{ $val->name }}</h2>
							<p>{{ $val->description }}</p>
							<span class="svl-card-more" aria-label="Explore {{ $val->name }}"><span>Explore</span><i class="bi bi-arrow-right"></i></span>
						</span>
					</a>
				@endforeach
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     OUR CLIENTS — live "Brands we work with" section, logos in the Home V2 marquee
	==================================================================== --}}
	<section class="hv2-section hv2-dark hv2-clients svl-clients" id="hv2-clients">
		<span class="hv2-watermark" data-parallax="0.12" aria-hidden="true">Clients</span>
		<div class="svl-deco svl-deco--dark svl-deco--clients" aria-hidden="true"><img class="svl-3d svl-3d--k1" src="{{ $svl3d }}/billboard-480.webp" srcset="{{ $svl3d }}/billboard-240.webp 240w, {{ $svl3d }}/billboard-480.webp 480w" sizes="(max-width: 991px) 140px, 240px" width="355" height="480" alt="" decoding="async" loading="lazy" data-svl-depth="0.06"></div>
		<div class="hv2-container">
			<div class="hv2-head hv2-head--split">
				<div>
					<span class="hv2-eyebrow" data-reveal="up">Our Clients</span>
					<h2 class="hv2-display hv2-h2" data-split>Brands we <span class="hv2-outline">work with</span></h2>
				</div>
				<p class="hv2-lead" data-reveal="up" data-delay="2">At DataMySite, we are proud to partner with a diverse range of clients across various industries, from startups to established global brands.</p>
			</div>
		</div>
		@include('web.home-v2.clients-marquee')
	</section>
</main>
@endsection

@section('addScript')
	<script src="{{ v2_asset('assets/js/home-v2.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/services-landing-v2.js') }}" defer></script>
@endsection
