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
                        <h1>&#10687; Building Wrap Advertising Dubai — Facade & Scaffolding Cover Rates</h1>
                        <h2>High-Quality Building Wraps across Dubai</h2>
                        <br>
                        <p>Building wraps in Dubai start from AED 50,000/month for secondary-road facades and reach AED 350,000/month for prime Marina or Downtown locations. DataMysite manages full-service campaigns including creative, print, installation, and permit handling.</p>
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
                    <h4>What Is Building Wrap Advertising?</h4>
                    <p>
                        Building wrap advertising (also known as full building branding or building facade advertising) involves covering the exterior surface of a building — either partially or completely — with a massive, custom-printed banner or mesh material that displays your brand’s message.
                        <br>
                        These wraps are typically made from durable, weather-resistant materials designed to withstand Dubai’s hot and humid climate while maintaining color vibrancy and clarity.
                        <br>
                        They’re ideal for:
                    </p>
                    <ul>
                        <li>Real estate project promotions</li>
                        <li>Event and product launches</li>
                        <li>Corporate branding campaigns</li>
                        <li>Long-term brand visibility in high-traffic areas    </li>                
                    </ul>
                    <p>
                        Unlike traditional billboards, building wraps turn the entire structure into a dynamic, larger-than-life advertisement — a true architectural statement.
                    </p>
                </div>
                <div class="col-lg-6 vertical-center">
                    <iframe class="service-iframe outdoor-iframe" src="https://www.youtube.com/embed/Ny7v7w6dtq4?si=oAHAurPi9RcZjBPT" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
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
                    <h4>Why Choose Building Wrap Advertising in Dubai?</h4>
                    <p>Dubai is one of the world’s top destinations for outdoor media, with thousands of vehicles and pedestrians passing through major commercial zones every day. Here’s why building wrap advertising is particularly effective in the UAE market:</p>
                </div>
                <div class="col-lg-6">
                    <h5>Massive Visibility</h5>
                    <p>
                        A well-placed building wrap in Sheikh Zayed Road or Downtown Dubai can attract thousands of daily impressions. It provides your brand with an unparalleled opportunity to dominate the skyline and be noticed by both locals and international visitors.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Premium Brand Perception</h5>
                    <p>
                       In Dubai, size and visibility often correlate with prestige. A full building branding campaign signals confidence, scale, and dominance — aligning your company with top-tier brands and developers.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Extended Exposure Time</h5>
                    <p>
                        Unlike digital ads that appear for a few seconds, building wraps offer weeks or months of consistent exposure. This makes them ideal for long-term branding campaigns or major events like Expo exhibitions or product launches.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Cost-Effective Impact</h5>
                    <p>
                        When measured in cost per impression, building wraps are among the most cost-effective outdoor advertising formats in Dubai. They reach massive audiences at a lower per-view cost compared to TV, digital, or print.
                    </p>
                </div>  


                <div class="col-lg-12">
                    <h5>Strategic Locations</h5>
                    <p>
                        From Business Bay to Dubai Marina, Jumeirah, and Downtown, building wraps can be strategically placed on high-traffic structures to target key demographics — ensuring your brand reaches exactly who you want.
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
                    <h4>Ideal Locations for Building Wraps in Dubai</h4>
                    <p>
                        Dubai’s skyline offers numerous iconic locations perfect for building wrap advertising, including:
                    </p>
                    <ul>
                        <li><strong>Sheikh Zayed Road</strong> – Dubai’s most iconic outdoor advertising hub</li>
                        <li><strong>Business Bay & Downtown</strong> – Ideal for corporate and luxury campaigns</li>
                        <li><strong>Dubai Marina & JLT</strong> – Perfect for real estate and lifestyle brands</li>
                        <li><strong>Al Barsha & Deira</strong> – Great for high-traffic consumer brand promotions</li>
                        <li><strong>Airport Road & E11 Highways</strong> – Maximum visibility for mass-market exposure</li>
                    </ul>
                    
                </div>
                <div class="col-lg-3 mob-not-visible">
                    <img src="{{URL::to('/public/section-images/image46.png')}}" width="100%" alt="Why choose us">
                </div> 
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section section striped-section pr-icon-section">

          <div class="container">

            <div class="row gy-4 first-row outdoor-service-col-section">

                <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">
                    <h4>Our Building Wrap Advertising Services</h4>
                    <p>At DataMySite, we handle the entire process — from concept to installation — ensuring smooth execution and maximum visibility. Our services include:</p>
                </div>
                <div class="col-lg-6">
                    <h5>Creative Concept & Design</h5>
                    <p>
                        Our creative team collaborates with your brand to design visually impactful and high-resolution artwork that matches both your campaign goals and building dimensions.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Material Selection</h5>
                    <p>
                       We use UV-resistant, waterproof, and fire-retardant mesh materials, ensuring safety and durability in all weather conditions — a crucial factor in the UAE’s climate.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Printing & Production</h5>
                    <p>
                        Using large-format printing technology, we produce crisp, high-quality graphics that retain sharpness and color brilliance even from long viewing distances.
                    </p>
                </div>
                <div class="col-lg-6">
                    <h5>Site Survey & Permissions</h5>
                    <p>
                        We conduct detailed site surveys, handle necessary permissions, and comply with all Dubai Municipality advertising regulations to ensure a smooth approval process.
                    </p>
                </div>  


                <div class="col-lg-6">
                    <h5>Professional Installation</h5>
                    <p>
                        Our skilled installation team ensures that every building wrap is securely and aesthetically installed — guaranteeing visibility, safety, and structural integrity.
                    </p>
                </div>  

                <div class="col-lg-6">
                    <h5>Maintenance & Removal</h5>
                    <p>
                        We also provide maintenance services to keep your wraps looking fresh and handle safe removal once the campaign period ends.
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

                <div class="col-lg-3 mob-not-visible">
                    <img src="{{URL::to('/public/section-images/image47.png')}}" width="100%" alt="Why choose us">
                </div> 
                <div class="col-lg-1"></div>
                <div class="col-lg-8 vertical-center">
                    <h4>Advantages of Choosing DataMySite</h4>
                    <p>
                        At DataMySite, we bring deep local expertise and modern advertising technology together to create campaigns that deliver measurable impact.
                        <br>
                        End-to-End Project Management – From design to permits, we manage everything.
                        <br>
                        Local Market Knowledge – Experience with Dubai and UAE outdoor advertising regulations.
                    </p>
                    <ul>
                        <li><strong>High-Quality Materials</strong> – UV-protected, weatherproof, and eco-friendly media.</li>
                        <li><strong>Strategic Guidance</strong> – We help you select the right building and location for maximum ROI.</li>
                        <li><strong>Quick Turnaround</strong> – Fast, reliable installation without compromising quality.</li>
                    </ul>
                    <br>
                    <h4>Transform Your Brand Visibility Today</h4>
                    <p>
                        In a city that never stops building higher and brighter, your brand deserves to be seen. Let DataMySite help you turn buildings into bold brand statements with building wrap advertising in Dubai that captures attention and drives awareness.
                        <br>
                        Contact DataMySite today to discuss your next building branding project in Dubai or anywhere across the UAE.

                    </p>
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
                                        <span class="badge">&nbsp;</span>How much does building wrap advertising cost in Dubai?		
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-10" class="collapse show" aria-labelledby="faqHeading-10" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Building wrap advertising in Dubai costs between AED 50,000 and AED 350,000 per month depending on location. Secondary roads start at AED 50,000/month while prime locations such as Sheikh Zayed Road and Downtown Dubai range from AED 150,000 to AED 350,000/month.		</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-11">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-11" data-aria-expanded="false" data-aria-controls="faqCollapse-11">
                                        <span class="badge">&nbsp;</span>Which locations are best for building wraps in Dubai?		
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-11" class="collapse" aria-labelledby="faqHeading-11" data-parent="#accordion">
                                <div class="card-body">
                                    <p>The highest-impact locations for building wraps in Dubai are Sheikh Zayed Road, Downtown Dubai, DIFC, Dubai Marina, and JBR. These zones guarantee maximum impressions from both residents and tourists.		</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-12">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-12" data-aria-expanded="false" data-aria-controls="faqCollapse-12">
                                        <span class="badge">&nbsp;</span>How long does it take to install a building wrap in Dubai?		
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-12" class="collapse" aria-labelledby="faqHeading-12" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Installation typically takes 3 to 7 days depending on facade size. Creative production and permits require an additional 2 to 4 weeks lead time.		</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-13">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-13" data-aria-expanded="false" data-aria-controls="faqCollapse-13">
                                        <span class="badge">&nbsp;</span>Do I need a permit for building wrap advertising in Dubai?		
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-13" class="collapse" aria-labelledby="faqHeading-13" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes. Building wrap advertisements in Dubai require approval from the Roads and Transport Authority (RTA) and, in some zones, additional NOCs from developer authorities. DataMysite handles all permit applications as part of the campaign.		</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-14">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-14" data-aria-expanded="false" data-aria-controls="faqCollapse-14">
                                        <span class="badge">&nbsp;</span>What is the minimum campaign duration for building wraps?		
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-14" class="collapse" aria-labelledby="faqHeading-14" data-parent="#accordion">
                                <div class="card-body">
                                    <p>The standard minimum booking for building wrap advertising in Dubai is one month. Multi-month bookings typically attract discounts of 10 to 20 percent.		</p>
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
      "name": "How much does building wrap advertising cost in Dubai?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Building wrap advertising in Dubai costs between AED 50,000 and AED 350,000 per month depending on location. Secondary roads start at AED 50,000/month while prime locations such as Sheikh Zayed Road and Downtown Dubai range from AED 150,000 to AED 350,000/month."
      }
    },
    {
      "@type": "Question",
      "name": "Which locations are best for building wraps in Dubai?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "The highest-impact locations for building wraps in Dubai are Sheikh Zayed Road, Downtown Dubai, DIFC, Dubai Marina, and JBR. These zones guarantee maximum impressions from both residents and tourists."
      }
    },
    {
      "@type": "Question",
      "name": "How long does it take to install a building wrap in Dubai?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Installation typically takes 3 to 7 days depending on facade size. Creative production and permits require an additional 2 to 4 weeks lead time."
      }
    },
    {
      "@type": "Question",
      "name": "Do I need a permit for building wrap advertising in Dubai?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes. Building wrap advertisements in Dubai require approval from the Roads and Transport Authority (RTA) and, in some zones, additional NOCs from developer authorities. DataMysite handles all permit applications as part of the campaign."
      }
    },
    {
      "@type": "Question",
      "name": "What is the minimum campaign duration for building wraps?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "The standard minimum booking for building wrap advertising in Dubai is one month. Multi-month bookings typically attract discounts of 10 to 20 percent."
      }
    }
  ]
}
</script>
@endsection