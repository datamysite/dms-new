{{--
|==========================================================================
| BLOGS LANDING V2 — redesigned /blogs (independent copy of web.blogs.index)
|--------------------------------------------------------------------------
| Self-contained page: this view + public/assets/css/blogs-v2.css
|                                + public/assets/js/blogs-v2.js
| Uses the shared V2 shell (web.home-v2.master) and the Home V2 design system
| (home-v2.css / home-v2.js). Does not include or depend on the live blogs view or any other V2 page layer.
| Same text + images + links as web.blogs.index.
| Preview: /blogs-new   Switch: BlogController@index -> view('web.blogs-v2')
|==========================================================================
--}}
@extends('web.home-v2.master')
@php require_once resource_path('views/web/home-v2/helpers.php'); @endphp

@section('v2lite', '1')
@section('v2crit', 'assets/css/blogs.critical.css')
@section('v2form', '1')

@section('metaAddition')
	@if(!empty($tags))<meta name="robots" content="noindex, follow">@endif
	<script>(function(){var d=document.documentElement;try{if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches)return;}catch(e){}d.classList.add('hv2-loading');setTimeout(function(){d.classList.remove('hv2-loading','hv2-leaving');},2500);})();</script>
	<link rel="preload" as="image" href="{{ URL::to('/public/assets/img/logo-white.webp') }}">
	{{-- self-hosted fonts: the two key files are preloaded, @font-face is inlined (font-display: swap) --}}
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/league-spartan-latin.woff2') }}">
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/nunito-sans-latin.woff2') }}">
	<style>{!! str_replace('../fonts/', URL::to('/public/assets/fonts') . '/', (string) @file_get_contents(public_path('assets/css/v2-fonts.css'))) !!}</style>
@endsection

@section('addStyle')
	<link href="{{ v2_asset('assets/css/home-v2.css') }}" rel="stylesheet">
	<link href="{{ v2_asset('assets/css/blogs-v2.css') }}" rel="stylesheet">
@endsection

@php
	$hv2Img = URL::to('/public/assets/img');
	$blvPhoto = is_file(public_path('assets/img/title/' . $titleImg)) ? $hv2Img . '/title/' . $titleImg : null;
	$blvImg = function ($val) { return empty($val->banner) ? URL::to('/public/og-logo.jpeg') : URL::to('public/storage/blogs/' . $val->banner); };
	$blvMin = function ($val) { return max(1, (int) ceil(str_word_count(strip_tags($val->description ?? '')) / 200)); };
	$blvInitial = function ($val) { return strtoupper(substr($val->author->name ?? '', 0, 1)); };
	$blvPieces = [
		'r' => '<div class="blv-book"><span class="blv-page p1"></span><span class="blv-page p2"></span><span class="blv-cover"><b>DMS</b></span></div>',
		'l' => '<div class="blv-sheets"><span class="blv-sheet s1"></span><span class="blv-sheet s2"></span><span class="blv-sheet s3"><em>PDF</em></span></div>',
		'a' => '<div class="blv-bubbles"><span class="blv-bubble b1"><i></i><i></i><i></i></span><span class="blv-bubble b2"><i></i><i></i></span></div>',
		'b' => '<div class="blv-book"><span class="blv-page p1"></span><span class="blv-page p2"></span><span class="blv-cover"><b>DMS</b></span></div>',
	];
	$blvStage = function ($mod, $depth) use ($blvPieces) {
		return '<div class="blv-stage blv-stage--' . $mod . '" data-blv-depth="' . $depth . '"><div class="blv-pc">' . ($blvPieces[$mod] ?? reset($blvPieces)) . '</div></div>';
	};
	$blvTitle = trim($title . ' ' . ($page ?? ''));
	$blvFirst = $data->first();
	$blvRest = $data->slice(1);
@endphp

