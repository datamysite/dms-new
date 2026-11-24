{{--
|==========================================================================
| CASE STUDIES V2 — redesigned /client-case-studies (independent copy of web.case-studies)
|--------------------------------------------------------------------------
| Self-contained page: this view + public/assets/css/case-studies-v2.css
|                                + public/assets/js/case-studies-v2.js
| Uses the shared V2 shell (web.home-v2.master) and the Home V2 design system
| (home-v2.css / home-v2.js). Does not include or depend on the live case-studies view,
| service-v2.* or services-landing-v2.*. Same text + images as web.case-studies.
| Preview: /case-studies-new   Switch: WebController@caseStudies -> view('web.case-studies-v2')
|==========================================================================
--}}
@extends('web.home-v2.master')
@php require_once resource_path('views/web/home-v2/helpers.php'); @endphp

@section('v2lite', '1')
@section('v2crit', 'assets/css/case-studies.critical.css')
@section('v2form', '1')

@section('metaAddition')
	<script>(function(){var d=document.documentElement;try{if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches)return;}catch(e){}d.classList.add('hv2-loading');setTimeout(function(){d.classList.remove('hv2-loading','hv2-leaving');},2500);})();</script>
	<link rel="preload" as="image" href="{{ URL::to('/public/assets/img/logo-white.webp') }}">
	{{-- self-hosted fonts: the two key files are preloaded, @font-face is inlined (font-display: swap) --}}
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/league-spartan-latin.woff2') }}">
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/nunito-sans-latin.woff2') }}">
	<style>{!! str_replace('../fonts/', URL::to('/public/assets/fonts') . '/', (string) @file_get_contents(public_path('assets/css/v2-fonts.css'))) !!}</style>
@endsection

@section('addStyle')
	<link href="{{ v2_asset('assets/css/home-v2.css') }}" rel="stylesheet">
	<link href="{{ v2_asset('assets/css/case-studies-v2.css') }}" rel="stylesheet">
@endsection

@php
	$hv2Img = URL::to('/public/assets/img');
	$csvPhoto = is_file(public_path('assets/img/title/faqs.png')) ? $hv2Img . '/title/faqs.png' : null;

	/* ---------- Case studies: config/case_studies.php (PDFs + first-slide covers in public/case-studies/v2/) ---------- */
	$csvCases = v2_case_studies();
	$csvBenefits = [
		['Inspire Confidence', 'Highlight our expertise and proven track record in transforming brands.'],
		['Demonstrate Value', 'Show how our customized strategies create meaningful outcomes, from increased visibility to higher ROI.'],
		['Educate and Empower', 'Help potential clients understand the scope of our services and how we can address their unique challenges.'],
		['Build Trust', 'Reinforce our commitment to results-driven marketing and advertising by presenting real examples of success.'],
	];
	/* reusable 3D cluster (cube + orb + rings) */
	$csvPieces = [
		'r' => '<div class="csv-medal"><span class="csv-ribbon l"></span><span class="csv-ribbon r"></span><span class="csv-coin"><b>★</b></span></div>',
		'a' => '<div class="csv-sheets"><span class="csv-sheet s1"></span><span class="csv-sheet s2"></span><span class="csv-sheet s3"><em>PDF</em></span></div>',
		'b' => '<div class="csv-columns"><i class="csv-tw" style="--h:70px;--w:30px;--dl:0s"></i><i class="csv-tw" style="--h:120px;--w:30px;--dl:.4s"></i><i class="csv-tw" style="--h:170px;--w:30px;--dl:.8s"></i><i class="csv-tw" style="--h:230px;--w:30px;--dl:1.2s"></i></div>',
		'l' => '<div class="csv-sheets"><span class="csv-sheet s1"></span><span class="csv-sheet s2"></span><span class="csv-sheet s3"><em>PDF</em></span></div>',
	];
	$csvStage = function ($mod, $depth) use ($csvPieces) {
		return '<div class="csv-stage csv-stage--' . $mod . '" data-csv-depth="' . $depth . '"><div class="csv-pc">' . ($csvPieces[$mod] ?? reset($csvPieces)) . '</div></div>';
	};
@endphp

