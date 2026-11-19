@extends('web.includes.master')
@section('metaAddition')
@if(!empty($tags))
<meta name="robots" content="noindex, follow">
@endif
@endsection
@section('content')

	<main class="main">

	    <!-- Page Title -->
          <div class="page-title dark-background" data-aos="fade" style="background-image: url('{{URL::to('/public/assets/img/title/'.$titleImg)}}');">
            <div class="container">
              <h1>{{$title}} {{@$page}}</h1>
              <nav class="breadcrumbs">
                <ol>
                  <li><a href="{{route('home')}}">Home</a></li>
                  @if(!empty($type))
                  <li><a href="{{route('blogs')}}">Blogs</a></li>
                  @endif
                  <li class="current">{{$title}}</li>
                </ol>
              </nav>
            </div>
          </div><!-- End Page Title -->

	    <section id="service-cards" class="blog-section section">

	      <div class="container">

	        <div class="row gy-4 first-row">

	          <div class="col-lg-9" data-aos="fade-up" data-aos-delay="100">
	          	<div class="post-container">

                    @foreach($data as $val)

                      @if($loop->first)
                        {{-- Featured / lead post --}}
                        <article class="feature-post" data-aos="fade-up" data-aos-delay="100">
                          <a class="feature-post__media" href="{{route('blogs.detail', [$val->slug])}}" target="_blank">
                            <img src="{{empty($val->banner) ? 'https://datamysite.com/public/og-logo.jpeg' : URL::to('public/storage/blogs/'.$val->banner)}}" alt="{{$val->banner_alt}}">
                            <span class="feature-post__tag">Latest</span>
                          </a>
                          <div class="feature-post__body">
                            <p class="post-eyebrow">Fresh off the desk</p>
                            <a href="{{route('blogs.detail', [$val->slug])}}" target="_blank"><h2>{{$val->heading}}</h2></a>
                            <div class="post-meta">
                              <span class="post-meta__avatar">{{ strtoupper(substr($val->author->name ?? '', 0, 1)) }}</span>
                              <span>{{$val->author->name}}</span>
                              <span class="post-meta__dot">&middot;</span>
                              <span>{{date('F d, Y', strtotime($val->created_at))}}</span>
                              <span class="post-meta__dot">&middot;</span>
                              <span>{{ max(1, (int) ceil(str_word_count(strip_tags($val->description ?? '')) / 200)) }} min read</span>
                            </div>
                            <p class="feature-post__excerpt">{{$val->short_description}}</p>
                            <a class="post-cta" href="{{route('blogs.detail', [$val->slug])}}" target="_blank">Read the full story <span aria-hidden="true">&rarr;</span></a>
                          </div>
                        </article>
                      @else
                        {{-- Grid post --}}
                        <article class="post-card" data-aos="fade-up" data-aos-delay="{{ 100 + (min($loop->index, 6) * 40) }}">
                          <a class="post-card__media" href="{{route('blogs.detail', [$val->slug])}}" target="_blank">
                            <img src="{{empty($val->banner) ? 'https://datamysite.com/public/og-logo.jpeg' : URL::to('public/storage/blogs/'.$val->banner)}}" alt="{{empty($val->banner_alt) ? $val->heading : $val->banner_alt}}">
                          </a>
                          <div class="post-card__body">
                            <div class="post-meta post-meta--sm">
                              <span>{{date('F d, Y', strtotime($val->created_at))}}</span>
                              <span class="post-meta__dot">&middot;</span>
                              <span>{{ max(1, (int) ceil(str_word_count(strip_tags($val->description ?? '')) / 200)) }} min read</span>
                            </div>
                            <a href="{{route('blogs.detail', [$val->slug])}}" target="_blank"><h4>{{$val->heading}}</h4></a>
                            <p class="post-card__excerpt">{{$val->short_description}}</p>
                            <div class="post-card__footer">
                              <span class="post-meta__avatar post-meta__avatar--sm">{{ strtoupper(substr($val->author->name ?? '', 0, 1)) }}</span>
                              <span class="post-card__author">{{$val->author->name}}</span>
                              <a class="post-cta post-cta--sm" href="{{route('blogs.detail', [$val->slug])}}" target="_blank">Read more <span aria-hidden="true">&rarr;</span></a>
                            </div>
                          </div>
                        </article>
                      @endif

                    @endforeach

                  <div class="blog-pagination">
                    {!! $data->withQueryString()->links('pagination::bootstrap-5') !!}
                  </div>
                </div>
	        </div>

            <div class="col-lg-3 aside-block" data-aos="fade-up" data-aos-delay="100">
                <div class="aside-form aside-form2">
                    <form action="{{route('enquiry.submit')}}" id="enquiry-form">
                        @csrf
                        <div class="form1-wrapper">
                            <div class="form1-bg">
                              <h2 class="form1-headline">Ready to Get <span>Results?</span></h2>
                              <p class="form1-sub">Drop us your details — our experts respond within 60 minutes.</p>
                
                              <div class="form1-field">
                                <input type="text" placeholder="Your Full Name" name="name" required>
                              </div>
                
                              <div class="form1-field">
                                  <input type="tel" class="form-control" name="mainphone" id="phone-field2" required="">
                                  <input type="hidden" name="phone" id="fullphone-field2" required="">
                              </div>
                
                              <div class="form1-field">
                                <input type="email" placeholder="Your Email Address" class="aside-email" name="email" required>
                              </div>
                
                              <div class="form1-field">
                                <textarea class="form1-textarea" name="description" placeholder="Tell us what you need — the more detail, the better we can help." required></textarea>
                              </div>
                
                              <button class="form1-btn" type="submit">
                                Get Free Consultation
                                <span class="form1-btn-arrow">→</span>
                              </button>
                
                              <div class="form1-trust">
                                <div class="form1-trust-item">
                                  <svg viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                                  No spam, ever
                                </div>
                                <div class="form1-trust-item">
                                  <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                  Reply in 60 min
                                </div>
                                <div class="form1-trust-item">
                                  <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                                  100% Private
                                </div>
                              </div>
                            </div>
                        </div>
                    </form>
                    <div class="loading"><img src="{{URL::to('/public/loader-gif.gif')}}" width="80px" class="newsletter-loader"></div>
                </div>
                
                <div class="aside-link">
                    <h4>Our Services</h4>
                    @foreach($header_services as $val)
                        @if(!empty($val->intro_html))
                            <span class="inter-link-para">
                                {!! $val->intro_html !!}
                            </span>
                        @else
                            <a href="{{URL::to('/'.$val->slug)}}">{{$val->name}}</a>
                        @endif
                    @endforeach
                </div>
                
                @if(count($blog_categories) > 0)
                <div class="aside-link">
                    <h4 style="display: flex;justify-content: space-between;"><span>Blog Categories</span>  <a href="{{route('blogs.categories')}}">View all →</a></h4>
                    @foreach($blog_categories as $val)
                        <a href="{{URL::to('/blogs/'.$val->slug)}}">
                            {{$val->name}} <small>({{count($val->blogs)}})</small>
                        </a>
                    @endforeach
                </div>
                @endif

            </div>

	      </div>

	    </section>

	</main>

