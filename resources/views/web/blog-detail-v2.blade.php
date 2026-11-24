{{--
|==========================================================================
| BLOG POST V2 — redesigned blog details (independent copy of web.blogs.details)
|--------------------------------------------------------------------------
| Self-contained page: this view + public/assets/css/blog-detail-v2.css
|                                + public/assets/js/blog-detail-v2.js
| Uses the shared V2 shell (web.home-v2.master) and the Home V2 design system
| (home-v2.css / home-v2.js). Does not include or depend on the live details view or any other V2 page layer.
| Same text + images + schema as web.blogs.details.
| Preview: /blogs-new/post/<slug>   Switch: BlogController@details -> view('web.blog-detail-v2')
|==========================================================================
--}}
@extends('web.home-v2.master')
@php require_once resource_path('views/web/home-v2/helpers.php'); @endphp

@section('v2lite', '1')
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
	<link href="{{ v2_asset('assets/css/blog-detail-v2.css') }}" rel="stylesheet">
@endsection

@php
	$bdvBanner = empty($data->banner) ? URL::to('/public/og-logo.jpeg') : URL::to('public/storage/blogs/' . $data->banner);
	$bdvPieces = [
		'r' => '<div class="bdv-book"><span class="bdv-page p1"></span><span class="bdv-page p2"></span><span class="bdv-bkc"><b>DMS</b></span></div>',
		'l' => '<div class="bdv-bubbles"><span class="bdv-bubble b1"><i></i><i></i><i></i></span><span class="bdv-bubble b2"><i></i><i></i></span></div>',
		'a' => '<div class="bdv-bubbles"><span class="bdv-bubble b1"><i></i><i></i><i></i></span><span class="bdv-bubble b2"><i></i><i></i></span></div>',
		'b' => '<div class="bdv-sheets"><span class="bdv-sheet s1"></span><span class="bdv-sheet s2"></span><span class="bdv-sheet s3"><em>PDF</em></span></div>',
	];
	$bdvStage = function ($mod, $depth) use ($bdvPieces) {
		return '<div class="bdv-stage bdv-stage--' . $mod . '" data-bdv-depth="' . $depth . '"><div class="bdv-pc">' . ($bdvPieces[$mod] ?? reset($bdvPieces)) . '</div></div>';
	};
	$bdvMotifs = '<span class="bdv-dots" data-bdv-depth="-0.06"></span><span class="bdv-plus p1" data-bdv-depth="0.18">+</span><span class="bdv-plus p2" data-bdv-depth="-0.14">+</span><span class="bdv-plus p3" data-bdv-depth="0.12">+</span><span class="bdv-tri t1" data-bdv-depth="-0.2"></span><span class="bdv-tri t2" data-bdv-depth="0.16"></span><span class="bdv-glow g1" data-bdv-depth="0.08"></span><span class="bdv-glow g2" data-bdv-depth="-0.1"></span><span class="bdv-bar b1" data-bdv-depth="0.2"></span><span class="bdv-bar b2" data-bdv-depth="-0.18"></span>';

	/* related posts: same category, nearest by id, topped up with the newest (same logic as the live page) */
	try {
		$relatedCat = \App\Models\Blogs::where('status', 1)
			->where('id', '!=', $data->id)
			->where('category_id', $data->category_id)
			->orderByRaw('ABS(CAST(id AS SIGNED) - ?)', [(int) $data->id])
			->limit(3)->get();
		if ($relatedCat->count() < 3) {
			$relatedCat = $relatedCat->concat($related->whereNotIn('id', $relatedCat->pluck('id')->push($data->id)))->take(3);
		}
		$related = $relatedCat;
	} catch (\Throwable $e) { /* keep the controller's list */ }
@endphp

@section('content')
    <script>
      window.gtmCustomPage = "blog-page";
    </script>
