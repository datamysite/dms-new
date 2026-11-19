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
                        <h1>&#10687; Billboard Advertising Company in Dubai</h1>
                        <h2>Transform Your Brand Presence</h2>
                        <h4>with Billboards Advertising Dubai</h4>
                        <br>
                        <p>
                            Billboard advertising in Dubai starts from AED 30,000 per month for secondary highway sites, with prime Sheikh Zayed Road unipoles running AED 150,000–350,000. Production and installation adds AED 15,000–40,000 on top of the media cost. Below: the 2026 rate table by location and format.
                        </p>
                        <span><strong>Get a Free Billboard Strategy Call → <a href="https://api.whatsapp.com/send/?phone=971525287259" target="_blank">WhatsApp us: +971 52 528 7259</a></strong></span>
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
                    <h4>Why Billboard Advertising Still Wins in Dubai</h4>
                    <p>
                        <br>
                        Dubai's roads carry some of the highest-value commuter traffic in the world — executives, residents, and tourists moving along arteries like Sheikh Zayed Road every single day. A well-placed billboard reaches them repeatedly, builds instant credibility, and works around the clock. Unlike digital ads that compete with infinite distraction, outdoor advertising owns its space. For product launches, real estate developments, and brand-building campaigns, it remains one of the highest-recall formats available.
                        <br>
                        
                        <i><p>See why <a href="/billboard-advertising-in-dubai-still-king-of-the-streets-in-2025">billboard advertising in Dubai still leads in 2026</a>.</p></i>
                    </p>
                    
                </div>
                <div class="col-lg-6 vertical-center">
                    <iframe class="service-iframe airline-iframe" src="https://www.youtube.com/embed/Ny7v7w6dtq4?si=oAHAurPi9RcZjBPT" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>  
                <div class="col-lg-12">
                    <h4>Billboard Advertising Rates Dubai 2026</h4>
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Location / Format</th>
                          <th>Monthly Rate (AED)</th>
                          <th>Production extra</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr><td>SZR prime unipole</td><td>150,000 – 350,000</td><td>AED 25,000–40,000</td></tr>
                        <tr><td>Downtown / DXB approach</td><td>100,000 – 250,000</td><td>AED 20,000–35,000</td></tr>
                        <tr><td>Marina &amp; JBR billboard</td><td>60,000 – 150,000</td><td>AED 15,000–25,000</td></tr>
                        <tr><td>Al Khail Road / E311</td><td>30,000 – 80,000</td><td>AED 12,000–20,000</td></tr>
                        <tr><td>Bridge banner (highway)</td><td>45,000 – 120,000</td><td>AED 18,000–28,000</td></tr>
                        <tr><td>Abu Dhabi Corniche / E11</td><td>25,000 – 80,000</td><td>AED 10,000–20,000</td></tr>
                        <tr><td>Sharjah E88 corridor</td><td>20,000 – 60,000</td><td>AED 8,000–15,000</td></tr>
                        <tr><td>Digital billboard / DOOH</td><td>60,000 – 350,000</td><td>Creative only</td></tr>
                        <tr><td>Hoarding (construction wrap)</td><td>30,000 – 60,000</td><td>AED 80–150 per sqm</td></tr>
                      </tbody>
                    </table>
                    <p><em>Production AED 15,000-40,000; municipality approval required.</em></p>
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

        <section id="service-cards" class="blog-section section striped-section pr-icon-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">
                    <h4>Our Billboard Advertising Formats</h4>
                    <p>We plan and place every major out-of-home format across Dubai and the wider UAE:</p>
                </div>
                <div class="col-lg-4">
                    <p>
                        <strong>Static billboards & unipoles </strong> <br>— commanding highway presence on Dubai's busiest routes. <a href="https://datamysite.com/outdoor-advertising/unipoles">Learn about unipoles →</a>

                    </p>
                </div>
                <div class="col-lg-4">
                    <p>
                        <strong>Digital billboards (DOOH)  </strong><br>— dynamic, day-parted screens for timely, rotating campaigns. <a href="https://datamysite.com/digital-advertising/dooh-advertising">Learn about DOOH →</a>

                    </p>
                </div>
                <div class="col-lg-4">
                    <p>
                        <strong>Building wraps </strong> <br>— full-façade dominance that turns a tower into your canvas. <a href="https://datamysite.com/outdoor-advertising/billboards-advertising/building-wraps">Explore building wraps →</a>

                    </p>
                </div>
                <div class="col-lg-4">
                    <p>
                        <strong>Hoardings </strong> <br>—  large-format coverage on construction sites in high-footfall districts. <a href="https://datamysite.com/outdoor-advertising/billboards-advertising/hoarding">See hoarding options →</a>

                    </p>
                </div>
                <div class="col-lg-4">
                    <p>
                        <strong>Wallscapes </strong> <br>— oversized wall displays for sustained, high-impact exposure. <a href="https://datamysite.com/outdoor-advertising/billboards-advertising/wallscapes">View wallscapes →</a>

                    </p>
                </div> 
            </div>

          </div>

        </section>


        <section class="bb-locs section testimonials">
            <div class="container">
              <div class="in">
                
                <div class="row">
                    <div class="col-lg-7">
                        <div class="top">
                          <div>
                            <div class="tag">Prime Billboard Locations We Cover</div>
                            <h2>Dubai's Highest-Traffic<br><em>Billboard Spots</em></h2>
                          </div>
                        </div>
                        <p>
                            Placement is everything. We secure inventory in the locations that move the needle:
                        </p>
                        <ul>
                            <li><strong>Sheikh Zayed Road</strong><br> — Dubai's premier advertising corridor.<br><br></li>
                            <li><strong>Downtown & Burj Khalifa area</strong><br> — prestige placements in the city's most-photographed district<br><br></li>
                            <li><strong>Dubai Marina</strong><br> — high-density, high-income footfall.<br><br></li>
                            <li><strong>Business Bay, Al Ittihad Road, Airport Road</strong><br> —  and key interchanges across the emirate.</li>

                        </ul>
                        <p>We also extend campaigns into Abu Dhabi, Sharjah, and Ajman for brands running emirate-wide.</p>
                        <a href="https://datamysite.com/contact-us" class="bbtn btn-r">Check Location Availability <span class="arr">→</span></a>
                    </div>
                    <div class="col-lg-5 align-content-center">
                        <div class="loc-grid">
                          <div class="loc-card">
                            <div class="icon">🛣️</div>
                            <div class="loc">Sheikh Zayed Rd</div>
                            <div class="imp">2M+ daily impressions</div>
                            <div class="tag-pill">Premium</div>
                          </div>
                          <div class="loc-card">
                            <div class="icon">🏙️</div>
                            <div class="loc">Downtown Dubai</div>
                            <div class="imp">Tourist & business hub</div>
                            <div class="tag-pill">High Footfall</div>
                          </div>
                          <div class="loc-card">
                            <div class="icon">🌊</div>
                            <div class="loc">JBR / Marina</div>
                            <div class="imp">Lifestyle & F&B audience</div>
                            <div class="tag-pill">Expat Reach</div>
                          </div>
                          <div class="loc-card">
                            <div class="icon">✈️</div>
                            <div class="loc">DXB Airport Rd</div>
                            <div class="imp">Global traveller exposure</div>
                            <div class="tag-pill">Int'l Audience</div>
                          </div>
                          <div class="loc-card">
                            <div class="icon">🏗️</div>
                            <div class="loc">Business Bay</div>
                            <div class="imp">Corporate decision-makers</div>
                            <div class="tag-pill">B2B</div>
                          </div>
                          <div class="loc-card">
                            <div class="icon">🏙️</div>
                            <div class="loc">Dubai Investment Park</div>
                            <div class="imp">logistics campaigns</div>
                            <div class="tag-pill">B2B</div>
                          </div>
                        </div>
                    </div>
                </div>
              </div>
          </div>
        </section>

        <section id="service-cards" class="blog-section section striped-section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-4 vertical-center">
                    <img src="{{URL::to('/public/section-images/image48.png')}}" width="100%" alt="Why choose us">
                </div> 
                <div class="col-lg-1"></div>
                <div class="col-lg-7">
                    <h4>How Much Does Billboard Advertising Cost in Dubai?</h4>
                    <p>Billboard advertising in Dubai is priced by location, format, size, and campaign duration — a unipole on a secondary road and a building wrap on Sheikh Zayed sit at very different price points. Rather than a fixed rate card, we build a placement plan around your budget and objective so every dirham buys the most relevant eyeballs.</p>
                    <br>
                    <h4>Why Choose DataMySite</h4>
                    <p>
                        Not sure which format suits your campaign? Here's a quick guide:
                    </p>
                    <ul>
                        <li><strong>1,200+ campaigns delivered</strong><br> for brands across real estate, automotive, retail, and hospitality.</li>
                        <li><strong>In-house creative and production</strong><br> — design, print, and installation managed under one roof.</li>
                        <li><strong>End-to-end permits and compliance</strong><br> — we handle the paperwork so your campaign goes live on time.</li>
                        <li><strong>Trusted by 80+ brands,</strong><br> with case studies including Danube, Jetour, and Dubai Duty Free. <a href="https://datamysite.com/client-case-studies">See case studies →</a></li>
                        <li><strong>60-minute response,</strong><br> transparent reporting, and a 5.0 Google rating.</li>


                    </ul>
                    
                </div>
            </div>

          </div>

        </section>

        <section id="service-cards" class="blog-section section  why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-9">
                    <h4>Our Process</h4>
                    <ul>
                        <li><strong>Free strategy call</strong> — we learn your goal, audience, and budget.</li>
                        <li><strong>Location & format plan</strong> — we map the highest-ROI placements.</li>
                        <li><strong>Creative production</strong> — eye-catching design built for the format.</li>
                        <li><strong>Installation & go-live</strong> — permits, printing, and mounting handled in-house.</li>
                        <li><strong>Reporting</strong> — placement verification and campaign performance.</li>

                    </ul>
                    <br>
                    <h4>Ready to Own the Road?</h4>
                    <p>
                        Tell us your brand and your goal — we'll send a tailored billboard plan with locations and pricing.
                        <br><br>
                        <strong>Book Your Free Strategy Call → <a href="https://api.whatsapp.com/send/?phone=971525287259" target="_blank">WhatsApp us: +971 52 528 7259</a></strong> <br>No commitment, reply in 60 minutes.
                    </p>
                    
                </div>
                <div class="col-lg-3 mob-not-visible">
                    <img src="{{URL::to('/public/section-images/image1.png')}}" width="100%" alt="Why choose us">
                </div> 
            </div>

          </div>

        </section>


        <section class="bb-roi">
            <div class="container">
              <div class="in">
                <div>
                  <div class="tag white">Why Billboards Work in Dubai</div>
                  <h2>2 Million Eyes on Your<br>Brand <em>Every Single Day</em></h2>
                  <p>Dubai's commuter culture means billboards on Sheikh Zayed Road alone reach 2M+ daily impressions. Combined with our strategic placement expertise, your brand becomes impossible to ignore.</p>
                  <div class="roi-stats">
                    <div class="roi-stat"><div class="v">2M+</div><div class="l">Daily impressions SZR</div></div>
                    <div class="roi-stat"><div class="v">96%</div><div class="l">OOH buying expertise</div></div>
                    <div class="roi-stat"><div class="v">48hrs</div><div class="l">Availability confirmed</div></div>
                  </div>
                </div>
                <div class="r">
                  <a href="https://datamysite.com/contact-us" class="bbtn btn-w">Get My Billboard Quote <span class="arr">→</span></a>
                  <a href="https://api.whatsapp.com/send/?phone=971525287259" class="bbtn btn-ow">💬 Check Availability Now</a>
                  <span style="font-size:11px;color:rgba(255,255,255,.35);align-self:center">Free quote · No commitment</span>
                </div>
              </div>
          </div>
        </section>




        <section id="service-cards" class="blog-section section pr-blogs-section">

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

        <section id="service-cards" class="blog-section section striped-section faq-new-section">

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
                                        <span class="badge">&nbsp;</span>How much does billboard advertising cost in Dubai?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-10" class="collapse show" aria-labelledby="faqHeading-10" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Cost depends on location, format, size, and duration. Prime sites like Sheikh Zayed Road command a premium over secondary roads, and building wraps cost more than standard unipoles. We build a plan to your budget — request a quote for exact, current pricing.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-11">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-11" data-aria-expanded="false" data-aria-controls="faqCollapse-11">
                                        <span class="badge">&nbsp;</span>What are the best billboard locations in Dubai?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-11" class="collapse" aria-labelledby="faqHeading-11" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Sheikh Zayed Road, Downtown/Burj Khalifa, Dubai Marina, and Business Bay deliver the highest-value reach. The right choice depends on whether you're targeting commuters, tourists, or a specific district.</p>
                                </div>
                            </div>
                        </div>


                        <div class="card">
                            <div class="card-header" id="faqHeading-12">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-12" data-aria-expanded="false" data-aria-controls="faqCollapse-12">
                                        <span class="badge">&nbsp;</span>Do you handle permits and installation?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-12" class="collapse" aria-labelledby="faqHeading-12" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes — DataMySite manages the entire process end to end, including permits, creative production, printing, and installation across Dubai and the UAE.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-13">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-13" data-aria-expanded="false" data-aria-controls="faqCollapse-13">
                                        <span class="badge">&nbsp;</span>What's the difference between a static and a digital billboard?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-13" class="collapse" aria-labelledby="faqHeading-13" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Static billboards offer constant, uninterrupted presence; digital billboards (DOOH) let you rotate creative, day-part campaigns, and update messaging in real time. We advise on the best mix for your objective.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-14">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-14" data-aria-expanded="false" data-aria-controls="faqCollapse-14">
                                        <span class="badge">&nbsp;</span>How do I book a billboard campaign in Dubai?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-14" class="collapse" aria-labelledby="faqHeading-14" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Book a free strategy call or message us on WhatsApp. We'll check live inventory, recommend placements, and send a tailored quote within 60 minutes.</p>
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

@endsection