<style>
/* ---------------------------------------------------------------
   Blog listing — scoped redesign
   Palette: deep teal (brand), warm coral (accent), aged gold (meta),
   soft paper background. Display face is an editorial serif to give
   the listing a "magazine" feel; body copy stays a clean grotesque.
----------------------------------------------------------------*/
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap');

.blog-section{
  --ink:#182620;
  --ink-soft:#4b5a54;
  --teal:#0e3b35;
  --coral:#d9532f;
  --gold:#a9832f;
  --paper:#f7f4ee;
  --line:rgba(14,59,53,0.12);
  --card:#ffffff;
  background:var(--paper);
  padding-top:70px;
  padding-bottom:80px;
  font-family:'Inter',sans-serif;
  color:var(--ink);
}

.blog-section h1, .blog-section h2, .blog-section h4{
  font-family:'Fraunces', serif;
  color:var(--ink);
  letter-spacing:-0.01em;
}

/* Page title header */
.page-title h1{ font-family:'Fraunces', serif; }

/* ---- Eyebrow / meta ---- */
.post-eyebrow{
  font-family:'JetBrains Mono', monospace;
  font-size:0.72rem;
  letter-spacing:0.12em;
  text-transform:uppercase;
  color:var(--gold);
  margin-bottom:10px;
}
.post-meta{
  display:flex;
  align-items:center;
  gap:8px;
  font-size:0.85rem;
  color:var(--ink-soft);
  margin:10px 0 16px;
}
.post-meta--sm{ font-size:0.78rem; margin:0 0 10px; }
.post-meta__dot{ color:var(--line); }
.post-meta__avatar{
  width:30px; height:30px;
  border-radius:50%;
  background:var(--teal);
  color:#fff;
  font-family:'JetBrains Mono', monospace;
  font-weight:600;
  font-size:0.78rem;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  flex-shrink:0;
}
.post-meta__avatar--sm{ width:22px; height:22px; font-size:0.65rem; }

