{{--
|==========================================================================
| CONTACT V2 — redesigned /contact-us (independent copy of web.contact)
|--------------------------------------------------------------------------
| Self-contained page: this view + public/assets/css/contact-v2.css
|                                + public/assets/js/contact-v2.js
| Uses the shared V2 shell (web.home-v2.master) and the Home V2 design system
| (home-v2.css / home-v2.js). Does not include or depend on the live contact view or any other V2 page layer.
| Same text + images + map + form as web.contact. The 3D decoration is custom to this page
| (envelope, paper plane, chat bubbles, signal rings, map pin, brochure).
| Preview: /contact-us-new   Switch: WebController@contact -> view('web.contact-v2')
|==========================================================================
--}}
@extends('web.home-v2.master')
@php require_once resource_path('views/web/home-v2/helpers.php'); @endphp

@section('v2lite', '1')
@section('v2crit', 'assets/css/contact.critical.css')
@section('v2form', '1')

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
	<link href="{{ v2_asset('assets/css/contact-v2.css') }}" rel="stylesheet">
@endsection

@php
	$hv2Img = URL::to('/public/assets/img');
	$ctvPhoto = is_file(public_path('assets/img/title/contact.jpg')) ? $hv2Img . '/title/contact.jpg' : null;
	$ctvEnvelope = function ($cls, $depth) { return '<div class="ctv-envelope ' . $cls . '" data-ctv-depth="' . $depth . '"><span class="ctv-letter"><i></i><i></i><i></i></span><span class="ctv-env-body"></span><span class="ctv-flap"></span></div>'; };
	$ctvPlane = function ($cls, $depth) { return '<div class="ctv-plane ' . $cls . '" data-ctv-depth="' . $depth . '"><span class="ctv-trail"></span><span class="ctv-paper"></span></div>'; };
	$ctvBubbles = function ($cls, $depth) { return '<div class="ctv-bubbles ' . $cls . '" data-ctv-depth="' . $depth . '"><span class="ctv-bubble b1"><i></i><i></i><i></i></span><span class="ctv-bubble b2"><i></i><i></i></span></div>'; };
	$ctvSignal = function ($cls, $depth) { return '<div class="ctv-signal ' . $cls . '" data-ctv-depth="' . $depth . '"><span class="ctv-wave w1"></span><span class="ctv-wave w2"></span><span class="ctv-wave w3"></span><span class="ctv-phone"><i class="bi bi-telephone-fill"></i></span></div>'; };
	$ctvPin = function ($cls, $depth) { return '<div class="ctv-pin ' . $cls . '" data-ctv-depth="' . $depth . '"><span class="ctv-pin-head"></span><span class="ctv-ripple"></span><span class="ctv-ripple r2"></span></div>'; };
	$ctvBook = function ($cls, $depth) { return '<div class="ctv-book ' . $cls . '" data-ctv-depth="' . $depth . '"><span class="ctv-page p1"></span><span class="ctv-page p2"></span><span class="ctv-cover"><b>DMS</b></span></div>'; };
	$ctvAt = function ($cls, $depth) { return '<div class="ctv-at ' . $cls . '" data-ctv-depth="' . $depth . '"><b>@</b></div>'; };
	$ctvMotifs = '<span class="ctv-dots" data-ctv-depth="-0.06"></span><span class="ctv-plus p1" data-ctv-depth="0.18">+</span><span class="ctv-plus p2" data-ctv-depth="-0.14">+</span><span class="ctv-glow g1" data-ctv-depth="0.08"></span><span class="ctv-glow g2" data-ctv-depth="-0.1"></span>';
@endphp