@section('content')
<main class="main hv2 blv" id="hv2">

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

	{{-- HERO — breadcrumb + title, and the live page's sidebar enquiry form --}}
	<section class="hv2-hero hv2-dark blv-hero" id="hv2-hero">
		<div class="hv2-hero-bg" aria-hidden="true">
			@if($blvPhoto)<span class="blv-hero-photo" style="background-image: url('{{ $blvPhoto }}')"></span>@endif
			<span class="hv2-blob hv2-blob--1"></span><span class="hv2-blob hv2-blob--2"></span><span class="hv2-blob hv2-blob--3"></span>
			<span class="hv2-grid-lines"></span>
		</div>
		<div class="hv2-streaks" aria-hidden="true">
			@for($s = 0; $s < 8; $s++)<i style="top: {{ 10 + $s * 11 }}%; --t: {{ 2.8 + ($s % 4) * .7 }}s; --dl: {{ $s * .5 }}s"></i>@endfor
		</div>
		<div class="blv-deco blv-deco--dark blv-deco--hero2" aria-hidden="true"><div class="blv-hs blv-hs--n1" data-blv-depth="0.06"><div class="blv-hp"><div class="blv-penwrap"><svg class="blv-ink" viewBox="0 0 170 50" width="170" height="50" aria-hidden="true"><path d="M4,40 C30,4 52,50 80,26 S130,4 164,30" fill="none" stroke="#f44d46" stroke-width="5" stroke-linecap="round"/></svg><span class="blv-pen"><i></i></span></div></div></div><div class="blv-hs blv-hs--n2" data-blv-depth="-0.08"><div class="blv-hp"><div class="blv-news"><span class="blv-nw-head"></span><span class="blv-nw-img"></span><span class="blv-nw-col"></span><span class="blv-nw-col c2"></span></div></div></div><div class="blv-hs blv-hs--n3" data-blv-depth="0.1"><div class="blv-hp"><div class="blv-bm"><span></span><span class="s2"></span></div></div></div><div class="blv-hs blv-hs--n4" data-blv-depth="-0.05"><div class="blv-hp"><div class="blv-eye"><span class="blv-ey-lid"><i></i></span></div></div></div></div>
		<div class="hv2-container hv2-hero-inner">
			<div class="hv2-hero-copy">
				@include('web.home-v2.breadcrumbs', ['crumbs' => empty($type) ? [['Home', URL::to('/')], [$title, null]] : [['Home', URL::to('/')], ['Blogs', route('blogs')], [$title, null]]])
				<div class="blv-hero-body">
					<h1 data-reveal="up" data-delay="1">{{ $blvTitle }}</h1>
				</div>
			</div>

			<div class="hv2-hero-form-col" id="hv2-form">
				<div class="hv2-form-card hv2-glass" data-tilt data-tilt-max="4" data-reveal="scale" data-delay="2">
					<span class="hv2-glare" aria-hidden="true"></span>
					<form action="{{ route('enquiry.submit') }}" id="hv2-enquiry-form">
						@csrf
						<input type="hidden" name="service" value="Blogs">
						<span class="hv2-form-kicker"><i class="hv2-live-dot"></i> Experts online · reply in 60 min</span>
						<div class="hv2-form-title">Ready to Get <span class="hv2-hot">Results?</span></div>
						<p class="hv2-form-sub">Drop us your details — our experts respond within 60 minutes.</p>
						<label class="hv2-field"><input type="text" name="name" placeholder=" " required autocomplete="name"><span>Your Full Name</span></label>
						<div class="hv2-field hv2-field--tel">
							<input type="tel" name="mainphone" id="hv2-phone" required autocomplete="tel" placeholder="Your phone number" aria-label="Phone number">
							<input type="hidden" name="phone" id="hv2-fullphone">
						</div>
						<label class="hv2-field"><input type="email" name="email" id="hv2-email" placeholder=" " required autocomplete="email"><span>Your Email Address</span></label>
						<label class="hv2-field"><textarea name="description" rows="3" placeholder=" " required></textarea><span>Tell us what you need</span></label>
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
		</div>
		<a href="#blv-latest" class="hv2-scroll-cue" data-scrollto="#blv-latest" aria-label="Scroll to content"></a>
	</section>
	<span class="js-count-particles" hidden></span>

	{{-- FEATURED / LEAD POST --}}
	@if($blvFirst)
	<section class="hv2-section hv2-tint blv-latest" id="blv-latest">
		<div class="blv-deco" aria-hidden="true">
			{!! $blvStage('r', '0.10') !!}
			<span class="blv-dots" data-blv-depth="-0.06"></span>
			<span class="blv-plus p1" data-blv-depth="0.18">+</span><span class="blv-plus p2" data-blv-depth="-0.14">+</span><span class="blv-plus p3" data-blv-depth="0.12">+</span>
			<span class="blv-tri t1" data-blv-depth="-0.2"></span><span class="blv-tri t2" data-blv-depth="0.16"></span>
			<span class="blv-glow g1" data-blv-depth="0.08"></span><span class="blv-glow g2" data-blv-depth="-0.1"></span>
			<span class="blv-bar b1" data-blv-depth="0.2"></span><span class="blv-bar b2" data-blv-depth="-0.18"></span>
		</div>
		<div class="hv2-container">
			<article class="blv-feature" data-tilt data-tilt-max="3" data-reveal="up">
				<a class="blv-feature-media hv2-media" href="{{ v2_blog_url($blvFirst->slug) }}" target="_blank">
					<img {!! v2_img('public/storage/blogs/' . $blvFirst->banner, '(max-width: 991px) 92vw, 46vw') !!} alt="{{ $blvFirst->banner_alt ?: $blvFirst->heading }}" fetchpriority="high">
					<span class="blv-feature-tag">Latest</span>
				</a>
				<div class="blv-feature-body">
					<p class="blv-eyebrow">Fresh off the desk</p>
					<a href="{{ v2_blog_url($blvFirst->slug) }}" target="_blank"><h2>{{ $blvFirst->heading }}</h2></a>
					<div class="blv-meta">
						<span class="blv-avatar">{{ $blvInitial($blvFirst) }}</span>
						<span>{{ $blvFirst->author->name }}</span>
						<span class="blv-dot">&middot;</span>
						<span>{{ date('F d, Y', strtotime($blvFirst->created_at)) }}</span>
						<span class="blv-dot">&middot;</span>
						<span>{{ $blvMin($blvFirst) }} min read</span>
					</div>
					<p class="blv-excerpt">{{ $blvFirst->short_description }}</p>
					<a class="blv-cta" href="{{ v2_blog_url($blvFirst->slug) }}" target="_blank">Read the full story <span aria-hidden="true">&rarr;</span></a>
				</div>
			</article>
		</div>
	</section>
	@endif

	{{-- POST GRID --}}
	<section class="hv2-section hv2-light blv-posts" id="blv-posts">
		<span class="hv2-watermark" data-parallax="0.12" aria-hidden="true">Blogs</span>
		<div class="blv-deco" aria-hidden="true">
			{!! $blvStage('l', '0.1') !!}
			<span class="blv-dots" data-blv-depth="-0.06"></span>
			<span class="blv-plus p1" data-blv-depth="0.18">+</span><span class="blv-plus p2" data-blv-depth="-0.14">+</span><span class="blv-plus p3" data-blv-depth="0.12">+</span>
			<span class="blv-tri t1" data-blv-depth="-0.2"></span><span class="blv-tri t2" data-blv-depth="0.16"></span>
			<span class="blv-glow g1" data-blv-depth="0.08"></span><span class="blv-glow g2" data-blv-depth="-0.1"></span>
			<span class="blv-bar b1" data-blv-depth="0.2"></span><span class="blv-bar b2" data-blv-depth="-0.18"></span>
		</div>
		<div class="hv2-container">
			<div class="blv-grid" id="blv-grid">
				@foreach($blvRest as $val)
					<article class="blv-card" data-tilt data-tilt-max="4" data-reveal="up" data-delay="{{ ($loop->index % 3) + 1 }}">
						<a class="blv-card-media hv2-media" href="{{ v2_blog_url($val->slug) }}" target="_blank">
							<img {!! v2_img('public/storage/blogs/' . $val->banner, '(max-width: 640px) 92vw, (max-width: 1100px) 46vw, 31vw') !!} alt="{{ empty($val->banner_alt) ? $val->heading : $val->banner_alt }}" loading="lazy">
							<span class="blv-card-go" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
						</a>
						<div class="blv-card-body">
							<div class="blv-meta blv-meta--sm">
								<span>{{ date('F d, Y', strtotime($val->created_at)) }}</span>
								<span class="blv-dot">&middot;</span>
								<span>{{ $blvMin($val) }} min read</span>
							</div>
							<a href="{{ v2_blog_url($val->slug) }}" target="_blank"><h3>{{ $val->heading }}</h3></a>
							<p class="blv-card-excerpt">{{ $val->short_description }}</p>
							<div class="blv-card-footer">
								<span class="blv-avatar blv-avatar--sm">{{ $blvInitial($val) }}</span>
								<span class="blv-card-author">{{ $val->author->name }}</span>
								<a class="blv-cta blv-cta--sm" href="{{ v2_blog_url($val->slug) }}" target="_blank">Read more <span aria-hidden="true">&rarr;</span></a>
							</div>
						</div>
					</article>
				@endforeach
			</div>

			<div class="blv-pagination">
				{!! $data->withQueryString()->withPath(url()->current())->links('pagination::bootstrap-5') !!}
			</div>
		</div>
	</section>

	{{-- EXPLORE — the live sidebar's "Our Services" and "Blog Categories" link lists --}}
	<section class="hv2-section hv2-dark blv-explore" id="blv-explore">
		<div class="blv-deco blv-deco--dark" aria-hidden="true">
			{!! $blvStage('a', '0.08') !!}
			{!! $blvStage('b', '-0.1') !!}
			<span class="blv-dots blv-dots--l"></span><span class="blv-dots blv-dots--r"></span>
			<span class="blv-glow g1" data-blv-depth="0.08"></span><span class="blv-glow g2" data-blv-depth="-0.1"></span>
			<span class="blv-plus p1" data-blv-depth="0.18">+</span><span class="blv-plus p2" data-blv-depth="-0.14">+</span>
			<span class="blv-tri t1" data-blv-depth="-0.2"></span><span class="blv-bar b1" data-blv-depth="0.2"></span>
		</div>
		<div class="hv2-container blv-explore-grid">
			<div class="blv-links" data-reveal="up">
				<h2>Our Services</h2>
				<div class="blv-chips">
					@foreach($header_services as $val)
						@if(!empty($val->intro_html))
							<span class="inter-link-para">{!! v2_links($val->intro_html) !!}</span>
						@else
							<a href="{{ v2_url('/' . $val->slug) }}">{{ $val->name }}</a>
						@endif
					@endforeach
				</div>
			</div>
			@if(count($blog_categories) > 0)
			<div class="blv-links" data-reveal="up" data-delay="1">
				<h2><span>Blog Categories</span> <a href="{{ v2_url(route('blogs.categories')) }}">View all →</a></h2>
				<div class="blv-chips">
					@foreach($blog_categories as $val)
						<a href="{{ v2_url('/blogs/' . $val->slug) }}">{{ $val->name }} <small>({{ count($val->blogs) }})</small></a>
					@endforeach
				</div>
			</div>
			@endif
		</div>
	</section>
</main>
@endsection

@section('addScript')
	<script src="{{ v2_asset('assets/js/home-v2.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/blogs-v2.js') }}" defer></script>
@endsection
