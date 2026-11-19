@extends('web.includes.master')
@section('metaAddition')
    
@endsection
@section('addStyle')
    <link href="{{URL::to('/public')}}/assets/css/service.css" rel="stylesheet">
    <style>
    </style>
@endsection
@section('content')

    <main class="main">

        <!-- Page Title -->
          <div class="page-title dark-background" data-aos="fade" style="background-image: url('{{URL::to('public/assets/img/service-new/'.$subservice->slug.'.jpg')}}');">
            <div class="container head-container-service">
                <div class="row">
                    <div class="col-lg-8 head-para-service">
                        <h1>&#10687; {{$subservice->name}}</h1>
                        <h2>Light Up the Streets</h2>
                        <h4>with Powerful Lamp Post Advertising</h4>
                        <br>
                        <p>
                            Lamp post advertising in Dubai costs from AED 500 per pole per month for static banners, rising to AED 5,000+ for digital LED units in premium zones. A campaign of 20 poles along Jumeirah Beach Road runs about AED 15,000-20,000 for the month including production. That's the number most people ask us for first, so we put it upfront.
                            <br><br>
                            The format works differently from billboards. You're not buying one large impact — you're buying repetition. A driver on Al Wasl Road passes your banner at lamp post 1, lamp post 4, lamp post 8. By lamp post 12, the brand is familiar. That frequency effect is harder to achieve with a single billboard at twice the cost.
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
                <div class="col-lg-6 vertical-center">
                    <iframe class="service-iframe" src="https://www.youtube.com/embed/Ny7v7w6dtq4?si=oAHAurPi9RcZjBPT" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>  
                <div class="col-lg-6 vertical-center" data-aos="fade-up" data-aos-delay="100">

                    <h4>What is Lamp Post Advertising?</h4>
                    <p>
                        Lamp Post Advertising (also known as streetlight advertising) involves placing promotional banners, flags, or digital screens on lamp posts along roads, walkways, or parking areas.
                        <br><bR>
                        These ads are placed at eye level — making them easily visible to both drivers and pedestrians. Unlike large-scale billboards, lamp post ads offer repetition and proximity, with multiple ads placed in sequence along a route.
                        <br>
                        In Dubai’s vibrant and fast-moving landscape, where every brand competes for attention, Lamp Post Advertising offers a unique way to reach audiences through consistent, high-frequency exposure. Whether along bustling highways, urban streets, or commercial districts, lamp post ads ensure your message shines — day and night.
                        <br><br>
                        This repetition reinforces brand awareness and helps create a strong visual identity across key city zones.
                    </p>

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
                </div>
                <div class="col-lg-8 vertical-center">
                    
                    <h4>Why Choose Lamp Post Advertising in Dubai?</h4>
                    <br>
                    <p>Dubai's roads are long and the commutes are longer. The average resident drives 45+ minutes each direction on routes that don't change much week to week. Lamp post advertising takes advantage of that routine — the same commuter sees your banner Monday, Wednesday, Friday, for a month. That's more touchpoints than most digital campaigns achieve with a fraction of the production complexity.</p>
                    <br>
                    <p>It's also genuinely local in a way that unipoles aren't. A restaurant in Jumeirah can brand the 500 metres around its location for AED 8,000-12,000 a month. A school opening in JVC can saturate the feeder roads. A real estate launch can wrap an entire community perimeter. The format scales down to a neighbourhood level that large-format outdoor simply can't serve.</p>
                    <br>
                    <p>One practical point most agencies don't mention: lamp post ads are visible at night when the streetlights are on. A static banner on an illuminated pole gets passive lighting for free. Digital units are self-lit and visible even in the deep evening hours along beach roads and promenades. For hospitality, food and entertainment brands, the night-time window often outperforms the daytime one.</p>
                </div> 
                <div class="col-lg-1"></div>
                <div class="col-lg-3 vertical-center">
                    <img src="{{URL::to('/public/section-images/image54.png')}}" class="flyer-img" width="100%" alt="human billboard">
                </div>
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section section pr-icon-section">

          <div class="container">

            <div class="row gy-4 first-row split-block">

                <div class="col-lg-3 vertical-center">
                    <img src="{{URL::to('/public/section-images/image55.png')}}" class="flyer-img" width="100%" alt="human billboard">
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-8 vertical-center">
                    
                    <h4>Types of Lamp Post Advertising in Dubai</h4>
                    <br>
                    <p>Static lamp post banners are double-sided printed vinyl panels, typically 70x140cm or 80x160cm, mounted on the pole brackets. They carry one message for the campaign duration. Most real estate developers and event promoters use static — the message is fixed, the cost is predictable, and the format handles long-running campaigns well. Production runs AED 80-150 per banner including printing and installation; the main ongoing cost is the monthly media fee.</p>
                    <br>
                    <p>Digital lamp post units are LED screens on smart poles. They rotate between multiple ads, update remotely, and work for brands that need to change their message quickly — flash sale, countdown to event, product launch week. The media cost is higher (AED 2,000-5,000+ per unit monthly) and availability is concentrated in Downtown, Marina and Business Bay for now. For most campaign types, static is the right call. Digital makes sense when you genuinely need the flexibility.</p>
                    <br>
                    <p>The honest comparison: digital lamp posts get you three or four ad slots on a rotating basis, not exclusive ownership of the unit. If you're buying digital, make sure you understand the share-of-time you're actually getting.</p>
                </div> 
            </div>

          </div>

        </section>

        <section id="service-cards" class="blog-section section striped-section pr-icon-section">

          <div class="container">

            <div class="row gy-4 first-row outdoor-service-col-section">

                <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">
                    <h4>Our Lamp Post Advertising Services in Dubai</h4>
                    <p>
                        At DataMySite, we provide complete end-to-end lamp post advertising solutions, ensuring every campaign is well-planned, visually stunning, and fully compliant with Dubai Municipality regulations.
                        <br>
                        Our services include:

                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Location Planning & Strategy</h5>
                    <p>
                        We identify high-traffic routes and key city zones — such as Sheikh Zayed Road, Downtown, Marina, and Business Bay — to position your ads where they’ll have the most impact.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Creative Design</h5>
                    <p>
                       Our design team creates striking visuals that stand out on the move — ensuring brand consistency and clear messaging, even when viewed at a glance.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Printing & Production</h5>
                    <p>
                        We use high-quality, UV-resistant printing materials that withstand Dubai’s heat, sand, and humidity — ensuring your ads remain vibrant throughout the campaign.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Installation & Maintenance</h5>
                    <p>
                        Our skilled technicians handle safe and secure installation, along with regular maintenance checks to ensure banners stay in perfect condition.
                    </p>
                </div>  


                <div class="col-lg-12">
                    <h5>Digital Campaign Management</h5>
                    <p>
                        For digital lamp posts, we handle screen scheduling, creative rotations, and performance tracking to ensure optimal visibility and engagement.
                    </p>
                </div>  
            </div>
            <div class="uni-strip-see-more">
                <button data-class="outdoor-service-col-section">See More</button>
            </div>

          </div>

        </section>

        <section id="service-cards" class="blog-section section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-9">

                    <h4>Advantages of Choosing DataMySite</h4>
                    <ul>
                        <li><strong>Expertise in UAE Outdoor Media</strong>: We know the best-performing lamp post locations across Dubai.</li>
                        <li><strong>Creative Excellence</strong>: Our in-house designers craft visuals optimized for both day and night exposure.</li>
                        <li><strong>Complete Campaign Support</strong>: From concept to removal — we handle everything.</li>
                        <li><strong>Permit Handling</strong>: We manage all municipal and authority permissions for a smooth process.</li>
                        <li><strong>Cost Transparency</strong>: No hidden costs — you get clear, customized quotes based on campaign needs.</li>
                    </ul>
                    <br>
                    <h4>Top Locations for Lamp Post Advertising in Dubai</h4>
                    <p>The locations that earn results for our clients: Jumeirah Beach Road for lifestyle, retail and food brands (high pedestrian mix with vehicle traffic); Al Wasl Road for community-facing brands targeting Jumeirah and Umm Suqeim residents; Business Bay and DIFC for professional services; Deira's main commercial streets for South Asian and Arabic audiences at high volume; JBR Walk for tourist-skewing campaigns.</p>
                    <br>
                    <p>Sheikh Zayed Road lamp posts exist but the speed and lane width mean your banner competes with enormous billboards that dominate that streetscape. It works better as supporting frequency once a brand has unipole presence — not as a standalone play. For SZR-level reach without the budget, Al Khail Road lamp posts give you similar commuter exposure at lower rates.</p>
                    <br>
                    <p>Permit approval comes from the RTA and in some zones Dubai Municipality as well. Lead time is 10-14 business days for most locations. We handle all permit filing — it's part of every campaign, not a separate service charge.</p>

                </div>
                <div class="col-lg-3 vertical-center">
                    <img src="{{URL::to('/public/section-images/image56.png')}}" width="100%" alt="Why choose us">
                </div> 
            </div>

          </div>

        </section>



        <section id="service-cards" class="blog-section section striped-section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row split-block">

                <div class="col-lg-3 vertical-center">
                    <img src="{{URL::to('/public/section-images/image57.png')}}" width="100%" alt="Why choose us">
                </div> 
                <div class="col-lg-1"></div>
                <div class="col-lg-8 vertical-center">
                    <h4>Cost of Lamp Post Advertising in Dubai</h4>
                    <br>
                    <table class="table">
                    <tr><th>Type</th><th>Location</th><th>Monthly rate per pole</th><th>Production per unit</th></tr>
                    <tr><td>Static banner</td><td>Secondary roads / residential</td><td>AED 500-800</td><td>AED 80-120</td></tr>
                    <tr><td>Static banner</td><td>Prime zones (SZR, Downtown, JBR)</td><td>AED 800-1,500</td><td>AED 100-150</td></tr>
                    <tr><td>Digital LED unit</td><td>Downtown / Marina smart poles</td><td>AED 2,000-5,000+</td><td>Creative only</td></tr>
                    <tr><td>Event corridor package (50+ units)</td><td>Negotiated per zone</td><td>AED 400-700 per unit</td><td>Included in some packages</td></tr>
                    </table>
                    
                    <p>Minimum campaign size: most vendors set a minimum of 5-10 poles to make permit and installation costs worthwhile. Practical campaigns for real results run 15-30 poles minimum on a target route. The sweet spot for a local brand launch — 20-30 poles on two connected roads — typically runs AED 18,000-35,000 for a month including everything.</p>
                    

                </div>
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-8 vertical-center">
                    <h4>Light Up Dubai with Your Brand</h4>
                    <p>
                        Lamp post advertising is more than a display — it’s a city-wide storytelling medium that connects your brand with thousands every day. With their frequency, visibility, and versatility, lamp post ads help brands stay top-of-mind across Dubai’s most vibrant districts.
                        <br><br>
                        At DataMySite, we combine strategic placement, creative excellence, and local expertise to deliver lamp post advertising campaigns that truly shine — both day and night.
                        <br><br>
                        Contact DataMySite today to discuss your lamp post advertising campaign in Dubai or anywhere in the UAE and let your brand light up the skyline.
                    </p>

                </div>

                <div class="col-lg-1"></div>
                <div class="col-lg-3 mob-not-visible">
                    <img src="{{URL::to('/public/section-images/image58.png')}}" width="100%" alt="Why choose us">
                </div> 
            </div>

          </div>

        </section>



        <section id="service-cards" class="blog-section section  striped-section pr-blogs-section">

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

                <div class="col-lg-12">
                    <h4 class="text-center">Frequently Asked Questions</h4>
                    <br>
                    <div class="faq " id="accordion">
                        <div class="card">
                            <div class="card-header" id="faqHeading-10">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-10" data-aria-expanded="true" data-aria-controls="faqCollapse-10">
                                        <span class="badge">&nbsp;</span>How many lamp posts do I need for an effective campaign?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-10" class="collapse show" aria-labelledby="faqHeading-10" data-parent="#accordion">
                                <div class="card-body">
                                    <p>For a specific neighbourhood or road, 15-20 poles creates enough frequency that regular commuters notice the brand within a week. For a broader awareness campaign across a district, 30-50 poles over 2-3 connected roads. Five poles gets you presence but not frequency — the minimum for any meaningful brand recall is around 10-15 in a concentrated area.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-11">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-11" data-aria-expanded="false" data-aria-controls="faqCollapse-11">
                                        <span class="badge">&nbsp;</span>Can I choose which streets I want?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-11" class="collapse" aria-labelledby="faqHeading-11" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes. You specify the roads and zones, we map the available bracket positions and confirm what's live and what's currently occupied. Popular commercial streets fill up during festival and event seasons — DSF in December and Eid periods — so book 4-6 weeks ahead if you have a specific target road.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-12">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-12" data-aria-expanded="false" data-aria-controls="faqCollapse-12">
                                        <span class="badge">&nbsp;</span>What size are the banners?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-12" class="collapse" aria-labelledby="faqHeading-12" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Standard sizes are 70x140cm and 80x160cm for double-sided pole banners, though this varies by municipality zone and bracket type. We confirm the exact spec for your chosen locations before production so nothing is printed to the wrong size.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-13">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-13" data-aria-expanded="false" data-aria-controls="faqCollapse-13">
                                        <span class="badge">&nbsp;</span>What happens if a banner gets damaged mid-campaign?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-13" class="collapse" aria-labelledby="faqHeading-13" data-parent="#accordion">
                                <div class="card-body">
                                    <p>We do route checks weekly. Any damaged or sagging banner gets replaced at no additional charge — it's included in the campaign fee. Dubai's sand and heat in summer months mean occasional wear; that's expected and covered.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-14">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-14" data-aria-expanded="false" data-aria-controls="faqCollapse-14">
                                        <span class="badge">&nbsp;</span>Can I run lamp posts alongside billboards?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-14" class="collapse" aria-labelledby="faqHeading-14" data-parent="#accordion">
                                <div class="card-body">
                                    <p>That's usually the right structure. A billboard or unipole on SZR gives you scale and prestige; lamp posts on the neighbourhood roads around your actual location give you frequency with your local audience. The two formats do different jobs and combine well for real estate, clinic and retail launches.</p>
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