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
                        <h1>&#10687; Technical SEO Services in Dubai</h1>
                        <h2>Fix the Site Before You Scale</h2>
                        <h4>Per-URL Fix Lists · Code for Your Developer · Per Project or Monthly</h4>
                        <br>
                        <p>
                            Technical SEO fixes in Dubai run from AED 3,500 for a one-off project to AED 7,000 a month as part of a retainer, and they're the cheapest ranking gains most sites will ever get because the content already exists. Slow templates, duplicate pages, broken redirects and missing schema quietly cap how high a site can rank, whatever you publish. Here's what we fix, how we work with your developer and what it costs.
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
                                        <textarea class="form1-textarea" name="description" placeholder="Your website address, platform (Laravel, WordPress, Shopify…) and what's going wrong." required></textarea>
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


        <div class="container">
            <section class="hub-guides"><h2>SEO insights</h2><ul>
            <li><a href="/seo-services">SEO services across the UAE</a></li>
            <li><a href="/seo-services/seo-audit">SEO audit: what we check and what it costs</a></li>
            <li><a href="/why-website-not-ranking-dubai">Why your website is not ranking in Dubai</a></li>
            </ul></section>
        </div>

        <!-- H2: What technical SEO covers -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">

                </div>
                <div class="col-lg-6">
                    <iframe class="service-iframe" src="https://www.youtube.com/embed/Kbxm_z_ScYU?si=589eks2setWiGTUc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <div class="col-lg-6 vertical-center">
                    <h2 class="h4">What Technical SEO Covers</h2>
                    <p>Technical SEO is everything about how your site is built and served to Google. If it's broken, good content can't rank. We cover:</p>
                    <ul>
                        <li><strong>Crawl and indexation</strong> — robots.txt, sitemaps, noindex tags and orphan pages, so Google finds the pages you want found.</li>
                        <li><strong>Site speed and Core Web Vitals</strong> — image weight, render-blocking scripts, caching and server response.</li>
                        <li><strong>Mobile</strong> — rendering, layout shift and tap targets on the phones your customers use.</li>
                        <li><strong>Redirects and canonicals</strong> — redirect chains, 404s and duplicate URLs consolidated.</li>
                        <li><strong>Schema</strong> — structured data for your business, services, FAQs and reviews.</li>
                        <li><strong>International and Arabic setup</strong> — hreflang, right-to-left templates and language URLs.</li>
                        <li><strong>Migrations</strong> — URL mapping, redirects and monitoring when you redesign or replatform.</li>
                    </ul>
                </div>
            </div>

          </div>

        </section>


        <div class="section-striped">
                @include('web.includes.elements.clients')
        </div>


        <!-- H2: On-page SEO -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12">
                    <div class="row services gy-4">
                        <div class="col-lg-6">
                            <h2 class="h3">On-Page SEO</h2>
                            <p>On-page SEO is what's on each page: the words, structure and links Google reads to decide what the page is about. Technical SEO makes a page reachable; on-page SEO makes it relevant. We work on:</p>
                            <ul>
                                <li><strong>Titles and meta descriptions</strong> — one target keyword per page, written to be clicked.</li>
                                <li><strong>Headings</strong> — one H1, logical H2s that match what people search.</li>
                                <li><strong>Internal links</strong> — descriptive anchors that point authority to your money pages.</li>
                                <li><strong>Content structure</strong> — answer-first intros, FAQs and sections that cover the topic fully.</li>
                            </ul>
                            <p>A page-level fix list shows each URL with its current title, H1 and target keyword, then the exact replacement for each, so nothing is left to interpretation.</p>
                            <br>

                            <h5>Book Your Free SEO Audit — <a href="javascript:void(0)" class="scroll-up">Get Started Now</a></h5>
                        </div>
                        <div class="col-lg-1"></div>
                        <div class="col-lg-5">
                            <img src="{{URL::to('/public/seo2.jpg')}}" alt="On-page and technical SEO in Dubai" width="100%">
                        </div>
                    </div>

                </div>
            </div>

          </div>

        </section>

        <!-- H2: How we work with your developer -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-12">
                    <h2 class="h3">How We Work With Your Developer</h2>
                    <p>Most technical SEO fails at handover: a vague report lands on a developer's desk and nothing changes. We write fixes so your developer can ship them, or we ship them ourselves.</p>

                    <div class="row services seo-need-block gy-4 mt-0">
                        <div class="col-lg-3 filled">
                          <i class="bi bi-list-check"></i>
                          <h4>Fix List per URL</h4>
                          <p>Every issue, the URL it's on, the priority and the exact change.</p>
                        </div>
                        <div class="col-lg-3">
                          <i class="bi bi-code-slash"></i>
                          <h4>Code Snippets</h4>
                          <p>Ready-to-use code for Laravel, WordPress and Shopify, or step-by-step instructions.</p>
                        </div>
                        <div class="col-lg-3 filled">
                          <i class="bi bi-shield-check"></i>
                          <h4>Staging First</h4>
                          <p>Changes go to staging and a sample of pages is tested before anything goes live.</p>
                        </div>
                        <div class="col-lg-3">
                          <i class="bi bi-journal-text"></i>
                          <h4>Change Log</h4>
                          <p>Every fix is dated and logged, so ranking changes can be traced to the work.</p>
                        </div>
                    </div>

                    <br>
                    <p>Not sure what needs fixing yet? Start with an <a href="/seo-services/seo-audit">SEO audit</a>: it produces the fix list this work runs from.</p>
                </div>
            </div>

          </div>

        </section>

        <!-- H2: Pricing -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-12">
                    <h2 class="h3">Technical SEO Pricing</h2>
                    <p>Prices are in AED and depend on site size and platform. Ongoing technical and on-page work is included in our monthly <a href="/seo-services">SEO packages</a>.</p>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Price</th>
                                    <th>Best for</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>One-off technical project</strong></td>
                                    <td>From AED 10,000</td>
                                    <td>Fixing a known set of issues, usually after an audit</td>
                                </tr>
                                <tr>
                                    <td><strong>Monthly technical &amp; on-page</strong></td>
                                    <td>AED 3,500 / month</td>
                                    <td>Sites that publish often or change templates regularly</td>
                                </tr>
                                <tr>
                                    <td><strong>Migration support</strong></td>
                                    <td>From AED 3,000</td>
                                    <td>Redesigns, replatforming and domain changes</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

          </div>

        </section>

        <!-- H2: Example: what fixing one template did -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12">
                    <div class="row services gy-4">
                        <div class="col-lg-5">
                            <img src="{{URL::to('/public/seo3.png')}}" alt="Technical SEO template fix example" width="100%">
                        </div>
                        <div class="col-lg-7">
                            <h2 class="h3">Example: What Fixing One Template Did</h2>
                            <p>Fixing a template fixes every page built on it, which is why technical work pays back so fast. Anonymised from a recent client in Dubai:</p>
                            <ul>
                                <li><strong>Before</strong> — Product pages loaded slowly on mobile, had no structured data, and many of them weren’t being indexed by Google.</li>
                                <li><strong>The fix</strong> — We compressed and resized images, deferred non-essential scripts, and added Product schema so Google could understand each page properly.</li>
                                <li><strong>After</strong> — Pages loaded noticeably faster on mobile, more product pages were indexed, and more of them began showing in search and bringing in enquiries.</li>
                            </ul>
                            <br>

                            <h5>See What Your Templates Are Costing You — <a href="javascript:void(0)" class="scroll-up">Get Started Now</a></h5>
                        </div>
                    </div>

                </div>
            </div>

          </div>

        </section>


        <section id="testimonials" class="testimonials case-studies-section section">
           @include('web.includes.elements.case-studies')
        </section>


        <!-- H2: Who you'll work with (EEAT) -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-6">
                    <h2 class="h3">Who You'll Work With</h2>
                    <p>Technical SEO is done by people who can read and write code, not just run a crawler. Our own site runs on Laravel, so we fix frameworks from the inside.</p>
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
                    <img src="{{URL::to('/public/local-seo2.jpg')}}" alt="DataMySite technical SEO team in Dubai" width="100%">
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
                                        <span class="badge">&nbsp;</span>What does technical SEO include?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-1" class="collapse show" aria-labelledby="faqHeading-1" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Crawling and indexation, site speed and Core Web Vitals, mobile rendering, redirects and canonical tags, structured data, hreflang for Arabic and English, and migration planning. Everything is delivered as a per-URL fix list with code where needed.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-2">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-2" data-aria-expanded="false" data-aria-controls="faqCollapse-2">
                                        <span class="badge">&nbsp;</span>What is the difference between technical and on-page SEO?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-2" class="collapse" aria-labelledby="faqHeading-2" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Technical SEO is how the site is built and served; on-page SEO is what's on each page: titles, headings, copy, links and schema. Most sites need a technical pass first, then on-page work page by page.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-3">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-3" data-aria-expanded="false" data-aria-controls="faqCollapse-3">
                                        <span class="badge">&nbsp;</span>How much do technical SEO services cost in Dubai?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-3" class="collapse" aria-labelledby="faqHeading-3" data-parent="#accordion">
                                <div class="card-body">
                                    <p>One-off technical projects start from AED 3,000 and depend on site size and platform. Ongoing technical and on-page work is included in our monthly SEO packages.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-4">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-4" data-aria-expanded="false" data-aria-controls="faqCollapse-4">
                                        <span class="badge">&nbsp;</span>Do you work with Laravel, WordPress and Shopify?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-4" class="collapse" aria-labelledby="faqHeading-4" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes. Our own site runs on Laravel, so we know the framework from the inside, and we deliver fixes for WordPress and Shopify as code snippets or step-by-step instructions so your developer isn't guessing. Other platforms are quoted after a quick technical look.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-5">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-5" data-aria-expanded="false" data-aria-controls="faqCollapse-5">
                                        <span class="badge">&nbsp;</span>Can you handle a website migration?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-5" class="collapse" aria-labelledby="faqHeading-5" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes. We map every URL, set the redirects, test on staging, and monitor rankings daily for the first two weeks after launch. Migrations without this lose 20 to 40 percent of traffic in our experience.</p>
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
                    <h3>Fix the Foundations, Then Scale</h3>
                    <br>

                    <div class="row services gy-4">
                        <div class="col-lg-7">
                            <p>Publishing more content on a broken site is like adding floors to a building with cracked foundations. We fix how your site is built first, so every page you publish afterwards has a fair chance to rank. Then our <a href="/seo-services">SEO services</a> team can scale content and links on top.</p>
                            {{-- NP-010 web development (W3): add an in-body link here once that page is live --}}

                            <p><strong>Your journey starts with a free SEO audit.</strong></p>

                            @include('web.includes.elements.contact')
                        </div>

                        <div class="col-lg-5">

                            <div class="web-form-div">
                                <h4>Technical SEO Is About Leads</h4>
                                <p>A faster, cleaner site should bring more enquiries, not just better scores. See what a lead should cost you with our <a href="/lead-generation/cost-per-lead">cost-per-lead guide</a>, or read <a href="/how-to-get-leads-for-real-estate-in-dubai-full-guide">how to get real estate leads in Dubai</a>.</p>

                                <h5>Want to know what's holding your site back? <br><a href="javascript:void(0)" class="scroll-up">Let’s Talk</a>!</h5>
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
           "name": "What does technical SEO include?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Crawling and indexation, site speed and Core Web Vitals, mobile rendering, redirects and canonical tags, structured data, hreflang for Arabic and English, and migration planning. Everything is delivered as a per-URL fix list with code where needed."
           }
          },
          {
           "@type": "Question",
           "name": "What is the difference between technical and on-page SEO?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Technical SEO is how the site is built and served; on-page SEO is what's on each page: titles, headings, copy, links and schema. Most sites need a technical pass first, then on-page work page by page."
           }
          },
          {
           "@type": "Question",
           "name": "How much do technical SEO services cost in Dubai?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "One-off technical projects start from AED 3,000 and depend on site size and platform. Ongoing technical and on-page work is included in our monthly SEO packages."
           }
          },
          {
           "@type": "Question",
           "name": "Do you work with Laravel, WordPress and Shopify?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes. Our own site runs on Laravel, so we know the framework from the inside, and we deliver fixes for WordPress and Shopify as code snippets or step-by-step instructions so your developer isn't guessing. Other platforms are quoted after a quick technical look."
           }
          },
          {
           "@type": "Question",
           "name": "Can you handle a website migration?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes. We map every URL, set the redirects, test on staging, and monitor rankings daily for the first two weeks after launch. Migrations without this lose 20 to 40 percent of traffic in our experience."
           }
          }
         ]
        }
    </script>

    @include('web.includes.elements.local-schema')
@endsection