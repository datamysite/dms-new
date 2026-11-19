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
                        <h1>&#10687; E-commerce SEO in Dubai</h1>
                        <h2>Category Pages That Actually Sell</h2>
                        <h4>Shopify · WooCommerce · Magento · Custom Stores · Priced in AED</h4>
                        <br>
                        <p>
                            E-commerce SEO in Dubai starts from AED 4,500 a month for a store with up to 500 products and scales with catalogue size and languages. Online stores win or lose on category pages, not blog posts: a category page with real copy, the right filters indexed and product schema in place will outrank a marketplace listing for 'buy X in Dubai'. Here's how we do it and what it costs.
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
                                        <textarea class="form1-textarea" name="description" placeholder="Your store address, platform and roughly how many products you sell." required></textarea>
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
            <li><a href="/seo-services/technical-seo">Technical SEO services in Dubai</a></li>
            <li><a href="/seo-services/seo-audit">SEO audit: what we check and what it costs</a></li>
            </ul></section>
        </div>

        <!-- H2: Why category pages matter most -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">

                </div>
                <div class="col-lg-6">
                    <iframe class="service-iframe" src="https://www.youtube.com/embed/Kbxm_z_ScYU?si=589eks2setWiGTUc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <div class="col-lg-6 vertical-center">
                    <h2 class="h4">Why Category Pages Matter Most</h2>
                    <p>
                        Shoppers in the UAE rarely search for a product code. They search for a type of product with a buying word attached: "buy office chairs dubai", "sofa price uae", "running shoes online dubai". Those searches land on category pages, so that's where most store revenue from Google comes from.
                        <br><br>
                        Most store category pages are just a grid of products with a heading. Google has little to go on, so marketplaces with more authority win. Adding real copy changes that: a short intro, buying guidance and FAQs written for the search. One strong category page also lifts every product listed under it.
                        <br><br>
                        Filters matter just as much. A store with colour, size and brand filters can generate thousands of near-duplicate URLs. We set rules for which filter combinations are worth indexing, for example "black leather sofas", and block the rest, so Google spends its time on pages that can sell.
                    </p>
                </div>
            </div>

          </div>

        </section>


        <div class="section-striped">
                @include('web.includes.elements.clients')
        </div>


        <!-- H2: What's included -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12">
                    <div class="row services gy-4">
                        <div class="col-lg-6">
                            <h2 class="h3">What's Included</h2>
                            <ul>
                                <li><strong>Category and product optimisation</strong> — titles, copy, headings and internal links for every category, plus product page templates that scale.</li>
                                <li><strong>Product schema and reviews</strong> — price, stock and review markup so your listings show stars and prices in search.</li>
                                <li><strong>Faceted navigation</strong> — indexing rules for filters, canonicals and crawl control.</li>
                                <li><strong>Site speed</strong> — image compression, app and plugin clean-up and Core Web Vitals on product and category templates.</li>
                                <li><strong>Arabic storefront</strong> — hreflang, right-to-left templates and Arabic category copy written from scratch.</li>
                                <li><strong>Google Merchant Center (free listings)</strong> — feed set-up and maintenance so products appear in the free Shopping tab.</li>
                                <li><strong>Link building</strong> — links from UAE publications, gift guides and industry sites to your key categories.</li>
                            </ul>
                            <br>

                            <h5>Book Your Free SEO Audit — <a href="javascript:void(0)" class="scroll-up">Get Started Now</a></h5>
                        </div>
                        <div class="col-lg-1"></div>
                        <div class="col-lg-5">
                            <img src="{{URL::to('/public/seo2.jpg')}}" alt="E-commerce SEO for online stores in Dubai" width="100%">
                        </div>
                    </div>

                </div>
            </div>

          </div>

        </section>

        <!-- H2: Platforms -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-12">
                    <h2 class="h3">Platforms We Work With</h2>
                    <p>Every platform has its own SEO limits. We know where each one breaks and deliver fixes your developer can apply directly.</p>

                    <div class="row services seo-need-block gy-4 mt-0">
                        <div class="col-lg-3 filled">
                          <i class="bi bi-bag"></i>
                          <h4>Shopify</h4>
                          <p>Duplicate collection URLs, app bloat and theme speed fixed; quickest to set up.</p>
                        </div>
                        <div class="col-lg-3">
                          <i class="bi bi-cart3"></i>
                          <h4>WooCommerce</h4>
                          <p>Plugin clean-up, category templates and hosting speed on WordPress.</p>
                        </div>
                        <div class="col-lg-3 filled">
                          <i class="bi bi-box-seam"></i>
                          <h4>Magento</h4>
                          <p>Layered navigation, large catalogues and indexing at scale.</p>
                        </div>
                        <div class="col-lg-3">
                          <i class="bi bi-code-slash"></i>
                          <h4>Custom Stores</h4>
                          <p>Laravel and bespoke builds, quoted after a quick technical check.</p>
                        </div>
                    </div>

                    <br>
                    <p>Underlying speed or template problems are handled by our <a href="/seo-services/technical-seo">technical SEO</a> team.</p>
                    {{-- NP-012 e-commerce development (W3): add "building a new store? see our e-commerce development" link here once that page is live --}}
                </div>
            </div>

          </div>

        </section>

        <!-- H2: Pricing -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-12">
                    <h2 class="h3">E-commerce SEO Pricing</h2>
                    <p>Monthly prices in AED, set by catalogue size. Bilingual English and Arabic stores are priced as a separate line so you can see the cost. See all <a href="/seo-services/price-and-package">SEO packages and prices</a>.</p>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Catalogue size</th>
                                    <th>Monthly price</th>
                                    <th>Typical store</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Up to 500 products</strong></td>
                                    <td>AED 4,500</td>
                                    <td>Single-brand or niche store, one language</td>
                                </tr>
                                <tr>
                                    <td><strong>500–5,000 products</strong></td>
                                    <td>AED 10,000</td>
                                    <td>Multi-category store, often bilingual</td>
                                </tr>
                                <tr>
                                    <td><strong>5,000+ products</strong></td>
                                    <td>AED 15,000</td>
                                    <td>Large catalogue, faceted navigation, several markets</td>
                                </tr>
                                <tr>
                                    <td><strong>Arabic storefront</strong></td>
                                    <td>+ AED 4,500</td>
                                    <td>Added to any package</td>
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
                            <img src="{{URL::to('/public/seo3.png')}}" alt="E-commerce SEO category page example" width="100%">
                        </div>
                        <div class="col-lg-7">
                            <h2 class="h3">Example: Rewriting the Category Pages</h2>
                            <p>Anonymised from a store selling across the UAE:</p>
                            <ul>
                                <li><strong>Before</strong> — Category pages had little or no written content, product filters were creating large numbers of duplicate URLs in Google’s index, and product pages had no structured data.</li>
                                <li><strong>What we did</strong> — We wrote useful category content in both English and Arabic, set indexing rules so filter pages stopped cluttering search results, and added Product schema across the catalogue.</li>
                                <li><strong>Result</strong> — Category pages began ranking for their main keywords, and organic traffic turned into more orders from search.</li>
                            </ul>
                            <br>

                            <h5>See What Your Category Pages Could Do — <a href="javascript:void(0)" class="scroll-up">Get Started Now</a></h5>
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
                    <p>Your store is handled by a small named team with e-commerce experience, not a general content writer. You'll know who wrote every category page and who approved every change.</p>
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
                    <img src="{{URL::to('/public/local-seo2.jpg')}}" alt="DataMySite e-commerce SEO team in Dubai" width="100%">
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
                                        <span class="badge">&nbsp;</span>How much does e-commerce SEO cost in Dubai?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-1" class="collapse show" aria-labelledby="faqHeading-1" data-parent="#accordion">
                                <div class="card-body">
                                    <p>From AED 4,500 a month for stores under 500 products, rising with catalogue size and the number of languages. Shopify and WooCommerce stores are quickest to set up; custom platforms are quoted after a technical check.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-2">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-2" data-aria-expanded="false" data-aria-controls="faqCollapse-2">
                                        <span class="badge">&nbsp;</span>Which pages should an online store optimise first?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-2" class="collapse" aria-labelledby="faqHeading-2" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Category pages, every time. They match how people search in the UAE, 'buy running shoes dubai' rather than a product code, and one good category page lifts every product under it.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-3">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-3" data-aria-expanded="false" data-aria-controls="faqCollapse-3">
                                        <span class="badge">&nbsp;</span>Do you handle Arabic storefronts?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-3" class="collapse" aria-labelledby="faqHeading-3" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes. We set up hreflang correctly, write Arabic category copy rather than translating the English, and make sure the Arabic store isn't treated as duplicate content. For UAE stores the Arabic side is usually the least competitive traffic available.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-4">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-4" data-aria-expanded="false" data-aria-controls="faqCollapse-4">
                                        <span class="badge">&nbsp;</span>Can you fix faceted navigation?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-4" class="collapse" aria-labelledby="faqHeading-4" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes. We decide which filter combinations deserve to be indexed (colour, size, brand) and block the rest so Google spends its crawl budget on pages that can sell.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-5">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-5" data-aria-expanded="false" data-aria-controls="faqCollapse-5">
                                        <span class="badge">&nbsp;</span>Does e-commerce SEO include Google Shopping?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-5" class="collapse" aria-labelledby="faqHeading-5" data-parent="#accordion">
                                <div class="card-body">
                                    <p>We set up and maintain the free Google Merchant Center listings as part of the work, so your products appear in the free Shopping tab with correct prices and stock. Paid Shopping campaigns are not something we run; we'll say so rather than sell them.</p>
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
                    <h3>Turn Category Pages Into Your Best Salespeople</h3>
                    <br>

                    <div class="row services gy-4">
                        <div class="col-lg-7">
                            <p>Marketplaces don't have to own the first page for your products. With the right category copy, filters and schema, your own store can rank for the searches that end in a purchase, and you keep the margin. Our <a href="/seo-services">SEO services</a> team runs it end to end.</p>

                            <p><strong>Your journey starts with a free SEO audit of your store.</strong></p>

                            @include('web.includes.elements.contact')
                        </div>

                        <div class="col-lg-5">

                            <div class="web-form-div">
                                <h4>SEO Is About Revenue</h4>
                                <p>Rankings only matter if they bring orders and enquiries. See what a customer should cost you with our <a href="/lead-generation/cost-per-lead">cost-per-lead guide</a>, or read <a href="/how-to-get-leads-for-real-estate-in-dubai-full-guide">how to get real estate leads in Dubai</a>.</p>

                                <h5>Want more sales from Google? <br><a href="javascript:void(0)" class="scroll-up">Let’s Talk</a>!</h5>
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
           "name": "How much does e-commerce SEO cost in Dubai?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "From AED 4,500 a month for stores under 500 products, rising with catalogue size and the number of languages. Shopify and WooCommerce stores are quickest to set up; custom platforms are quoted after a technical check."
           }
          },
          {
           "@type": "Question",
           "name": "Which pages should an online store optimise first?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Category pages, every time. They match how people search in the UAE, 'buy running shoes dubai' rather than a product code, and one good category page lifts every product under it."
           }
          },
          {
           "@type": "Question",
           "name": "Do you handle Arabic storefronts?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes. We set up hreflang correctly, write Arabic category copy rather than translating the English, and make sure the Arabic store isn't treated as duplicate content. For UAE stores the Arabic side is usually the least competitive traffic available."
           }
          },
          {
           "@type": "Question",
           "name": "Can you fix faceted navigation?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes. We decide which filter combinations deserve to be indexed (colour, size, brand) and block the rest so Google spends its crawl budget on pages that can sell."
           }
          },
          {
           "@type": "Question",
           "name": "Does e-commerce SEO include Google Shopping?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "We set up and maintain the free Google Merchant Center listings as part of the work, so your products appear in the free Shopping tab with correct prices and stock. Paid Shopping campaigns are not something we run; we'll say so rather than sell them."
           }
          }
         ]
        }
    </script>

    @include('web.includes.elements.local-schema')
@endsection