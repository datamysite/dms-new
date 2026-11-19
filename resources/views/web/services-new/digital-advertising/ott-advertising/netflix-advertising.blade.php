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
                        <h2>Unlock the Power</h2>
                        <h4>of Premium OTT Campaign</h4>
                        <br>
                        <p>
                            Netflix advertising in the UAE runs through the platform's ad-supported tier, which launched across the region in 2023. CPMs typically run AED 40-80 for UAE-targeted campaigns, with campaign minimums that most buying desks set at AED 15,000-20,000. That's the real-money starting point — not a ""get in touch for pricing"" deflection.
                            <br><br>
                            The honest context: Netflix in the UAE competes with StarzPlay, Shahid, OSN+ and Saudi-strong SPTV for regional OTT budgets. For brands that specifically want premium, English-first, international content audiences, Netflix is the platform. For Arabic-first or regional content alignment, StarzPlay and Shahid have better options. Knowing the difference saves budget.

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
                    <h4>What Netflix Advertising Actually Is</h4>
                    <p>Netflix's ad tier (called "Standard with Ads") launched globally in late 2022 and rolled out across MENA markets through 2023-24. It gives subscribers access to Netflix's full library at a lower subscription price in exchange for 4-5 minutes of ads per hour of content — non-skippable, 15 or 30 second spots that play pre-roll before episodes and as mid-roll breaks during longer content.</p>
                    <p>What you can and cannot control: you can target by age, gender, emirate, device type and content genre. You cannot choose specific shows or series — Netflix doesn't sell placement against specific titles. You buy audience segments, not show sponsorships. For true show-level integration or co-branded content, the conversation goes to Netflix's branded content team directly, and the minimums are considerably higher.</p>
                    <p>Completion rates on Netflix ads run higher than most digital formats — typically 85-95% — because the non-skippable format plays out before content viewers are actively engaged with. That's the genuine value: attention quality, not just impression volume.</p>
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

        

        <section id="service-cards" class="blog-section section striped-section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row">
                <div class="col-lg-12 mb-0">
                    
                </div>
                <div class="col-lg-6 mt-0">
                    <h4>Why Advertise on Netflix?</h4>
                    <table class="table">
                    <tr><th>Format</th><th>CPM (AED)</th><th>Campaign minimum</th><th>Targeting</th></tr>
                    <tr><td>15-sec pre-roll / mid-roll</td><td>AED 40-65</td><td>AED 15,000</td><td>Demo + geo + genre</td></tr>
                    <tr><td>30-sec pre-roll / mid-roll</td><td>AED 55-80</td><td>AED 20,000</td><td>Demo + geo + genre</td></tr>
                    <tr><td>Pause ads (display overlay)</td><td>AED 35-55</td><td>AED 12,000</td><td>Demo + geo</td></tr>
                    </table>
                    
                    <p>Production requirements: Netflix runs video ads at 1920x1080, MP4/H.264, max 200MB, stereo audio. Their review process takes 5-7 business days. Creative that works on Netflix tends to be cinematic in quality — not repurposed social media content. Budget AED 8,000-20,000 for production if you don't have pre-existing video assets at the right spec.</p>
                    
                    <p>Campaign timelines: plan 3-4 weeks from brief to launch to allow for buying, creative review and trafficking. Running a campaign in parallel with <a href="https://datamysite.com/digital-advertising/ott-advertising/starzplay-advertising">StarzPlay</a> or Shahid typically costs 10-15% more in combined fees but doubles the UAE OTT reach.</p>
                </div>

                <div class="col-lg-6 mt-0">
                    <h4>Types of Netflix Advertising Formats</h4>
                    <p>Netflix offers multiple ad formats designed for maximum viewer engagement and brand impact:</p>
                    <div class="faq " id="accordion">
                        <div class="card">
                            <div class="card-header" id="faqHeading-30">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-30" data-aria-expanded="true" data-aria-controls="faqCollapse-30">
                                        <span class="badge">&nbsp;</span>Pre-Roll Ads
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-30" class="collapse" aria-labelledby="faqHeading-30" data-parent="#accordion">
                                <div class="card-body">
                                    <p>These short video ads play before a show or movie begins. They’re ideal for brand introductions or product awareness campaigns. With unskippable formats, pre-roll ads ensure complete message delivery.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-31">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-31" data-aria-expanded="false" data-aria-controls="faqCollapse-31">
                                        <span class="badge">&nbsp;</span>Mid-Roll Ads
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-31" class="collapse" aria-labelledby="faqHeading-31" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Mid-roll ads appear during natural breaks in content, similar to TV commercials but shorter and more contextual. These ads achieve higher retention rates as they blend seamlessly within the viewing experience.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-32">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-32" data-aria-expanded="false" data-aria-controls="faqCollapse-32">
                                        <span class="badge">&nbsp;</span>Display Overlay Ads
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-32" class="collapse" aria-labelledby="faqHeading-32" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Display overlays or companion banners appear subtly during playback or within the Netflix interface. These non-intrusive placements enhance brand visibility without interrupting the viewer’s journey.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-34">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-34" data-aria-expanded="false" data-aria-controls="faqCollapse-34">
                                        <span class="badge">&nbsp;</span>Branded Content & Product Integrations
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-34" class="collapse" aria-labelledby="faqHeading-34" data-parent="#accordion">
                                <div class="card-body">
                                    <p>
                                        For a more storytelling-driven approach, brands can collaborate with Netflix content teams to integrate products organically into scenes or sponsor original productions. This creates emotional connections that last longer than traditional ad impressions.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-33">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-33" data-aria-expanded="false" data-aria-controls="faqCollapse-33">
                                        <span class="badge">&nbsp;</span>Programmatic OTT Campaigns
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-33" class="collapse" aria-labelledby="faqHeading-33" data-parent="#accordion">
                                <div class="card-body">
                                    <p>
                                        Through programmatic advertising, Netflix allows brands to automate media buying, ensuring optimized delivery, real-time adjustments, and data-backed decisions for better performance.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

          </div>

        </section>



    
        <section id="service-cards" class="blog-section  section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row split-block">


                <div class="col-lg-3 vertical-center">
                    <img src="{{URL::to('/public/section-images/image107.png')}}" width="100%" alt="Why choose us">
                </div> 
                <div class="col-lg-1"></div>
                <div class="col-lg-8 vertical-center">
                    <h4>Netflix Advertising in the UAE: What the Audience Looks Like</h4>
                    <p>UAE Netflix users skew English-speaking, 25-44, high household income, and device-diverse (Smart TV primary, mobile secondary). They're watching premium content — US and UK dramas, documentaries, original series — and the ad environment is brand-safe by design. Netflix keeps ad load deliberately low compared to traditional TV to protect the viewing experience, which means fewer ads per viewer but higher attention per ad.</p>
                    
                    <p>The audience profile makes Netflix the right platform for premium consumer categories: automotive, real estate, financial services, luxury retail, hospitality and technology. It's a harder fit for categories that need Arabic-first reach, mass-market FMCG, or South Asian diaspora audiences — those audiences are better served by regional OTT platforms and Hindi/Arabic radio.</p>
                    
                    <p>One regional nuance: Netflix penetration in the UAE is strong in Dubai and Abu Dhabi's expat-majority communities. If your target is UAE nationals or Arabic-speaking residents, StarzPlay's Arabicontent and Shahid's catalogue alignment will serve that audience better. The two platforms can run simultaneously for a combined UAE reach strategy — we build those cross-OTT plans regularly.</p>
                    <br>
                    <h4>Advantages of Partnering with DataMySite for Netflix Advertising</h4>
                    <ul>
                        <li><strong>Expert OTT Strategists</strong> – Our team specializes in crafting performance-driven OTT campaigns tailored for the UAE and GCC markets.</li>
                        <li><strong>End-to-End Campaign Management</strong> – From concept creation to launch and optimization, we handle it all.</li>
                        <li><strong>Creative Excellence</strong> – We design high-quality video ads that fit Netflix’s cinematic standards.</li>
                        <li><strong>Real Results, Not Vanity Metrics</strong> – Our focus is on measurable outcomes like brand recall, engagement, and lead quality.</li>
                        <li><strong>Cross-Platform Synergy</strong> – We integrate your Netflix campaign with YouTube, StarzPlay, and digital ads for unified brand storytelling.</li>
                    </ul>
                </div>
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section section  striped-section why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row">


                <div class="col-lg-8 vertical-center">
                    <h4>How Netflix Advertising Works</h4>
                    <p>
                        Netflix advertising follows a structured, data-driven approach to ensure that your campaign is strategic, measurable, and impactful. Here’s how we manage it at DataMySite:
                    </p>
                    <ul>
                        <li>
                            <strong>Campaign Planning & Strategy</strong><br>
                            We start by defining your objectives — brand awareness, lead generation, or conversions — and identifying your target audience based on demographics and viewing behavior.
                        </li>
                        <li>
                            <strong>Creative Development</strong><br>
                            Our team helps you craft compelling video creatives that align with Netflix’s high production standards and resonate emotionally with your audience.
                        </li>
                        <li>
                            <strong>Ad Placement & Targeting</strong><br>
                            We leverage Netflix’s audience insights and AI-based data tools to ensure your ads are shown to the most relevant viewers, across multiple devices — from smart TVs to mobile apps.
                        </li>
                        <li>
                            <strong>Performance Tracking & Optimization</strong><br>
                            Using Netflix’s analytics dashboard, we track ad performance in real-time, adjusting targeting, creatives, and budgets to maximize efficiency and ROI.
                        </li>
                    </ul>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-3 vertical-center">
                    <img src="{{URL::to('/public/section-images/image108.png')}}" width="100%" alt="Why choose us">
                </div> 
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-sectionsection why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row split-block">

                <div class="col-lg-3 vertical-center">
                    <img src="{{URL::to('/public/section-images/image109.png')}}" width="100%" alt="Why choose us">
                </div>

                <div class="col-lg-1"></div>
                <div class="col-lg-8 vertical-center">
                    <h4>Is Netflix Advertising Right for Your Brand?</h4>
                    <p>If your audience values storytelling, entertainment, and premium experiences — Netflix advertising is the perfect fit. It’s ideal for:</p>
                    <ul>
                        <li>Luxury and lifestyle brands</li>
                        <li>Automotive companies</li>
                        <li>Tourism and travel agencies</li>
                        <li>Tech and e-commerce platforms</li>
                        <li>Education and entertainment brands</li>
                        <li>Financial services and real estate firms</li>
                    </ul>
                    <p>By placing your brand within Netflix’s cinematic world, you build trust, emotional connection, and long-term recall — the three pillars of successful digital marketing.</p>
                    <br>
                    <h4>Start Your Netflix Advertising Campaign Today</h4>
                    <p>
                        In the age of streaming, consumers are no longer just watching — they’re connecting emotionally with the stories they love. Advertising on Netflix allows your brand to become part of that emotional journey.
                        <br><br>
                        At DataMySite, we help you harness the power of OTT advertising with tailored Netflix campaigns that engage, inspire, and convert. Whether you want to reach audiences in Dubai, Sharjah, Abu Dhabi, or across the globe — our expert team ensures your brand gets noticed by the right people at the right time.
                        <br>
                        Take your marketing strategy beyond screens — and into the hearts of millions of Netflix viewers.
                        <br><br>
                        Contact DataMySite today to discuss your Netflix Advertising campaign and see how we can turn your brand story into a screen-worthy experience.
                    </p>
                    <ul>
                        
                    </ul>
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
                                        <span class="badge">&nbsp;</span>How do I place my brand on Netflix in the UAE?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-10" class="collapse show" aria-labelledby="faqHeading-10" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Netflix's ad-supported tier is available to advertisers through certified buying partners. DMS books UAE campaigns directly — we handle the media plan, creative trafficking, targeting setup and performance reporting. There's no direct self-serve option for UAE advertisers as of 2026.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-11">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-11" data-aria-expanded="false" data-aria-controls="faqCollapse-11">
                                        <span class="badge">&nbsp;</span>Can I choose which Netflix shows my ads appear in?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-11" class="collapse" aria-labelledby="faqHeading-11" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Not by individual title. You can target by genre (drama, documentary, comedy etc.) and by content maturity rating, which filters broadly by content type. For show-level integration, Netflix's original content team handles branded content deals separately with substantially higher minimums. Most brand campaigns run on audience targeting, not content targeting.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-12">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-12" data-aria-expanded="false" data-aria-controls="faqCollapse-12">
                                        <span class="badge">&nbsp;</span>How is Netflix different from advertising on StarzPlay or Shahid?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-12" class="collapse" aria-labelledby="faqHeading-12" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Audience profile is the main difference. Netflix skews English-speaking, expat-heavy, international content fans. StarzPlay and Shahid reach stronger Arabic-speaking and regional Arabic content audiences. CPMs on Netflix are typically higher because the audience is premium and ad load is low. For full UAE OTT coverage, a split across both platforms makes sense for most categories.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-13">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-13" data-aria-expanded="false" data-aria-controls="faqCollapse-13">
                                        <span class="badge">&nbsp;</span>What completion rate should I expect on Netflix ads?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-13" class="collapse" aria-labelledby="faqHeading-13" data-parent="#accordion">
                                <div class="card-body">
                                    <p>
                                        Non-skippable format means completion rates run 85-95% on most Netflix campaigns, significantly above YouTube TrueView (typically 30-50%) and social video. The viewing intent is high because users are mid-episode, not scrolling a feed.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-14">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-14" data-aria-expanded="false" data-aria-controls="faqCollapse-14">
                                        <span class="badge">&nbsp;</span>What is the minimum budget to start on Netflix?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-14" class="collapse" aria-labelledby="faqHeading-14" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Buying desks typically set a minimum of AED 15,000 for a 15-second spot campaign, AED 20,000 for 30 seconds. Below that, the impression volume isn't sufficient to generate meaningful reach. Production is additional — budget AED 8,000-20,000 if you need video creative built to Netflix spec.</p>
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