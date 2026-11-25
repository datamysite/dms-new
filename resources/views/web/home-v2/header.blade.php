{{--
|==========================================================================
| HOME V2 HEADER — standalone (does not use web.includes.header / header.css)
| Styles: public/assets/css/home-v2-shell.css   (prefix .hs-)
| Script: public/assets/js/home-v2-shell.js
| Data  : $header_services (ViewServiceProvider) · optional $nav for the active link
|==========================================================================
--}}
@php
	require_once resource_path('views/web/home-v2/helpers.php');
	$hsNav = $nav ?? '';
	$hsLinks = [
		['home', 'Home', v2_url(route('home'))],
		['case-studies', 'Case Studies', v2_url(route('caseStudies'))],
		['blogs', 'Blogs', v2_url(route('blogs'))],
		['about', 'About Us', v2_url(route('about'))],
		['contact', 'Contact Us', v2_url(route('contact'))],
	];
@endphp
<header id="header" class="hs-header" data-hs-header>
	{{-- compat: the shared public/assets/js/main.js binds a click handler to ".mobile-nav-toggle" and throws if it is missing --}}
	<i class="mobile-nav-toggle" hidden aria-hidden="true"></i>
	<div class="hs-bar">
		<div class="hs-wrap">
			<a href="{{ v2_url(route('home')) }}" class="hs-logo" aria-label="DataMySite — Home">
				<img src="{{ v2_asset('assets/img/logo-white.webp', false) }}" alt="DataMySite" width="150" height="88">
			</a>

			<nav class="hs-nav" aria-label="Primary">
				<ul class="hs-nav-list">
					<li><a href="{{ $hsLinks[0][2] }}" class="hs-link {{ $hsNav === 'home' ? 'is-active' : '' }}">{{ $hsLinks[0][1] }}</a></li>

					<li><a href="{{ v2_url(route('services')) }}" class="hs-link {{ $hsNav === 'services' ? 'is-active' : '' }}">Services</a></li>

					@foreach(array_slice($hsLinks, 1) as $l)
						<li><a href="{{ $l[2] }}" class="hs-link {{ $hsNav === $l[0] ? 'is-active' : '' }}">{{ $l[1] }}</a></li>
					@endforeach
				</ul>
			</nav>

			<div class="hs-actions">
				<a href="tel:{{ v2_biz('phone') }}" class="hs-phone" aria-label="Call {{ v2_biz('phone_display') }}"><i class="bi bi-telephone" aria-hidden="true"></i><span>{{ v2_biz('phone_display') }}</span></a>
				<button type="button" class="open-newsletter hs-cta">Book Now <i class="bi bi-arrow-up-right" aria-hidden="true"></i></button>
				<button type="button" class="hs-burger" aria-label="Open menu" aria-expanded="false" aria-controls="hs-drawer"><span></span><span></span><span></span></button>
			</div>
		</div>
	</div>

	{{-- desktop service bar: 10 short names (the `sudo` column), each opens its sub-services; the other 6 services are in the footer --}}
	@php
		$hsBarSlugs = ['digital-marketing', 'outdoor-advertising', 'digital-advertising', 'influencer-marketing', 'seo-services', 'transit-media', 'celebrity-marketing', 'public-relations-pr-coverage', 'lead-generation', 'radio-advertising'];
		$hsSvcBy = $header_services->keyBy('slug');
		$hsBar = collect($hsBarSlugs)->map(fn ($sl) => $hsSvcBy->get($sl))->filter()->values();
	@endphp
	<div class="hs-subbar" data-hs-svcbar>
		<div class="hs-wrap">
			<ul class="hs-svc-list" role="list" aria-label="Our services">
				@foreach($hsBar as $val)
					@php $hsLabel = trim($val->sudo ?? '') !== '' ? trim($val->sudo) : $val->name; $hsSubs = $val->subServices; @endphp
					<li class="hs-svc {{ $loop->index >= 6 ? 'hs-svc--end' : '' }}" data-hs-svc>
						<a href="{{ v2_url('/'.$val->slug) }}" class="hs-svc-link" @if(count($hsSubs)) aria-haspopup="true" aria-expanded="false" @endif>{{ $hsLabel }}@if(count($hsSubs)) <i class="bi bi-chevron-down" aria-hidden="true"></i>@endif</a>
						@if(count($hsSubs))
							<div class="hs-svc-drop {{ count($hsSubs) > 6 ? 'is-wide' : '' }}">
								<a href="{{ v2_url('/'.$val->slug) }}" class="hs-svc-head"><strong>{{ $val->name }}</strong><span>View all <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span></a>
								<ul>
									@foreach($hsSubs as $sub)
										<li><a href="{{ v2_url('/'.$val->slug.'/'.$sub->slug) }}"><i class="bi bi-chevron-right" aria-hidden="true"></i>{{ $sub->name }}</a></li>
									@endforeach
								</ul>
							</div>
						@endif
					</li>
				@endforeach
			</ul>
		</div>
	</div>
	{{-- mobile / tablet drawer --}}
	<div class="hs-scrim" data-hs-close></div>
	<aside class="hs-drawer" id="hs-drawer" aria-label="Menu" aria-hidden="true">
		<div class="hs-drawer-top">
			<img src="{{ v2_asset('assets/img/logo-white.webp', false) }}" alt="DataMySite" width="150" height="88">
			<button type="button" class="hs-drawer-x" data-hs-close aria-label="Close menu"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
		</div>
		<nav class="hs-drawer-nav" aria-label="Mobile">
			<a href="{{ $hsLinks[0][2] }}" class="hs-d-link">{{ $hsLinks[0][1] }}</a>
			<details class="hs-d-acc">
				<summary class="hs-d-link">Services <i class="bi bi-plus-lg" aria-hidden="true"></i></summary>
				<div class="hs-d-sub">
					<a href="{{ v2_url(route('services')) }}" class="hs-d-one"><strong>All Services</strong></a>
					@foreach($header_services as $val)
						@if(count($val->subServices))
							<details class="hs-d-acc hs-d-acc--in">
								<summary>{{ $val->name }} <i class="bi bi-chevron-down" aria-hidden="true"></i></summary>
								<ul>
									<li><a href="{{ v2_url('/'.$val->slug) }}"><strong>All {{ $val->name }}</strong></a></li>
									@foreach($val->subServices as $sub)
										<li><a href="{{ v2_url('/'.$val->slug.'/'.$sub->slug) }}">{{ $sub->name }}</a></li>
									@endforeach
								</ul>
							</details>
						@else
							<a href="{{ v2_url('/'.$val->slug) }}" class="hs-d-one">{{ $val->name }}</a>
						@endif
					@endforeach
				</div>
			</details>
			@foreach(array_slice($hsLinks, 1) as $l)
				<a href="{{ $l[2] }}" class="hs-d-link">{{ $l[1] }}</a>
			@endforeach
		</nav>
		<div class="hs-drawer-foot">
			<button type="button" class="open-newsletter hs-cta hs-cta--block">Book a Free Strategy Call <i class="bi bi-arrow-up-right" aria-hidden="true"></i></button>
			<a href="tel:{{ v2_biz('phone') }}" class="hs-d-phone"><i class="bi bi-telephone" aria-hidden="true"></i> {{ v2_biz('phone_display') }}</a>
		</div>
	</aside>
</header>
