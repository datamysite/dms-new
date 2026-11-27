@php require_once resource_path('views/web/home-v2/helpers.php'); @endphp
{{--
|==========================================================================
| HOME V2 FOOTER — standalone (does not use web.includes.footer)
| Styles: public/assets/css/home-v2-shell.css   (prefix .hs-)
| Keeps the existing hooks: #newsletterForm + .subscribe-btn (newsletter.js),
| copyEmail() (main.js), route('newsletter.subscribe').
|==========================================================================
--}}
<footer id="footer" class="hs-footer">
	<div class="hs-foot-glow" aria-hidden="true"></div>
	<span class="hs-foot-mark" aria-hidden="true">DataMySite</span>

	<div class="hs-wrap hs-foot-grid">
		<div class="hs-foot-brand">
			<a href="{{ v2_url(route('home')) }}" class="hs-foot-logo" aria-label="DataMySite — Home">
				<img src="{{ v2_asset('assets/img/logo-white.webp', false) }}" alt="DataMySite" width="150" height="88" loading="lazy">
			</a>
			<p>Smarter Marketing, Better Outcomes. Boost your brand’s visibility and campaign performance across Dubai and the UAE.</p>
			@php
				/* live Google data from WebController@indexNew (cached); the modal only exists when Google returned reviews */
				/* pages other than the home page don't pass $googleReviews: the same cached copy (fetched again here when the cache is empty) */
				$googleReviews = $googleReviews ?? \App\Services\GooglePlaces::cached();
				$hsRevs = [];
				foreach (array_values($googleReviews['reviews'] ?? []) as $i => $r) {
					$t = trim((string) ($r['text'] ?? ''));
					if ($t === '') continue;
					$hsRevs[] = ['name' => $r['author'] ?? 'Google user', 'time' => $r['time'] ?? '', 'rating' => max(1, min(5, (int) ($r['rating'] ?? 5))), 'text' => $t];
				}
				$hsScore = !empty($googleReviews['rating']) ? (float) $googleReviews['rating'] : (float) config('business.reviews.fallback_score');
				$hsTotal = !empty($googleReviews['total']) ? (int) $googleReviews['total'] : (int) config('business.reviews.fallback_total');
				$hsColors = ['#4285f4', '#ea4335', '#34a853', '#ff7043', '#7c4dff', '#00897b', '#d81b60', '#0288d1', '#f57c00'];
				$hsStar = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
				$hsSeeAll = config('business.reviews.url');   /* Maps place page opened on its Reviews tab (same link as the old home page) */
				$hsWrite = $googleWriteUrl ?? (env('GOOGLE_PLACE_ID') ? 'https://search.google.com/local/writereview?placeid=' . env('GOOGLE_PLACE_ID') : null);
			@endphp
			@if(count($hsRevs))
				<button type="button" class="hs-rating hs-rating--btn" data-hs-reviews aria-haspopup="dialog" aria-controls="hs-reviews" aria-label="Google rating {{ number_format($hsScore, 1) }} from {{ $hsTotal }} reviews — read reviews">
					<span class="hs-g" aria-hidden="true"><b class="b">G</b><b class="r">o</b><b class="y">o</b><b class="b">g</b><b class="g">l</b><b class="r">e</b></span>
					<span class="hs-stars" aria-hidden="true">★★★★★</span>
					<span class="hs-score">{{ number_format($hsScore, 1) }} <small>({{ $hsTotal }} reviews)</small></span>
					<span class="hs-rating-go" aria-hidden="true">Read reviews <i class="bi bi-arrow-up-right"></i></span>
				</button>
			@else
				<a class="hs-rating" href="{{ $hsSeeAll }}" target="_blank" rel="noopener" aria-label="Google rating {{ number_format($hsScore, 1) }} from {{ $hsTotal }} reviews">
					<span class="hs-g" aria-hidden="true"><b class="b">G</b><b class="r">o</b><b class="y">o</b><b class="b">g</b><b class="g">l</b><b class="r">e</b></span>
					<span class="hs-stars" aria-hidden="true">★★★★★</span>
					<span class="hs-score">{{ number_format($hsScore, 1) }} <small>({{ $hsTotal }} reviews)</small></span>
					<span class="hs-rating-go" aria-hidden="true">Read reviews <i class="bi bi-arrow-up-right"></i></span>
				</a>
			@endif
			<div class="hs-social">
				@foreach(v2_biz('social', []) as $hsSoc)
				<a href="{{ $hsSoc['url'] }}" aria-label="{{ $hsSoc['label'] }}" target="_blank" rel="noopener"><i class="bi {{ $hsSoc['icon'] }}"></i></a>
				@endforeach
			</div>
		</div>

		<nav class="hs-foot-col hs-foot-services" aria-label="Services">
			<div class="hs-foot-h">Services</div>
			<ul>
				{{-- only these services are listed in the footer (slug => label), in this order --}}
				@php
					$hsFootServices = ['website-development' => 'Website', 'event-marketing' => 'Events', 'sponsorships' => 'Sponsorships', 'creative-and-cgi-advertising' => 'Creatives', 'experiential-marketing' => 'Experiential', 'global-marketing' => 'Global'];
					$hsBySlug = $header_services->keyBy('slug');
				@endphp
				@foreach($hsFootServices as $slug => $label)
					@if($hsBySlug->has($slug))
						<li><a href="{{ v2_url('/'.$slug) }}">{{ $label }}</a></li>
					@endif
				@endforeach
			</ul>
		</nav>

		<nav class="hs-foot-col" aria-label="Company">
			<div class="hs-foot-h">Company</div>
			<ul>
				<li><a href="{{ v2_url(route('about')) }}">About Us</a></li>
				<li><a href="{{ v2_url(route('services')) }}">Services</a></li>
				<li><a href="{{ v2_url(route('caseStudies')) }}">Case Studies</a></li>
				<li><a href="{{ v2_url(route('blogs')) }}">Blogs</a></li>
				<li><a href="{{ v2_url(route('faq')) }}">FAQ</a></li>
				<li><a href="{{ v2_url(route('contact')) }}">Contact Us</a></li>
				<li><a href="{{ v2_url(route('privacy')) }}">Privacy Policy</a></li>
				<li><a href="{{ v2_url(route('terms')) }}">Terms &amp; Conditions</a></li>
			</ul>
		</nav>

		<div class="hs-foot-col hs-foot-touch">
			<div class="hs-foot-h">Stay in the loop</div>
			<p class="hs-foot-note">Sign up now and get the edge — marketing insights, straight to your inbox.</p>
			<form id="newsletterForm" class="hs-news" action="{{ route('newsletter.subscribe') }}">
				@csrf
				<input type="email" name="email" placeholder="Your email address" aria-label="Your email address" required>
				<button class="subscribe-btn" type="submit">Subscribe</button>
			</form>
			<ul class="hs-contact">
				<li><i class="bi bi-telephone" aria-hidden="true"></i><a href="tel:{{ v2_biz('phone') }}">{{ v2_biz('phone_display') }}</a></li>
				<li class="hs-mail">
					<i class="bi bi-envelope-at" aria-hidden="true"></i>
					<a href="mailto:{{ v2_biz('email') }}" class="hs-mail-addr">{{ v2_biz('email') }}</a>
					<button type="button" class="hs-copy" data-hs-copy="{{ v2_biz('email') }}" aria-label="Copy email address">
						<i class="bi bi-copy" aria-hidden="true"></i><span>Copy</span>
					</button>
					<span class="hs-copy-live" role="status" aria-live="polite"></span>
				</li>
				<li><i class="bi bi-pin-map" aria-hidden="true"></i><span>{{ v2_biz('address_line') }}</span></li>
			</ul>
		</div>
	</div>

	<div class="hs-foot-bar">
		<div class="hs-wrap">
			<p>© {{ date('Y') }} <strong>DataMySite</strong>. All rights reserved.</p>
			<p>Powered by <a href="https://daralafkarmarketing.ae/" target="_blank" rel="noopener"><strong>Dar Alafkar Marketing LLC.</strong></a></p>
		</div>
	</div>