@section('content')
<main class="main hv2 csv" id="hv2">

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
	     HERO — same design as Home V2: breadcrumb + title, enquiry form (the live page's sidebar form)
	==================================================================== --}}
	<section class="hv2-hero hv2-dark csv-hero" id="hv2-hero">
		<div class="hv2-hero-bg" aria-hidden="true">
			@if($csvPhoto)<span class="csv-hero-photo" style="background-image: url('{{ $csvPhoto }}')"></span>@endif
			<span class="hv2-blob hv2-blob--1"></span><span class="hv2-blob hv2-blob--2"></span><span class="hv2-blob hv2-blob--3"></span>
			<span class="hv2-grid-lines"></span>
		</div>
		<div class="hv2-streaks" aria-hidden="true">
			@for($s = 0; $s < 8; $s++)<i style="top: {{ 10 + $s * 11 }}%; --t: {{ 2.8 + ($s % 4) * .7 }}s; --dl: {{ $s * .5 }}s"></i>@endfor
		</div>
		<div class="csv-deco csv-deco--dark csv-deco--hero2" aria-hidden="true"><div class="csv-hs csv-hs--n1" data-csv-depth="0.06"><div class="csv-hp"><div class="csv-trophy"><span class="csv-tr-cup"></span><span class="csv-tr-h l"></span><span class="csv-tr-h r"></span><span class="csv-tr-stem"></span><span class="csv-tr-base"></span></div></div></div><div class="csv-hs csv-hs--n2" data-csv-depth="-0.08"><div class="csv-hp"><div class="csv-target"><span class="csv-tg-rings"></span><span class="csv-tg-dart"></span></div></div></div><div class="csv-hs csv-hs--n3" data-csv-depth="0.1"><div class="csv-hp"><svg class="csv-trend" viewBox="0 0 180 130" width="180" height="130" aria-hidden="true"><polyline class="ln" points="6,118 44,86 80,98 120,46 160,14" fill="none" stroke="#f44d46" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/><polygon class="hd" points="146,8 174,6 162,32" fill="#f44d46"/><circle cx="44" cy="86" r="7" fill="#fff" stroke="#0b3d2e" stroke-width="4"/><circle cx="80" cy="98" r="7" fill="#fff" stroke="#0b3d2e" stroke-width="4"/><circle cx="120" cy="46" r="7" fill="#fff" stroke="#0b3d2e" stroke-width="4"/></svg></div></div><div class="csv-hs csv-hs--n4" data-csv-depth="-0.05"><div class="csv-hp"><div class="csv-clip"><span class="csv-cl-top"></span><i><b>✓</b></i><i><b>✓</b></i><i><b>✓</b></i></div></div></div></div>
		<div class="hv2-container hv2-hero-inner">
			<div class="hv2-hero-copy">
				@include('web.home-v2.breadcrumbs', ['crumbs' => [['Home', URL::to('/')], ['Client Case Studies', null]]])
				<div class="csv-hero-body">
					<h1 data-reveal="up" data-delay="1">Client Case Studies</h1>
				</div>
			</div>

			<div class="hv2-hero-form-col" id="hv2-form">
				<div class="hv2-form-card hv2-glass" data-tilt data-tilt-max="4" data-reveal="scale" data-delay="2">
					<span class="hv2-glare" aria-hidden="true"></span>
					<form action="{{ route('enquiry.submit') }}" id="hv2-enquiry-form">
						@csrf
						<input type="hidden" name="service" value="Client Case Studies">
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
		</div>
		<a href="#csv-intro" class="hv2-scroll-cue" data-scrollto="#csv-intro" aria-label="Scroll to content"></a>
	</section>
	<span class="js-count-particles" hidden></span>

	{{-- ===================================================================
	     INTRO — "Why Does DataMySite Share Case Studies?"
	==================================================================== --}}
	<section class="hv2-section hv2-tint csv-intro" id="csv-intro">
		<div class="csv-deco" aria-hidden="true">
			{!! $csvStage('r', '0.10') !!}
			<span class="csv-dots" data-csv-depth="-0.06"></span>
			<span class="csv-plus p1" data-csv-depth="0.18">+</span><span class="csv-plus p2" data-csv-depth="-0.14">+</span><span class="csv-plus p3" data-csv-depth="0.12">+</span>
			<span class="csv-tri t1" data-csv-depth="-0.2"></span><span class="csv-tri t2" data-csv-depth="0.16"></span>
			<span class="csv-glow g1" data-csv-depth="0.08"></span><span class="csv-glow g2" data-csv-depth="-0.1"></span>
			<span class="csv-bar b1" data-csv-depth="0.2"></span><span class="csv-bar b2" data-csv-depth="-0.18"></span>
		</div>
		<div class="hv2-container csv-intro-grid">
			<div class="csv-intro-copy">
				<span class="hv2-eyebrow" data-reveal="up">Client Case Studies</span>
				<h2 class="hv2-display hv2-h2" data-split>Why Does DataMySite Share <span class="hv2-outline">Case Studies?</span></h2>
				<p data-reveal="up" data-delay="1">At DataMySite, we believe in transparency and trust. Sharing case studies allows us to demonstrate the tangible impact of our marketing and advertising solutions. They provide a clear, evidence-based view of how we address specific business challenges and deliver measurable results.
					<br>
				By showcasing real-world success stories, we aim to:</p>
			</div>
			<ul class="csv-benefits">
				@foreach($csvBenefits as $b)
					<li class="csv-benefit" data-tilt data-tilt-max="5" data-reveal="up" data-delay="{{ $loop->iteration }}">
						<span class="csv-benefit-no hv2-display" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
						<strong>{{ $b[0] }}</strong>: {{ $b[1] }}
					</li>
				@endforeach
			</ul>
		</div>
		<div class="hv2-container">
			<p class="csv-closing" data-reveal="up">Each case study is a testament to our dedication, creativity, and expertise, offering a glimpse into the innovative solutions we craft to help businesses thrive in Dubai's competitive market.</p>
		</div>
	</section>

	{{-- ===================================================================
	     CASE STUDIES — cards linking to the PDFs (same covers, same titles)
	==================================================================== --}}
	<section class="hv2-section hv2-dark csv-cases" id="csv-cases">
		<span class="hv2-watermark" data-parallax="0.12" aria-hidden="true">Case Studies</span>
		<div class="csv-deco csv-deco--dark" aria-hidden="true">
			{!! $csvStage('a', '0.08') !!}
			{!! $csvStage('b', '-0.1') !!}
			<span class="csv-dots csv-dots--l"></span><span class="csv-dots csv-dots--r"></span>
			<span class="csv-glow g1" data-csv-depth="0.08"></span><span class="csv-glow g2" data-csv-depth="-0.1"></span>
			<span class="csv-plus p1" data-csv-depth="0.18">+</span><span class="csv-plus p2" data-csv-depth="-0.14">+</span>
			<span class="csv-tri t1" data-csv-depth="-0.2"></span><span class="csv-bar b1" data-csv-depth="0.2"></span>
		</div>
		<div class="hv2-container">
			<div class="csv-grid" id="csv-grid">
				@foreach($csvCases as $c)
					<div class="case-studies-new csv-card-wrap" data-reveal="up" data-delay="{{ ($loop->index % 3) + 1 }}">
						<a href="{{ $c['pdf_url'] }}" target="_blank" rel="noopener" class="open-case-study csv-card" data-tilt data-tilt-max="5">
							<span class="csv-card-media hv2-media">
								<img {!! v2_img($c['cover'], '(max-width: 640px) 92vw, (max-width: 1100px) 46vw, 31vw') !!} class="{{ $loop->iteration % 2 ? 'case-studies-img' : 'case-studies-img-even' }}" alt="{{ $c['alt'] }}" loading="lazy">
								<span class="csv-card-no hv2-display" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
								<span class="csv-card-go" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
							</span>
							<h3><span>{{ $c['name'] }}</span> x <span>DMS</span> |  Case Study</h3>
						</a>
					</div>
				@endforeach
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     TRANSFORMING BRANDS + WHY CHOOSE DATAMYSITE
	==================================================================== --}}
	<section class="hv2-section hv2-tint csv-why" id="csv-why">
		<div class="csv-deco" aria-hidden="true">
			{!! $csvStage('l', '0.1') !!}
			<span class="csv-dots" data-csv-depth="-0.06"></span>
			<span class="csv-plus p1" data-csv-depth="0.18">+</span><span class="csv-plus p2" data-csv-depth="-0.14">+</span>
			<span class="csv-tri t1" data-csv-depth="-0.2"></span>
			<span class="csv-glow g1" data-csv-depth="0.08"></span><span class="csv-glow g2" data-csv-depth="-0.1"></span>
			<span class="csv-bar b1" data-csv-depth="0.2"></span><span class="csv-bar b2" data-csv-depth="-0.18"></span>
		</div>
		<div class="hv2-container csv-why-grid">
			<article class="csv-why-card" data-tilt data-tilt-max="4" data-reveal="up" data-delay="1">
				<span class="csv-why-no hv2-display" aria-hidden="true">01</span>
				<h2>Transforming Brands Through Comprehensive Marketing Solutions</h2>
				<p>At DataMySite, we offer a full suite of marketing and advertising services designed to drive measurable results. From digital advertising to outdoor campaigns, public relations to influencer marketing, we help brands stand out in a competitive market. Below, we highlight the before-and-after transformations our clients experienced through our tailored marketing solutions in Dubai, UAE.</p>
			</article>
			<article class="csv-why-card csv-why-card--hot" data-tilt data-tilt-max="4" data-reveal="up" data-delay="2">
				<span class="csv-why-no hv2-display" aria-hidden="true">02</span>
				<h2>Why Choose DataMySite?</h2>
				<p>Through our comprehensive marketing and advertising services, we help businesses in Dubai achieve remarkable results. Whether you're looking to enhance your online presence with digital advertising, build brand recognition through outdoor ads, or generate qualified leads through event marketing, DataMySite has the expertise to drive success.</p>
			</article>
		</div>
	</section>
</main>
@endsection

@section('addScript')
	<script src="{{ v2_asset('assets/js/home-v2.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/case-studies-v2.js') }}" defer></script>
@endsection