<main class="main hv2 bdv" id="hv2">

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

	{{-- HERO — breadcrumb + title only; the enquiry form is the sticky sidebar of the article below --}}
	<section class="hv2-hero hv2-dark bdv-hero" id="hv2-hero">
		<div class="hv2-hero-bg" aria-hidden="true">
			<span class="hv2-blob hv2-blob--1"></span><span class="hv2-blob hv2-blob--2"></span><span class="hv2-blob hv2-blob--3"></span>
			<span class="hv2-grid-lines"></span>
		</div>
		<div class="hv2-streaks" aria-hidden="true">
			@for($s = 0; $s < 8; $s++)<i style="top: {{ 10 + $s * 11 }}%; --t: {{ 2.8 + ($s % 4) * .7 }}s; --dl: {{ $s * .5 }}s"></i>@endfor
		</div>
		<div class="hv2-container hv2-hero-inner">
			<div class="hv2-hero-copy">
				@include('web.home-v2.breadcrumbs', ['crumbs' => [['Home', URL::to('/')], ['Blogs', route('blogs')], [$data->heading, null]], 'crumbSchema' => false])
				<div class="bdv-hero-body">
					<h1 data-reveal="up" data-delay="1">{{ $data->heading }}</h1>
					<div class="bdv-hero-meta">
						<span class="bdv-hero-author"><i class="bi bi-person" aria-hidden="true"></i> Author: <strong>{{ $data->author->name }}</strong></span>
						<time datetime="{{ date('Y-m-d', strtotime($data->created_at)) }}"><i class="bi bi-calendar2-week" aria-hidden="true"></i> {{ date('F d, Y', strtotime($data->created_at)) }}</time>
						@php $bdvRead = (int) preg_replace('/\D+/', '', (string) ($data->read_time ?? '')); @endphp
						@if($bdvRead > 0)<span class="bdv-hero-read"><i class="bi bi-clock" aria-hidden="true"></i> {{ $bdvRead }} min read</span>@endif
					</div>
				</div>
			</div>

		</div>
		<a href="#bdv-article" class="hv2-scroll-cue" data-scrollto="#bdv-article" aria-label="Scroll to content"></a>
	</section>
	<span class="js-count-particles" hidden></span>

	{{-- ARTICLE --}}
	<section class="hv2-section hv2-tint bdv-article-sec" id="bdv-article">
		<div class="bdv-deco" aria-hidden="true">
			{!! $bdvMotifs !!}
		</div>
		<div class="hv2-container bdv-layout-grid">
			<div class="bdv-main">
			<div class="bdv-cover" data-reveal="up">
				<img {!! v2_img('public/storage/blogs/' . $data->banner, '(max-width: 1020px) 94vw, 980px') !!} alt="{{ $data->heading }}">
				<div class="bdv-summary">
					<p class="summary"><strong>Summary</strong>:<br>
						{{ $data->short_description }}
					</p>
				</div>
			</div>

			<div class="bdv-article" data-reveal="up">
				{!! v2_links($data->description) !!}
			</div>

			<div class="bdv-tags">
				<span>Tags: </span>
				@foreach($data->tags as $val)
					@if(!empty($val->data->slug))
						<a href="{{ v2_url('/tag/' . @$val->data->slug) }}"><span>{{ $val->tag }}</span></a>
					@endif
				@endforeach
			</div>

			<div class="bdv-share">
				<div class="bdv-share-content">
					<p><i class="fa fa-share"></i> Share:</p>
					<a href="https://www.linkedin.com/shareArticle?mini=true&url={{ URL::current() }}" target="_blank" rel="noopener" aria-label="Linkedin" class="share-linkedin"><i class="bi bi-linkedin"></i> <span>Linkedin</span></a>
					<a href="https://www.facebook.com/sharer/sharer.php?u={{ URL::current() }}&amp;src=sdkpreparse" target="_blank" rel="noopener" class="share-facebook"><i class="bi bi-facebook"></i> <span>Facebook</span></a>
					<a href="https://twitter.com/intent/tweet?url={{ urlencode(URL::current()) }}&text={{ urlencode($data->heading) }}" target="_blank" rel="noopener" aria-label="twitter" class="share-twitter"><i class="bi bi-twitter"></i> <span>Twitter</span></a>
					<a href="https://api.whatsapp.com/send?text={{ urlencode(URL::current()) }}" target="_blank" rel="noopener" data-action="share/whatsapp/share" class="share-whatsapp"><i class="bi bi-whatsapp"></i> <span>Whatsapp</span></a>
					<a href="javascript:void(0)" data-link="{{ URL::current() }}" class="share-copy"><i class="bi bi-copy"></i> <span>Copy Link</span></a>
				</div>
			</div>
			</div>
			<aside class="bdv-side" id="hv2-form" aria-label="Free consultation">
				<div class="hv2-form-card hv2-glass hv2-dark bdv-sticky" data-tilt data-tilt-max="4" data-reveal="scale" data-delay="2">
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
			</aside>
		</div>
	</section>

	{{-- RELATED ARTICLES --}}
	<section class="hv2-section hv2-dark bdv-related" id="bdv-related">
		<span class="hv2-watermark" data-parallax="0.12" aria-hidden="true">Articles</span>
		<div class="bdv-deco bdv-deco--dark" aria-hidden="true">
			<span class="bdv-dots bdv-dots--l"></span><span class="bdv-dots bdv-dots--r"></span>
			<span class="bdv-glow g1" data-bdv-depth="0.08"></span><span class="bdv-glow g2" data-bdv-depth="-0.1"></span>
			<span class="bdv-plus p1" data-bdv-depth="0.18">+</span><span class="bdv-plus p2" data-bdv-depth="-0.14">+</span>
			<span class="bdv-tri t1" data-bdv-depth="-0.2"></span><span class="bdv-bar b1" data-bdv-depth="0.2"></span>
		</div>
		<div class="hv2-container">
			<h2 class="hv2-display hv2-h2 bdv-related-title" data-split>Related Articles</h2>
			<div class="bdv-grid" id="bdv-grid">
				@foreach($related as $val)
					<article class="bdv-card" data-tilt data-tilt-max="4" data-reveal="up" data-delay="{{ $loop->iteration }}">
						<a class="bdv-card-media hv2-media" href="{{ v2_blog_url($val->slug) }}" target="_blank" tabindex="-1" aria-hidden="true">
							<img {!! v2_img('public/storage/blogs/' . $val->banner, '(max-width: 640px) 92vw, 31vw') !!} alt="{{ $val->heading }}" loading="lazy">
							<span class="bdv-card-go"><i class="bi bi-arrow-up-right"></i></span>
						</a>
						<div class="bdv-card-body">
							<h3 class="related-blog-heading">{{ $val->heading }}</h3>
							<p class="date">Published On - {{ date('F d, Y', strtotime($val->created_at)) }}</p>
							<div class="post-content"><p>{{ $val->short_description }}</p></div>
							<div class="post-footer">
								<p><i class="bi bi-person"></i> {{ $val->author->name }} </p>
								<a href="{{ v2_blog_url($val->slug) }}" target="_blank">► Read More</a>
							</div>
						</div>
					</article>
				@endforeach
			</div>
		</div>
	</section>
