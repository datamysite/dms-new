{{--
|==========================================================================
| BLOG CATEGORIES V2 — redesigned /blogs/categories (independent copy of web.blogs.categories)
|--------------------------------------------------------------------------
| Self-contained page: this view + public/assets/css/blogs-categories-v2.css
|                                + public/assets/js/blogs-categories-v2.js
| Uses the shared V2 shell (web.home-v2.master) and the Home V2 design system
| (home-v2.css / home-v2.js). Does not include or depend on the live categories view or any other V2 page layer.
| Same text + links + search/toggle behaviour as web.blogs.categories.
| Preview: /blogs/categories-new   Switch: BlogController@allCategories -> view('web.blogs-categories-v2')
|==========================================================================
--}}
@extends('web.home-v2.master')
@php require_once resource_path('views/web/home-v2/helpers.php'); @endphp

@section('v2lite', '1')
@section('v2crit', 'assets/css/blogs-categories.critical.css')

@section('metaAddition')
	<script>(function(){var d=document.documentElement;try{if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches)return;}catch(e){}d.classList.add('hv2-loading');setTimeout(function(){d.classList.remove('hv2-loading','hv2-leaving');},2500);})();</script>
	<link rel="preload" as="image" href="{{ v2_asset('assets/img/logo-white.webp', false) }}">
	{{-- self-hosted fonts: the two key files are preloaded, @font-face is inlined (font-display: swap) --}}
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/league-spartan-latin.woff2') }}">
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/nunito-sans-latin.woff2') }}">
	<style>{!! str_replace('../fonts/', URL::to('/public/assets/fonts') . '/', (string) @file_get_contents(public_path('assets/css/v2-fonts.css'))) !!}</style>
@endsection

@section('addStyle')
	<link href="{{ v2_asset('assets/css/home-v2.css') }}" rel="stylesheet">
	<link href="{{ v2_asset('assets/css/blogs-categories-v2.css') }}" rel="stylesheet">
@endsection

@php
	$hv2Img = URL::to('/public/assets/img');
	$bcvPhoto = is_file(public_path('assets/img/title/' . $titleImg)) ? $hv2Img . '/title/' . $titleImg : null;
	$bcvPieces = [
		't' => '<div class="bcv-tagpc"><span class="bcv-tag"><i></i></span><span class="bcv-tag t2"><i></i></span></div>',
		'm' => '<div class="bcv-tagpc"><span class="bcv-tag"><i></i></span><span class="bcv-tag t2"><i></i></span></div>',
		'b' => '<div class="bcv-book"><span class="bcv-page p1"></span><span class="bcv-page p2"></span><span class="bcv-cover"><b>DMS</b></span></div>',
	];
	$bcvStage = function ($mod, $depth) use ($bcvPieces) {
		return '<div class="bcv-stage bcv-stage--' . $mod . '" data-bcv-depth="' . $depth . '"><div class="bcv-pc">' . ($bcvPieces[$mod] ?? reset($bcvPieces)) . '</div></div>';
	};
	$grouped = $categories->sortBy(function ($c) { return \Illuminate\Support\Str::lower($c->name); })
		->groupBy(function ($c) { return \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($c->name, 0, 1)); });
@endphp

