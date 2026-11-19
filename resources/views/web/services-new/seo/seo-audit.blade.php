{{--
    NP-002  /seo-services/seo-audit  (Model E)
    TITLE TAG:  SEO Audit in Dubai: What We Check and What It Costs
    META DESC:  SEO audit in Dubai: crawl, indexation, Core Web Vitals, content, links and competitors, delivered as a per-URL fix list. Free 15-point audit first.
    Keywords:   seo audit dubai (600) · free seo audit (150) · technical seo audit (100)
    Before publishing: replace every [rate card], [case files] and [team] placeholder.
--}}
@extends('web.includes.master')

@section('addStyle')
    <link href="{{URL::to('/public')}}/assets/css/service.css" rel="stylesheet">
@endsection
@section('content')

    <main class="main">

        <!-- Page Title -->
          <div class="page-title dark-background" data-aos="fade" style="background-image: url('{{URL::to('public/assets/img/service-new/'.$service->slug.'.jpg')}}');">
            <div class="container head-container-service">
                <div class="row">
                    <div class="col-lg-8 head-para-service">
                        <h1>&#10687; SEO Audit in Dubai: What We Check and What It Costs</h1>
                        <h2>A Per-URL Fix List, Not a 60-Page PDF</h2>
                        <h4>Free 15-Point Audit First · Full Audit in 5–10 Working Days</h4>
                        <br>
                        <p>
                            A full SEO audit in Dubai costs from AED 3,000 for a site under 500 pages, and we run a free 15-point version first so you can see whether the full audit is worth it. An audit is only useful if it ends in a fix list with owners and dates, so ours does: every issue line by line, per URL, with the exact change to make. What we check, what you get and how long it takes are below.
                        </p>
                        <span><strong>Get a Free SEO Audit → · <a href="https://api.whatsapp.com/send/?phone=971525287259" target="_blank">WhatsApp: +971 52 528 7259</a></strong></span>
                    </div>
                    <div class="col-lg-4">
                        <div class="aside-form">

                            <form action="{{route('enquiry.submit')}}" id="enquiry-form">
                                @csrf
                                <input type="hidden" name="service" value="{{$service->name}}" required>
                                <div class="form1-wrapper">
                                    <div class="form1-bg">
                                      <h2 class="form1-headline">Get Your Free <span>15-Point Audit</span></h2>
                                      <p class="form1-sub">Send your website address — our experts respond within 60 minutes.</p>

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
                                        <textarea class="form1-textarea" name="description" placeholder="Your website address and the keywords you want to rank for." required></textarea>
                                      </div>

                                      <button class="form1-btn" type="submit">
                                        Get My Free Audit
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


        <div class="container">
            <section class="hub-guides"><h2>SEO insights</h2><ul>
            <li><a href="/seo-services">SEO services across the UAE</a></li>
            <li><a href="/why-website-not-ranking-dubai">Why your website is not ranking in Dubai</a></li>
            <li><a href="/seo-services/price-and-package">SEO packages and prices</a></li>
            </ul></section>
        </div>

        <!-- H2: The free 15-point audit -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">

                </div>
                <div class="col-lg-6">
                    <iframe class="service-iframe" src="https://www.youtube.com/embed/Kbxm_z_ScYU?si=589eks2setWiGTUc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <div class="col-lg-6 vertical-center">
                    <h2 class="h4">The Free 15-Point Audit</h2>
                    <p>
                        The free audit checks the fifteen things that most often stop UAE websites from ranking. It takes two working days and comes back as a short list of what's wrong and how serious it is. It covers:
                    </p>
                    <ul>
                        <li><strong>Indexation</strong> — whether Google can find and index your key pages.</li>
                        <li><strong>Titles and meta descriptions</strong> — missing, duplicated or off-target.</li>
                        <li><strong>Speed</strong> — Core Web Vitals on your main templates.</li>
                        <li><strong>Mobile</strong> — layout, tap targets and mobile-only errors.</li>
                        <li><strong>Schema</strong> — whether your business, services and FAQs are marked up.</li>
                        <li><strong>Top-10 keyword gaps</strong> — the searches your competitors rank for and you don't.</li>
                    </ul>
                    <p>It tells you, honestly, whether a full audit will find enough to pay for itself.</p>
                    <br>
                    <h5>Get Your Free 15-Point Audit — <a href="javascript:void(0)" class="scroll-up">Send Your URL</a></h5>
                </div>
            </div>

          </div>

        </section>


        <div class="section-striped">
                @include('web.includes.elements.clients')
        </div>


        <!-- H2: The full audit: 120 checks in six areas -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-12">
                    <h2 class="h3">The Full Audit: 120 Checks in Six Areas</h2>
                    <p>The full audit goes through your whole site, page by page. Each area below includes an example of an issue we regularly find on Dubai websites.</p>

                    <div class="row services seo-need-block gy-4 mt-0">
                        <div class="col-lg-4 filled">
                          <i class="bi bi-diagram-3"></i>
                          <h4>Crawl and Indexation</h4>
                          <p>Example: service pages blocked by a leftover noindex tag from the staging site.</p>
                        </div>
                        <div class="col-lg-4">
                          <i class="bi bi-speedometer2"></i>
                          <h4>Technical and Speed</h4>
                          <p>Example: a 4 MB hero image pushing mobile load time past five seconds.</p>
                        </div>
                        <div class="col-lg-4 filled">
                          <i class="bi bi-file-earmark-text"></i>
                          <h4>On-Page</h4>
                          <p>Example: twenty pages sharing the same title tag and H1.</p>
                        </div>
                    </div>
                    <div class="row services split-block seo-need-block gy-4 mt-0">
                        <div class="col-lg-4">
                          <i class="bi bi-intersect"></i>
                          <h4>Content and Cannibalisation</h4>
                          <p>Example: a blog post and a service page competing for the same keyword, so neither ranks.</p>
                        </div>
                        <div class="col-lg-4 filled">
                          <i class="bi bi-link-45deg"></i>
                          <h4>Links and Toxicity</h4>
                          <p>Example: hundreds of spam directory links bought by a previous agency.</p>
                        </div>
                        <div class="col-lg-4">
                          <i class="bi bi-bar-chart-line"></i>
                          <h4>Competitors</h4>
                          <p>Example: a rival ranking with area pages for Marina, JLT and Business Bay that you don't have.</p>
                        </div>
                    </div>

                    <br>
                    <p>If you only need the technical side, for example after a migration or redesign, see our <a href="/seo-services/tech-support">technical SEO service</a>.</p>
                    {{-- NP-003 technical SEO: swap the link above to the new page once it is live --}}
                </div>
            </div>

          </div>

        </section>

        <!-- H2: What you get -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12">
                    <div class="row services gy-4">
                        <div class="col-lg-6">
                            <h2 class="h3">What You Get</h2>
                            <p>No slide deck full of screenshots. You get a working document your team can act on the same day:</p>
                            <ul>
                                <li><strong>A line-by-line fix list per URL</strong> — every issue, the page it's on and the exact change to make.</li>
                                <li><strong>Priorities by deadline</strong> — each fix marked 7, 30 or 90 days by impact and effort.</li>
                                <li><strong>Developer notes</strong> — written so any developer can implement without guessing.</li>
                                <li><strong>A keyword-to-page map</strong> — one target keyword per page, so pages stop competing with each other.</li>
                                <li><strong>A 90-day plan</strong> — what to fix, write and build, in order.</li>
                            </ul>
                            <p>Everything is delivered in a shared sheet, plus a call to walk your developer through it.</p>
                            <br>

                            <h5>Start With the Free Audit — <a href="javascript:void(0)" class="scroll-up">Get Started Now</a></h5>
                        </div>
                        <div class="col-lg-1"></div>
                        <div class="col-lg-5">
                            <img src="{{URL::to('/public/seo2.jpg')}}" alt="SEO audit fix list for a Dubai website" width="100%">
                        </div>
                    </div>

                </div>
            </div>

          </div>

        </section>

        <!-- H2: Pricing and timing -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-12">
                    <h2 class="h3">SEO Audit Pricing and Timing</h2>
                    <p>Prices are in AED and depend on site size and languages. If you go on to an SEO retainer, the audit fee is credited against your first month. See all <a href="/seo-services/price-and-package">SEO packages and prices</a>.</p>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Site size</th>
                                    <th>Price</th>
                                    <th>Delivery</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>15-point audit</strong> (any size)</td>
                                    <td>Free</td>
                                    <td>2 working days</td>
                                </tr>
                                <tr>
                                    <td><strong>Under 500 pages</strong></td>
                                    <td>AED 3,000</td>
                                    <td>5 working days</td>
                                </tr>
                                <tr>
                                    <td><strong>500–5,000 pages</strong></td>
                                    <td>AED 8,000</td>
                                    <td>10 working days</td>
                                </tr>
                                <tr>
                                    <td><strong>5,000+ pages</strong></td>
                                    <td>AED 10,000</td>
                                    <td>20 working days</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p>Bilingual English and Arabic sites take ten working days regardless of size.</p>
                </div>
            </div>

          </div>

        </section>

        <!-- H2: A sample fix from a real audit -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12">
                    <div class="row services gy-4">
                        <div class="col-lg-5">
                            <img src="{{URL::to('/public/seo3.png')}}" alt="Sample fix from a Dubai SEO audit" width="100%">
                        </div>
                        <div class="col-lg-7">
                            <h2 class="h3">A Sample Fix From a Real Audit</h2>
                            <p>Anonymised from a recent audit for a business in Dubai:</p>
                            <ul>
                                <li><strong>Issue:</strong> Several pages were targeting the same main keyword, so they competed with each other and none of them ranked well.</li>
                                <li><strong>Impact:</strong> The client’s most important service was hard to find on Google, which meant lost traffic and fewer enquiries.</li>
                                <li><strong>Fix:</strong> We merged the overlapping pages into one strong page, redirected the old URLs to it, and gave each remaining page its own clear keyword focus.</li>
                                <li><strong>Result:</strong> With one clear page to rank, Google could show the right result, and the main service page began to climb and bring in more leads.</li>
                            </ul>
                            <p>Every fix in your audit is written in this same format, so you know what it costs to do and what it should change.</p>
                            <br>

                            <h5>See What Your Site Needs — <a href="javascript:void(0)" class="scroll-up">Get the Free Audit</a></h5>
                        </div>
                    </div>

                </div>
            </div>

          </div>

        </section>


        <section id="testimonials" class="testimonials case-studies-section section">
           @include('web.includes.elements.case-studies')
        </section>


        <!-- H2: Who does the audit (EEAT) -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-6">
                    <h2 class="h3">Who Does the Audit</h2>
                    <p>Your audit is done by named specialists, not exported from a tool and sent as-is. Tools do the crawling; people decide what matters and write the fixes.</p>
                    <br>

                    <ul>
                      <li><strong>Dedicated UAE SEO Lead</strong><br>
                        A senior strategist with years of hands-on UAE market experience plans and oversees your campaign.</li>
                        <li><strong>Native Arabic Content Specialist</strong><br>
                        Researches Arabic keywords first, then writes each page from scratch. No translations.</li>
                        <li><strong>Technical SEO Specialist</strong><br>
                        Owns fixes, speed and indexing, with every change recorded in your change log.</li>
                        <li><strong>100% White-Hat Practices</strong><br>
                        Ethical, sustainable methods that protect your site long term.</li>
                    </ul>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-5">
                    <img src="{{URL::to('/public/local-seo2.jpg')}}" alt="DataMySite SEO audit team in Dubai" width="100%">
                </div>
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section section  pr-blogs-section">

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


        <section id="service-cards" class="blog-section section faq-new-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12 vertical-center">
                    <h2 class="h4 text-center">Frequently Asked Questions</h2>
                    <br>
                    <div class="faq " id="accordion">
                        <div class="card">
                            <div class="card-header" id="faqHeading-1">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-1" data-aria-expanded="true" data-aria-controls="faqCollapse-1">
                                        <span class="badge">&nbsp;</span>How much does an SEO audit cost in Dubai?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-1" class="collapse show" aria-labelledby="faqHeading-1" data-parent="#accordion">
                                <div class="card-body">
                                    <p>The 15-point audit is free. The full audit starts from AED 3,000 for sites under 500 pages and scales with size and languages. The fee is credited against the first month if you go on to a retainer.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-2">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-2" data-aria-expanded="false" data-aria-controls="faqCollapse-2">
                                        <span class="badge">&nbsp;</span>Is a free SEO audit worth anything?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-2" class="collapse" aria-labelledby="faqHeading-2" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Ours checks the fifteen things that most often block rankings in the UAE: indexation, titles, speed, mobile, schema and your biggest keyword gaps. It tells you whether a full audit will find enough to pay for itself.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-3">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-3" data-aria-expanded="false" data-aria-controls="faqCollapse-3">
                                        <span class="badge">&nbsp;</span>What is the difference between a technical SEO audit and a full audit?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-3" class="collapse" aria-labelledby="faqHeading-3" data-parent="#accordion">
                                <div class="card-body">
                                    <p>A technical audit covers crawling, indexation, speed, mobile and code. The full audit adds content, cannibalisation, links and competitors. Most Dubai sites need both; a technical-only audit is right after a migration or redesign.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-4">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-4" data-aria-expanded="false" data-aria-controls="faqCollapse-4">
                                        <span class="badge">&nbsp;</span>How long does an audit take?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-4" class="collapse" aria-labelledby="faqHeading-4" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Five working days for sites under 500 pages, ten for larger or bilingual sites. You get the fix list in a shared sheet, plus a call to walk your developer through it.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-5">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-5" data-aria-expanded="false" data-aria-controls="faqCollapse-5">
                                        <span class="badge">&nbsp;</span>Will you fix the issues too?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-5" class="collapse" aria-labelledby="faqHeading-5" data-parent="#accordion">
                                <div class="card-body">
                                    <p>If you want. The audit is designed so any developer can act on it, but most clients ask us to run the fixes as part of an SEO retainer so one team owns the result.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

          </div>

        </section>

        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12">
                    <h3>Find Out What's Holding Your Site Back</h3>
                    <br>

                    <div class="row services gy-4">
                        <div class="col-lg-7">
                            <p>Most sites we audit have three or four issues doing most of the damage. The free audit shows you which ones, and the full audit tells you exactly how to fix them. When you're ready, our <a href="/seo-services">SEO services</a> team can run the fixes for you.</p>

                            <p><strong>Your journey starts with a free 15-point audit.</strong></p>

                            @include('web.includes.elements.contact')
                        </div>

                        <div class="col-lg-5">

                            <div class="web-form-div">
                                <h4>SEO Is About Leads</h4>
                                <p>An audit should end in more enquiries, not just cleaner code. See what a lead should cost you with our <a href="/lead-generation/cost-per-lead">cost-per-lead guide</a>, or read <a href="/how-to-get-leads-for-real-estate-in-dubai-full-guide">how to get real estate leads in Dubai</a>.</p>

                                <h5>Want to know what's blocking your rankings? <br><a href="javascript:void(0)" class="scroll-up">Get the Free Audit</a>!</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

          </div>

        </section>
    </main>
    <script type="application/ld+json">
        {
         "@context": "https://schema.org",
         "@type": "FAQPage",
         "mainEntity": [
          {
           "@type": "Question",
           "name": "How much does an SEO audit cost in Dubai?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "The 15-point audit is free. The full audit starts from AED 3,000 for sites under 500 pages and scales with size and languages. The fee is credited against the first month if you go on to a retainer."
           }
          },
          {
           "@type": "Question",
           "name": "Is a free SEO audit worth anything?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Ours checks the fifteen things that most often block rankings in the UAE: indexation, titles, speed, mobile, schema and your biggest keyword gaps. It tells you whether a full audit will find enough to pay for itself."
           }
          },
          {
           "@type": "Question",
           "name": "What is the difference between a technical SEO audit and a full audit?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "A technical audit covers crawling, indexation, speed, mobile and code. The full audit adds content, cannibalisation, links and competitors. Most Dubai sites need both; a technical-only audit is right after a migration or redesign."
           }
          },
          {
           "@type": "Question",
           "name": "How long does an audit take?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Five working days for sites under 500 pages, ten for larger or bilingual sites. You get the fix list in a shared sheet, plus a call to walk your developer through it."
           }
          },
          {
           "@type": "Question",
           "name": "Will you fix the issues too?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "If you want. The audit is designed so any developer can act on it, but most clients ask us to run the fixes as part of an SEO retainer so one team owns the result."
           }
          }
         ]
        }
    </script>

    @include('web.includes.elements.local-schema')
@endsection