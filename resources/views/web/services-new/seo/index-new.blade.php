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
                        <h1>&#10687; SEO Agency in Dubai That Reports in Leads, Not Just Rankings</h1>
                        <h2>#1 SEO Agency in Dubai</h2>
                        <h4>1st Page or Money Back Guaranteed</h4>
                        <br>
                        <p>
                            Most Dubai businesses pay between AED [rate card: low] and AED [rate card: high] a month for SEO, and the difference is almost always how many pages, languages and emirates the campaign has to cover. DataMysite runs SEO for UAE companies that want enquiries rather than vanity rankings: we map every keyword to a page, fix the technical base, write the pages in plain English and Arabic, build the links, and report leads every Monday. Below is what's included at each level, how long results take in the UAE, and what we'd do first on your site.
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

    
        <div class="container">
            <section class="hub-guides"><h2>SEO insights</h2><ul>
            <li><a href="/seo-agency-dubai-trends-2026">SEO trends in Dubai for 2026</a></li>
            <li><a href="/why-website-not-ranking-dubai">Why your website is not ranking in Dubai</a></li>
            <li><a href="/seo-services/price-and-package">SEO packages and prices</a></li>
            </ul></section>
        </div>

        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">
                    
                </div>
                <div class="col-lg-6">
                    <iframe class="service-iframe" src="https://www.youtube.com/embed/Kbxm_z_ScYU?si=589eks2setWiGTUc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <div class="col-lg-6 vertical-center">
                    <h4>Our SEO Services in Dubai Include:</h4>
                    <p>
                        Our SEO services in Dubai include keyword research, on-page optimization, technical SEO, content creation, link building, local SEO, and performance tracking — all tailored to boost your search rankings, drive traffic, and generate leads for your business.
                        <br>
                        As a trusted SEO company Dubai businesses rely on, we deliver SEO services that combine data, creativity, and proven strategies — all tailored to your unique goals.
                    </p>
                    <br>
                    <h4>Our SEO Services</h4>
                    <ul>
                        <li><a href="https://datamysite.com/seo-services/local-seo">Local SEO</a> — Google Maps & GBP rankings.</li>
                        <li><a href="https://datamysite.com/seo-services/ai-seo-optimization">AI SEO optimisation</a> — visibility in AI search.</li>
                        <li><a href="https://datamysite.com/seo-services/tech-support">Technical SEO support</a> — fix what blocks rankings.</li>
                        <li><a href="https://datamysite.com/seo-services/free-seo-audit">Free SEO audit</a> and transparent <a href="https://datamysite.com/seo-services/price-and-package">pricing</a>.</li>

                    </ul>
                </div>  
            </div>

          </div>

        </section>


        <div class="section-striped">
                @include('web.includes.elements.clients')
        </div>



        <section id="testimonials" class="testimonials case-studies-section section">
           @include('web.includes.elements.case-studies')
        </section>


        <section id="service-cards" class="blog-section section-striped section ">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12">
                    <div class="row services gy-4">
                        <div class="col-lg-6">
                            <h3>Why SEO Matters for UAE Businesses</h3>
                            <p>Every day, thousands of potential customers search for products and services just like yours. Without a solid SEO strategy, your website risks being invisible while your competitors claim top spots and valuable traffic.</p>
                            <br>
                            <p>Effective SEO bridges the gap between your business and your audience. Whether you run a small local shop, an e-commerce store, or a multi-location enterprise, ranking higher in search results means more leads, more sales, and more growth.</p>
                            <br>

                            <h5>Book Your Free SEO Audit — <a href="javascript:void(0)" class="scroll-up">Get Started Now</a></h5>
                        </div>
                        <div class="col-lg-1"></div>
                        <div class="col-lg-5">
                            <img src="{{URL::to('/public/seo2.jpg')}}" alt="Why Local SEO Matters for Your Business in Dubai" width="100%">
                        </div>
                    </div>

                </div>
            </div>

          </div>

        </section>

        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12">
                    <div class="row services gy-4">
                        <div class="col-lg-5">
                            <img src="{{URL::to('/public/seo3.png')}}" alt="Why Local SEO Matters for Your Business in Dubai" width="100%">
                        </div>
                        <div class="col-lg-7">
                            <h3>What Makes DataMySite Different?</h3>
                            <p>
                                We’re not your average SEO agency Dubai has to offer — we go beyond rankings and traffic. Our mission is to help you achieve sustainable, long-term growth with customized SEO solutions.
                                <br><br>
                                When you choose DataMySite, you get:
                            </p>
                            <ul>
                                <li><strong>Proven Results</strong> — We don’t just promise. We deliver.</li>
                                <li><strong>Transparent Reporting</strong> — Know exactly how your campaigns are performing.</li>
                                <li><strong>Custom Strategies</strong> — One-size-fits-all? Not here.</li>
                                <li><strong>Dedicated SEO Experts</strong> — Your business deserves undivided attention.</li>
                                <li><strong>UAE-Focused Approach</strong> — Local SEO expertise for Dubai’s competitive market.</li>
                            </ul>
                            <br>

                            <h5>Book Your Free SEO Audit — <a href="javascript:void(0)" class="scroll-up">Get Started Now</a></h5>
                        </div>
                    </div>

                </div>
            </div>

          </div>

        </section>

         <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-12">
                    <h3>We Work With:</h3>
                    <p>Whether you're a local entrepreneur or an international brand expanding to the UAE, we offer web development services that match your needs and goals.</p>

                    <div class="row services seo-need-block gy-4 mt-0">
                        <div class="col-lg-3 filled">
                          <i class="bi bi-house-heart"></i>
                          <h4>Real Estate Agencies</h4>
                        </div>
                        <div class="col-lg-3 ">
                          <i class="bi bi-hospital"></i>
                          <h4>Clinics & Healthcare Providers</h4>
                        </div>
                        <div class="col-lg-3 filled">
                          <i class="bi bi-shop-window"></i>
                          <h4>Restaurants & Cafes</h4>
                        </div>
                        <div class="col-lg-3 ">
                          <i class="bi bi-cart-check"></i>
                          <h4>E-Commerce Stores</h4>
                        </div>
                    </div>
                    <div class="row services split-block seo-need-block gy-4 mt-0">
                        <div class="col-lg-3">
                          <i class="bi bi-book"></i>
                          <h4>Educational Institutions</h4>
                        </div>
                        <div class="col-lg-3 filled">
                          <i class="bi bi-luggage"></i>
                          <h4>Travel & Tourism Agencies</h4>
                        </div>
                        <div class="col-lg-3">
                          <i class="bi bi-buildings"></i>
                          <h4>Corporate Enterprises</h4>
                        </div>
                        <div class="col-lg-3 filled">
                          <i class="bi bi-building"></i>
                          <h4>Startup Companies</h4>
                        </div>
                    </div>
                    
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

         <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-6">
                    <h3>Why Dubai Businesses Trust Us</h3>
                    <p>We deliver real results, clear communication, and custom digital solutions tailored to Dubai’s competitive market—helping local brands grow and stand out online.</p>
                    <br>

                    <ul>
                      <li><strong>9/10 Clients Renew</strong><br> because they see results that matter.</li>
                      <li><strong>Experienced SEO Specialists</strong><br> our team stays ahead of Google’s latest updates.</li>
                      <li><strong>100% White-Hat Practices</strong><br> we use ethical, sustainable SEO methods that protect your site long term.</li>
                      <li><strong>Flexible Packages</strong><br> we work with your budget, goals, and timelines.</li>                    
                    </ul>
                    
                    <br>
                    <p>We don’t just claim to be the best — we back it up with data and results.</p>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-5">
                    <img src="{{URL::to('/public/local-seo2.jpg')}}" alt="Why Choose DataMySite as Your Local SEO Agency in Dubai?" width="100%">
                </div>
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section section faq-new-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12 vertical-center">
                    <h4 class="text-center">Frequently Asked Questions</h4>
                    <br>
                    <div class="faq " id="accordion">
                        <div class="card">
                            <div class="card-header" id="faqHeading-1">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-1" data-aria-expanded="true" data-aria-controls="faqCollapse-1">
                                        <span class="badge">&nbsp;</span>How much does SEO cost in Dubai?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-1" class="collapse show" aria-labelledby="faqHeading-1" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Expect AED 3,000 to AED 25,000 a month. The low end covers one site, one language and around 20 keywords; the high end covers multi-emirate, bilingual campaigns with monthly content and links. Our packages are priced in AED with no lock-in contract.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-2">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-2" data-aria-expanded="false" data-aria-controls="faqCollapse-2">
                                        <span class="badge">&nbsp;</span>How long before we see results?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-2" class="collapse" aria-labelledby="faqHeading-2" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Technical fixes and page rewrites usually show movement within 30 days. Low-competition keywords reach the top three in 60-90 days; competitive head terms take six to twelve months because they depend on authority, not just content.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-3">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-3" data-aria-expanded="false" data-aria-controls="faqCollapse-3">
                                        <span class="badge">&nbsp;</span>Do you guarantee rankings?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-3" class="collapse" aria-labelledby="faqHeading-3" data-parent="#accordion">
                                <div class="card-body">
                                    <p>No, and nobody honest does. We guarantee the work: a keyword-to-page map, fixes logged, pages written and published, links built, and a Monday report that shows exactly where every keyword moved.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-4">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-4" data-aria-expanded="false" data-aria-controls="faqCollapse-4">
                                        <span class="badge">&nbsp;</span>Do you do Arabic SEO?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-4" class="collapse" aria-labelledby="faqHeading-4" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes. We write Arabic pages properly, not through translation tools, with right-to-left layouts and Arabic keyword research. Bilingual campaigns are priced as a separate line so you can see the cost.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-5">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-5" data-aria-expanded="false" data-aria-controls="faqCollapse-5">
                                        <span class="badge">&nbsp;</span>What is in the monthly report?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-5" class="collapse" aria-labelledby="faqHeading-5" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Top-three keyword count, position changes per keyword, organic leads and calls, pages published, links earned, and the next month's plan. It goes out every Monday, not just monthly.</p>
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
                    <h3>Don’t Settle for an Average Website — Let’s Build Something Powerful</h3>
                    <br>

                    <div class="row services gy-4">
                        <div class="col-lg-7">
                            <p>Whether you need a sleek corporate site, a robust e-commerce platform, or a lead-generation powerhouse, DMS delivers websites that work as hard as you do.</p>

                            <p><strong>Your journey starts with free consultation.</strong></p>

                            @include('web.includes.elements.contact')
                        </div>

                        <div class="col-lg-5">

                            <div class="web-form-div">
                                <h4>Who We Work With</h4>
                                <p>Our clients span industries, sizes, and markets — from startups to large enterprises. Whether you’re in retail, real estate, hospitality, healthcare, or e-commerce, our SEO company Dubai experts tailor strategies to match your unique needs.</p>

                                <h5>Want a website that actually drives business? <br><a href="javascript:void(0)" class="scroll-up">Let’s Talk</a>!</h5>
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
           "name": "How much does SEO cost in Dubai?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Expect AED [rate card: low] to AED [rate card: high] a month. The low end covers one site, one language and around 20 keywords; the high end covers multi-emirate, bilingual campaigns with monthly content and links. Our packages are priced in AED with no lock-in contract."
           }
          },
          {
           "@type": "Question",
           "name": "How long before we see results?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Technical fixes and page rewrites usually show movement within 30 days. Low-competition keywords reach the top three in 60-90 days; competitive head terms take six to twelve months because they depend on authority, not just content."
           }
          },
          {
           "@type": "Question",
           "name": "Do you guarantee rankings?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "No, and nobody honest does. We guarantee the work: a keyword-to-page map, fixes logged, pages written and published, links built, and a Monday report that shows exactly where every keyword moved."
           }
          },
          {
           "@type": "Question",
           "name": "Do you do Arabic SEO?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes. We write Arabic pages properly, not through translation tools, with right-to-left layouts and Arabic keyword research. Bilingual campaigns are priced as a separate line so you can see the cost."
           }
          },
          {
           "@type": "Question",
           "name": "What is in the monthly report?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Top-three keyword count, position changes per keyword, organic leads and calls, pages published, links earned, and the next month's plan. It goes out every Monday, not just monthly."
           }
          }
         ]
        }
    </script>
    
    @include('web.includes.elements.local-schema')
@endsection