@section('content')
<main class="main hv2 bcv" id="hv2">

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

	{{-- HERO — breadcrumb + title (the live page has no form) --}}
	<section class="hv2-hero hv2-dark bcv-hero" id="hv2-hero">
		<div class="hv2-hero-bg" aria-hidden="true">
			@if($bcvPhoto)<span class="bcv-hero-photo" style="background-image: url('{{ $bcvPhoto }}')"></span>@endif
			<span class="hv2-blob hv2-blob--1"></span><span class="hv2-blob hv2-blob--2"></span><span class="hv2-blob hv2-blob--3"></span>
			<span class="hv2-grid-lines"></span>
		</div>
		<div class="hv2-streaks" aria-hidden="true">
			@for($s = 0; $s < 8; $s++)<i style="top: {{ 10 + $s * 11 }}%; --t: {{ 2.8 + ($s % 4) * .7 }}s; --dl: {{ $s * .5 }}s"></i>@endfor
		</div>
		<div class="bcv-deco bcv-deco--dark bcv-deco--hero2" aria-hidden="true"><div class="bcv-hs bcv-hs--n1" data-bcv-depth="0.06"><div class="bcv-hp"><div class="bcv-folder"><span class="bcv-fd-back"></span><span class="bcv-fd-paper"></span><span class="bcv-fd-front"></span></div></div></div><div class="bcv-hs bcv-hs--n2" data-bcv-depth="-0.08"><div class="bcv-hp"><div class="bcv-letters"><b>A</b><b>B</b><b>C</b></div></div></div><div class="bcv-hs bcv-hs--n3" data-bcv-depth="0.1"><div class="bcv-hp"><div class="bcv-quote"><b>“</b></div></div></div></div>
		<div class="hv2-container hv2-hero-inner">
			<div class="hv2-hero-copy">
				@include('web.home-v2.breadcrumbs', ['crumbs' => [['Home', URL::to('/')], ['Blogs', route('blogs')], [$title, null]]])
				<div class="bcv-hero-body">
					<h1 data-reveal="up" data-delay="1">{{ $title }}</h1>
				</div>
			</div>
		</div>
		<a href="#bcv-index" class="hv2-scroll-cue" data-scrollto="#bcv-index" aria-label="Scroll to content"></a>
	</section>
	<span class="js-count-particles" hidden></span>

	{{-- CATEGORY INDEX — search, A–Z / most published toggle, letter rail --}}
	<section class="hv2-section hv2-tint bcv-index" id="bcv-index">
		<div class="bcv-deco" aria-hidden="true">
			{!! $bcvStage('t', '0.10') !!}{!! $bcvStage('m', '-0.08') !!}{!! $bcvStage('b', '0.06') !!}
			<span class="bcv-dots" data-bcv-depth="-0.06"></span><span class="bcv-dots bcv-dots--r" data-bcv-depth="0.05"></span>
			<span class="bcv-plus p1" data-bcv-depth="0.18">+</span><span class="bcv-plus p2" data-bcv-depth="-0.14">+</span><span class="bcv-plus p3" data-bcv-depth="0.12">+</span>
			<span class="bcv-tri t1" data-bcv-depth="-0.2"></span><span class="bcv-tri t2" data-bcv-depth="0.16"></span>
			<span class="bcv-glow g1" data-bcv-depth="0.08"></span><span class="bcv-glow g2" data-bcv-depth="-0.1"></span>
			<span class="bcv-bar b1" data-bcv-depth="0.2"></span><span class="bcv-bar b2" data-bcv-depth="-0.18"></span>
		</div>
		<div class="hv2-container">
			<div class="bcv-controls" data-reveal="up">
				<div class="bcv-search">
					<i class="bi bi-search" aria-hidden="true"></i>
					<input type="text" id="catSearch" placeholder="Search categories…" aria-label="Search categories">
				</div>
				<div class="bcv-toggle" role="group" aria-label="View">
					<button type="button" class="bcv-toggle-btn is-active" data-view="az">A&ndash;Z index</button>
					<button type="button" class="bcv-toggle-btn" data-view="popular">Most published</button>
				</div>
			</div>

			<p class="bcv-count" id="catCount" aria-live="polite">Showing {{ $categories->count() }} of {{ $categories->count() }} categories</p>

			<div class="bcv-layout">
				<div class="bcv-main">
					<div class="bcv-groups" id="azView">
						@foreach($grouped as $letter => $group)
							<div class="bcv-letter" id="letter-{{ $letter }}" data-reveal="up">
								<div class="bcv-letter-mark hv2-display" aria-hidden="true">{{ $letter }}</div>
								<div class="bcv-letter-rows">
									@foreach($group as $cat)
										<a href="{{ v2_url('/blogs/' . $cat->slug) }}" class="bcv-row" data-name="{{ strtolower($cat->name) }}" data-tilt data-tilt-max="3">
											<span class="bcv-row-name">{{ $cat->name }}</span>
											<span class="bcv-row-count">{{ $cat->blogs_count }} {{ \Illuminate\Support\Str::plural('post', $cat->blogs_count) }}</span>
											<span class="bcv-row-arrow" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
										</a>
									@endforeach
								</div>
							</div>
						@endforeach
					</div>

					<div class="bcv-groups" id="popularView" hidden>
						<div class="bcv-letter-rows bcv-letter-rows--ranked">
							@foreach($categories->sortByDesc('blogs_count')->values() as $i => $cat)
								<a href="{{ v2_url('/blogs/' . $cat->slug) }}" class="bcv-row bcv-row--ranked" data-name="{{ strtolower($cat->name) }}">
									<span class="bcv-row-rank hv2-display" aria-hidden="true">{{ sprintf('%02d', $i + 1) }}</span>
									<span class="bcv-row-name">{{ $cat->name }}</span>
									<span class="bcv-row-count">{{ $cat->blogs_count }} {{ \Illuminate\Support\Str::plural('post', $cat->blogs_count) }}</span>
									<span class="bcv-row-arrow" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
								</a>
							@endforeach
						</div>
					</div>

					<p class="bcv-empty" id="catEmpty" hidden>No categories match your search. Try a different word.</p>
				</div>

				<aside class="bcv-rail" id="azRail" aria-label="Jump to letter">
					@foreach(range('A', 'Z') as $letter)
						@if(isset($grouped[$letter]))
							<a href="#letter-{{ $letter }}">{{ $letter }}</a>
						@else
							<span>{{ $letter }}</span>
						@endif
					@endforeach
				</aside>
			</div>
		</div>
	</section>
</main>
@endsection

@section('addScript')
	<script src="{{ v2_asset('assets/js/home-v2.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/blogs-categories-v2.js') }}" defer></script>
@endsection
