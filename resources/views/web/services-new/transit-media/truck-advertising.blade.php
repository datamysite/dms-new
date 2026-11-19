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
                        <h1>&#10687; Truck Advertising and Branding in Dubai</h1>
                        <h2>Transforms Ordinary Transport</h2>
                        <h4>vehicles into mobile billboards</h4>
                        <br>
                        <p>
                            Branding a truck in Dubai costs from AED 4,500 per vehicle for a partial wrap and AED 15,000 for a full wrap, plus a monthly media fee if you're renting a mobile billboard rather than wrapping your own fleet. It's the cheapest way to put a twelve-metre ad on Sheikh Zayed Road every working day. Formats, routes, timing and the maths against a static billboard are below.
                        </p>
                        <span><strong>Get a Free Truck Advertising Quote → · <a href="https://api.whatsapp.com/send/?phone=971525287259" target="_blank">WhatsApp: +971 52 528 7259</a></strong></span>
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
                    <h4>Why Truck Advertising Works</h4>
                    <br>
                    <p>
                        Trucks travel routes that static billboards can't reach, delivering repeated exposure across multiple neighbourhoods for a single production cost. It's mobile, high-impact, and one of the most cost-effective outdoor formats in the UAE.
                    </p>
                    <h4>Our Truck Advertising Formats</h4>
                    <ul>
                        <li><strong>Full truck wraps</strong> — total-surface branding for maximum impact.</li>
                        <li><strong>Branded delivery fleets</strong> — turn your own vehicles into rolling ads.</li>
                        <li><strong>LED & illuminated trucks</strong> — dynamic campaigns for events and launches.</li>

                    </ul>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-5">
                    <iframe class="service-iframe airline-iframe" src="https://www.youtube.com/embed/Slt71QddNGc?si=Bn768ai2rsCC0YBI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
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


        <section id="service-cards" class="blog-section striped-section section pr-impact-section">

          <div class="container">

            <div class="row gy-4 first-row split-block">

                <div class="col-lg-4 vertical-center">
                    <img src="{{URL::to('/public/section-images/image144.png')}}" width="100%" alt="Impact of PR Coverage">
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-7">
                    <h4>Why Choose Truck Advertising?</h4>
                    <br>
                    <div class="faq " id="accordion">
                        <div class="card">
                            <div class="card-header" id="faqHeading-210">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-210" data-aria-expanded="true" data-aria-controls="faqCollapse-210">
                                        <span class="badge">&nbsp;</span>Massive Reach & Mobility
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-210" class="collapse" aria-labelledby="faqHeading-210" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Unlike static billboards, truck ads move across different routes daily—covering shopping malls, highways, business districts, and residential areas. This mobility increases brand exposure exponentially.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-211">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-211" data-aria-expanded="false" data-aria-controls="faqCollapse-211">
                                        <span class="badge">&nbsp;</span>Cost-Effective Branding
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-211" class="collapse" aria-labelledby="faqHeading-211" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Compared to other outdoor advertising options, truck advertising delivers high visibility at a fraction of the cost. You pay once for printing and installation, and your brand remains visible for months.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-212">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-212" data-aria-expanded="false" data-aria-controls="faqCollapse-212">
                                        <span class="badge">&nbsp;</span>Targeted Audience Reach
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-212" class="collapse" aria-labelledby="faqHeading-212" data-parent="#accordion">
                                <div class="card-body">
                                    <p>You can select routes and schedules that align with your target demographics—from high-income neighborhoods to industrial areas or central business zones.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-213">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-213" data-aria-expanded="false" data-aria-controls="faqCollapse-213">
                                        <span class="badge">&nbsp;</span>Non-Intrusive Yet Highly Visible
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-213" class="collapse" aria-labelledby="faqHeading-213" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Truck ads grab attention naturally. With their large, colorful, and creative designs, they attract both pedestrians and motorists without disrupting their daily routine.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-214">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-214" data-aria-expanded="false" data-aria-controls="faqCollapse-214">
                                        <span class="badge">&nbsp;</span>Round-the-Clock Visibility
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-214" class="collapse" aria-labelledby="faqHeading-214" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Whether it’s daytime traffic or evening rush hours, branded trucks ensure 24/7 brand exposure, making them one of the most persistent forms of outdoor marketing.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> 
            </div>

          </div>

        </section>



        <section id="service-cards" class="blog-section  section pr-impact-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-7">
                    <h4>Types of Truck Advertising</h4>
                    <p>At DataMySite, we offer several customizable formats depending on your brand goals, duration, and budget:</p>
                    <div class="faq " id="accordion">
                        <div class="card">
                            <div class="card-header" id="faqHeading-220">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-220" data-aria-expanded="true" data-aria-controls="faqCollapse-220">
                                        <span class="badge">&nbsp;</span>Full Truck Wrap
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-220" class="collapse" aria-labelledby="faqHeading-220" data-parent="#accordion">
                                <div class="card-body">
                                    <p>The entire vehicle—both sides, rear, and sometimes even the front—is covered in branded vinyl graphics. This offers maximum visibility and strong recall value.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-221">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-221" data-aria-expanded="false" data-aria-controls="faqCollapse-221">
                                        <span class="badge">&nbsp;</span>Partial Wrap
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-221" class="collapse" aria-labelledby="faqHeading-221" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Ideal for brands looking for budget-friendly campaigns. It covers specific sections (like side panels or rear doors) while still maintaining an eye-catching look.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-222">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-222" data-aria-expanded="false" data-aria-controls="faqCollapse-222">
                                        <span class="badge">&nbsp;</span>Trailer or Container Branding
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-222" class="collapse" aria-labelledby="faqHeading-222" data-parent="#accordion">
                                <div class="card-body">
                                    <p>For logistics companies or brands using trailers, this option offers large-format visibility—turning cargo carriers into powerful advertising mediums.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-223">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-223" data-aria-expanded="false" data-aria-controls="faqCollapse-223">
                                        <span class="badge">&nbsp;</span>LED or Digital Truck Advertising
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-223" class="collapse" aria-labelledby="faqHeading-223" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Modern trucks can be equipped with LED screens or digital billboards to display animated content, videos, and promotional visuals for premium campaigns.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-224">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-224" data-aria-expanded="false" data-aria-controls="faqCollapse-224">
                                        <span class="badge">&nbsp;</span>Event & Campaign Trucks
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-224" class="collapse" aria-labelledby="faqHeading-224" data-parent="#accordion">
                                <div class="card-body">
                                    <p>For short-term campaigns, events, or product launches, branded trucks can act as mobile event booths, reaching audiences directly across key city locations.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> 

                <div class="col-lg-1"></div>
                <div class="col-lg-4 vertical-center">
                    <img src="{{URL::to('/public/section-images/image145.png')}}" width="100%" alt="Impact of PR Coverage">
                </div>
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section striped-section section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row">
                <div class="col-lg-12 mb-0">
                    
                </div>
                <div class="col-lg-5 vertical-center">
                    <h4>Strategic Impact of Truck Advertising</h4>
                    <ul>
                        <li><strong>Boosts local brand awareness</strong> – Perfect for small and mid-size businesses looking to dominate their local market.</li>
                        <li><strong>Supports national campaigns</strong> – For larger brands, trucks can complement digital and billboard advertising across multiple emirates.</li>
                        <li><strong>Enhances brand credibility</strong> – A well-designed truck wrap signals professionalism and trustworthiness.</li>
                        <li><strong>Drives customer recall</strong> – Repeated exposure from mobile visibility ensures long-term brand remembrance.</li>
                    </ul>
                </div>
                <div class="col-lg-2 vertical-center">
                    <img src="{{URL::to('/public/section-images/image146.png')}}" width="100%" alt="Why choose us">
                </div> 

                <div class="col-lg-5 vertical-center">
                    <h4>Where Truck Advertising Works Best</h4>
                    <ul>
                        <li><strong>Dubai</strong> – Sheikh Zayed Road, Al Quoz, Jebel Ali, Business Bay, Downtown</li>
                        <li><strong>Abu Dhabi</strong> – Mussafah, Khalifa City, Corniche Road</li>
                        <li><strong>Sharjah & Ajman</strong> – Industrial areas, highways, and residential routes</li>
                        <li><strong>Northern Emirates</strong> – Fujairah, Ras Al Khaimah, Umm Al Quwain routes</li>
                    </ul>
                    <p>Our team at DataMySite collaborates with local transport fleets to ensure your ads travel through high-impact, traffic-dense routes, maximizing impressions and ROI.</p>
                </div>
                <div class="col-lg-12">
                    <h4>Transform Your Fleet into a Moving Billboard</h4>
                    <p>
                        Truck advertising is more than just a branding technique—it’s a powerful storytelling tool on wheels. Whether you’re promoting a new product, increasing brand visibility, or launching a seasonal campaign, your message will reach thousands of eyes daily. 
                        At DataMySite, we make sure every kilometer your truck drives adds value to your brand.
                    </p>
                </div>
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-2 vertical-center mob-not-visible">
                    <img src="{{URL::to('/public/assets/img/why-us.png')}}" width="80%" alt="Why choose us">
                </div>
                <div class="col-lg-2"></div>
                <div class="col-lg-8 vertical-center">
                    <h4>How Much Does Truck Advertising Cost in Dubai?</h4>
                    <p>Pricing depends on fleet size, wrap coverage, and campaign duration.</p>
                    <br>
                    <h4>Why DataMySite</h4>
                    <p>
                        In-house design, printing, and wrapping · route planning across Dubai and the UAE · 1,200+ campaigns delivered. <a href="https://datamysite.com/client-case-studies" target="_blank">See case studies →</a>
                    </p>
                    
                </div> 
            </div>

          </div>

        </section>




        <section id="service-cards" class="blog-section  striped-section section pr-blogs-section">

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

                <div class="col-lg-4">
                    <img src="{{URL::to('/public/assets/img/faq-new.png')}}" width="100%" alt="FAQs">
                </div> 
                <div class="col-lg-1"></div>
                <div class="col-lg-7 vertical-center">
                    <h4>FAQs</h4>
                    <div class="faq " id="accordion">
                        <div class="card">
                            <div class="card-header" id="faqHeading-10">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-10" data-aria-expanded="true" data-aria-controls="faqCollapse-10">
                                        <span class="badge">&nbsp;</span>How much does truck advertising cost in Dubai?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-10" class="collapse show" aria-labelledby="faqHeading-10" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Rented mobile billboard trucks run from AED 25,000 a month per truck including the driver and route. Wrapping your own truck is a one-off cost from AED 2,500 for a partial wrap to AED 8,000 for a full wrap, with no monthly fee.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-11">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-11" data-aria-expanded="false" data-aria-controls="faqCollapse-11">
                                        <span class="badge">&nbsp;</span>Should we wrap our own fleet or rent a mobile billboard?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-11" class="collapse" aria-labelledby="faqHeading-11" data-parent="#accordion">
                                <div class="card-body">
                                    <p>If you already run vans or trucks daily, wrap them: one payment, two to three years of exposure. If you need a specific route or a short campaign around a launch, rent a mobile billboard for the month.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-12">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-12" data-aria-expanded="false" data-aria-controls="faqCollapse-12">
                                        <span class="badge">&nbsp;</span>Which routes do the trucks cover?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-12" class="collapse" aria-labelledby="faqHeading-12" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Standard routes run Sheikh Zayed Road, Al Khail Road, Emirates Road and the port and free-zone corridors, with time at traffic lights where the ad is read. We can fix a route to your target area and share GPS logs.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-13">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-13" data-aria-expanded="false" data-aria-controls="faqCollapse-13">
                                        <span class="badge">&nbsp;</span>Do we need an RTA permit?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-13" class="collapse" aria-labelledby="faqHeading-13" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes for commercial vehicle branding, and we handle it. Approval usually takes five to ten working days once the artwork is final, and the design must meet the content rules for outdoor media in Dubai.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-14">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-14" data-aria-expanded="false" data-aria-controls="faqCollapse-14">
                                        <span class="badge">&nbsp;</span>How long does a truck wrap last?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-14" class="collapse" aria-labelledby="faqHeading-14" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Quality cast vinyl lasts two to three years in UAE heat if it's installed in a shaded bay and washed properly. We use cast vinyl, not calendared, for exactly that reason.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-15">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-15" data-aria-expanded="false" data-aria-controls="faqCollapse-15">
                                        <span class="badge">&nbsp;</span>Can I choose specific routes for my branded trucks?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-15" class="collapse" aria-labelledby="faqHeading-15" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes. We work with our logistics partners to plan route-specific campaigns. You can target certain areas—like Sheikh Zayed Road, Jebel Ali, or Dubai Marina—to align with your target audience’s location and travel behavior.</p>
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
   "name": "How much does truck advertising cost in Dubai?",
   "acceptedAnswer": {
    "@type": "Answer",
    "text": "Rented mobile billboard trucks run from AED 25,000 a month per truck including the driver and route. Wrapping your own truck is a one-off cost from AED 2,500 for a partial wrap to AED 8,000 for a full wrap, with no monthly fee."
   }
  },
  {
   "@type": "Question",
   "name": "Should we wrap our own fleet or rent a mobile billboard?",
   "acceptedAnswer": {
    "@type": "Answer",
    "text": "If you already run vans or trucks daily, wrap them: one payment, two to three years of exposure. If you need a specific route or a short campaign around a launch, rent a mobile billboard for the month."
   }
  },
  {
   "@type": "Question",
   "name": "Which routes do the trucks cover?",
   "acceptedAnswer": {
    "@type": "Answer",
    "text": "Standard routes run Sheikh Zayed Road, Al Khail Road, Emirates Road and the port and free-zone corridors, with time at traffic lights where the ad is read. We can fix a route to your target area and share GPS logs."
   }
  },
  {
   "@type": "Question",
   "name": "Do we need an RTA permit?",
   "acceptedAnswer": {
    "@type": "Answer",
    "text": "Yes for commercial vehicle branding, and we handle it. Approval usually takes five to ten working days once the artwork is final, and the design must meet the content rules for outdoor media in Dubai."
   }
  },
  {
   "@type": "Question",
   "name": "How long does a truck wrap last?",
   "acceptedAnswer": {
    "@type": "Answer",
    "text": "Quality cast vinyl lasts two to three years in UAE heat if it's installed in a shaded bay and washed properly. We use cast vinyl, not calendared, for exactly that reason."
   }
  }
 ]
}
    </script>
@endsection