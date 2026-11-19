@extends('web.includes.master')
@section('addStyle')
	<style type="text/css">
		.hero .blockquote {
		  color: #fff;
		  padding-left: 20px;
		  border-left: 2px solid #e04a43;
		  margin: 14px 0;
		}
		.hero-title-sec h1 {
		    font-family: system-ui !important;
		}
			@media (max-width: 767px) {
			    .hero-title-sec h1 {
			        font-size: 42px;
			        line-height: 52px;
			    }
			}

		/* ---- W2 pack sections (11 Nov 2026) ---- */
		.home-intro { padding: 50px 0 30px; }
		.home-intro-lead { font-size: 18px; line-height: 1.7; margin-bottom: 22px; }
		.home-intro-lead a { color: #f44d46; text-decoration: none; }
		.home-intro-btn { display: inline-block; background: #f44d46; color: #fff !important; padding: 12px 28px; border-radius: 4px; text-decoration: none; font-weight: 600; }
		.home-proof h3, .home-steps h3, .home-team h3 { font-size: 20px; font-weight: 700; margin-bottom: 10px; }
		.home-proof .proof-item, .home-steps .step-item { height: 100%; padding: 26px; border: 1px solid rgba(0,0,0,.08); border-radius: 6px; background: #fff; }
		.home-steps .step-num { display: inline-block; width: 38px; height: 38px; line-height: 38px; text-align: center; border-radius: 50%; background: #f44d46; color: #fff; font-weight: 700; margin-bottom: 12px; }
		.home-team ul { padding-left: 18px; }
		.home-team li { margin-bottom: 6px; }
		.home-faq .faq-title { cursor: pointer; }
		@media (max-width: 767px) {
			.home-intro { padding: 35px 0 20px; }
			.home-intro-lead { font-size: 16px; }
		}
	</style>
@endsection
@section('content')

	<main class="main">

	    <!-- Hero Section -->
	    <section id="hero" class="hero section dark-background">

	      <img src="{{URL::to('/public')}}/assets/img/background/banner.jpeg" class="hero-img-bg" alt="Hero Image" loading="lazy">

	      <div class="container">
	        <div class="row">
	          <div class="col-xl-1 hide-mobile">
	          	<p class="hero-title">DataMySite</p>
	          </div>
	          <div class="col-xl-4">
	          	<div class="hero-img">
		          	<img src="{{URL::to('/public')}}/assets/img/background/banner2.png" alt="DataMySite - Dubai UAE" loading="lazy">
		          </div>
	          </div>
	          <div class="col-xl-5 hero-title-sec">
	          	<div class="hero-spacing"></div>
	          	<h6 class="text-theme text-bold">D M S</h6>
	            <h1>Advertising and Marketing Agency in Dubai</h1>
	            <br>
	            <p class="blockquote">People do not buy goods and services.</p>
	            <p class="blockquote">They buy relations, stories, and magic.</p>

	          </div>
	        </div>
	      </div>

	    </section><!-- /Hero Section -->


	    <!-- Intro Section (W2 pack, 11 Nov 2026) -->
	    <section id="home-intro" class="home-intro section">
	      <div class="container">
	        <div class="row justify-content-center">
	          <div class="col-lg-10 text-center">
	            <p class="home-intro-lead">A Dubai campaign can start from AED 3,000 a month on <a href="{{URL::to('/digital-advertising/elevator-advertising/residential-lift')}}">residential lift screens</a> or run past AED 150,000 a month for a Sheikh Zayed Road unipole, and we plan both. DataMySite is a full-service advertising and marketing agency: <a href="{{URL::to('/outdoor-advertising')}}">outdoor</a>, <a href="{{URL::to('/transit-media')}}">transit</a>, <a href="{{URL::to('/radio-advertising')}}">radio</a>, <a href="{{URL::to('/digital-advertising/ott-advertising')}}">OTT</a>, <a href="{{URL::to('/seo-services')}}">SEO</a>, <a href="{{URL::to('/digital-marketing/social-media-marketing')}}">social</a>, <a href="{{URL::to('/public-relations-pr-coverage')}}">PR</a> and <a href="{{URL::to('/lead-generation')}}">lead generation</a>, run by a team that buys this media every week and knows what each format actually returns. Pick a service below, or tell us the budget and we'll tell you what it buys.</p>
	            <a class="home-intro-btn" href="{{route('contact')}}">Tell us your budget →</a>
	          </div>
	        </div>
	      </div>
	    </section><!-- /Intro Section -->



	    <!-- Services Section -->
	    <section class="home-business">

	      <div class="container">

	        <div class="row gy-4">

	          <div class="col-lg-3 business-first">
	            <div>
	              <h5 class="text-theme">BEST SOLUTIONS</h5>
	              <h2 class="text-bold-700" style="font-size:40px">For Your Business</h2>
	            </div>
	          </div>
	          <!-- End Service Item -->

	          <div class="col-lg-3 business-item">
	          	<a href="{{URL::to('/outdoor-advertising/billboards-advertising')}}">
		          	<img src="{{URL::to('/public/assets/img/service/1.png')}}" alt="Billboards Advertising dubai uae" loading="lazy">
		          	<p class="business-title">Billboards Advertising</p>
		          	<p class="main-service-desc">Billboards dominate outdoor advertising with their commanding presence in high-traffic areas.</p>
		          </a>

	          </div><!-- End Service Item -->

	          <div class="col-lg-3 business-item"  >
	          	<a href="{{URL::to('/influencer-marketing')}}">
		          	<img src="{{URL::to('/public/assets/img/service/2.png')}}" alt="Influencer Marketing dubai uae" loading="lazy">
		          	<p class="business-title">Influencer Marketing</p>
		          	<p class="main-service-desc">Access 4,500+ influencers across the GCC to boost your brand and drive targeted engagement.</p>
		          </a>

	          </div><!-- End Service Item -->

	          <div class="col-lg-3 business-item"  >
	          	<a href="{{URL::to('/public-relations-pr-coverage')}}">
		          	<img src="{{URL::to('/public/assets/img/service/3.png')}}" alt="PR Coverage dubai uae" loading="lazy">
		          	<p class="business-title">PR Coverage</p>
		          	<p class="main-service-desc">Elevate your brand’s presence with TOP PR Media houses internationally that secure high-impact media placements and amplify your message.</p>
		          </a>

	          </div><!-- End Service Item -->

	        </div>

	      </div>

	    </section><!-- /Services Section -->

	     <!-- Service Cards Section -->
	    <section id="service-cards" class="section-striped service-cards section">

	      <!-- Section Title -->
	      <div class="container section-title">
	        <h3 class="text-theme">What We Do</h3>
	        <p class="service-tp">We provide <strong>outdoor advertising</strong>, DOOH, PR Coverage, <strong>influencer marketing</strong>, <strong>event marketing</strong>, <strong>transit media</strong>, and more to elevate your brand’s impact and drive success. Let’s craft the perfect strategy together!</p>
	      </div><!-- End Section Title -->

	      <div class="container">

	        <div class="row gy-4 first-row">

	          <div class="col-lg-6"  >
	          	<div class="row">
	          		<div class="col-lg-4 col-sm-6 col-xs-6">
			            <div class="box-circle">
					      <div class="circle-border red-background" data-color1="#fff" data-color2="#f44d46" style="background-color: rgb(244, 77, 70); background-image: linear-gradient(223.2deg, transparent 50%, rgb(244, 77, 70) 50%), linear-gradient(90deg, rgb(255, 255, 255) 50%, transparent 50%);">
					        <div class="circle-percentage">
					          <span class="percentage" data-percentage="87">87%</span>
					        </div>
					      </div>
					    </div>
					    <p class="wwd-para">Digital Media Buying</p>
	          		</div>

	          		<div class="col-lg-4 col-sm-6 col-xs-6">
			            <div class="box-circle">
					      <div class="circle-border red-background" data-color1="#fff" data-color2="#f44d46" style="background-color: rgb(244, 77, 70); background-image: linear-gradient(255.6deg, transparent 50%, rgb(244, 77, 70) 50%), linear-gradient(90deg, rgb(255, 255, 255) 50%, transparent 50%);">
					        <div class="circle-percentage">
					          <span class="percentage" data-percentage="96">96%</span>
					        </div>
					      </div>
					    </div>
					    <p class="wwd-para">OOH Media Buying</p>
	          		</div>

	          		<div class="col-lg-4 col-sm-6 col-xs-6 service-card-last">
			            <div class="box-circle">
					      <div class="circle-border red-background" data-color1="#fff" data-color2="#f44d46" style="background-color: rgb(244, 77, 70); background-image: linear-gradient(198deg, transparent 50%, rgb(244, 77, 70) 50%), linear-gradient(90deg, rgb(255, 255, 255) 50%, transparent 50%);">
					        <div class="circle-percentage">
					          <span class="percentage" data-percentage="80">80%</span>
					        </div>
					      </div>
					    </div>
					    <p class="wwd-para">Strategic planning</p>
	          		</div>
	          	</div>
	          </div><!-- End Card Item -->

	          <div class="col-lg-6 mob-text-center"  >
	            <span class="text-theme">DataMySite</span>
	            <h2 class="text-bold-700">Marketing Solutions for Your Business's Growth</h2>
	          </div><!-- End Card Item -->

	        </div>

	      </div>

	    </section><!-- /Service Cards Section -->



	    <!-- Services Section -->
	    <section id="services" class="services section">

	      <!-- Section Title -->
	      <div class="container section-title">
	        <h2 class="text-theme">Our Services</h2>
	        <p class="service-p">Elevate Your Brand Visibility and Boost Sales</p>
	        <p class="service-tp">
	        	We use services like <strong>outdoor advertising</strong>, <strong>CGI advertising</strong> and more to maximize your brand’s visibility and impact. Let's craft a strategy to drive your business success!
	        </p>
	      </div><!-- End Section Title -->

	      <div class="container">

	        <div class="row service-row gy-4">

	        	@foreach($header_services as $val)
		          <div class="col-lg-4 col-md-6 service-item d-flex"  >
		            <div class="icon flex-shrink-0"><i class="{{$val->icon}}" style="color: #f44d46;"></i></div>
		            <div>
		              <h4 class="title">{{$val->name}}</h4>
		              <p class="description">{{$val->description}}</p>
		              <a href="{{URL::to('/'.$val->slug)}}" class="readmore stretched-link"><i class="bi bi-arrow-right"></i></a>
		            </div>
		          </div>
		          <!-- End Service Item -->
	          @endforeach

	        </div>

	      </div>

	    </section><!-- /Services Section -->


	    <!-- Why Clients Pick Us (W2 pack, 11 Nov 2026) -->
	    <section id="why-clients-pick-us" class="home-proof section section-striped">
	      <div class="container section-title">
	        <h2 class="text-theme">Why Clients Pick Us</h2>
	      </div>
	      <div class="container">
	        <div class="row gy-4">
	          <div class="col-lg-4">
	            <div class="proof-item">
	              <h3>We buy the media ourselves</h3>
	              <p>Billboards, unipoles and bridge banners, taxis, buses and the metro, lift and mall screens, <a href="{{URL::to('/radio-advertising/english-stations')}}">English</a> and Arabic radio, OTT and <a href="{{URL::to('/sponsorships/entertainment')}}">entertainment sponsorships</a>. More than 15 years in Dubai media means we know which sites and stations are worth the money and which are not.</p>
	            </div>
	          </div>
	          <div class="col-lg-4">
	            <div class="proof-item">
	              <h3>Work we can show</h3>
	              <p>More than 1,200 campaigns delivered for more than 80 clients across outdoor, transit, digital, PR, influencer and lead generation. The <a href="{{URL::to('/client-case-studies')}}">client case studies</a> show the campaigns and what they did for the business.</p>
	            </div>
	          </div>
	          <div class="col-lg-4">
	            <div class="proof-item">
	              <h3>Prices in AED, before you commit</h3>
	              <p>Every service page publishes its 2026 price bands in AED, and every quote lists media, production and permits on separate lines, so the number you approve is the number you pay.</p>
	            </div>
	          </div>
	        </div>
	      </div>
	    </section><!-- /Why Clients Pick Us -->


	    <!-- How a Campaign Comes Together (W2 pack, 11 Nov 2026) -->
	    <section id="how-a-campaign-comes-together" class="home-steps section">
	      <div class="container section-title">
	        <h2 class="text-theme">How a Campaign Comes Together</h2>
	      </div>
	      <div class="container">
	        <div class="row gy-4">
	          <div class="col-lg-3 col-md-6">
	            <div class="step-item">
	              <span class="step-num">1</span>
	              <h3>Brief and budget</h3>
	              <p>Tell us who you need to reach, where, by when and what you can spend. One call or one WhatsApp message is enough to start.</p>
	            </div>
	          </div>
	          <div class="col-lg-3 col-md-6">
	            <div class="step-item">
	              <span class="step-num">2</span>
	              <h3>Media plan in AED</h3>
	              <p>You get a plan with the formats, sites or channels, dates and the cost of each line in AED, plus what we expect each one to deliver.</p>
	            </div>
	          </div>
	          <div class="col-lg-3 col-md-6">
	            <div class="step-item">
	              <span class="step-num">3</span>
	              <h3>Production and permits</h3>
	              <p>We produce the creative, print or upload it, and secure the RTA or municipality approvals that outdoor and transit formats need.</p>
	            </div>
	          </div>
	          <div class="col-lg-3 col-md-6">
	            <div class="step-item">
	              <span class="step-num">4</span>
	              <h3>Live reporting</h3>
	              <p>Proof of display, leads, clicks and spend are reported while the campaign runs, so the budget can move to whatever is working.</p>
	            </div>
	          </div>
	        </div>
	      </div>
	    </section><!-- /How a Campaign Comes Together -->


	    <div class="section-striped">
				@include('web.includes.elements.clients')
		</div>

	     <!-- Service Cards Section -->
	    <section id="service-cards" class="service-cards section">
	    	<!-- Section Title -->
	      <div class="container section-title">
	        <h2 class="text-theme">Our Successful Projects</h2>
	        <p class="service-tp">
	        	At <strong>DataMySite</strong>, we deliver impactful marketing campaigns across <strong>billboards ads</strong>, DOOH, influencer marketing, PR Coverage, <strong>lead generation</strong>, <strong>event marketing</strong>, and <strong>sponsorships</strong>, driving real business success and measurable results.
						<br>
						Let's make your brand our next success!

	        </p>
	      </div><!-- End Section Title -->
	      <div class="container">

	        <div class="row">
          		<div class="col-lg-3 col-sm-6 col-xs-6">
		            <div class="counter-container">
		            	<div>
							      <div class="counter">1219</div>
							      <div><label>+</label></div>
							    </div>
						      <span>Succesful Project</span>
						    </div>
          		</div>

          		<div class="col-lg-3 col-sm-6 col-xs-6">
		            <div class="counter-container">
		            	<div>
							      <div class="counter">15</div>
							      <div><label>+</label></div>
							    </div>
						      <span>Years Experience</span>
						    </div>
          		</div>

          		<div class="col-lg-3 col-sm-6 col-xs-6">
		            <div class="counter-container">
		            	<div>
							      <div class="counter">5</div>
							      <div><label>+</label></div>
							    </div>
						      <span>Winning Awards</span>
						    </div>
          		</div>

          		<div class="col-lg-3 col-sm-6 col-xs-6">
		            <div class="counter-container">
		            	<div>
							      <div class="counter">81</div>
							      <div><label>+</label></div>
							    </div>
						      <span>Satisfied Clients</span>
						    </div>
          		</div>
          	</div>

	      </div>

	    </section><!-- /Service Cards Section -->


	    <!-- Who We Are (W2 pack, 11 Nov 2026) -->
	    <section id="who-we-are" class="home-team section section-striped">
	      <div class="container">
	        <div class="row gy-4">
	          <div class="col-lg-6">
	            <h2 class="text-theme">Who We Are</h2>
	            <p>DataMySite is a Dubai advertising and marketing agency with more than 15 years in Dubai media, founded and led by Abhishek, Founder and Growth Strategy Lead. Each discipline has its own lead, so the person planning your campaign is the person who knows that channel.</p>
	            <p>Our office is Office 205, Al Bayan Building, Dubai Investment Park, Dubai. Call or WhatsApp <a href="https://api.whatsapp.com/send/?phone=971525287259" target="_blank" rel="noopener">+971 52 528 7259</a> or email <a href="mailto:contact@datamysite.com">contact@datamysite.com</a>.</p>
	          </div>
	          <div class="col-lg-6">
	            <h3>The team leads</h3>
	            <ul>
	              <li><strong>Abhishek</strong> - Founder and Growth Strategy Lead</li>
	              <li><strong>Aan</strong> - Traditional and Out-of-Home Media Lead</li>
	              <li><strong>Pooja</strong> - Social Media and Paid Media Lead</li>
	              <li><strong>Kasturi</strong> - Content, Creator and PR Lead</li>
	            </ul>
	          </div>
	        </div>
	      </div>
	    </section><!-- /Who We Are -->


	    <!-- FAQ (W2 pack, 11 Nov 2026) - the FAQPage JSON-LD below must match these answers word for word -->
	    <section id="home-faq" class="home-faq blog-section section faq-new-section">
	      <div class="container">
	        <div class="row gy-4">
	          <div class="col-lg-12">
	            <h2 class="text-theme text-center">Frequently Asked Questions</h2>
	            <br>
	            <div class="faq" id="homeFaq">

	              <div class="card">
	                <div class="card-header" id="homeFaqHeading-1">
	                  <div class="mb-0">
	                    <h3 class="faq-title h5" data-toggle="collapse" data-target="#homeFaqCollapse-1" data-aria-expanded="true" data-aria-controls="homeFaqCollapse-1">
	                      <span class="badge">1</span>What does an advertising agency in Dubai charge?
	                    </h3>
	                  </div>
	                </div>
	                <div id="homeFaqCollapse-1" class="collapse show" aria-labelledby="homeFaqHeading-1" data-parent="#homeFaq">
	                  <div class="card-body">
	                    <p>Planning and buying are usually built into the media cost, so you pay the media rate plus production. Retainers for ongoing work start from AED 1,500 a month for SEO and AED 5,000 a month for social media. We quote everything in AED before you commit.</p>
	                  </div>
	                </div>
	              </div>

	              <div class="card">
	                <div class="card-header" id="homeFaqHeading-2">
	                  <div class="mb-0">
	                    <h3 class="faq-title h5" data-toggle="collapse" data-target="#homeFaqCollapse-2" data-aria-expanded="false" data-aria-controls="homeFaqCollapse-2">
	                      <span class="badge">2</span>Do you handle permits and production?
	                    </h3>
	                  </div>
	                </div>
	                <div id="homeFaqCollapse-2" class="collapse" aria-labelledby="homeFaqHeading-2" data-parent="#homeFaq">
	                  <div class="card-body">
	                    <p>Yes. Outdoor and transit formats need RTA or municipality approval and printed or digital production; we handle both and build them into the quote so there are no surprise invoices.</p>
	                  </div>
	                </div>
	              </div>

	              <div class="card">
	                <div class="card-header" id="homeFaqHeading-3">
	                  <div class="mb-0">
	                    <h3 class="faq-title h5" data-toggle="collapse" data-target="#homeFaqCollapse-3" data-aria-expanded="false" data-aria-controls="homeFaqCollapse-3">
	                      <span class="badge">3</span>Which services can be combined?
	                    </h3>
	                  </div>
	                </div>
	                <div id="homeFaqCollapse-3" class="collapse" aria-labelledby="homeFaqHeading-3" data-parent="#homeFaq">
	                  <div class="card-body">
	                    <p>Any of them. The usual pairings are outdoor plus radio for launches, SEO plus lead generation for B2B, and social plus influencer for consumer brands. One team plans the mix so the budget isn't split by accident.</p>
	                  </div>
	                </div>
	              </div>

	              <div class="card">
	                <div class="card-header" id="homeFaqHeading-4">
	                  <div class="mb-0">
	                    <h3 class="faq-title h5" data-toggle="collapse" data-target="#homeFaqCollapse-4" data-aria-expanded="false" data-aria-controls="homeFaqCollapse-4">
	                      <span class="badge">4</span>Do you work outside Dubai?
	                    </h3>
	                  </div>
	                </div>
	                <div id="homeFaqCollapse-4" class="collapse" aria-labelledby="homeFaqHeading-4" data-parent="#homeFaq">
	                  <div class="card-body">
	                    <p>Yes: Abu Dhabi, Sharjah, Ajman and the northern emirates for outdoor and transit, the whole UAE for digital, and Saudi Arabia, India, China and the UK through our global marketing team.</p>
	                  </div>
	                </div>
	              </div>

	              <div class="card">
	                <div class="card-header" id="homeFaqHeading-5">
	                  <div class="mb-0">
	                    <h3 class="faq-title h5" data-toggle="collapse" data-target="#homeFaqCollapse-5" data-aria-expanded="false" data-aria-controls="homeFaqCollapse-5">
	                      <span class="badge">5</span>How fast can a campaign go live?
	                    </h3>
	                  </div>
	                </div>
	                <div id="homeFaqCollapse-5" class="collapse" aria-labelledby="homeFaqHeading-5" data-parent="#homeFaq">
	                  <div class="card-body">
	                    <p>Digital screens and social can be live within a week. Printed outdoor needs two to three weeks for permits and production. Radio is usually ten days from script to air.</p>
	                  </div>
	                </div>
	              </div>

	            </div>
	          </div>
	        </div>
	      </div>
	    </section><!-- /FAQ -->

	    @verbatim
	    <script type="application/ld+json">
	    {
	      "@context": "https://schema.org",
	      "@type": "FAQPage",
	      "mainEntity": [
	        {
	          "@type": "Question",
	          "name": "What does an advertising agency in Dubai charge?",
	          "acceptedAnswer": {
	            "@type": "Answer",
	            "text": "Planning and buying are usually built into the media cost, so you pay the media rate plus production. Retainers for ongoing work start from AED 1,500 a month for SEO and AED 5,000 a month for social media. We quote everything in AED before you commit."
	          }
	        },
	        {
	          "@type": "Question",
	          "name": "Do you handle permits and production?",
	          "acceptedAnswer": {
	            "@type": "Answer",
	            "text": "Yes. Outdoor and transit formats need RTA or municipality approval and printed or digital production; we handle both and build them into the quote so there are no surprise invoices."
	          }
	        },
	        {
	          "@type": "Question",
	          "name": "Which services can be combined?",
	          "acceptedAnswer": {
	            "@type": "Answer",
	            "text": "Any of them. The usual pairings are outdoor plus radio for launches, SEO plus lead generation for B2B, and social plus influencer for consumer brands. One team plans the mix so the budget isn't split by accident."
	          }
	        },
	        {
	          "@type": "Question",
	          "name": "Do you work outside Dubai?",
	          "acceptedAnswer": {
	            "@type": "Answer",
	            "text": "Yes: Abu Dhabi, Sharjah, Ajman and the northern emirates for outdoor and transit, the whole UAE for digital, and Saudi Arabia, India, China and the UK through our global marketing team."
	          }
	        },
	        {
	          "@type": "Question",
	          "name": "How fast can a campaign go live?",
	          "acceptedAnswer": {
	            "@type": "Answer",
	            "text": "Digital screens and social can be live within a week. Printed outdoor needs two to three weeks for permits and production. Radio is usually ten days from script to air."
	          }
	        }
	      ]
	    }
	    </script>
	    @endverbatim


	    <!-- Call To Action Section -->
	    <section id="call-to-action" class="call-to-action section dark-background">

	      <img src="{{URL::to('/public')}}/assets/img/background/call-to.jpg" alt="Call to action background">

	      <div class="container">
	        <div class="row justify-content-center">
	          <div class="col-xl-10">
	          	<div class="hero-spacing hide-mobile"></div>
	            <div class="text-center">
	              <p class="text-theme" style="text-transform: uppercase;">Schedule Your Appointment Today</p>
	              <h3>Don't Miss Out on This Opportunity to <br>Elevate Your Advertising Game</h3>
	              <br>
	              <p><span class="text-theme">Smarter Marketing, Better Outcomes.</span>
					<br>Boost your brand’s visibility, enhance campaign performance</p>
	              <a class="cta-btn" href="{{route('contact')}}">Get Started - Free Consultation →</a>
	            </div>
	          	<div class="hero-spacing hide-mobile"></div>
	          </div>
	        </div>
	      </div>

	    </section><!-- /Call To Action Section -->

	</main>


@endsection