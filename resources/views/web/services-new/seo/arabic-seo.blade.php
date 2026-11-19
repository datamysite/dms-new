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
                        <h1>&#10687; Arabic SEO Services</h1>
                        <h2>Rank in Arabic Search Across the Gulf</h2>
                        <h4>Native Arabic Writers · RTL Pages · UAE &amp; Saudi Arabia</h4>
                        <br>
                        <p>
                            Arabic SEO costs from AED 650 per page or AED 6,500 a month alongside an English campaign, and it's usually the least competitive traffic a UAE business can win. Most competitors translate their English pages with a tool, which Google treats as thin, so a properly written Arabic page often ranks in weeks. Here's how we research, write and build Arabic pages, and the dialect decisions that matter.
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
                                      <h2 class="form1-headline">Ready to Rank <span>in Arabic?</span></h2>
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
                                        <textarea class="form1-textarea" name="description" placeholder="Your website and the markets you want to reach (UAE, Saudi, wider Gulf)." required></textarea>
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
            <li><a href="/global-marketing/saudi-marketing">Marketing in Saudi Arabia</a></li>
            <li><a href="/seo-services/technical-seo">Technical SEO, including hreflang and RTL setup</a></li>
            </ul></section>
        </div>

        <!-- H2: Why translated pages don't rank -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">

                </div>
                <div class="col-lg-6">
                    <iframe class="service-iframe" src="https://www.youtube.com/embed/Kbxm_z_ScYU?si=589eks2setWiGTUc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <div class="col-lg-6 vertical-center">
                    <h2 class="h4">Why Translated Pages Don't Rank</h2>
                    <p>Most Arabic pages in the UAE are an afterthought: the English site is run through a translation tool and published. They almost never rank, for four reasons:</p>
                    <ul>
                        <li><strong>Tool translation</strong> — stiff, literal Arabic that reads as thin content to Google and as careless to the reader.</li>
                        <li><strong>Wrong search terms</strong> — Arabic searchers don't use a word-for-word translation of the English keyword, so the page targets a phrase nobody types.</li>
                        <li><strong>No Arabic keyword research</strong> — volumes, spellings and variants are never checked, so the page misses the term that actually brings traffic.</li>
                        <li><strong>Broken RTL layouts</strong> — icons pointing the wrong way, mixed-direction text and forms that don't mirror, which push visitors straight back to Google.</li>
                    </ul>
                </div>
            </div>

          </div>

        </section>


        <div class="section-striped">
                @include('web.includes.elements.clients')
        </div>


        <!-- H2: What Arabic SEO includes -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12">
                    <div class="row services gy-4">
                        <div class="col-lg-6">
                            <h2 class="h3">What Arabic SEO Includes</h2>
                            <ul>
                                <li><strong>Arabic keyword research</strong> — real Arabic search terms, volumes and spelling variants for the UAE and Saudi markets.</li>
                                <li><strong>Native writing</strong> — every page written from scratch by a native Arabic writer, never machine-translated.</li>
                                <li><strong>RTL templates</strong> — right-to-left layouts checked component by component.</li>
                                <li><strong>Hreflang</strong> — Arabic and English versions linked so neither is treated as a duplicate.</li>
                                <li><strong>Arabic schema</strong> — structured data in Arabic for your business, services and FAQs.</li>
                                <li><strong>Arabic Google Business Profile posts</strong> — so your map listing speaks to Arabic searchers too.</li>
                                <li><strong>Arabic links</strong> — mentions and links from Arabic-language news and industry sites.</li>
                            </ul>
                            <br>

                            <h5>Book Your Free SEO Audit — <a href="javascript:void(0)" class="scroll-up">Get Started Now</a></h5>
                        </div>
                        <div class="col-lg-1"></div>
                        <div class="col-lg-5">
                            <img src="{{URL::to('/public/seo2.jpg')}}" alt="Arabic SEO services for UAE and Saudi businesses" width="100%">
                        </div>
                    </div>

                </div>
            </div>

          </div>

        </section>

        <!-- H2: MSA or Gulf dialect? -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-12">
                    <h2 class="h3">MSA or Gulf Dialect?</h2>
                    <p>It's the first question every client asks. The short answer: Modern Standard Arabic (MSA) for your website, Gulf dialect for social media and ads.</p>

                    <div class="row services seo-need-block gy-4 mt-0">
                        <div class="col-lg-6 filled">
                          <i class="bi bi-file-earmark-text"></i>
                          <h4>Modern Standard Arabic — Web Pages</h4>
                          <p>People type searches in MSA, and Google indexes it consistently, so service and information pages are written in it. Example service page heading:</p>
                          <p lang="ar" dir="rtl"><strong>خدمات تحسين محركات البحث في دبي</strong></p>
                        </div>
                        <div class="col-lg-6">
                          <i class="bi bi-chat-dots"></i>
                          <h4>Gulf Dialect — Social and Ads</h4>
                          <p>Dialect feels personal and works where people scroll and listen: Instagram captions, ads and radio scripts. Example social caption:</p>
                          <p lang="ar" dir="rtl"><strong>تبي موقعك يطلع أول في قوقل؟</strong></p>
                        </div>
                    </div>

                    <br>
                    <p>Planning Arabic radio alongside your website? See our guide to <a href="/radio-advertising/arabic-stations">Arabic radio stations in the UAE</a>.</p>
                </div>
            </div>

          </div>

        </section>

        <!-- H2: Pricing -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-12">
                    <h2 class="h3">Arabic SEO Pricing</h2>
                    <p>Prices are in AED. Arabic is always quoted as a separate line so you can see exactly what the second language costs.</p>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Option</th>
                                    <th>Price</th>
                                    <th>What's included</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Per page</strong></td>
                                    <td>From AED 650</td>
                                    <td>Arabic keyword research, native writing, RTL check, hreflang and schema for one page</td>
                                </tr>
                                <tr>
                                    <td><strong>Monthly, with English SEO</strong></td>
                                    <td>AED 6,500 / month</td>
                                    <td>Ongoing Arabic pages, GBP posts and links as part of a bilingual <a href="/seo-services">SEO package</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

          </div>

        </section>

        <!-- H2: Example -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12">
                    <div class="row services gy-4">
                        <div class="col-lg-5">
                            <img src="{{URL::to('/public/seo3.png')}}" alt="Arabic page versus translated page ranking example" width="100%">
                        </div>
                        <div class="col-lg-7">
                            <h2 class="h3">Example: Written vs Translated</h2>
                            <p>Anonymised from a client in the UAE:</p>
                            <ul>
                                <li><strong>Translated page</strong> — The Arabic service page had been machine-translated from English. It targeted a word-for-word translation of the English keyword, which isn’t what Arabic-speaking customers actually search for, so it stayed buried in the results and brought in almost no traffic.</li>
                                <li><strong>What we did</strong> — We researched the Arabic terms people really use, rewrote the page from scratch in natural Arabic, fixed the right-to-left layout, and set up hreflang so Google showed the right language version to the right audience.</li>
                                <li><strong>Written page</strong> — The native Arabic page started ranking for the terms customers search, bringing in steady Arabic-speaking traffic and new enquiries.</li>
                            </ul>
                            <br>

                            <h5>See What Your Arabic Pages Could Do — <a href="javascript:void(0)" class="scroll-up">Get Started Now</a></h5>
                        </div>
                    </div>

                </div>
            </div>

          </div>

        </section>


        <section id="testimonials" class="testimonials case-studies-section section">
           @include('web.includes.elements.case-studies')
        </section>


        <!-- H2: Who writes it (EEAT) -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-6">
                    <h2 class="h3">Who Writes It</h2>
                    <p>Your Arabic pages are written by a named native writer who researches the keyword before writing a word, not by a translator working from your English copy.</p>
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
                    <img src="{{URL::to('/public/local-seo2.jpg')}}" alt="DataMySite Arabic SEO writers" width="100%">
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
                                        <span class="badge">&nbsp;</span>How much does Arabic SEO cost?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-1" class="collapse show" aria-labelledby="faqHeading-1" data-parent="#accordion">
                                <div class="card-body">
                                    <p>From AED 650 per page for research, writing and setup, or AED 6,500 a month as part of a bilingual SEO package. Arabic pages are quoted separately so you can see exactly what the second language costs.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-2">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-2" data-aria-expanded="false" data-aria-controls="faqCollapse-2">
                                        <span class="badge">&nbsp;</span>Should we write in Modern Standard Arabic or Gulf dialect?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-2" class="collapse" aria-labelledby="faqHeading-2" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Service and information pages in Modern Standard Arabic, because that's how people search and how Google indexes. Dialect belongs in social captions and radio scripts, not on web pages.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-3">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-3" data-aria-expanded="false" data-aria-controls="faqCollapse-3">
                                        <span class="badge">&nbsp;</span>Can we just translate our English pages?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-3" class="collapse" aria-labelledby="faqHeading-3" data-parent="#accordion">
                                <div class="card-body">
                                    <p>You can, but they rarely rank. Arabic searchers use different terms, and tool translations read as thin content. We research the Arabic keyword first and write the page from scratch.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-4">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-4" data-aria-expanded="false" data-aria-controls="faqCollapse-4">
                                        <span class="badge">&nbsp;</span>Do you handle right-to-left layouts?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-4" class="collapse" aria-labelledby="faqHeading-4" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes. We set the RTL templates, check every component mirrors correctly, and make sure the Arabic and English versions are linked with hreflang so neither is seen as a duplicate.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-5">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-5" data-aria-expanded="false" data-aria-controls="faqCollapse-5">
                                        <span class="badge">&nbsp;</span>Does Arabic SEO work for Saudi Arabia too?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-5" class="collapse" aria-labelledby="faqHeading-5" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes, and it's where it pays most. Saudi search volume is larger and more Arabic-heavy than the UAE. We run KSA campaigns through our <a href="/global-marketing/saudi-marketing">Saudi marketing team</a> with the same writers.</p>
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
                    <h3>Win the Arabic Searches Your Competitors Ignore</h3>
                    <br>

                    <div class="row services gy-4">
                        <div class="col-lg-7">
                            <p>Arabic search in the UAE and Saudi Arabia is large, and most of your competitors are still showing it a machine translation. A properly researched, natively written Arabic page is often the fastest ranking win available. Run it on its own or alongside your English <a href="/seo-services">SEO campaign</a>.</p>
                            {{-- NP-018 Arabic websites (W4): add "need a full Arabic website? see our Arabic web design" link here once that page is live --}}

                            <p><strong>Your journey starts with a free consultation.</strong></p>

                            @include('web.includes.elements.contact')
                        </div>

                        <div class="col-lg-5">

                            <div class="web-form-div">
                                <h4>Expanding Into Saudi Arabia?</h4>
                                <p>Arabic SEO pays most in KSA, where search volume is larger and more Arabic-heavy. See how we run <a href="/global-marketing/saudi-marketing">marketing in Saudi Arabia</a>.</p>

                                <h5>Want to rank in Arabic? <br><a href="javascript:void(0)" class="scroll-up">Let’s Talk</a>!</h5>
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
           "name": "How much does Arabic SEO cost?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "From AED 650 per page for research, writing and setup, or AED 6,500 a month as part of a bilingual SEO package. Arabic pages are quoted separately so you can see exactly what the second language costs."
           }
          },
          {
           "@type": "Question",
           "name": "Should we write in Modern Standard Arabic or Gulf dialect?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Service and information pages in Modern Standard Arabic, because that's how people search and how Google indexes. Dialect belongs in social captions and radio scripts, not on web pages."
           }
          },
          {
           "@type": "Question",
           "name": "Can we just translate our English pages?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "You can, but they rarely rank. Arabic searchers use different terms, and tool translations read as thin content. We research the Arabic keyword first and write the page from scratch."
           }
          },
          {
           "@type": "Question",
           "name": "Do you handle right-to-left layouts?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes. We set the RTL templates, check every component mirrors correctly, and make sure the Arabic and English versions are linked with hreflang so neither is seen as a duplicate."
           }
          },
          {
           "@type": "Question",
           "name": "Does Arabic SEO work for Saudi Arabia too?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes, and it's where it pays most. Saudi search volume is larger and more Arabic-heavy than the UAE. We run KSA campaigns through our Saudi marketing team with the same writers."
           }
          }
         ]
        }
    </script>

    @include('web.includes.elements.local-schema')
@endsection