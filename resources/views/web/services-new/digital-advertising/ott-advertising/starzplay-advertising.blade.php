@extends('web.includes.master')
@section('metaAddition')
    
@endsection
@section('addStyle')
    <link href="{{URL::to('/public')}}/assets/css/service.css" rel="stylesheet">
@endsection
@section('content')

    <main class="main">

        <!-- Page Title -->
          <div class="page-title dark-background" data-aos="fade" style="background-image: url('{{URL::to('public/assets/img/service-new/'.$subservice->slug.'.jpg')}}');">
            <div class="container head-container-service">
                <div class="row">
                    <div class="col-lg-8 head-para-service">
                        <h1>&#10687; {{$subservice->name}}</h1>
                        <h2>Reach Engaged Audiences</h2>
                        <h4>Across the Middle East with Premium OTT Campaigns</h4>
                        <br>
                        <p>
                            Ask anyone who plans media in the Gulf which streaming platform actually gets watched in Arabic-speaking households, and StarzPlay comes up in the first breath. It grew up here — Dubai-headquartered, built for MENA from day one — and its mix of Hollywood series, Arabic originals, Bollywood titles and live sport has earned it millions of paying subscribers across the UAE, Saudi Arabia and the wider region. For advertisers, that history matters more than the subscriber count: this is an audience that chose a regional platform, watches it in the evening with the family, and doesn't treat ads as background noise.
                        </p>
                    </div>
                    <div class="col-lg-4">
                        <div class="aside-form">
                            
                            <form action="{{route('enquiry.submit')}}" id="enquiry-form">
                                @csrf
                                <input type="hidden" name="service" value="{{$subservice->name}}" required>
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
                    </div>
                </div>
              
            </div>
          </div><!-- End Page Title -->



        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-6 vertical-center" data-aos="fade-up" data-aos-delay="100">
                    <h4>What is StarzPlay Advertising?</h4>
                    <p class="mb-2">
                        StarzPlay advertising puts your brand inside the viewing session itself — a video spot before the show starts, a break inside a longer title, or a banner while someone browses for what to watch tonight. Nobody scrolls past it. Nobody has it running in another tab.
                        <br><br>
                        The difference from booking a TV spot is control. Instead of buying a time slot and hoping the right people are watching, you tell us who you want: UAE only, Arabic-preference viewers, sports audiences, drama watchers, families streaming on the living-room TV versus commuters on phones. The platform's viewing data does the aiming; your budget only pays for the people you actually asked for.
                        <br><br>
                        We'll be straight about when it fits: if your product needs to be seen — a development launch, a car, a destination, an app — StarzPlay earns its budget quickly. If you're selling something that needs a paragraph of explanation, we'd usually point that money at search first and come back to OTT for the awareness layer.

                    </p>
                </div>
                <div class="col-lg-6 vertical-center">
                    <iframe class="service-iframe airline-iframe" src="https://www.youtube.com/embed/bfcE9AMwrrM?si=c-GXCMSK669sV8qn" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>  
            </div>

          </div>

        </section>



        <section id="testimonials" class="testimonials striped-section section">
          @include('web.includes.elements.clients')
        </section>



        <section id="testimonials" class="testimonials case-studies-section section">
           @include('web.includes.elements.case-studies')
        </section>

        

    
        <section id="service-cards" class="blog-section striped-section  section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row split-block">


                <div class="col-lg-2 vertical-center">
                    <img src="{{URL::to('/public/section-images/image147.png')}}" width="100%" alt="Why choose us">
                </div> 
                <div class="col-lg-1"></div>
                <div class="col-lg-9 vertical-center">
                    <h4>Why StarzPlay Advertising Works in the UAE</h4>
                    <p>
                        A few honest reasons this platform keeps ending up on our clients' plans:
                    </p>
                    <ul>
                        <li><strong>The audience actually pays attention.</strong> Streaming viewers picked their show and settled in. When an ad plays in that setting, recall runs far ahead of what the same 30 seconds achieves in a social feed — we see it every time a client runs the same creative in both places.</li>
                        <li><strong>It reaches the households other platforms miss.</strong> Arabic-first families, GCC nationals, long-tenure expats — segments that are expensive and imprecise to reach on Meta are simply there on StarzPlay, watching Arabic drama and regional football.</li>
                        <li><strong>Live sport, without a TV-sized cheque.</strong> StarzPlay's sports rights (its football coverage in particular) create appointment viewing — the closest thing streaming has to old-school prime time — at rates a mid-size UAE brand can actually consider.</li>
                        <li><strong>Your brand sits next to licensed, premium content.</strong> No user-generated roulette. Whatever plays before your ad went through a studio, not an upload button.</li>
                        <li><strong>The money goes further than you'd expect.</strong> Compared with what global platforms charge for UAE impressions, StarzPlay's rates leave room to run proper frequency — and frequency, not reach, is usually what makes OTT campaigns stick.</li>
                    </ul>
                </div>
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row">


                <div class="col-lg-8 vertical-center">
                    <h4>Popular StarzPlay Ad Formats</h4>
                    <ul>
                        <li><strong>Pre-roll</strong> is where most campaigns start: your spot plays before the chosen title, at the moment of highest attention. If we're launching something, this is the workhorse.</li>

                        <li><strong>Mid-roll</strong> lands inside longer content, and it performs better than people expect — twenty minutes into a series, viewers are committed and the ad break gets watched rather than fled.</li>
                        
                        <li><strong>Display banners</strong> across the browse screens keep the brand visible between sessions. Cheap, cheerful, and the right supporting act — we rarely recommend them alone.</li>
                        
                        <li><strong>Sponsorships and branded moments</strong> are the premium play: attaching your name to a show, a season, or a match window. These are negotiated deals rather than rate-card buys, and they're where the platform's regional identity really pays off.</li>
                        
                        <li><strong>Genre targeting</strong> ties it together — your ad against Arabic drama, or Hollywood blockbusters, or live football, depending on whose evening you want to be part of. Choosing the genre is choosing the household.</li>


                    </ul>
                    <br>
                    <h4>Key Benefits Summary</h4>
                    <p>The short version: engaged viewers, targeting that actually holds at the emirate and language level, live-sport moments without broadcast pricing, and reporting you can read the next morning rather than a coverage certificate a month later. It also slots cleanly alongside YouTube and Netflix campaigns when we're building a full video plan — one set of creative, three platforms, one report.</p>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-3 vertical-center">
                    <img src="{{URL::to('/public/section-images/image148.png')}}" width="100%" alt="Why choose us">
                </div> 
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section striped-section section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row split-block">

                <div class="col-lg-3 vertical-center">
                    <img src="{{URL::to('/public/section-images/image149.png')}}" width="100%" alt="Why choose us">
                </div>

                <div class="col-lg-1"></div>
                <div class="col-lg-8 vertical-center">
                    <h4>Advantages of Advertising on StarzPlay with DataMySite</h4>
                    <p>
                        Here's what working through us actually changes. We plan the campaign around your objective, not the platform's media kit — which sometimes means telling you StarzPlay should get 40% of the video budget, not 100%. We handle the creative end-to-end, and we've learned the hard way that a spot cut for Instagram needs re-editing before it belongs in a living room; the first five seconds carry different weight when nobody can scroll. We watch the campaign daily and move budget between audiences while it runs, not after. And because we buy OTT across StarzPlay, Netflix, YouTube and Abu Dhabi TV, we can tell you — with numbers — which platform earned its share for your audience, and reweight the next flight accordingly.
                        <br><br>
                        That last part is the honest advantage of an agency over booking direct: the platform will always tell you the platform worked. We'll tell you where it didn't.

                    </p>
                    <br>
                    <h4>Industries That Benefit from StarzPlay Advertising</h4>
                    <p>Real estate developers are the heaviest StarzPlay advertisers we book — a project film in front of a family audience, geo-fenced to the UAE, does exactly what a hoarding does but inside the home. Automotive launches and tourism campaigns follow the same logic: products bought with the eyes. E-commerce brands use it to put a face on a name people only ever see as a checkout page. And banks, schools and healthcare groups come for the brand-safe environment — categories where where the ad appears matters as much as who sees it. If your sector isn't on that list, the question isn't whether StarzPlay "suits your industry"; it's whether your customer is in front of that screen at 9pm. Usually we can answer that from the data before you spend a dirham.</p>
                </div> 
            </div>

          </div>

        </section>

        <section id="service-cards" class="blog-section section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row">


                <div class="col-lg-8 vertical-center">
                    <h4>Why StarzPlay Advertising is the Future of Digital Branding in MENA</h4>
                    <p>
                        The regional shift from cable to streaming isn't a prediction anymore — it happened, and the advertising money is still catching up to the audience. That lag is the opportunity: attention has moved to platforms like StarzPlay faster than ad budgets have, which is why the impressions there still cost less than they're worth. Our usual structure for UAE brands: StarzPlay for the Arabic-speaking and sports audience, YouTube for scale and search-adjacent intent, and linear TV only when the moment (Ramadan, a national launch) genuinely demands everyone at once.
                    </p>
                    <br>
                    <h4>Partner with DataMySite for StarzPlay Advertising in Dubai</h4>
                    <p>
                        If you're weighing StarzPlay against Netflix, YouTube or TV, don't guess — send us the brief and we'll come back with a split, the expected cost per completed view, and creative recommendations for each platform. One campaign, every screen that matters, and a report that tells you plainly what worked. Message us on WhatsApp or book the strategy call — an OTT specialist, not a salesperson, will call you back within the hour.
                    </p>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-3 vertical-center">
                    <img src="{{URL::to('/public/section-images/image150.png')}}" width="100%" alt="Why choose us">
                </div> 
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section section striped-section pr-blogs-section">

          <div class="container section-title">
            <h2 class="text-theme">Blogs</h2>
            <p class="pr-blog-sub">What’s Going On in the Industry?</p>
            <div class="row gy-4 blog-portrait-card first-row">
                @foreach($blogs as $val)
                    <div class="col-lg-4">
                        <a href="{{route('blogs.detail', [$val->slug])}}">
                          <div class="popular-card blog-card-2 flex-grow-1" bis_skin_checked="1">
                            <div class="card-image" bis_skin_checked="1">
                              <img src="{{URL::to('public/storage/blogs/'.$val->banner)}}" alt="{{$val->banner_alt}}">
                            </div>
                            <div class="popular-text" bis_skin_checked="1">
                              <h6 class="mt-2" title="{{$val->heading}}">{{$val->heading}}</h6>
                              <p class="line-break-3">{{$val->short_description}}</p>
                              <small>by <font class="text-theme2">{{$val->author->name}}</font> - <font class="text-bold">{{date('F d, Y', strtotime($val->created_at))}}</font></small>
                            </div>
                          </div>
                        </a>
                    </div>
                @endforeach
                <div class="col-lg-12">
                    <a href="{{URL::to('/blogs/pr-and-media-relations')}}" class="see-all-btn" target="_blank">See All Blogs</a>
                </div>
            </div>

          </div>

        </section>

        <section id="service-cards" class="blog-section section   faq-new-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12">
                    <h4 class="text-center">Frequently Asked Questions</h4>
                    <br>
                    <div class="faq " id="accordion">
                        <div class="card">
                            <div class="card-header" id="faqHeading-10">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-10" data-aria-expanded="true" data-aria-controls="faqCollapse-10">
                                        <span class="badge">&nbsp;</span>Why should I choose StarzPlay over other streaming platforms?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-10" class="collapse show" aria-labelledby="faqHeading-10" data-parent="#accordion">
                                <div class="card-body">
                                    <p>StarzPlay has a massive footprint in the MENA region and exclusive sports rights (like Serie A). It’s perfect for brands targeting a regional or sports-focused audience.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-11">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-11" data-aria-expanded="false" data-aria-controls="faqCollapse-11">
                                        <span class="badge">&nbsp;</span>Can I target specific countries within the StarzPlay network?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-11" class="collapse" aria-labelledby="faqHeading-11" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes, we can geo-fence your campaign to specific countries (e.g., UAE only or KSA only) to ensure your local offers are relevant.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-12">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-12" data-aria-expanded="false" data-aria-controls="faqCollapse-12">
                                        <span class="badge">&nbsp;</span>Are there "Live Sports" ad opportunities on StarzPlay?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-12" class="collapse" aria-labelledby="faqHeading-12" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes, we can place your brand during high-stakes live football matches and sporting events, which offer some of the highest engagement rates in digital media.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-13">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-13" data-aria-expanded="false" data-aria-controls="faqCollapse-13">
                                        <span class="badge">&nbsp;</span>What ad formats are available on StarzPlay?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-13" class="collapse" aria-labelledby="faqHeading-13" data-parent="#accordion">
                                <div class="card-body">
                                    <p>
                                        Options include pre-roll (before the content), mid-roll (during breaks), and branded "pauses" where your ad appears when a user pauses their show.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-14">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-14" data-aria-expanded="false" data-aria-controls="faqCollapse-14">
                                        <span class="badge">&nbsp;</span>How do you measure the ROI on StarzPlay?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-14" class="collapse" aria-labelledby="faqHeading-14" data-parent="#accordion">
                                <div class="card-body">
                                    <p>We track brand lift and attribution, showing a direct correlation between your ad flight and increases in web traffic or app installs.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

          </div>

        </section>


    </main>



@endsection

@section('addScript')

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Why should I choose StarzPlay over other streaming platforms?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "StarzPlay has a massive footprint in the MENA region and exclusive sports rights (like Serie A). It's perfect for brands targeting a regional or sports-focused audience."
      }
    },
    {
      "@type": "Question",
      "name": "Can I target specific countries within the StarzPlay network?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes, we can geo-fence your campaign to specific countries (e.g., UAE only or KSA only) to ensure your local offers are relevant."
      }
    },
    {
      "@type": "Question",
      "name": "Are there 'Live Sports' ad opportunities on StarzPlay?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes, we can place your brand during high-stakes live football matches and sporting events, which offer some of the highest engagement rates in digital media."
      }
    },
    {
      "@type": "Question",
      "name": "What ad formats are available on StarzPlay?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Options include pre-roll (before the content), mid-roll (during breaks), and branded 'pauses' where your ad appears when a user pauses their show."
      }
    },
    {
      "@type": "Question",
      "name": "How do you measure the ROI on StarzPlay?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "We track brand lift and attribution, showing a direct correlation between your ad flight and increases in web traffic or app installs."
      }
    }
  ]
}
</script>

@endsection