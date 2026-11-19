
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
                        <h1>&#10687; Bridge Banner Advertising in Dubai</h1>
                        <h2>Pedestrian Bridges and Mega Banners Over Dubai's Busiest Roads</h2>
                        <h4>Sheikh Zayed Road · Al Khail Road · Emirates Road · 2026 Rates in AED</h4>
                        <br>
                        <p>
                            A bridge banner on Sheikh Zayed Road rents from AED 25,000 a month, and a mega banner on a construction site is quoted per site before printing. Pedestrian bridges put your message directly over eight lanes of slow-moving traffic at the exact height drivers look, which is why banks and property launches book the same bridges every year. Locations, sizes, rates and lead times are below.
                        </p>
                        <span><strong>Get Locations &amp; Rates → · <a href="https://api.whatsapp.com/send/?phone=971525287259" target="_blank">WhatsApp: +971 52 528 7259</a></strong></span>
                    </div>
                    <div class="col-lg-4">
                        <div class="aside-form">

                            <form action="{{route('enquiry.submit')}}" id="enquiry-form">
                                @csrf
                                <input type="hidden" name="service" value="{{$service->name}}" required>
                                <div class="form1-wrapper">
                                    <div class="form1-bg">
                                      <h2 class="form1-headline">Check Bridge <span>Availability</span></h2>
                                      <p class="form1-sub">Drop us your details — our team responds within 60 minutes.</p>

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
                                        <textarea class="form1-textarea" name="description" placeholder="Preferred roads, campaign dates and budget." required></textarea>
                                      </div>

                                      <button class="form1-btn" type="submit">
                                        Get Locations &amp; Rates
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
            <section class="hub-guides"><h2>Outdoor advertising guides</h2><ul>
            <li><a href="/outdoor-advertising">Outdoor advertising in Dubai</a></li>
            <li><a href="/unipole-advertising-dubai">Unipole advertising in Dubai</a></li>
            <li><a href="/large-format-hoardings-dubai-marina">Large-format hoardings in Dubai Marina</a></li>
            </ul></section>
        </div>

        <!-- H2: Where the bridges are -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-12">
                    <h2 class="h3">Where the Bridges Are</h2>
                    <p>Ten pedestrian bridge sites across Dubai's main roads. Traffic figures are daily vehicle estimates; we share the full data sheet for any bridge before you book.</p>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                  <th>Road</th>
                                  <th>Bridge location</th>
                                  <th>Daily traffic (est.)</th>
                                  <th>Rate band / month</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>Sheikh Zayed Road</td>
                                  <td>Trade Centre</td>
                                  <td>400,000–450,000</td>
                                  <td>AED 130,000–150,000</td>
                                </tr>
                                <tr>
                                  <td>Sheikh Zayed Road</td>
                                  <td>Financial Centre</td>
                                  <td>380,000–420,000</td>
                                  <td>AED 120,000–145,000</td>
                                </tr>
                                <tr>
                                  <td>Sheikh Zayed Road</td>
                                  <td>Al Safa (formerly Noor Bank)</td>
                                  <td>350,000–400,000</td>
                                  <td>AED 95,000–120,000</td>
                                </tr>
                                <tr>
                                  <td>Sheikh Zayed Road</td>
                                  <td>Equiti (formerly FGB)</td>
                                  <td>330,000–380,000</td>
                                  <td>AED 85,000–110,000</td>
                                </tr>
                                <tr>
                                  <td>Al Khail Road</td>
                                  <td>Business Bay / Al Jaddaf</td>
                                  <td>250,000–300,000</td>
                                  <td>AED 55,000–75,000</td>
                                </tr>
                                <tr>
                                  <td>Al Khail Road</td>
                                  <td>Al Quoz / Meydan</td>
                                  <td>220,000–270,000</td>
                                  <td>AED 45,000–65,000</td>
                                </tr>
                                <tr>
                                  <td>Emirates Road</td>
                                  <td>Dubai Silicon Oasis</td>
                                  <td>180,000–220,000</td>
                                  <td>AED 35,000–50,000</td>
                                </tr>
                                <tr>
                                  <td>Emirates Road</td>
                                  <td>Al Barsha South / Arabian Ranches</td>
                                  <td>160,000–200,000</td>
                                  <td>AED 30,000–45,000</td>
                                </tr>
                                <tr>
                                  <td>Al Ittihad Road</td>
                                  <td>Al Nahda</td>
                                  <td>250,000–280,000</td>
                                  <td>AED 50,000–70,000</td>
                                </tr>
                                <tr>
                                  <td>Al Ittihad Road</td>
                                  <td>Al Qusais</td>
                                  <td>230,000–260,000</td>
                                  <td>AED 45,000–65,000</td>
                                </tr>
                              </tbody>
                        </table>
                    </div>
                    <p>The Sheikh Zayed Road bridges between Trade Centre and Financial Centre carry the heaviest and slowest traffic in the city, so they book out first.</p>
                    {{-- NP-087 roads hub (W9): add "see all Dubai roads for outdoor advertising" link here once that page is live --}}
                </div>
            </div>

          </div>

        </section>


        <div class="section-striped">
                @include('web.includes.elements.clients')
        </div>


        <!-- H2: Sizes and formats -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">

                </div>
                <div class="col-lg-6">
                    <iframe class="service-iframe" src="https://www.youtube.com/embed/Kbxm_z_ScYU?si=589eks2setWiGTUc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <div class="col-lg-6 vertical-center">
                    <h2 class="h4">Sizes and Formats</h2>
                    <ul>
                        <li><strong>Standard bridge banner</strong> — 40M × 2.4M, fixed to the side of the pedestrian bridge facing oncoming traffic.</li>
                        <li><strong>Double-sided</strong> — both faces of the same bridge booked together, so you reach traffic in both directions. Priced per side.</li>
                        <li><strong>Mega banners</strong> — large-format banners, often 20 metres or more, on building facades, towers under construction or hoarding structures. Priced per site and month.</li>
                        <li><strong>Mesh vs PVC</strong> — mesh lets wind through and is the usual choice for very large or exposed sites; PVC gives sharper colour and suits smaller bridge banners. We advise per site.</li>
                    </ul>
                    <p>Artwork should be simple: one image, a short headline and a brand. Drivers have a few seconds, so anything more than seven words rarely gets read.</p>
                </div>
            </div>

          </div>

        </section>

        <!-- H2: Rates and lead times -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12">
                    <div class="row services gy-4">
                        <div class="col-lg-6">
                            <h2 class="h3">Rates and Lead Times</h2>
                            <p>Monthly rental rates in AED by road, per side, before printing and installation:</p>

                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Road / format</th>
                                            <th>From (AED / month)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Sheikh Zayed Road bridges</strong></td>
                                            <td>From AED 25,000 a month</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Al Khail and Emirates Road bridges</strong></td>
                                            <td>From AED 20,000 a month</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Al Ittihad Road bridges</strong></td>
                                            <td>From AED 20,000 a month</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Mega banner (per site)</strong></td>
                                            <td>Quoted per site</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Printing &amp; installation</strong></td>
                                            <td>2,500 per banner</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h5>Lead times</h5>
                            <ul>
                                <li><strong>Permit</strong> — about one week.</li>
                                <li><strong>Printing</strong> — seven to ten days from final artwork.</li>
                                <li><strong>Installation</strong> — overnight.</li>
                            </ul>
                            <p>Allow two to three weeks from final artwork to going live. For Sheikh Zayed Road in November and December, book six weeks ahead.</p>
                            <br>

                            <h5>Check Availability — <a href="javascript:void(0)" class="scroll-up">Get Started Now</a></h5>
                        </div>
                        <div class="col-lg-1"></div>
                        <div class="col-lg-5">
                            <img src="{{URL::to('/public/seo2.jpg')}}" alt="Bridge banner advertising on Sheikh Zayed Road, Dubai" width="100%">
                        </div>
                    </div>

                </div>
            </div>

          </div>

        </section>

        <!-- H2: Bridge banner or unipole? -->
        <section id="service-cards" class="blog-section section-striped section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-12">
                    <h2 class="h3">Bridge Banner or Unipole?</h2>
                    <p>Both reach drivers on the same main roads. Which one wins depends on how long your campaign runs and whether the message changes.</p>

                    <div class="row services seo-need-block gy-4 mt-0">
                        <div class="col-lg-6 filled">
                          <i class="bi bi-signpost-split"></i>
                          <h4>Bridge Banner Wins When…</h4>
                          <p>You're running a two-to-four-week launch or longer, the message is fixed (a property launch or bank offer with a date on it), and you want to sit right at drivers' eye line over slow traffic. The print cost is small against a month of exposure.</p>
                        </div>
                        <div class="col-lg-6">
                          <i class="bi bi-display"></i>
                          <h4>Unipole Wins When…</h4>
                          <p>The campaign is shorter than two weeks, you want to change creative during the run, or you need a digital screen with no printing. Unipoles are also easier to see from a distance on faster stretches of road.</p>
                        </div>
                    </div>

                    <br>
                    <p>Compare locations and rates on our <a href="/unipole-advertising-dubai">unipole advertising page</a>.</p>
                </div>
            </div>

          </div>

        </section>


        <section id="testimonials" class="testimonials case-studies-section section">
           @include('web.includes.elements.case-studies')
        </section>


        <!-- H2: Who you'll work with (EEAT) -->
        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4">

                <div class="col-lg-6">
                    <h2 class="h3">Who You'll Work With</h2>
                    <p>Your bridge campaign is planned and booked by Aan, who handles outdoor media for DataMySite and works with the site owners and permit authorities directly.</p>
                    <br>

                    <ul>
                      <li><strong>Site data before you book</strong><br> traffic estimates, photos and availability for every bridge you shortlist.</li>
                      <li><strong>Permits and production handled</strong><br> one contact from artwork to installation.</li>
                      <li><strong>Proof of posting</strong><br> photos of every banner on the night it goes up.</li>
                    </ul>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-5">
                    {{-- Replace with a photo of Aan or a live bridge banner campaign --}}
                    <img src="{{URL::to('/public/local-seo2.jpg')}}" alt="Aan, outdoor media planner at DataMySite" width="100%">
                </div>
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section section-striped section  pr-blogs-section">

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
                                        <span class="badge">&nbsp;</span>How much does bridge advertising cost in Dubai?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-1" class="collapse show" aria-labelledby="faqHeading-1" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Bridge banners on Sheikh Zayed Road run from AED 25,000 a month; Al Khail and Emirates Road bridges are quoted per bridge at lower rates. Printing and installation are quoted per banner size, usually 10 to 20 percent on top of the media cost.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-2">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-2" data-aria-expanded="false" data-aria-controls="faqCollapse-2">
                                        <span class="badge">&nbsp;</span>Which bridges have the most traffic?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-2" class="collapse" aria-labelledby="faqHeading-2" data-parent="#accordion">
                                <div class="card-body">
                                    <p>The Sheikh Zayed Road bridges between Trade Centre and Financial Centre carry the heaviest and slowest traffic in the city, followed by the Al Khail Road bridges near Business Bay. We share traffic estimates per bridge before you choose.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-3">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-3" data-aria-expanded="false" data-aria-controls="faqCollapse-3">
                                        <span class="badge">&nbsp;</span>What is a mega banner?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-3" class="collapse" aria-labelledby="faqHeading-3" data-parent="#accordion">
                                <div class="card-body">
                                    <p>A large-format banner, often 20 metres or more, hung on a building facade, tower under construction or a hoarding structure. It's priced per site and month, and permits depend on the building owner and the municipality.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-4">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-4" data-aria-expanded="false" data-aria-controls="faqCollapse-4">
                                        <span class="badge">&nbsp;</span>How long does it take to go live?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-4" class="collapse" aria-labelledby="faqHeading-4" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Two to three weeks from final artwork: a week for the permit, seven to ten days for printing, and an overnight installation. Book six weeks ahead for Sheikh Zayed Road in November and December.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-5">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-5" data-aria-expanded="false" data-aria-controls="faqCollapse-5">
                                        <span class="badge">&nbsp;</span>Do bridge banners work for short campaigns?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-5" class="collapse" aria-labelledby="faqHeading-5" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes for two-to-four-week launches, especially property and banking offers with a date on them, because the print cost is small against a month of Sheikh Zayed Road traffic. For anything shorter, a digital unipole gives you the flexibility without the printing.</p>
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
                    <h3>Put Your Brand Over Dubai's Busiest Roads</h3>
                    <br>

                    <div class="row services gy-4">
                        <div class="col-lg-7">
                            <p>The best bridges on Sheikh Zayed Road go to the brands that book early. Tell us your dates and budget, and we'll send a shortlist of available bridges with traffic, photos and an all-in price. For other formats, see our full range of <a href="/outdoor-advertising">outdoor advertising in Dubai</a>.</p>

                            <p><strong>Your journey starts with a free consultation.</strong></p>

                            @include('web.includes.elements.contact')
                        </div>

                        <div class="col-lg-5">

                            <div class="web-form-div">
                                <h4>Need Something Bigger?</h4>
                                <p>For longer campaigns and bigger formats, see our <a href="/outdoor-advertising/billboards-advertising/hoarding">hoarding advertising</a> options and <a href="/large-format-hoardings-dubai-marina">large-format hoardings in Dubai Marina</a>.</p>

                                <h5>Want bridge availability for your dates? <br><a href="javascript:void(0)" class="scroll-up">Let’s Talk</a>!</h5>
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
           "name": "How much does bridge advertising cost in Dubai?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Bridge banners on Sheikh Zayed Road run from AED 25,000 a month; Al Khail and Emirates Road bridges are quoted per bridge at lower rates. Printing and installation are quoted per banner size, usually 10 to 20 percent on top of the media cost."
           }
          },
          {
           "@type": "Question",
           "name": "Which bridges have the most traffic?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "The Sheikh Zayed Road bridges between Trade Centre and Financial Centre carry the heaviest and slowest traffic in the city, followed by the Al Khail Road bridges near Business Bay. We share traffic estimates per bridge before you choose."
           }
          },
          {
           "@type": "Question",
           "name": "What is a mega banner?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "A large-format banner, often 20 metres or more, hung on a building facade, tower under construction or a hoarding structure. It's priced per site and month, and permits depend on the building owner and the municipality."
           }
          },
          {
           "@type": "Question",
           "name": "How long does it take to go live?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Two to three weeks from final artwork: a week for the permit, seven to ten days for printing, and an overnight installation. Book six weeks ahead for Sheikh Zayed Road in November and December."
           }
          },
          {
           "@type": "Question",
           "name": "Do bridge banners work for short campaigns?",
           "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes for two-to-four-week launches, especially property and banking offers with a date on them, because the print cost is small against a month of Sheikh Zayed Road traffic. For anything shorter, a digital unipole gives you the flexibility without the printing."
           }
          }
         ]
        }
    </script>

    @include('web.includes.elements.local-schema')
@endsection