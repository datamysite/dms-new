{{--
|==========================================================================
| ABOUT V2 — redesigned /about-us (independent copy of web.about)
|--------------------------------------------------------------------------
| Self-contained page: this view + public/assets/css/about-v2.css
|                                + public/assets/js/about-v2.js
| Uses the shared V2 shell (web.home-v2.master) and the Home V2 design system
| (home-v2.css / home-v2.js). Does not include or depend on the live about view or any other V2 page layer.
| Same text + images + links as web.about. 3D decoration is custom to this page (skyline, compass, medal,
| growth columns, document sheets, orbit, billboard, gears, chain links, map pins).
| Preview: /about-us-new   Switch: WebController@about -> view('web.about-v2')
|==========================================================================
--}}
@extends('web.home-v2.master')
@php require_once resource_path('views/web/home-v2/helpers.php'); @endphp

@section('v2lite', '1')
@section('v2form', '1')

@section('metaAddition')
	<script>(function(){var d=document.documentElement;try{if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches)return;}catch(e){}d.classList.add('hv2-loading');setTimeout(function(){d.classList.remove('hv2-loading','hv2-leaving');},2500);})();</script>
	<link rel="preload" as="image" href="{{ URL::to('/public/assets/img/logo-white.png') }}">
	{{-- self-hosted fonts: the two key files are preloaded, @font-face is inlined (font-display: swap) --}}
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/league-spartan-latin.woff2') }}">
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/nunito-sans-latin.woff2') }}">
	<style>{!! str_replace('../fonts/', URL::to('/public/assets/fonts') . '/', (string) @file_get_contents(public_path('assets/css/v2-fonts.css'))) !!}</style>
@endsection

@section('addStyle')
	<link href="{{ v2_asset('assets/css/home-v2.css') }}" rel="stylesheet">
	<link href="{{ v2_asset('assets/css/about-v2.css') }}" rel="stylesheet">
@endsection