@section('content')
<main class="main hv2 ctv" id="hv2">

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

	{{-- HERO — breadcrumb + title; signal rings, chat bubbles and a letter in flight --}}
	<section class="hv2-hero hv2-dark ctv-hero" id="hv2-hero">
		<div class="hv2-hero-bg" aria-hidden="true">
			@if($ctvPhoto)<span class="ctv-hero-photo" style="background-image: url('{{ $ctvPhoto }}')"></span>@endif
			<span class="hv2-blob hv2-blob--1"></span><span class="hv2-blob hv2-blob--2"></span><span class="hv2-blob hv2-blob--3"></span>
			<span class="hv2-grid-lines"></span>
		</div>
		<div class="hv2-streaks" aria-hidden="true">
			@for($s = 0; $s < 8; $s++)<i style="top: {{ 10 + $s * 11 }}%; --t: {{ 2.8 + ($s % 4) * .7 }}s; --dl: {{ $s * .5 }}s"></i>@endfor
		</div>
		<div class="ctv-deco ctv-deco--dark" aria-hidden="true"><div class="ctv-hs ctv-hs--n1" data-ctv-depth="0.06"><div class="ctv-hp"><div class="ctv-hs2"><span class="ctv-hs-band"></span><span class="ctv-hs-cup l"></span><span class="ctv-hs-cup r"></span><span class="ctv-hs-mic"></span></div></div></div><div class="ctv-hs ctv-hs--n2" data-ctv-depth="-0.08"><div class="ctv-hp"><div class="ctv-clk"><span class="ctv-ck-h"></span><span class="ctv-ck-m"></span><span class="ctv-ck-dot"></span></div></div></div>
			{!! $ctvSignal('ctv-signal--hero', '0.05') !!}
			{!! $ctvBubbles('ctv-bubbles--hero', '-0.08') !!}
			{!! $ctvPlane('ctv-plane--hero', '0.1') !!}
		</div>
		<div class="hv2-container hv2-hero-inner">
			<div class="hv2-hero-copy">
				@include('web.home-v2.breadcrumbs', ['crumbs' => [['Home', URL::to('/')], ['Contact Us', null]]])
				<div class="ctv-hero-body">
					<h1 data-reveal="up" data-delay="1">Contact Us</h1>
				</div>
			</div>
		</div>
		<a href="#ctv-touch" class="hv2-scroll-cue" data-scrollto="#ctv-touch" aria-label="Scroll to content"></a>
	</section>
	<span class="js-count-particles" hidden></span>

	{{-- LET'S GET IN TOUCH — details + enquiry form --}}
	<section class="hv2-section hv2-tint ctv-sec ctv-touch" id="ctv-touch">
		<div class="ctv-deco" aria-hidden="true">
			{!! $ctvEnvelope('ctv-envelope--r', '0.1') !!}
			{!! $ctvAt('ctv-at--l', '-0.08') !!}
			{!! $ctvBubbles('ctv-bubbles--l', '0.1') !!}
			{!! $ctvMotifs !!}
		</div>
		<div class="hv2-container ctv-grid">
			<div class="ctv-info">
				<span class="hv2-eyebrow" data-reveal="up">CONTACT US</span>
				<h2 class="hv2-display hv2-h2 text-bold" data-split>Let's Get <span class="hv2-outline">In Touch</span></h2>
				<p class="ctv-lead" data-reveal="up" data-delay="1">We’d love to hear from you! Whether you have a question, need a quote, or want to discuss how we can help your business, feel free to reach out. Contact us today and let’s start a conversation.</p>
				<div class="ctv-cards">
					@include('web.home-v2.contact-cards', ['cardClass' => 'ctv-card'])
				</div>
			</div>
			<div class="ctv-form hv2-form-card hv2-glass" data-tilt data-tilt-max="3" data-reveal="scale" data-delay="2">
				<span class="hv2-glare" aria-hidden="true"></span>
				<form action="{{ route('enquiry.submit') }}" id="hv2-enquiry-form">
					@csrf
					<label class="hv2-field"><input type="text" name="name" placeholder=" " required autocomplete="name"><span>Your Name</span></label>
					<label class="hv2-field"><input type="email" name="email" id="hv2-email" placeholder=" " required autocomplete="email"><span>Your Email</span></label>
					<div class="hv2-field hv2-field--tel">
						<input type="tel" name="mainphone" id="hv2-phone" required autocomplete="tel" placeholder="Your phone number" aria-label="Phone number">
						<input type="hidden" name="phone" id="hv2-fullphone">
					</div>
					<div class="ctv-dd" data-ctv-dd>
						<select name="service" aria-label="Select Service" class="ctv-dd-native" tabindex="-1">
							<option value="">Select Service</option>
							@foreach($header_services as $val)
								<option value="{{ $val->name }}" data-icon="{{ $val->icon }}">{{ $val->name }}</option>
							@endforeach
							<option value="Others" data-icon="bi bi-three-dots">Others</option>
						</select>
					</div>
					<label class="hv2-field"><textarea name="description" rows="5" placeholder=" " required></textarea><span>Describe your requirement</span></label>
					<button class="hv2-btn hv2-btn--primary hv2-btn--block" type="submit"><span>Submit Your Enquiry <i class="bi bi-arrow-right"></i></span></button>
				</form>
				<div class="hv2-form-loader" aria-hidden="true"><span class="hv2-spinner"></span></div>
			</div>
		</div>
	</section>

	{{-- MAP — the live page's Google map in a tilted 3D frame --}}
	<section class="hv2-section hv2-dark ctv-sec ctv-map" id="ctv-map">
		<span class="hv2-watermark" data-parallax="0.12" aria-hidden="true">Dubai</span>
		<div class="ctv-deco ctv-deco--dark" aria-hidden="true">
			{!! $ctvPin('ctv-pin--a', '0.1') !!}{!! $ctvPin('ctv-pin--b', '-0.08') !!}
			<span class="ctv-grid-floor"></span>
			<span class="ctv-glow g1" data-ctv-depth="0.08"></span><span class="ctv-glow g2" data-ctv-depth="-0.1"></span>
			<span class="ctv-plus p1" data-ctv-depth="0.18">+</span><span class="ctv-plus p2" data-ctv-depth="-0.14">+</span>
		</div>
		<div class="hv2-container">
			<div class="ctv-map-frame" data-tilt data-tilt-max="2" data-reveal="up">
				<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14464.219277779735!2d55.16920962642821!3d24.99825314644458!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f7347c3a1a1d1%3A0xad7a49fd99d3061!2sDataMySite!5e0!3m2!1sen!2sae!4v1736759203303!5m2!1sen!2sae" width="100%" height="550" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="DataMySite on Google Maps"></iframe>
			</div>
		</div>
	</section>

	{{-- COMPANY PROFILE --}}
	<section class="hv2-section hv2-green ctv-sec ctv-profile" id="call-to-action">
		<span class="ctv-profile-bg" aria-hidden="true" style="background-image: url('{{ $hv2Img }}/background/call-to.webp')"></span>
		<div class="ctv-deco ctv-deco--dark" aria-hidden="true">
			{!! $ctvBook('ctv-book--l', '0.1') !!}{!! $ctvBook('ctv-book--r', '-0.08') !!}
			<span class="ctv-glow g1" data-ctv-depth="0.08"></span><span class="ctv-glow g2" data-ctv-depth="-0.1"></span>
			<span class="ctv-plus p1" data-ctv-depth="0.18">+</span><span class="ctv-plus p2" data-ctv-depth="-0.14">+</span>
		</div>
		<div class="hv2-container">
			<div class="ctv-profile-copy">
				<h2 class="hv2-display hv2-h2 text-theme" data-split>COMPANY <span class="hv2-outline">PROFILE</span></h2>
				<p class="ctv-profile-p" data-reveal="up" data-delay="1">A full overview of the services we provide, tailored for partners and clients.</p>
				<a class="hv2-btn hv2-btn--primary cta-btn" href="{{ url('/profile') }}/" data-magnetic data-reveal="up" data-delay="2"><span>View Profile → </span></a>
			</div>
		</div>
	</section>
</main>
@endsection

@section('addScript')
	<script src="{{ v2_asset('assets/js/home-v2.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/contact-v2.js') }}" defer></script>
@endsection
