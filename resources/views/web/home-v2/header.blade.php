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

					<li class="hs-has-mega" data-hs-mega>
						<button type="button" class="hs-link hs-link--btn {{ $hsNav === 'services' ? 'is-active' : '' }}" data-hs-href="{{ v2_url(route('services')) }}" aria-expanded="false" aria-controls="hs-mega" aria-haspopup="true">
							Services <i class="bi bi-chevron-down" aria-hidden="true"></i>
						</button>

						<div class="hs-mega" id="hs-mega" role="region" aria-label="Services">
							<div class="hs-mega-inner">
								<ul class="hs-mega-tabs" role="list">
									@foreach($header_services as $val)
										<li>
											<a href="{{ v2_url('/'.$val->slug) }}" class="hs-mega-tab {{ $loop->first ? 'is-on' : '' }}" data-hs-tab="hs-pane-{{ $val->id }}">
												<span class="hs-mega-ico"><i class="{{ $val->icon }}" aria-hidden="true"></i></span>
												<span class="hs-mega-name">{{ $val->name }}</span>
												<i class="bi bi-arrow-right hs-mega-arrow" aria-hidden="true"></i>
											</a>
										</li>
									@endforeach
								</ul>

								<div class="hs-mega-panes">
									@foreach($header_services as $val)
										<div class="hs-pane {{ $loop->first ? 'is-on' : '' }}" id="hs-pane-{{ $val->id }}">
											<a href="{{ v2_url('/'.$val->slug) }}" class="hs-pane-head">
												<strong>{{ $val->name }}</strong>
												<span>{{ \Illuminate\Support\Str::limit($val->description, 130) }}</span>
											</a>
											@if(count($val->subServices))
												<ul class="hs-pane-grid">
													@foreach($val->subServices as $sub)
														<li><a href="{{ v2_url('/'.$val->slug.'/'.$sub->slug) }}"><i class="bi bi-chevron-right" aria-hidden="true"></i>{{ $sub->name }}</a></li>
													@endforeach
												</ul>
											@endif
											<a href="{{ v2_url('/'.$val->slug) }}" class="hs-pane-all">Explore {{ $val->name }} <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
										</div>
									@endforeach
								</div>
							</div>
						</div>
					</li>

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
