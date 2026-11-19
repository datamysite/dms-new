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
                        <h1>&#10687; Radio Advertising in Dubai</h1>
                        <h2>Improve Your Brand's Voice</h2>
                        <h4>with Radio Advertising Dubai</h4>
                        <br>
                        <p>
                            Radio still owns the Dubai commute. During drive time, work hours, and weekends, it reaches a uniquely diverse, multilingual audience that no single channel matches. DataMySite plans and places radio campaigns across the UAE's top stations so your message reaches the right community in the right language.
                        </p>
                        <span><strong>Get a Free Radio Advertising Quote → · <a href="https://api.whatsapp.com/send/?phone=971525287259" target="_blank">WhatsApp: +971 52 528 7259</a></strong></span>
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
            <section class="hub-guides"><h2>Radio advertising guides</h2><ul>
            <li><a href="/radio-advertising-uae">Radio advertising in the UAE explained</a></li>
            <li><a href="/how-to-build-a-brand-through-radio-ads-in-the-uae">Building a brand with radio ads</a></li>
            </ul></section>
        </div>


        <section id="service-cards" class="blog-section section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-6">
                    <iframe class="service-iframe inf-iframe" src="https://www.youtube.com/embed/4qP3sAMhMkM?si=Bwuovnsu7mqpSDKs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <div class="col-lg-6">
                    <h4>Why Radio Works in the UAE</h4>
                    <p>
                        The UAE's expat-majority population splits across language communities — and radio targets each one precisely. Arabic, English, Hindi, and Urdu stations each serve distinct, loyal audiences, making radio one of the most targeted mass-reach formats in the country.
                    </p>
                    <br>
                    <h4>Stations We Cover</h4>
                    <ul>
                        <li><strong>English:</strong> <a href="https://datamysite.com/radio-advertising/english-stations/tag-91-1" target="_blank">TAG 91.1</a>, <a href="https://datamysite.com/radio-advertising/english-stations/hit-96-7" target="_blank">HIT 96.7</a>, <a href="https://datamysite.com/radio-advertising/english-stations/dubai-eye-103-8" target="_blank">Dubai Eye 103.8</a>, <a href="https://datamysite.com/radio-advertising/english-stations/virgin-radio-dubai-104-4" target="_blank">Virgin Radio 104.4</a>, <a href="https://datamysite.com/radio-advertising/english-stations/city-101-6" target="_blank">City 101.6</a></li>
                        <li><strong>Arabic:</strong> <a href="https://datamysite.com/radio-advertising/arabic-stations/dubai-92" target="_blank">Dubai 92</a>, <a href="https://datamysite.com/radio-advertising/arabic-stations/al-arabiya-99" target="_blank">Al Arabiya 99</a>, <a href="https://datamysite.com/radio-advertising/arabic-stations/al-khaleejiya-100-9" target="_blank">Al Khaleejiya 100.9</a></li>
                        <li><strong>Hindi / Urdu:</strong> <a href="https://datamysite.com/radio-advertising/hindi-urdu-stations/radio-shoma-93-4" target="_blank">Radio Shoma 93.4</a></li>

                    </ul>
                </div> 
                <div class="col-lg-12">
                    <h4>Radio Advertising Rates Dubai 2026</h4>
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">Station</th>
                          <th scope="col">Language</th>
                          <th scope="col">Off-peak spot</th>
                          <th scope="col">Prime drive-time</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>Virgin 104.4</td>
                          <td>English</td>
                          <td>AED 800–1,200</td>
                          <td>AED 1,200–1,800</td>
                        </tr>
                        <tr>
                          <td>Dubai Eye 103.8</td>
                          <td>English</td>
                          <td>AED 600–900</td>
                          <td>AED 900–1,400</td>
                        </tr>
                        <tr>
                          <td>Al Arabiya</td>
                          <td>Arabic</td>
                          <td>AED 700–1,100</td>
                          <td>AED 1,100–1,600</td>
                        </tr>
                        <tr>
                          <td>City 1016</td>
                          <td>Hindi/Urdu</td>
                          <td>AED 250–500</td>
                          <td>AED 500–800</td>
                        </tr>
                        <tr>
                          <td>Shoma FM</td>
                          <td>Malayalam</td>
                          <td>AED 200–400</td>
                          <td>AED 400–600</td>
                        </tr>
                        <tr>
                          <td>Al Khaleejiya 100.9</td>
                          <td>Arabic</td>
                          <td>AED 600–900</td>
                          <td>AED 900–1,400</td>
                        </tr>
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="4">
                            Rates are for 30-second spots. 45-second and 60-second spots run 1.4x and 1.8x the 30-second rate.
                            Most stations require a minimum 4-week buy.
                            Drive time = 7–9 AM and 4–7 PM Saturday–Thursday.
                          </td>
                        </tr>
                      </tfoot>
                    </table>
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

        <section id="service-cards" class="blog-section striped-section section pr-icon-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-8 vertical-center">
                    <h4>How Much Does Radio Advertising Cost in Dubai?</h4>
                    <p>
                        Cost depends on station, spot length, frequency, and time slot. We recommend the highest-ROI station mix for your audience and budget.
                    </p>
                    <br>
                    <h4>Why DataMySite</h4>
                    <p>End-to-end campaign planning, multilingual scriptwriting, and production · 1,200+ campaigns · transparent reporting. <a href="https://datamysite.com/client-case-studies" target="_blank">See case studies →</a></p>
                </div>
                <div class="col-lg-4 vertical-center">
                    <img src="{{URL::to('/public/section-images/image36.png')}}" class="side-image" width="100%" alt="Impact of PR Coverage">
                </div>
            </div>

          </div>

        </section>

        <section id="service-cards" class="blog-section section pr-icon-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-8 vertical-center">
                    <h4>Radio Campaign Services UAE - What We Bring on Offer at DataMySite</h4>
                    <p>We start by learning your business objectives. Our team analyses your target demographics, preferred languages, and core message through these steps</p>
                    <div class="faq " id="accordion">
                        <div class="card">
                            <div class="card-header" id="faqHeading-211">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-211" data-aria-expanded="false" data-aria-controls="faqCollapse-211">
                                        <span class="badge">&nbsp;</span>Tactical Broadcast Planning
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-211" class="collapse show" aria-labelledby="faqHeading-211" data-parent="#accordion">
                                <div class="card-body">
                                    <p>
                                        Your campaign deserves prime airtime. We select radio stations based on listener profiles and peak engagement hours. Whether you need coverage on Dubai's leading English stations or want to reach Arabic, Hindi, or Malayalam speakers, we place your ads where they'll make the strongest impact.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-212">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-212" data-aria-expanded="false" data-aria-controls="faqCollapse-212">
                                        <span class="badge">&nbsp;</span>Creative Excellence in Audio Production
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-212" class="collapse" aria-labelledby="faqHeading-212" data-parent="#accordion">
                                <div class="card-body">
                                    <p>
                                        Great radio ads tell stories that stick. Our creative team crafts scripts that capture attention within seconds. We collaborate with professional voice artists who bring your message to life in multiple languages. What's the result? Memorable ads that prompt action.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-213">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-213" data-aria-expanded="false" data-aria-controls="faqCollapse-213">
                                        <span class="badge">&nbsp;</span>Measuring Your Radio Campaign Success
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-213" class="collapse" aria-labelledby="faqHeading-213" data-parent="#accordion">
                                <div class="card-body">
                                    <p>
                                        Modern radio advertising Dubai campaigns need clear performance metrics. We can track these with our radio campaign services UAE
                                    </p>
                                    <ul>
                                        <li>Real-time listener engagement data</li>
                                        <li>Call volume during and after broadcast periods</li>
                                       <li> Website traffic correlation with air times Campaign reach and frequency metrics</li>
                                       <li> Audience response patterns</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-214">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-214" data-aria-expanded="false" data-aria-controls="faqCollapse-214">
                                        <span class="badge">&nbsp;</span>Complete Campaign Management from Our Team
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-214" class="collapse" aria-labelledby="faqHeading-214" data-parent="#accordion">
                                <div class="card-body">
                                    <p>
                                        From concept to execution, we manage every aspect of your radio campaign services UAE:
                                    </p>
                                        <ul>
                                            <li>Script development and translations</li>
                                            <li>Voice talent selection</li>
                                            <li>Music and sound design</li>
                                            <li>Airtime booking and scheduling</li>
                                           <li> Performance tracking</li>
                                            <li>Campaign optimisation</li>
                                        </ul>
                                    <p>
                                        Radio advertising amplifies your other marketing efforts. We synchronise your radio campaigns with your digital presence, print ads, and outdoor advertising to create a cohesive brand message across all channels.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-3 vertical-center mob-not-visible">
                    <img src="{{URL::to('/public/assets/img/why-us.png')}}" class="side-image" width="100%" alt="Why choose us">
                </div>
            </div>

          </div>

        </section>




        <section id="service-cards" class="blog-section striped-section  section pr-impact-section">

          <div class="container">

            <div class="row gy-4 first-row">

                <div class="col-lg-4">
                    <img src="{{URL::to('/public/section-images/image37.png')}}" width="100%" alt="Impact of PR Coverage">
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-7 vertical-center">
                    <h4>Advanced Targeting Through Radio Ad Placements Dubai</h4>
                    <p>
                        Your message will reach the right ears at the right time. We analyse listener demographics, peak listening hours, and program preferences to position your ads strategically. The targeted approach maximise your return on investment.
                    </p>
                    <br>
                    <h4>Start Your Radio Success Story with DataMySite</h4>
                    <p>
                        Ready to make your brand heard across Dubai? Contact DataMySite for a consultation. Our team will analyse your requirements and create a radio advertising method with the best radio ad rates UAE to deliver the best results.
                        <br>
                        Make your brand's voice heard across Dubai. Contact DataMySite today to discuss your radio advertising needs. Contact DataMySite today to discuss your radio advertising needs. Discuss your requirements today with our team.
                    </p>

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

        <section id="service-cards" class="blog-section section striped-section faq-new-section">

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
                                        <span class="badge">&nbsp;</span>How much does radio advertising cost in Dubai?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-10" class="collapse show" aria-labelledby="faqHeading-10" data-parent="#accordion">
                                <div class="card-body">
                                    <p>It depends on station, spot length, and frequency — request a quote.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-11">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-11" data-aria-expanded="false" data-aria-controls="faqCollapse-11">
                                        <span class="badge">&nbsp;</span>Which stations should I advertise on?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-11" class="collapse" aria-labelledby="faqHeading-11" data-parent="#accordion">
                                <div class="card-body">
                                    <p>That depends on your target community; we map the best mix across English, Arabic, and Hindi/Urdu stations. </p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-12">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-12" data-aria-expanded="false" data-aria-controls="faqCollapse-12">
                                        <span class="badge">&nbsp;</span>Do you write and produce the ad?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-12" class="collapse" aria-labelledby="faqHeading-12" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes — multilingual scripting and production included.</p>
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