/* ---- Featured / lead post ---- */
.feature-post{
  display:grid;
  grid-template-columns:1.05fr 1fr;
  gap:40px;
  align-items:center;
  background:var(--card);
  border:1px solid var(--line);
  border-radius:8px;
}
.feature-post__media{
  position:relative;
  display:block;
  border-radius:12px;
  overflow:hidden;
  aspect-ratio:4/3;
}
.feature-post__media img{
  width:100%; height:100%; object-fit:cover;
  transition:transform .5s ease;
}
.feature-post__media:hover img{ transform:scale(1.05); }
.feature-post__tag{
  position:absolute; top:14px; left:14px;
  background:var(--coral);
  color:#fff;
  font-family:'JetBrains Mono', monospace;
  font-size:0.68rem;
  letter-spacing:0.08em;
  text-transform:uppercase;
  padding:5px 10px;
  border-radius:100px;
}
.feature-post__body h2{
  font-size:1.9rem;
  line-height:1.2;
  font-weight:600;
  margin:0;
}
.feature-post__body a{ text-decoration:none; }
.feature-post__body h2:hover{ color:var(--coral); }
.feature-post__excerpt{
  color:var(--ink-soft);
  font-size:1rem;
  line-height:1.65;
  margin-bottom:18px;
}

/* ---- CTA link ---- */
.post-cta{
  display:inline-flex;
  align-items:center;
  gap:6px;
  color:var(--coral);
  font-weight:600;
  font-size:0.92rem;
  text-decoration:none;
  border-bottom:1px solid transparent;
}
.post-cta:hover{ border-color:var(--coral); }
.post-cta--sm{ font-size:0.82rem; margin-left:auto; }
.post-cta span{ transition:transform .2s ease; display:inline-block; }
.post-cta:hover span{ transform:translateX(3px); }

/* ---- Grid of remaining posts ---- */
.post-container{
  display:grid;
  grid-template-columns:repeat(3, 1fr);
  gap:16px;
}
.feature-post{ grid-column:1/-1; }
.blog-pagination{ grid-column:1/-1; }
.post-container > .post-card{ display:flex; flex-direction:column; }

.post-card{
  background:var(--card);
  border:1px solid var(--line);
  border-radius:8px;
  overflow:hidden;
  transition:box-shadow .25s ease, transform .25s ease;
}
.post-card:hover{
  box-shadow:0 14px 30px rgba(14,59,53,0.10);
  transform:translateY(-3px);
}
.post-card__media{ display:block; aspect-ratio:16/10; overflow:hidden; }
.post-card__media img{
  width:100%; height:100%; object-fit:cover;
  transition:transform .5s ease;
}
.post-card__media:hover img{ transform:scale(1.06); }
.post-card__body{ padding:10px; display:flex; flex-direction:column; flex:1; }
.post-card__body h4{
  font-size:14px;
  line-height:1.3;
  font-weight:600;
  margin:0 0 10px;
}
.post-card__body a{ text-decoration:none; }
.post-card__body h4:hover{ color:var(--coral); }
.post-card__excerpt{
  color:var(--ink-soft);
  font-size:0.9rem;
  line-height:1.55;
  margin-bottom:16px;
  display:-webkit-box;
  -webkit-line-clamp:3;
  -webkit-box-orient:vertical;
  overflow:hidden;
}
.post-container p {
    padding-left: 0px;
    font-size: 11px;
}
.post-card__footer{
  margin-top:auto;
  display:flex;
  align-items:center;
  gap:8px;
  padding-top:14px;
  border-top:1px solid var(--line);
}
.post-card__author{ font-size:0.82rem; color:var(--ink-soft); }

/* ---- Pagination ---- */
.blog-pagination{ margin-top:16px; display:flex; justify-content:center; }
.blog-pagination .pagination{ gap:6px; flex-wrap:wrap; }
.blog-pagination .page-link{
  border-radius:10px !important;
  border:1px solid var(--line);
  color:var(--ink);
  font-family:'JetBrains Mono', monospace;
  font-size:0.85rem;
}
.blog-pagination .page-item.active .page-link{
  background:var(--coral);
  border-color:var(--coral);
  color:#fff;
}
.blog-pagination .page-link:hover{
  background:rgba(217,83,47,0.08);
  color:var(--coral);
}
.blog-pagination nav div:nth-child(2) {
    display: flex;
    flex-direction: column-reverse;
    gap: 12px;
}
/* ---- Sidebar column ---- */
.aside-block{ position:sticky; top:24px; align-self:flex-start; }

/* ---- Responsive ---- */
@media (max-width:992px){
  .feature-post{ grid-template-columns:1fr; }
  .post-container{ grid-template-columns:1fr; }
}
@media (max-width:576px){
  .feature-post{ padding:14px; }
  .feature-post__body h2{ font-size:1.5rem; }
}
</style>

@endsection
@section('addScript')
  <!--<script src="{{URL::to('/public')}}/assets/js/aside.js" defer></script>-->
@endsection