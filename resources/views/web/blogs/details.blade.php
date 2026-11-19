@extends('web.includes.master')

@section('content')
    <script>
      window.gtmCustomPage = "blog-page";
    </script>
	<main class="main">

	    <!-- Page Title -->
          <div class="page-title dark-background" data-aos="fade" style="background-image: url('{{URL::to('/public/storage/blogs/'.$data->banner)}}');">
            <div class="container">
              <h1>{{$data->heading}}</h1>
              <nav class="breadcrumbs">
                <ol>
                  <li><a href="{{route('home')}}">Home</a></li>
                  <li><a href="{{route('blogs')}}">Blogs</a></li>
                  <li class="current">{{$data->heading}}</li>
                </ol>
              </nav>
            </div>
          </div><!-- End Page Title -->

	    <section id="service-cards" class="blog-section blog-page section">

	      <div class="container">

	        <div class="row gy-4 first-row">

	          <div class="col-lg-9" data-aos="fade-up" data-aos-delay="100">
                <div class="post-footer">
                </div>
                <div class="detail-post-image">
                    <img src="{{empty($data->banner) ? 'https://datamysite.com/public/og-logo.jpeg' : URL::to('public/storage/blogs/'.$data->banner)}}" alt="{{$data->heading}}">
                    <div class="detail-post-summary">
                        <p class="summary">
                            <strong>Summary</strong>:<br>
                            {{ $data->short_description }}
                        </p>
                        <p class="author"><i class="bi bi-person"></i> {{$data->author->name}} </p>
                        <p class="date"><i class="bi bi-calendar2-week"></i> &nbsp;{{date('F d, Y', strtotime($data->created_at))}}</p>
                    </div>
                </div>
                <br>
	          	<div class="post-container">
                    {!! $data->description !!}
                </div>
                <div class="blog-tags">
                    <span>Tags: </span>
                    @foreach($data->tags as $val)
                        @if(!empty($val->data->slug))
                            <a href="{{URL::to('/tag/'.@$val->data->slug)}}">
                                <span>{{$val->tag}}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
                <div class="blog-share">
                    <div class="blog-share-content">
                        <p><i class="fa fa-share"></i> Share:</p>

                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ URL::current() }}" target="_blank" aria-label="Linkedin" class="share-linkedin">
                            <i class="bi bi-linkedin"></i> <span>Linkedin</span>
                        </a>

                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ URL::current() }}&amp;src=sdkpreparse" target="_blank" data-href="https://developers.facebook.com/docs/plugins/" data-layout="" data-size="" class="share-facebook">
                            <i class="bi bi-facebook"></i> <span>Facebook</span>
                        </a>

                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(URL::current()) }}&text={{ urlencode($data->heading) }}" target="_blank" aria-label="twitter" class="share-twitter">
                            <i class="bi bi-twitter"></i> <span>Twitter</span>
                        </a>

                        <a href="https://api.whatsapp.com/send?text={{ urlencode(URL::current()) }}" target="_blank" data-action="share/whatsapp/share" class="share-whatsapp">
                            <i class="bi bi-whatsapp"></i> <span>Whatsapp</span>
                        </a>

                        <a href="javascript:void(0)" data-link="{{ URL::current() }}" class="share-copy">
                            <i class="bi bi-copy"></i> <span>Copy Link</span>
                        </a>
                    </div>
                </div>  
	        </div>

            <div class="col-lg-3 aside-block" data-aos="fade-up" data-aos-delay="100">
            </div>

	      </div>

	    </section>
        <br>
        <section id="service-cards" class="blog-section section">

          <div class="container">
            <h2>Related Articles</h2>
            <div class="post-container">
                <div class="row gy-4 first-row">
                    @php
                        // TT-21: related posts from the same category, nearest by id, so every post
                        // is linked from its neighbours instead of every page showing the 3 newest posts.
                        try {
                            $relatedCat = \App\Models\Blogs::where('status', 1)
                                ->where('id', '!=', $data->id)
                                ->where('category_id', $data->category_id)
                                ->orderByRaw('ABS(CAST(id AS SIGNED) - ?)', [(int) $data->id])
                                ->limit(3)->get();
                            if ($relatedCat->count() < 3) {
                                $relatedCat = $relatedCat->concat(
                                    $related->whereNotIn('id', $relatedCat->pluck('id')->push($data->id))
                                )->take(3);
                            }
                            $related = $relatedCat;
                        } catch (\Throwable $e) { /* keep the controller's list */ }
                    @endphp
                    @foreach($related as $val)
                        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                          <div class="post row">
                            <div class="col-lg-12">
                                <div class="post-image">
                                    <img src="{{empty($val->banner) ? 'https://datamysite.com/public/og-logo.jpeg' : URL::to('public/storage/blogs/'.$val->banner)}}" alt="{{$val->heading}}">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <br>
                                <h2 class="related-blog-heading">{{$val->heading}}</h2>
                                <p class="date">Published On - {{date('F d, Y', strtotime($val->created_at))}}</p>
                                <div class="post-content">
                                  <p>{{$val->short_description}}</p> 

                                </div>
                                <div class="post-footer">
                                    <p ><i class="bi bi-person"></i> {{$val->author->name}} </p>
                                    <a href="{{route('blogs.detail', [$val->slug])}}" target="_blank">► Read More</a>
                                </div>
                            </div>
                          </div>
                        </div>  
                  @endforeach
                </div>
            </div>

        </section>

	</main>


    <!-- Article Schema -->

    @php
    $siteUrl = 'https://datamysite.com';
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
                ? $siteUrl . '/public/og-logo.jpeg'
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
  <script src="{{URL::to('/public')}}/assets/js/aside.js" defer></script>
@endsection