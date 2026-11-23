{{--
|==========================================================================
| PRIVACY V2 — redesigned Privacy Policy (independent copy of web.privacy-policy)
|--------------------------------------------------------------------------
| Self-contained page: this view + public/assets/css/privacy-v2.css + public/assets/js/privacy-v2.js
| Uses the shared V2 shell (web.home-v2.master) and the Home V2 design system (home-v2.css / home-v2.js).
| Does not include or depend on the live view or any other V2 page layer. Same text as the live page.
| Custom 3D decoration for this page only.
|==========================================================================
--}}
@extends('web.home-v2.master')
@php require_once resource_path('views/web/home-v2/helpers.php'); @endphp

@section('v2lite', '1')

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
	<link href="{{ v2_asset('assets/css/privacy-v2.css') }}" rel="stylesheet">
@endsection

@php
	$hv2Img = URL::to('/public/assets/img');
	$plvPhoto = is_file(public_path('assets/img/title/faqs.png')) ? $hv2Img . '/title/faqs.png' : null;
@endphp

@section('content')
<main class="main hv2 plv" id="hv2">
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

	<section class="hv2-hero hv2-dark plv-hero" id="hv2-hero">
		<div class="hv2-hero-bg" aria-hidden="true">
			@if($plvPhoto)<span class="plv-hero-photo" style="background-image: url('{{ $plvPhoto }}')"></span>@endif
			<span class="hv2-blob hv2-blob--1"></span><span class="hv2-blob hv2-blob--2"></span><span class="hv2-blob hv2-blob--3"></span>
			<span class="hv2-grid-lines"></span>
		</div>
		<div class="hv2-streaks" aria-hidden="true">
			@for($s = 0; $s < 8; $s++)<i style="top: {{ 10 + $s * 11 }}%; --t: {{ 2.8 + ($s % 4) * .7 }}s; --dl: {{ $s * .5 }}s"></i>@endfor
		</div>
		<div class="plv-deco plv-deco--dark" aria-hidden="true"><div class="plv-hs plv-hs--n1" data-plv-depth="0.06"><div class="plv-hp"><div class="plv-fp"><svg viewBox="0 0 120 140" width="120" height="140" aria-hidden="true"><g fill="none" stroke="#f44d46" stroke-width="5" stroke-linecap="round"><path d="M60,18 C34,18 20,40 20,66"/><path d="M60,34 C44,34 36,50 36,68 C36,90 30,104 24,116"/><path d="M60,50 C54,50 52,58 52,70 C52,92 48,108 40,124"/><path d="M76,34 C92,38 100,54 100,72 C100,92 96,106 90,120"/><path d="M68,58 C76,60 82,68 82,80 C82,96 78,110 72,126"/></g></svg><span class="plv-fp-scan"></span></div></div></div><div class="plv-stage plv-stage--h1" data-plv-depth="0.05"><div class="plv-shield plv-p"><span class="plv-sh"><b>✓</b></span></div></div><div class="plv-stage plv-stage--h2" data-plv-depth="-0.08"><div class="plv-lock plv-p"><span class="plv-shackle"></span><span class="plv-lbody"><i></i></span></div></div><div class="plv-stage plv-stage--h3" data-plv-depth="0.1"><div class="plv-key plv-p"><span class="plv-kring"></span><span class="plv-kshaft"></span></div></div></div>
		<div class="hv2-container hv2-hero-inner">
			<div class="hv2-hero-copy">
				@include('web.home-v2.breadcrumbs', ['crumbs' => [['Home', URL::to('/')], ['Privacy Policy', null]]])
				<div class="plv-hero-body"><h1 data-reveal="up" data-delay="1">Privacy Policy</h1></div>
			</div>
		</div>
		<a href="#plv-main" class="hv2-scroll-cue" data-scrollto="#plv-main" aria-label="Scroll to content"></a>
	</section>
	<span class="js-count-particles" hidden></span>

	<section class="hv2-section hv2-tint plv-sec plv-main" id="plv-main">
		<div class="plv-deco" aria-hidden="true"><div class="plv-stage plv-stage--m1" data-plv-depth="0.1"><div class="plv-lock plv-p"><span class="plv-shackle"></span><span class="plv-lbody"><i></i></span></div></div><div class="plv-stage plv-stage--m2" data-plv-depth="-0.08"><div class="plv-shield plv-p"><span class="plv-sh"><b>✓</b></span></div></div><div class="plv-stage plv-stage--m3" data-plv-depth="0.06"><div class="plv-key plv-p"><span class="plv-kring"></span><span class="plv-kshaft"></span></div></div><span class="plv-dots" data-plv-depth="-0.06"></span><span class="plv-plus p1" data-plv-depth="0.18">+</span><span class="plv-plus p2" data-plv-depth="-0.14">+</span><span class="plv-glow g1" data-plv-depth="0.08"></span><span class="plv-glow g2" data-plv-depth="-0.1"></span></div>
		<div class="hv2-container plv-grid">
			<aside class="plv-toc" id="plv-toc" aria-label="Contents" hidden><p class="plv-toc-label">Contents</p><ol></ol></aside>
			<article class="plv-article" id="plv-article" data-reveal="up">
                    <p>
                        Welcome to <strong>DMS(DataMySite)</strong> Your privacy is critically important to us. <strong>DMS(DataMySite)</strong>, Dubai, UAE is committed to protecting your personal information and your right to privacy. If you have any questions or concerns about our policy or our practices with regards to your personal information, please contact us at <a href="mailto:{{ v2_biz('email') }}">{{ v2_biz('email') }}</a>. By using our website, you’re agreeing to be bound by this Policy. If you do not agree with the terms of this privacy policy, please do not access the site.
                    </p>
                    <h2>1. Information We Collect</h2>
                    
                    <p>We collect personal information that you voluntarily provide to us when registering at the Site, expressing an interest in obtaining information about us or our products and services, participating in activities on the Site or otherwise contacting us. The personal information that we collect depends on the context of your interactions with us and the Site, the choices you make and the products and features you use.</p>
                    <h2>2. How We Use Your Information</h2>

                    <p>We use personal information collected via our Site for a variety of business purposes described below:
                    To provide, operate and maintain our website
                    To improve, personalize and expand our website
                    To understand and analyze how you use our website
                    To develop new products, services, features and functionality
                    For customer support and to respond to your inquiries
                    For compliance purposes, including enforcing our Terms of Service or other legal rights</p>
                    <h2>3. Sharing Your Information</h2>

                    <p>We may share or transfer your information in specific situations:
                    With your consent.
                    For business transfers.
                    With service providers who perform services for us.
                    For legal reasons, including to meet government regulations or law enforcement requests.</p>
                    <h2>4. Third-Party Services</h2>

                    <p>Our Site may contain links to third-party websites or services that are not owned or controlled by <strong>DMS(DataMySite)</strong>. We are not responsible for the privacy practices of these third-party entities.</p>
                    <h2>5. Security</h2>

                    <p>We strive to protect your personal information through a system of organizational and technical security measures.</p>
                    <h2>6. International Transfers</h2>

                    <p>Your information, including personal data, may be transferred to — and maintained on — computers located outside of your state, province, country, or other governmental jurisdiction where the data protection laws may differ from those of your jurisdiction.</p>
                    <h2>7. Your Rights</h2>

                    <p>You have certain rights regarding the information we hold about you, including the right to access, correct, or delete the personal information we have about you.</p>
                    <h2>8. Children’s Privacy</h2>

                    <p>Our Site does not address anyone under the age of 13. We do not knowingly collect personally identifiable information from children under 13.</p>
                    <h2>9. Changes to This Policy</h2>

                    <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                    <h2>10. Contact US</h2>

                    <p>If you have any questions or suggestions about our Privacy Policy, do not hesitate to contact us at <a href="mailto:{{ v2_biz('email') }}">{{ v2_biz('email') }}</a></p>

                </article>
		</div>
	</section>
</main>
@endsection

@section('addScript')
	<script src="{{ v2_asset('assets/js/home-v2.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/privacy-v2.js') }}" defer></script>
@endsection
