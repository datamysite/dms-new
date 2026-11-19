@extends('web.includes.master')
@section('metaAddition')
    
@endsection
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
                        <h1>&#10687; Outdoor Advertising in Dubai</h1>
                        <h2>Elevate Your Brand's Visibility</h2>
                        <h4>and Stand Out with Impactful Outdoor Advertising</h4>
                        <br>
                        <p>
                            Out-of-home advertising owns the one space no ad-blocker can touch: the real world. DataMySite plans and places outdoor advertising across Dubai and the UAE — from a single highway unipole to a full building takeover — securing the prime locations that put your brand in front of the right audience all day, every day.
                        </p>
                        <span><strong>Get a Free Outdoor Advertising Quote → · <a href="https://api.whatsapp.com/send/?phone=971525287259" target="_blank">WhatsApp: +971 52 528 7259</a></strong></span>
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
            <section class="hub-guides"><h2>Outdoor advertising in practice</h2><ul>
            <li><a href="/advertise-on-burj-khalifa-costs">Advertising on the Burj Khalifa: costs and formats</a></li>
            <li><a href="/outdoor-advertising-in-ajman">Outdoor advertising in Ajman</a></li>
            <li><a href="/lamp-post-advertising-dubai">Lamp post advertising rates</a></li>
            <li><a href="/large-format-hoardings-dubai-marina">Hoarding advertising in Dubai Marina</a></li>
            </ul></section>
        </div>

        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">
                    
                </div>
                <div class="col-lg-6">
                    <iframe class="service-iframe outdoor-iframe" src="https://www.youtube.com/embed/Ny7v7w6dtq4?si=oAHAurPi9RcZjBPT" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <div class="col-lg-6">
                    <h4>Why Outdoor Advertising Works in Dubai</h4>
                    <p>
                        Dubai's roads carry some of the world's highest-value commuter traffic. OOH delivers unskippable, repeated exposure that builds instant brand credibility — which is why it remains a top-recall format for launches, real estate, and brand-building.
                    </p>
                    <br>
                    <h4>Our Outdoor Advertising Formats</h4>
                    <ul>
                        <li><a href="https://datamysite.com/outdoor-advertising/billboards-advertising">Billboard advertising</a> — prime static and digital sites across the emirate.</li>
                        <li><a href="https://datamysite.com/outdoor-advertising/billboards-advertising/building-wraps">Building wraps</a> — full-façade dominance.</li>
                        <li><a href="https://datamysite.com/outdoor-advertising/billboards-advertising/hoarding">Hoardings</a> — large-format coverage on construction sites.</li>
                        <li><a href="https://datamysite.com/outdoor-advertising/billboards-advertising/wallscapes">Wallscapes</a> — oversized wall displays.</li>
                        <li><a href="https://datamysite.com/outdoor-advertising/unipoles">Unipoles</a> — commanding highway presence.</li>
                        <li><a href="https://datamysite.com/outdoor-advertising/lamp-post">Lamp post banners</a> — corridor branding along key routes.</li>

                    </ul>
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

            <div class="row gy-4 first-row outdoor-service-col-section">

                <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">
                    <h4>Our Outdoor Advertising Services</h4>
                    <p>Here's what we offer:</p>
                </div>
                <div class="col-lg-6">
                    <h5>Maximise Brand Awareness with Premium Locations</h5>
                    <p>
                        Placing your advertisement in significant locations will increase its engagement. Sheikh Zayed Road, Downtown Dubai, and Dubai Marina provide unmatched footfall and traffic. Your message will remain in front of the audience.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Customised Outdoor Media Campaigns Dubai</h5>
                    <p>
                       Your brand has a unique story. Outdoor media campaigns Dubai ensure that the story reaches the right people. Every format brings a fresh opportunity to connect with your audience, from static billboards to dynamic LED screens. Well-placed messaging builds consumer confidence and drives engagement.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Round-the-Clock Visibility and Consistent Branding</h5>
                    <p>
                        Unlike digital ads that can be skipped, outdoor advertising Dubai stays visible 24/7. Your brand maintains a constant presence, ensuring top-of-mind awareness among potential customers – whether day or night, your message continues working. Your message can appear on premium sites across Dubai's most influential commercial districts.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Excellent Outdoor Billboard Marketing UAE</h5>
                    <p>
                        A strong marketing campaign blends online as well as offline engagements. Outdoor advertisements direct traffic to your website, social media, and store. QR codes, hashtags, and interactive displays encourage consumers to take action instantly.
                    </p>
                </div>  


                <div class="col-lg-6">
                    <h5>Seamless Integration with Digital Strategies</h5>
                    <p>
                        Unlike digital ads that can be skipped, outdoor advertising Dubai stays visible 24/7. Your brand maintains a constant presence, ensuring top-of-mind awareness among potential customers – whether day or night, your message continues working. Your message can appear on premium sites across Dubai's most influential commercial districts.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Converting High Traffic into Revenue</h5>
                    <p>
                        Research proves the impact of outdoor media campaigns Dubai on business growth. UAE residents notice outdoor advertisements during their daily commute. Your brand stays visible for over 18 hours every day. Digital displays allow you to refine your message based on real-time performance data.
                    </p>
                </div>  

                <div class="col-lg-6">
                    <h5>Best Outdoor Advertising Solutions GCC</h5>
                    <p>
                        Dubai's market extends beyond city limits. Best outdoor advertising solutions GCC ensure your brand reaches audiences across the Gulf region. Expanding your message beyond Dubai enhances credibility and market influence.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Outdoor Advertising that Delivers ROI</h5>
                    <p>
                        Every marketing investment must yield results. Outdoor advertising services UAE generate brand recall, customer engagement, and increased sales. Whether launching a new product or reinforcing brand positioning, well-placed ads drive measurable growth.
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

                <div class="col-lg-8 vertical-center">
                    <h4>Prime Locations We Cover</h4>
                    <p>Sheikh Zayed Road, Downtown & Burj Khalifa, Dubai Marina, Business Bay, and key interchanges — plus emirate-wide reach across Abu Dhabi, Sharjah, and Ajman.</p>
                    <br>
                    <h4>How Much Does Outdoor Advertising Cost in Dubai?</h4>
                    <p>Cost varies by format, location, size, and duration. We build a placement plan to your budget. <a href="https://datamysite.com/contact-us">Request a quote →</a></p>
                    <br>
                    <h4>Why DataMySite</h4>
                    <p>1,200+ campaigns · in-house creative, permits & installation · 80+ brands · 5.0 Google rating. <a href="https://datamysite.com/client-case-studies">See case studies →</a></p>
                    
                    
                </div>
                <div class="col-lg-2"></div>
                <div class="col-lg-2 mob-not-visible">
                    <img src="{{URL::to('/public/assets/img/why-us.png')}}" width="100%" alt="Why choose us">
                </div> 
            </div>

          </div>

        </section>




        <section id="service-cards" class="blog-section striped-section section pr-blogs-section">

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
                                        <span class="badge">&nbsp;</span>How much does outdoor advertising cost in Dubai?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-10" class="collapse show" aria-labelledby="faqHeading-10" data-parent="#accordion">
                                <div class="card-body">
                                    <p> It depends on format, location, and duration — request a quote.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-11">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-11" data-aria-expanded="false" data-aria-controls="faqCollapse-11">
                                        <span class="badge">&nbsp;</span>What outdoor formats are available?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-11" class="collapse" aria-labelledby="faqHeading-11" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Billboards, building wraps, hoardings, wallscapes, unipoles, lamp posts, and DOOH screens.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-12">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-12" data-aria-expanded="false" data-aria-controls="faqCollapse-12">
                                        <span class="badge">&nbsp;</span>Do you cover the whole UAE?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-12" class="collapse" aria-labelledby="faqHeading-12" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes — Dubai, Abu Dhabi, Sharjah, Ajman, and beyond.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" id="faqHeading-13">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-13" data-aria-expanded="false" data-aria-controls="faqCollapse-13">
                                        <span class="badge">&nbsp;</span>What happens if my outdoor ad is vandalized or damaged?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-13" class="collapse" aria-labelledby="faqHeading-13" data-parent="#accordion">
                                <div class="card-body">
                                    <p>We provide a maintenance guarantee. Any damage reported is addressed within 24–48 hours to ensure your brand image remains pristine.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-14">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-14" data-aria-expanded="false" data-aria-controls="faqCollapse-14">
                                        <span class="badge">&nbsp;</span>Do you handle the government permits required for outdoor ads?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-14" class="collapse" aria-labelledby="faqHeading-14" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes, we manage the entire legal and permitting process with local authorities, so you don’t have to worry about compliance.</p>
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
@include('web.includes.elements.local-schema')
@endsection