</footer>

{{-- ====================================================================
     GOOGLE REVIEWS MODAL — opens from the footer rating chip (same idea as the old home page's review panel).
     Content = live reviews fetched server-side (cached). Slider is initialised on first open (home-v2-shell.js).
===================================================================== --}}
@if(count($hsRevs))
<div class="hs-rv" id="hs-reviews" role="dialog" aria-modal="true" aria-labelledby="hs-rv-title" aria-hidden="true" data-lenis-prevent>
	<div class="hs-rv-scrim" data-hs-rv-close></div>
	<div class="hs-rv-panel">
		<button type="button" class="hs-rv-x" data-hs-rv-close aria-label="Close reviews"><i class="bi bi-x-lg" aria-hidden="true"></i></button>

		<header class="hs-rv-head">
			<div>
				<span class="hs-rv-kicker">Google Reviews</span>
				<h2 id="hs-rv-title">What our customers say</h2>
			</div>
			<div class="hs-rv-score">
				<span class="hs-g" aria-hidden="true"><b class="b">G</b><b class="r">o</b><b class="y">o</b><b class="b">g</b><b class="g">l</b><b class="r">e</b></span>
				<strong>{{ number_format($hsScore, 1) }}</strong>
				<span class="hs-rv-meta"><span class="hs-rv-stars">{!! str_repeat($hsStar, max(1, (int) round($hsScore))) !!}</span><small>{{ $hsTotal }} reviews</small></span>
			</div>
		</header>

		<div class="hs-rv-swiper" data-autoplay="1500">
			<div class="hs-rv-track">
				@foreach($hsRevs as $rv)
					<div class="hs-rv-slide">
						<article class="hs-rv-card">
							<header>
								<span class="hs-rv-avatar" style="background: {{ $hsColors[$loop->index % count($hsColors)] }}" aria-hidden="true">{{ mb_strtoupper(mb_substr($rv['name'], 0, 1)) }}</span>
								<span class="hs-rv-who"><strong>{{ $rv['name'] }}</strong><small>{{ $rv['time'] }}</small></span>
								<svg class="hs-rv-gmark" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
							</header>
							<div class="hs-rv-stars" role="img" aria-label="{{ $rv['rating'] }} out of 5 stars">{!! str_repeat($hsStar, $rv['rating']) !!}</div>
							<p>{{ $rv['text'] }}</p>
						</article>
					</div>
				@endforeach
			</div>
		</div>

		<footer class="hs-rv-foot">
			<div class="hs-rv-ctrl">
				<button type="button" class="hs-rv-nav hs-rv-prev" aria-label="Previous reviews"><i class="bi bi-arrow-left" aria-hidden="true"></i></button>
				<button type="button" class="hs-rv-nav hs-rv-next" aria-label="Next reviews"><i class="bi bi-arrow-right" aria-hidden="true"></i></button>
				<button type="button" class="hs-rv-nav hs-rv-toggle" aria-label="Pause auto-play" aria-pressed="false"><i class="bi bi-pause-fill" aria-hidden="true"></i></button>
			</div>
			<div class="hs-rv-actions">
				<a href="{{ $hsSeeAll }}" target="_blank" rel="noopener" class="hs-rv-btn hs-rv-btn--primary">See all reviews <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a>
				@if($hsWrite)<a href="{{ $hsWrite }}" target="_blank" rel="noopener" class="hs-rv-btn hs-rv-btn--ghost">Write a review <i class="bi bi-pencil-square" aria-hidden="true"></i></a>@endif
			</div>
		</footer>
	</div>
</div>
@endif