</main>

    <!-- Article Schema -->

    @php
    $siteUrl = rtrim(url('/'), '/');
    $pageUrl = $actual_link;
    $category = $data->category ?? null;
    $categoryName = $category->name ?? null;

    // Strip HTML, decode entities (twice, to catch double-encoded "&amp;nbsp;"),
    // turn non-breaking spaces into normal spaces and collapse whitespace.
    $cleanText = function ($html) {
        $text = strip_tags((string) $html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xC2\xA0", ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    };

    // Fix 6: clean articleBody (no &nbsp; / &amp;nbsp; artefacts)
    $articleBody = $cleanText($data->description ?? '');

    // Fix 1: wordCount as a real integer
    $wordCount = (int) count(preg_split('/\s+/u', $articleBody, -1, PREG_SPLIT_NO_EMPTY));

    // Fix 2: headline from the H1, max 110 characters
    $headline = $cleanText($data->heading ?? '');
    if (mb_strlen($headline) > 110) {
        $headline = \Illuminate\Support\Str::limit($headline, 109, '…');
    }

    $blogPosting = [
        '@type' => 'BlogPosting',
        '@id' => $pageUrl . '#article',
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $pageUrl,
        ],
        'url' => $pageUrl,
        'headline' => $headline,
        'description' => $cleanText($data->short_description ?? ''),
        'image' => [
            '@type' => 'ImageObject',
            'url' => empty($data->banner)
                ? $siteUrl . '/public/og-image-1200x630.jpg'
                : URL::to('public/storage/blogs/' . $data->banner),
            'width' => 1200,
            'height' => 630,
        ],
        'author' => [
            '@type' => 'Person',
            'name' => 'Osama Usmani',
            'url' => 'https://www.linkedin.com/in/osama-usmani/',
        ],
        // Fix 3: link to the site-wide WebSite and Organization entities.
        // The publisher keeps its full details so it still validates
        // before the homepage Organization block goes live.
        'isPartOf' => ['@id' => $siteUrl . '/#website'],
        'publisher' => [
            '@type' => 'Organization',
            '@id' => $siteUrl . '/#organization',
            'name' => 'DataMySite',
            'url' => $siteUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $siteUrl . '/public/assets/img/logo.png',
                'width' => 512,
                'height' => 512,
            ],
            'sameAs' => [
                'https://www.facebook.com/datamysite',
                'https://www.linkedin.com/company/datamysite',
                'https://twitter.com/datamysite',
            ],
        ],
        'articleSection' => $categoryName,
        'keywords' => $data->tags->pluck('tag')->values()->toArray(),
        'about' => $categoryName ? ['@type' => 'Thing', 'name' => $categoryName] : null,
        'inLanguage' => 'en-US',
        'wordCount' => $wordCount,
        'timeRequired' => 'PT5M',
        'articleBody' => $articleBody,
        'datePublished' => date('c', strtotime($data->created_at)),
        'dateModified' => date('c', strtotime($data->updated_at)),
    ];
    // Drop empty fields (e.g. no category)
    $blogPosting = array_filter($blogPosting, fn ($v) => $v !== null && $v !== '' && $v !== []);

    // Fix 4: BreadcrumbList  Home > Blogs > Category > Post
    // Check the Blogs and category URLs match your routes / TT-13.
    $crumbs = [
        ['name' => 'Home', 'item' => $siteUrl . '/'],
        ['name' => 'Blogs', 'item' => $siteUrl . '/blogs'],
    ];
    if ($category && !empty($category->slug)) {
        $crumbs[] = ['name' => $categoryName, 'item' => $siteUrl . '/blogs/' . $category->slug];
    }
    $crumbs[] = ['name' => $headline, 'item' => $pageUrl];

    $breadcrumb = [
        '@type' => 'BreadcrumbList',
        '@id' => $pageUrl . '#breadcrumb',
        'itemListElement' => array_map(fn ($c, $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $c['name'],
            'item' => $c['item'],
        ], $crumbs, array_keys($crumbs)),
    ];

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [$blogPosting, $breadcrumb],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_HEX_TAG) !!}
</script>

    <!-- Article Schemma -->

    @if(!empty($data->faq_schema))

        <!-- FAQs Schema -->

        {!! $data->faq_schema !!}

        <!-- FAQs Schema -->

    @endif
    
@endsection

@section('addScript')
	<script src="{{ v2_asset('assets/js/home-v2.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/blog-detail-v2.js') }}" defer></script>
@endsection
