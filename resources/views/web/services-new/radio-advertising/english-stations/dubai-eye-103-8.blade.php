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
                        <h1>&#10687; {{$subservice->name}}</h1>
                        <h2>Dedicated English-language</h2>
                        <h4>talk radio and business news station</h4>
                        <br>
                        <p>
                            DataMySite helps businesses run powerful advertising campaigns on Dubai Eye 103.8 FM that deliver real credibility and strong response from a business-focused audience.
                        </p>
                    </div>
                    <div class="col-lg-4">
                        <div class="aside-form">
                            
                            <form action="{{route('enquiry.submit')}}" id="enquiry-form">
                                @csrf
                                <input type="hidden" name="service" value="{{$service->name}} - {{$subservice->name}}" required>
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
                    <h4>Why Dubai Eye 103.8 FM is a Premium Advertising Platform</h4>
                    <br>
                    <p>Dubai Eye 103.8 FM stands apart from music-led stations by delivering a deeply engaged and intellectually active audience:</p>
                    <ul>
                        <li>Only English-language talk radio and business news station in the UAE</li>
                        <li>Listeners are actively engaged, not passively listening — they tune in for information and insight</li>
                        <li>Attracts high-net-worth individuals, C-suite executives, investors, and senior business professionals</li>
                        <li>Business and news content creates a credible, trustworthy environment for brand advertising</li>
                        <li>Strong audience overlap with financial, property, and premium consumer markets</li>
                        <li>Ideal for brands where trust and authority are essential to the sales process</li>

                    </ul>
                </div>
                <div class="col-lg-6 vertical-center">
                    <iframe class="service-iframe outdoor-iframe" src="https://www.youtube.com/embed/4qP3sAMhMkM?si=Bwuovnsu7mqpSDKs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
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

            <div class="row gy-4 first-row split-block">


                <div class="col-lg-3 vertical-center">
                    <img src="{{URL::to('/public/section-images/image177.png')}}" width="100%" alt="Why choose us">
                </div> 
                <div class="col-lg-1"></div>
                <div class="col-lg-8 vertical-center">
                    <h4>Who Listens to Dubai Eye 103.8 FM?</h4>
                    <p>
                        The Dubai Eye 103.8 FM audience is one of the most valuable in UAE media:
                    </p>
                    <ul>
                        <li>Senior business executives, CEOs, CFOs, and senior managers</li>
                        <li>Entrepreneurs and business owners looking for insights and opportunities</li>
                        <li>Investors and wealth managers following regional and global markets</li>
                        <li>Professionals in finance, law, consulting, real estate, and technology</li>
                        <li>English-speaking expats with high disposable income and decision-making authority</li>
                        <li>News consumers who are highly analytical, brand-aware, and responsive to well-targeted advertising</li>

                    </ul>
                    <br>
                    <h4>Advertising Formats on Dubai Eye 103.8 FM</h4>
                    <p>
                        DataMySite offers a range of strategic advertising placements on Dubai Eye 103.8 FM:
                    </p>
                    <ul>
                        <li>News Bulletin Sponsorships: Your brand introduced before or after key news updates throughout the day</li>
                        <li>Programme Sponsorships: Sponsor popular shows covering business, finance, technology, or lifestyle topics</li>
                        <li>Spot Commercials: 15, 30, or 60-second ads placed within commercial breaks</li>
                        <li>Presenter Live Reads: Credible, authoritative hosts personally deliver your brand message</li>
                        <li>Business Interview Integrations: Have your brand aligned with thought-leadership and industry expert interviews</li>
                        <li>Breakfast Show Placements: Reach the highest-concentration of professional listeners in the morning</li>


                    </ul>
                </div>
            </div>

          </div>

        </section>


        <section id="service-cards" class="blog-section section   why-us-section why-out-us-section">

          <div class="container">

            <div class="row gy-4 first-row split-block">


                <div class="col-lg-8 vertical-center">
                    <h4>How DataMySite Manages Your Dubai Eye Campaign</h4>
                    <p>We approach Dubai Eye 103.8 FM campaigns with the same level of professionalism the station demands:</p>
                    <ul>
                        <li>Authority-First Messaging: We develop advertising messages that establish credibility and build trust with a discerning business audience</li>
                        <li>Business-Register Script Writing: Our creative team writes intelligent, well-crafted scripts suitable for a talk radio environment</li>
                        <li>Experienced Voice Talent: We select voice artists who sound professional, confident, and informed</li>
                        <li>Targeted Time Slot Booking: We prioritise breakfast, mid-morning, and drive-time slots where professional listener concentration peaks</li>
                        <li>Campaign Performance Reporting: We provide detailed post-campaign reports tracking reach, frequency, and audience response</li>

                    </ul>

                    <br>
                    <h4>Best Industries for Dubai Eye 103.8 FM Advertising</h4>
                    <p>
                        Dubai Eye 103.8 FM delivers outstanding results for brands targeting business professionals and high-income consumers:
                    </p>
                    <ul>
                        <li>Financial services, investment firms, and private banking</li>
                        <li>Real estate — both commercial and luxury residential property</li>
                        <li>Business software, enterprise technology, and SaaS platforms</li>
                        <li>Legal, accounting, and professional services firms</li>
                        <li>Premium automotive brands targeting executive buyers</li>
                        <li>Business education, MBA programmes, and leadership training</li>
                        <li>Airlines, premium hotels, and business travel services</li>
                        <li>Corporate events, conferences, and exhibitions</li>

                    </ul>
                </div>
                <div class="col-lg-1"></div>

                <div class="col-lg-3 vertical-center">
                    <img src="{{URL::to('/public/section-images/image180.png')}}" width="100%" alt="Why choose us">
                </div> 
            </div>

          </div>

        </section>

        <section class="tm-captive tm-radio">
            <div class="container">
              <div class="in">
                <div>
                  <div class="tag w">Get Started</div>
                  <h2>Advertise Where Business Gets Done</h2>
                  <p>Dubai Eye 103.8 FM is where Dubai's most influential and economically active professionals tune in every day. Placing your brand in this environment does not just generate awareness — it builds credibility and trust that directly influence purchasing decisions.</p>
                  <div class="cap-list">
                    <div class="cap-item"><span class="cck">✓</span> Reach out to DataMySite today for a free consultation and a customised Dubai Eye 103.8 FM advertising strategy.</div>
                  </div>
                </div>
                <div class="cap-r">
                  <a href="https://datamysite.com/contact-us" class="bbtn btn-w">Get Transit Media Plan <span class="arr">→</span></a>
                  <a href="https://api.whatsapp.com/send/?phone=971525287259" class="bbtn btn-ow">💬 WhatsApp for Rates</a>
                  <span style="font-size:11px;color:rgba(255,255,255,.3);align-self:center">Free · Rates sent in 60 min</span>
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


        <section id="service-cards" class="blog-section section  faq-new-section">

          <div class="container">

            <div class="row gy-4 first-row">
                <div class="col-lg-12 vertical-center">
                    <h4 class="text-center">Frequently Asked Questions</h4>
                    <br>
                    <div class="faq " id="accordion">
                        <div class="card">
                            <div class="card-header" id="faqHeading-10">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-10" data-aria-expanded="true" data-aria-controls="faqCollapse-10">
                                        <span class="badge">&nbsp;</span>Is Dubai Eye 103.8 FM effective for B2B campaigns?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-10" class="collapse show" aria-labelledby="faqHeading-10" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Dubai Eye 103.8 FM is arguably the best radio station in the UAE for B2B advertising. Its listener base is made up of exactly the kind of people who make business purchasing decisions — executives, managers, business owners, and professionals. If you want to reach decision-makers in a credible environment, Dubai Eye 103.8 FM is one of the strongest options available.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-11">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-11" data-aria-expanded="false" data-aria-controls="faqCollapse-11">
                                        <span class="badge">&nbsp;</span>Can you create a thought leadership-style radio ad?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-11" class="collapse" aria-labelledby="faqHeading-11" data-parent="#accordion">
                                <div class="card-body">
                                    <p>Yes. Dubai Eye 103.8 FM's format supports more sophisticated and information-rich advertising messages. DataMySite can produce ads that position your brand as an industry authority, which works particularly well for professional services, technology, and financial companies.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="faqHeading-12">
                                <div class="mb-0">
                                    <h5 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-12" data-aria-expanded="false" data-aria-controls="faqCollapse-12">
                                        <span class="badge">&nbsp;</span>What is the ideal frequency for a Dubai Eye 103.8 FM campaign?
                                    </h5>
                                </div>
                            </div>
                            <div id="faqCollapse-12" class="collapse" aria-labelledby="faqHeading-12" data-parent="#accordion">
                                <div class="card-body">
                                    <p>For a business and news station like Dubai Eye 103.8 FM, frequency of 3 to 5 times per day over several weeks tends to work best. This keeps your brand consistently present without overwhelming the audience. DataMySite recommends a minimum campaign length of 4 weeks for meaningful brand recall.</p>
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