@php
	$hv2Img = URL::to('/public/assets/img');
	$abvPhoto = is_file(public_path('assets/img/title/about.png')) ? $hv2Img . '/title/about.png' : null;

	/* client logos — same as the Home V2 marquee */
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

	/* DATA — services + case studies. A controller may pass $aboutServices / $aboutCases (same shape) to replace these defaults;
	   the layout adapts to any count (grids auto-fit, last odd card spans, "See More" appears past the limit). */
	$abvSvcLimit = 6;
	$abvCaseLimit = 8;
	$abvServices = $aboutServices ?? [
		['title' => 'Outdoor Advertising', 'url' => '/outdoor-advertising', 'intro' => 'Our outdoor media strategies ensure your brand stays top-of-mind by dominating high-traffic locations across the UAE.', 'lead' => 'Services Include:',
			'list' => ['Billboard Advertising', 'Flyers Distribution', 'Human Billboards', 'Transit Media (Airline, Taxi, and Bus Advertising)'],
			'outro' => 'We provide strategic location planning, creative support, and full campaign management to maximize your return on visibility.'],
		['title' => 'Digital & DOOH Advertising', 'url' => '/digital-advertising', 'intro' => 'We take your digital presence to new heights with performance-driven campaigns across dynamic platforms and real-world digital screens.', 'lead' => 'Services Include:',
			'list' => ['OTT Advertising – Reach audiences on popular streaming platforms', 'DOOH (Digital Out-of-Home) – Eye-catching digital signage in public spaces', 'Hotel Digital Screens Advertising – Promote to premium guests at 4–5 star properties', 'Elevator Advertising – Captive messaging in residential and commercial towers'],
			'outro' => 'Our data-backed targeting ensures your digital ad spend reaches the right people, in the right place, at the right time.'],
		['title' => 'Public Relations (PR Coverage)', 'url' => '/public-relations-pr-coverage', 'intro' => 'We specialize in high-impact PR placements that enhance brand credibility and awareness across major regional and global media outlets.', 'lead' => 'Our PR partners include:',
			'list' => ['Gulf News', 'Forbes', 'Khaleej Times', 'Arabian Business'],
			'outro' => 'Whether it’s a product launch, leadership spotlight, or thought leadership article, we help shape a strong and authoritative voice for your brand.'],
		['title' => 'Celebrity & Influencer Marketing', 'url' => null, 'intro' => 'Nothing builds trust like the right face promoting your brand. We specialize in aligning your product or service with powerful personalities that your audience already admires.',
			'split' => [
				['Celebrity Marketing:', '/celebrity-marketing', ['Sports Personalities', 'Bollywood Celebrities']],
				['Influencer Marketing:', '/influencer-marketing', ['Nano Influencers (1K–10K followers)', 'Micro Influencers (10K–100K followers)', 'Macro Influencers (100K–1M+ followers)']],
			],
			'outro' => 'From negotiations to campaign execution, we handle every aspect of celebrity and influencer collaboration.'],
		['title' => 'Event Marketing', 'url' => '/event-marketing', 'intro' => 'Leave a lasting impression through unforgettable brand experiences. Whether it’s a private VIP launch or a large-scale corporate function, we bring your vision to life.', 'lead' => 'We Manage:',
			'list' => ['Corporate Events', 'Private Events', 'Experiential Activations'],
			'outro' => 'We integrate design, branding, and media into cohesive event strategies that drive attention and engagement.'],
		['title' => 'Transit Media Advertising', 'url' => '/transit-media', 'intro' => 'Your brand goes places with our powerful transit media solutions, ensuring mobile visibility across diverse audiences.', 'lead' => 'Transit Channels:',
			'list' => ['Airline Advertising', 'Taxi Wraps', 'Bus Branding'],
			'outro' => 'Be seen from city streets to airport lounges — every moment is a branding opportunity.'],
	];
	/* case studies: [pdf file, cover image file, img class, brand name] */
	$abvCases = $aboutCases ?? [['dubai-duty-free', 'dubai-duty-free', 'case-studies-img', 'Dubai Duty Free'], ['lenskart', 'lenskart', 'case-studies-img-even', 'Lenskart'], ['danube', 'danube', 'case-studies-img', 'Danube'], ['ksa', 'ksa', 'case-studies-img-even', 'KSA Influencers']];
	$abvSteps = [
		['bi-list-task', 'Select Your Service', 'Helps us understand your needs and aligns our strategies with your business goals.'],
		['bi-calendar4-week', 'Media Plan & Approvals', 'We’ll finalize the details and set a timeline to kick off your customized marketing strategy.'],
		['bi-credit-card', 'Campaign Onboarding', 'Ensures your service is reserved and allows us to start preparing for your campaign with full commitment.'],
		['bi-graph-up-arrow', 'Launch & Monitor', 'We launch your campaign and continuously track its performance'],
	];
	/* custom 3D pieces (decorative) */
	$abvTowers = function ($cls, $depth) {
		$h = [120, 190, 150, 250, 170, 110];
		$o = '<div class="abv-skyline ' . $cls . '" data-abv-depth="' . $depth . '">';
		foreach ($h as $i => $hh) { $o .= '<i class="abv-tw" style="--h:' . $hh . 'px;--w:' . (26 + ($i % 3) * 8) . 'px;--dl:' . ($i * .35) . 's"></i>'; }
		return $o . '</div>';
	};
	$abvCompass = function ($cls, $depth) { return '<div class="abv-compass ' . $cls . '" data-abv-depth="' . $depth . '"><span class="abv-compass-ring"></span><span class="abv-compass-ring r2"></span><span class="abv-compass-needle"></span><span class="abv-compass-dot"></span></div>'; };
	$abvMedal = function ($cls, $depth) { return '<div class="abv-medal ' . $cls . '" data-abv-depth="' . $depth . '"><span class="abv-ribbon l"></span><span class="abv-ribbon r"></span><span class="abv-coin"><b>★</b></span></div>'; };
	$abvColumns = function ($cls, $depth) { return '<div class="abv-columns ' . $cls . '" data-abv-depth="' . $depth . '"><i class="abv-tw" style="--h:70px;--w:30px;--dl:0s"></i><i class="abv-tw" style="--h:120px;--w:30px;--dl:.4s"></i><i class="abv-tw" style="--h:170px;--w:30px;--dl:.8s"></i><i class="abv-tw" style="--h:230px;--w:30px;--dl:1.2s"></i></div>'; };
	$abvSheets = function ($cls, $depth) { return '<div class="abv-sheets ' . $cls . '" data-abv-depth="' . $depth . '"><span class="abv-sheet s1"></span><span class="abv-sheet s2"></span><span class="abv-sheet s3"><em>PDF</em></span></div>'; };
	$abvOrbit = function ($cls, $depth) { return '<div class="abv-orbit ' . $cls . '" data-abv-depth="' . $depth . '"><span class="abv-core"></span><span class="abv-path p1"><i></i></span><span class="abv-path p2"><i></i></span><span class="abv-path p3"><i></i></span></div>'; };
	$abvBoard = function ($cls, $depth) { return '<div class="abv-board ' . $cls . '" data-abv-depth="' . $depth . '"><span class="abv-panel"><i></i><i></i><i></i></span><span class="abv-pole l"></span><span class="abv-pole r"></span></div>'; };
	$abvGears = function ($cls, $depth) { return '<div class="abv-gears ' . $cls . '" data-abv-depth="' . $depth . '"><span class="abv-gear g1"></span><span class="abv-gear g2"></span></div>'; };
	$abvChain = function ($cls, $depth) { return '<div class="abv-chain ' . $cls . '" data-abv-depth="' . $depth . '"><span class="abv-lk a"></span><span class="abv-lk b"></span></div>'; };
	$abvPin = function ($cls, $depth) { return '<div class="abv-pin ' . $cls . '" data-abv-depth="' . $depth . '"><span class="abv-pin-head"></span><span class="abv-pin-pulse"></span></div>'; };
	$abvMotifs = '<span class="abv-dots" data-abv-depth="-0.06"></span><span class="abv-plus p1" data-abv-depth="0.18">+</span><span class="abv-plus p2" data-abv-depth="-0.14">+</span><span class="abv-glow g1" data-abv-depth="0.08"></span><span class="abv-glow g2" data-abv-depth="-0.1"></span>';
@endphp

@section('content')
<main class="main hv2 abv" id="hv2">

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

	{{-- HERO — breadcrumb + title; the Dubai skyline rises along the bottom edge --}}
	<section class="hv2-hero hv2-dark abv-hero" id="hv2-hero">
		<div class="hv2-hero-bg" aria-hidden="true">
			@if($abvPhoto)<span class="abv-hero-photo" style="background-image: url('{{ $abvPhoto }}')"></span>@endif
			<span class="hv2-blob hv2-blob--1"></span><span class="hv2-blob hv2-blob--2"></span><span class="hv2-blob hv2-blob--3"></span>
			<span class="hv2-grid-lines"></span>
		</div>
		<div class="hv2-streaks" aria-hidden="true">
			@for($s = 0; $s < 8; $s++)<i style="top: {{ 10 + $s * 11 }}%; --t: {{ 2.8 + ($s % 4) * .7 }}s; --dl: {{ $s * .5 }}s"></i>@endfor
		</div>
		<div class="abv-deco abv-deco--dark abv-deco--hero" aria-hidden="true"><div class="abv-hs abv-hs--n1" data-abv-depth="0.06"><div class="abv-hp"><div class="abv-team"><i class="a"></i><i class="b"></i><i class="c"></i></div></div></div><div class="abv-hs abv-hs--n2" data-abv-depth="-0.08"><div class="abv-hp"><div class="abv-cal"><span class="abv-cal-top"><i></i><i></i></span><b>2021</b></div></div></div><div class="abv-hs abv-hs--n3" data-abv-depth="0.1"><div class="abv-hp"><div class="abv-rocket"><span class="abv-rk-body"><i></i></span><span class="abv-rk-fin l"></span><span class="abv-rk-fin r"></span><span class="abv-rk-fire"></span></div></div></div>
			{!! $abvTowers('abv-skyline--hero', '0.05') !!}
			{!! $abvPin('abv-pin--hero', '-0.1') !!}
		</div>
		<div class="hv2-container hv2-hero-inner">
			<div class="hv2-hero-copy">
				@include('web.home-v2.breadcrumbs', ['crumbs' => [['Home', URL::to('/')], ['About Us', null]]])
				<div class="abv-hero-body">
					<h1 data-reveal="up" data-delay="1">About Us</h1>
				</div>
			</div>
		</div>
		<a href="#abv-intro" class="hv2-scroll-cue" data-scrollto="#abv-intro" aria-label="Scroll to content"></a>
	</section>
	<span class="js-count-particles" hidden></span>

	{{-- INTRO — story + Mission / Vision / Values --}}
	<section class="hv2-section hv2-tint abv-sec abv-intro" id="abv-intro">
		<div class="abv-deco" aria-hidden="true">
			{!! $abvCompass('abv-compass--r', '0.1') !!}
			{!! $abvTowers('abv-skyline--l', '-0.06') !!}
			{!! $abvMotifs !!}
		</div>
		<div class="hv2-container">
			<div class="abv-head" data-reveal="up">
				<h3 class="hv2-display hv2-h2" data-split>Driving Impactful Brand Visibility <span class="hv2-outline">Since 2021</span></h3>
				<hr>
			</div>
			<div class="abv-intro-grid">
				<div class="abv-story" data-reveal="up" data-delay="1">
					<p>
						Founded in 2021 and proudly based in Dubai, UAE, DataMySite is a full-service marketing and media agency delivering results-driven solutions for ambitious brands. Over the past 5+ years, we have been providing high-quality marketing and advertising services, supported by a team with 20+ years of combined industry experience across marketing strategy, media planning, and brand growth.
						<br><br>
						We specialize in high-impact advertising solutions designed to cut through market noise and create lasting brand influence. Our expertise spans brand storytelling, experiential marketing, and multi-channel media execution, ensuring your brand doesn’t just participate in the conversation—it leads it.
					</p>
				</div>
				<div class="abv-acc" id="abv-acc" data-reveal="up" data-delay="2">
					<div class="abv-acc-item is-open">
						<h5 class="abv-acc-q"><button type="button" aria-expanded="true"><span class="abv-acc-no hv2-display" aria-hidden="true">01</span>Mission<i class="bi bi-plus-lg" aria-hidden="true"></i></button></h5>
						<div class="abv-acc-a"><div><p>To <strong>deliver measurable, innovative, and brand-aligned marketing solutions</strong> that elevate visibility, spark engagement, and drive results across physical and digital channels.</p></div></div>
					</div>
					<div class="abv-acc-item">
						<h5 class="abv-acc-q"><button type="button" aria-expanded="false"><span class="abv-acc-no hv2-display" aria-hidden="true">02</span>Vision<i class="bi bi-plus-lg" aria-hidden="true"></i></button></h5>
						<div class="abv-acc-a"><div><p>To become the most trusted and innovative <strong>marketing partner</strong> for brands across the UAE and beyond — providing creativity, strategy, and execution under one roof.</p></div></div>
					</div>
					<div class="abv-acc-item">
						<h5 class="abv-acc-q"><button type="button" aria-expanded="false"><span class="abv-acc-no hv2-display" aria-hidden="true">03</span>Values<i class="bi bi-plus-lg" aria-hidden="true"></i></button></h5>
						<div class="abv-acc-a"><div><p>We value <strong>creativity, integrity, innovation,</strong> and <strong>results—empowering</strong> brands to connect, engage, and grow through bold ideas and strategic execution.</p></div></div>
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- CLIENTS --}}
	<section class="hv2-section hv2-dark hv2-clients abv-sec abv-clients" id="hv2-clients">
		<span class="hv2-watermark" data-parallax="0.12" aria-hidden="true">Clients</span>
		<div class="abv-deco abv-deco--dark" aria-hidden="true">
			{!! $abvPin('abv-pin--a', '0.1') !!}{!! $abvPin('abv-pin--b', '-0.08') !!}
			<span class="abv-glow g1" data-abv-depth="0.08"></span><span class="abv-glow g2" data-abv-depth="-0.1"></span>
			<span class="abv-plus p1" data-abv-depth="0.18">+</span><span class="abv-plus p2" data-abv-depth="-0.14">+</span>
		</div>
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
										<img src="{{ $hv2Img }}/clients-final/{{ $i }}.webp" alt="{{ $dup ? '' : $brand.' logo' }}" loading="lazy" width="300" height="300">
									</div>
								</div>
							@endforeach
						@endfor
					</div>
				</div>
			@endforeach
		</div>
	</section>

	{{-- OUR SUCCESSFUL PROJECTS — counters --}}
	<section class="hv2-section hv2-tint abv-sec abv-stats" id="abv-stats">
		<div class="abv-deco" aria-hidden="true">
			{!! $abvMedal('abv-medal--l', '0.1') !!}
			{!! $abvColumns('abv-columns--r', '-0.08') !!}
			{!! $abvMotifs !!}
		</div>
		<div class="hv2-container">
			<div class="abv-head abv-head--center">
				<span class="hv2-eyebrow" data-reveal="up">Our Successful Projects</span>
				<h2 class="hv2-display hv2-h2" data-split>Our Successful <span class="hv2-outline">Projects</span></h2>
				<p class="abv-sub" data-reveal="up" data-delay="1">Every great brand has a story. At <strong>DataMySite</strong>, we help you write the next chapter with powerful campaigns across billboards, DOOH, influencer marketing, PR coverage, lead generation, event marketing, and sponsorships. Your success story starts here.</p>
			</div>
			<div class="abv-counters">
				@foreach([[1219, 'Succesful Project'], [20, 'Team Experience'], [5, 'Winning Awards'], [81, 'Satisfied Clients']] as $k => $c)
					<div class="abv-counter" data-tilt data-tilt-max="5" data-reveal="up" data-delay="{{ $k + 1 }}">
						<strong class="hv2-display"><span data-count="{{ $c[0] }}" data-suffix="+">{{ $c[0] }}<sup>+</sup></span></strong>
						<span>{{ $c[1] }}</span>
					</div>
				@endforeach
			</div>
		</div>
	</section>

	{{-- CASE STUDIES --}}
	<section class="hv2-section hv2-dark abv-sec abv-cases" id="abv-cases">
		<span class="hv2-watermark" data-parallax="0.12" aria-hidden="true">Case Studies</span>
		<div class="abv-deco abv-deco--dark" aria-hidden="true">
			{!! $abvSheets('abv-sheets--l', '0.1') !!}{!! $abvSheets('abv-sheets--r', '-0.08') !!}
			<span class="abv-glow g1" data-abv-depth="0.08"></span><span class="abv-glow g2" data-abv-depth="-0.1"></span>
			<span class="abv-plus p1" data-abv-depth="0.18">+</span><span class="abv-plus p2" data-abv-depth="-0.14">+</span>
		</div>
		<div class="hv2-container">
			<div class="abv-head abv-head--center">
				<span class="hv2-eyebrow" data-reveal="up">Case Studies</span>
				<h2 class="hv2-display hv2-h2" data-split>Case <span class="hv2-outline">Studies</span></h2>
				<p class="abv-sub" data-reveal="up" data-delay="1">At <strong>DataMySite</strong>, we believe in transparency and trust. Sharing case studies allows us to demonstrate the tangible impact of our marketing and advertising solutions.</p>
			</div>
			<div class="abv-cs-grid" id="abv-cs-grid">
				@foreach($abvCases as $c)
					<div class="case-studies-new abv-cs-wrap{{ $loop->iteration > $abvCaseLimit ? ' abv-extra' : '' }}" data-reveal="up" data-delay="{{ ($loop->index % 4) + 1 }}">
						<a href="{{ URL::to('/public/case-studies/' . $c[0] . '.pdf') }}" target="_blank" rel="noopener" class="open-case-study abv-cs" data-tilt data-tilt-max="5">
							<span class="abv-cs-media hv2-media">
								<img src="{{ URL::to('/public/case-studies/' . $c[1] . '.jpg') }}" class="{{ $c[2] }}" width="100%" alt="Case Studies" loading="lazy">
								<span class="abv-cs-go" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
							</span>
							<h5><span>{{ $c[3] }}</span> x <span>DMS</span> |  Case Study</h5>
						</a>
					</div>
				@endforeach
			</div>
			@if(count($abvCases) > $abvCaseLimit)
				<div class="abv-more"><button type="button" class="abv-more-btn" data-abv-more="#abv-cs-grid" aria-expanded="false" aria-controls="abv-cs-grid"><span class="abv-more-t">See More</span><i class="bi bi-chevron-down" aria-hidden="true"></i></button></div>
			@endif
		</div>
	</section>

	{{-- WHY BRANDS CHOOSE --}}
	<section class="hv2-section hv2-tint abv-sec abv-why" id="abv-why">
		<div class="abv-deco" aria-hidden="true">
			{!! $abvOrbit('abv-orbit--r', '0.1') !!}
			{!! $abvMotifs !!}
		</div>
		<div class="hv2-container abv-why-grid">
			<div class="abv-why-img" data-reveal="up">
				<span class="abv-why-frame hv2-media"><img src="{{ URL::to('/public/about.jpg') }}" width="100%" alt="Why Brands Choose DMS" loading="lazy"></span>
			</div>
			<div class="abv-why-copy">
				<h3 class="hv2-display hv2-h2" data-split>Why Brands Choose <span class="hv2-outline">DataMySite</span></h3>
				<ul class="abv-ticks">
					<li data-reveal="up" data-delay="1">Multi-channel expertise in both traditional and digital media</li>
					<li data-reveal="up" data-delay="2">Access to premium media networks across the MENA region</li>
					<li data-reveal="up" data-delay="3">Strategic, creative, and executional excellence under one roof</li>
					<li data-reveal="up" data-delay="4">Proven results with clients across multiple sectors</li>
				</ul>
				<p data-reveal="up" data-delay="5">We don’t just launch campaigns — we create memorable brand experiences that move audiences and generate measurable outcomes.</p>
			</div>
		</div>
	</section>

	{{-- OUR SERVICES --}}
	<section class="hv2-section hv2-light abv-sec abv-services" id="abv-services">
		<span class="hv2-watermark" data-parallax="0.12" aria-hidden="true">Services</span>
		<div class="abv-deco" aria-hidden="true">
			{!! $abvBoard('abv-board--l', '0.1') !!}{!! $abvBoard('abv-board--r', '-0.08') !!}
			{!! $abvMotifs !!}
		</div>
		<div class="hv2-container">
			<div class="abv-head abv-head--center" data-reveal="up">
				<h3 class="hv2-display hv2-h2 text-theme" data-split>OUR <span class="hv2-outline">SERVICES</span></h3>
			</div>
			<div class="abv-svc-grid" id="abv-svc-grid">
				@foreach($abvServices as $svc)
					<article class="abv-svc{{ $loop->iteration > $abvSvcLimit ? ' abv-extra' : '' }}" data-tilt data-tilt-max="4" data-reveal="up" data-delay="{{ ($loop->index % 2) + 1 }}">
						@if(!empty($svc['url']))
							<a href="{{ v2_url($svc['url']) }}"><h4>- {{ $svc['title'] }}</h4></a>
						@else
							<h4>- {{ $svc['title'] }}</h4>
						@endif
						<p>{{ $svc['intro'] }}</p>
						@if(!empty($svc['split']))
							<div class="abv-svc-split">
								@foreach($svc['split'] as $grp)
									<div>
										<a href="{{ v2_url($grp[1]) }}"><h5>{{ $grp[0] }}</h5></a>
										<ul>@foreach($grp[2] as $li)<li>{{ $li }}</li>@endforeach</ul>
									</div>
								@endforeach
							</div>
						@else
							@if(!empty($svc['lead']))<h5>{{ $svc['lead'] }}</h5>@endif
							@if(!empty($svc['list']))<ul>@foreach($svc['list'] as $li)<li>{{ $li }}</li>@endforeach</ul>@endif
						@endif
						@if(!empty($svc['outro']))<p>{{ $svc['outro'] }}</p>@endif
					</article>
				@endforeach
			</div>
			@if(count($abvServices) > $abvSvcLimit)
				<div class="abv-more"><button type="button" class="abv-more-btn" data-abv-more="#abv-svc-grid" aria-expanded="false" aria-controls="abv-svc-grid"><span class="abv-more-t">See More</span><i class="bi bi-chevron-down" aria-hidden="true"></i></button></div>
			@endif
		</div>
	</section>

	{{-- HOW WE WORK --}}
	<section class="hv2-section hv2-dark abv-sec abv-process" id="abv-process">
		<span class="abv-process-bg" aria-hidden="true" style="background-image: url('{{ $hv2Img }}/about-b.jpg')"></span>
		<div class="abv-deco abv-deco--dark" aria-hidden="true">
			{!! $abvGears('abv-gears--l', '0.1') !!}{!! $abvGears('abv-gears--r', '-0.08') !!}
			<span class="abv-glow g1" data-abv-depth="0.08"></span><span class="abv-glow g2" data-abv-depth="-0.1"></span>
			<span class="abv-plus p1" data-abv-depth="0.18">+</span><span class="abv-plus p2" data-abv-depth="-0.14">+</span>
		</div>
		<div class="hv2-container">
			<div class="abv-head abv-head--center">
				<span class="hv2-eyebrow" data-reveal="up">HOW WE WORK</span>
				<h3 class="hv2-display hv2-h2" data-split>Our Working <span class="hv2-outline">Proccess</span></h3>
			</div>
			<div class="abv-steps">
				@foreach($abvSteps as $k => $s)
					<div class="abv-step" data-tilt data-tilt-max="5" data-reveal="up" data-delay="{{ $k + 1 }}">
						<span class="abv-step-no hv2-display" aria-hidden="true">{{ str_pad($k + 1, 2, '0', STR_PAD_LEFT) }}</span>
						<i class="bi {{ $s[0] }}" aria-hidden="true"></i>
						<p class="working-title">{{ $s[1] }}</p>
						<p>{{ $s[2] }}</p>
					</div>
				@endforeach
			</div>
			<p class="abv-call" data-reveal="up">For more information, please call <strong>(+971) 42 957 001</strong></p>
		</div>
	</section>

	{{-- PARTNER WITH US — contact details + enquiry form --}}
	<section class="hv2-section hv2-tint abv-sec abv-partner" id="abv-partner">
		<div class="abv-deco" aria-hidden="true">
			{!! $abvChain('abv-chain--l', '0.1') !!}{!! $abvChain('abv-chain--r', '-0.08') !!}
			{!! $abvMotifs !!}
		</div>
		<div class="hv2-container">
			<div class="abv-head" data-reveal="up">
				<h3 class="hv2-display hv2-h2" data-split>Partner <span class="hv2-outline">With Us</span></h3>
				<p class="abv-sub abv-sub--left">At <strong>DataMySite</strong>, we're not just another agency — we’re your <strong>marketing growth partner</strong>. Whether you're looking to launch your brand, scale your reach, or dominate your market, we’re ready to help you make an impact.<br><br>
				<strong>Let’s talk</strong> — and let’s bring your brand to life.</p>
			</div>
			<div class="abv-partner-grid">
				<div class="abv-contact">
					<span class="hv2-eyebrow" data-reveal="up">MAKE APPOINTMENT</span>
					<h3 class="abv-h3 text-bold" data-reveal="up" data-delay="1">Get Our Service Easily</h3>
					<div class="abv-contact-grid">
						<div class="abv-cc" data-tilt data-tilt-max="4" data-reveal="up" data-delay="1">
							<i class="bi bi-pin-map" aria-hidden="true"></i>
							<div><p class="working-title">Location</p><p>Office 205, Al Bayan Building, Dubai Investment Park 1, Dubai - UAE</p></div>
						</div>
						<div class="abv-cc" data-tilt data-tilt-max="4" data-reveal="up" data-delay="2">
							<i class="bi bi-envelope-at" aria-hidden="true"></i>
							<div><p class="working-title">Email</p><p><strong class="email-slot">contact@datamysite.com</strong> <button type="button" class="copy-email" data-copy="contact@datamysite.com"><span class="bi bi-copy"></span>&nbsp;&nbsp;Copy</button></p></div>
						</div>
						<div class="abv-cc" data-tilt data-tilt-max="4" data-reveal="up" data-delay="3">
							<i class="bi bi-telephone" aria-hidden="true"></i>
							<div><p class="working-title">Phone</p><p class="mb-0">TEL: <a href="tel:042957001" class="webCellPhone">042 957 001</a></p><p class="mb-0">Cell: <a href="tel:0525287259" class="webCellPhone">+971 52 528 7259</a></p></div>
						</div>
						<div class="abv-cc" data-tilt data-tilt-max="4" data-reveal="up" data-delay="4">
							<i class="bi bi-clock" aria-hidden="true"></i>
							<div><p class="working-title">Work Hours</p><p><strong>Mon - Fri | 9:30 am - 7:30 pm</strong> <br>Saturday & Sunday | Closed</p></div>
						</div>
					</div>
				</div>
				<div class="abv-form hv2-form-card hv2-glass" data-tilt data-tilt-max="3" data-reveal="scale" data-delay="2">
					<span class="hv2-glare" aria-hidden="true"></span>
					<form action="{{ route('enquiry.submit') }}" id="hv2-enquiry-form">
						@csrf
						<label class="hv2-field"><input type="text" name="name" placeholder=" " required autocomplete="name"><span>Your Name</span></label>
						<label class="hv2-field"><input type="email" name="email" id="hv2-email" placeholder=" " required autocomplete="email"><span>Your Email</span></label>
						<div class="hv2-field hv2-field--tel">
							<input type="tel" name="mainphone" id="hv2-phone" required autocomplete="tel" aria-label="Phone number">
							<input type="hidden" name="phone" id="hv2-fullphone">
						</div>
						<div class="abv-dd" data-abv-dd>
								<select name="service" aria-label="Select Service" class="abv-dd-native" tabindex="-1">
									<option value="">Select Service</option>
									@foreach($header_services as $val)
										<option value="{{ $val->name }}" data-icon="{{ $val->icon }}">{{ $val->name }}</option>
									@endforeach
									<option value="Others" data-icon="bi bi-three-dots">Others</option>
								</select>
							</div>
						<label class="hv2-field"><textarea name="description" rows="4" placeholder=" " required></textarea><span>Describe your requirement</span></label>
						<button class="hv2-btn hv2-btn--primary hv2-btn--block" type="submit"><span>Submit Your Enquiry <i class="bi bi-arrow-right"></i></span></button>
					</form>
					<div class="hv2-form-loader" aria-hidden="true"><span class="hv2-spinner"></span></div>
				</div>
			</div>
		</div>
	</section>
</main>
@endsection

@section('addScript')
	<script src="{{ URL::to('/public/assets/vendor/lenis/lenis-1.1.13.min.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/home-v2.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/about-v2.js') }}" defer></script>
@endsection
