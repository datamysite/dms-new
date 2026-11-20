{{--
|==========================================================================
| HOME V2 — modern homepage (independent copy of web.index-beta)
|--------------------------------------------------------------------------
| Self-contained: this view + public/assets/css/home-v2.css
|                             + public/assets/js/home-v2.js
| Does not include or depend on any other homepage partial / CSS / JS.
| Switch homepage: WebController@index -> view('web.home-v2')
|==========================================================================
--}}
@extends('web.home-v2.master')
@php require_once resource_path('views/web/home-v2/helpers.php'); @endphp

@section('v2lite', '1')
@section('v2form', '1')
@section('v2keep', '1')

@section('metaAddition')
	<script>(function(){var d=document.documentElement;try{if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches)return;}catch(e){}d.classList.add('hv2-loading');setTimeout(function(){d.classList.remove('hv2-loading','hv2-leaving');},500);})();</script>
	<link rel="preload" as="image" href="{{ URL::to('/public/assets/img/logo-white.png') }}">
	{{-- self-hosted fonts: the two key files are preloaded, @font-face is inlined (font-display: swap) --}}
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/league-spartan-latin.woff2') }}">
	<link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/fonts/nunito-sans-latin.woff2') }}">
	<style>{!! str_replace('../fonts/', URL::to('/public/assets/fonts') . '/', (string) @file_get_contents(public_path('assets/css/v2-fonts.css'))) !!}</style>
@endsection

@section('addStyle')
	<link href="{{ v2_asset('assets/css/home-v2.css') }}" rel="stylesheet">
@endsection

@php
	/* ---------- Client logos (image number => brand), biggest brands first ---------- */
	$hv2Clients = [
		38 => 'Nike', 12 => 'Intel', 35 => 'Whole Foods', 31 => 'e&', 1 => 'Dubai Duty Free', 4 => 'DAMAC', 17 => 'MakeMyTrip',
		6 => 'Lenskart', 16 => 'Cerruti', 3 => 'Danube Properties', 2 => 'Jetour', 39 => 'Géant', 36 => "Haldiram's", 37 => 'GMG',
		25 => 'Al Rawabi', 28 => 'Al Ain Farms', 20 => 'SIVVI', 40 => 'Aswaaq', 7 => 'Logicom Group', 5 => 'ILG', 24 => 'MARQ Dubai',
		26 => 'Hayata', 27 => 'Cavo', 13 => 'Elite Group Holding', 14 => 'BNW Developments', 8 => 'Arabian Hills', 19 => 'Tranquil Developers', 21 => 'Prime Estate',
		18 => 'Oscar Luxury Auto', 15 => 'AYS Developers', 32 => 'USI Consultancy', 34 => 'First Super Car Rental', 33 => 'Royalty Tourism', 29 => 'Darcey Flowers', 30 => 'Peace Homes Development',
		9 => "D's Damat", 10 => 'Metamind Inspire', 11 => 'Ask Kubeir', 22 => 'Lasa Ona', 23 => 'Just Love UAE', 41 => 'Royal',
	];
	$hv2ClientRows = array_chunk($hv2Clients, (int) ceil(count($hv2Clients) / 2), true);

	/* ---------- Case studies ---------- */
	$hv2Cases = [
		['bnw', 'bnw.jpg', 'BNW Developers'],
		['ays', 'ays.jpg', 'AYS Developers'],
		['jetour', 'jetour.jpg', 'Jetour'],
		['tranquil', 'tranquil.jpg', 'Tranquil Developers'],
		['ilg', 'ilg.jpg', 'ILG Luxury Group'],
		['dubai-duty-free', 'dubai-duty-free.jpg', 'Dubai Duty Free'],
		['danube', 'danube.jpg', 'Danube'],
		['ksa', 'lenskart-p.png', 'KSA Influencers'],
	];

	/* ---------- What we do (service hubs) ---------- */
	$hv2Hubs = [
		['outdoor-advertising', 'Outdoor Advertising', "Billboards, unipoles and bridge banners on Dubai's busiest roads.", 'bi-signpost-split'],
		['transit-media', 'Transit Media', 'Taxis, buses and the metro, seen across the city every day.', 'bi-bus-front'],
		['radio-advertising', 'Radio Advertising', 'English, Arabic and Asian stations, planned by audience and drive time.', 'bi-broadcast'],
		['digital-advertising/dooh-advertising', 'Digital Screens (DOOH)', 'Lift, mall and roadside screens you can switch on within a week.', 'bi-display'],
		['digital-advertising/ott-advertising', 'OTT Advertising', 'Ads on streaming TV, targeted to UAE households.', 'bi-tv'],
		['seo-services', 'SEO Services', 'Rankings and enquiries from Google, priced per month in AED.', 'bi-graph-up-arrow'],
		['digital-marketing/social-media-marketing', 'Social Media Marketing', 'Content, community and paid social run by one team.', 'bi-chat-heart'],
		['event-marketing', 'Event Marketing', 'Launches, activations and brand experiences, planned end to end.', 'bi-stars'],
		['lead-generation', 'Lead Generation', 'Qualified enquiries delivered to your sales team, tracked by source.', 'bi-funnel'],
		['website-development', 'Website Development', 'Fast sites, stores and landing pages built to rank and convert.', 'bi-code-slash'],
	];

	/* ---------- Testimonials: slider content = the client reviews from the old home page (name, time, avatar colour, text) ---------- */
	$hv2Reviews = [
		['Ahmed Al Mansoori', '2 months ago', '#4285f4', 'DataMySite team delivered an outstanding campaign for our real estate project. The billboard placements on Sheikh Zayed Road were perfectly targeted and the results exceeded our expectations. Highly professional and responsive team!'],
		['Sara Hassan', '3 months ago', '#ea4335', 'We worked with DataMySite for influencer marketing across the GCC and the reach was incredible. They have a massive network and know exactly which influencers to pair with each brand. Our product sold out within weeks of the campaign going live.'],
		['Mohammed Tariq', '4 months ago', '#34a853', 'Got our brand featured in Gulf News and Forbes Middle East through DataMySite. The PR team was very professional — they handled the full content writing and editorial relationship. Excellent ROI and the backlinks have boosted our SEO rankings significantly.'],
		['Riya Sharma', '5 months ago', '#ff7043', 'We hired DataMySite for transit media across Dubai Metro and taxi fleet. The campaign ran across 5 key stations and the brand visibility was phenomenal. Within 3 weeks we saw a 40% spike in website traffic from Dubai. Very impressed!'],
		['Khalid Al Rashid', '6 months ago', '#7c4dff', 'DataMySite helped us with a full 360 campaign — digital, OOH and influencer marketing for our automotive launch in UAE. The coordination across all channels was seamless and results were beyond expectations. Will definitely work with them again.'],
		['Nadia Al Farsi', '7 months ago', '#00897b', 'We needed a strong SEO strategy for our e-commerce brand in Dubai. DataMySite delivered a detailed audit, fixed all technical issues and built quality backlinks. We went from page 4 to page 1 on Google in less than 3 months. Highly recommend!'],
		['Faisal Mahmood', '8 months ago', '#d81b60', 'Best advertising agency in Dubai, hands down. DataMySite managed our full campaign from concept to execution across billboards, metro, and social media. Communication was excellent throughout and delivery was always on time. 100% recommend.'],
		['Layla Benali', '9 months ago', '#0288d1', 'The team at DataMySite is incredibly knowledgeable about the UAE market. They recommended the perfect mix of transit and digital channels for our hospitality brand and managed everything professionally. A truly full-service agency.'],
		['Omar Qureshi', '10 months ago', '#f57c00', 'Exceptional service from start to finish. DataMySite\'s expertise in outdoor advertising is unmatched in Dubai. They secured prime billboard spots that were exactly what our campaign needed, and the creative production quality was top notch.'],
	];
	/* Score + count shown in the badge: live from Google when available, otherwise 5.0 / 46 */
	$hv2Rating = !empty($googleReviews['rating']) ? (float) $googleReviews['rating'] : 5.0;
	$hv2Total = !empty($googleReviews['total']) ? (int) $googleReviews['total'] : 46;
	$hv2GoogleUrl = 'https://www.google.com/maps/place/DMS+-+DataMySite+Marketing/@24.9978253,55.1727716,936m/data=!3m1!1e3!4m8!3m7!1s0x3e5f7347c3a1a1d1:0xad7a49fd99d3061!8m2!3d24.9978253!4d55.1727716!9m1!1b1!16s%2Fg%2F11ybf42kl_?entry=ttu&g_ep=EgoyMDI1MTIwOS4wIKXMDSoASAFQAw%3D%3D';

	/* ---------- Team ---------- */
	$hv2Team = [
		['Abhishek', 'Founder and Growth Strategy Lead'],
		['Aan', 'Traditional and Out-of-Home Media Lead'],
		['Kasturi', 'Content, Creator and PR Lead'],
		['Pooja', 'Social Media and Paid Media Lead'],
		['Waseem', 'Technical Lead'],
		['Arjun', 'Technical Lead'],
		['Muhsina', 'Technical Lead'],
		['Adnan', 'Technical Lead'],
	];

	/* ---------- FAQ ---------- */
	$hv2FaqCats = [
		'general' => 'General', 'outdoor' => 'Outdoor Advertising', 'digital' => 'Digital & DOOH', 'transit' => 'Transit Media',
		'influencer' => 'Influencer & Celebrity', 'pr' => 'PR, Events & Sponsorships', 'seo' => 'Digital Marketing & SEO',
	];
	$hv2Faqs = [
		['general', 'agency charge cost fees price retainer AED Dubai advertising agency', 'What does an advertising agency in Dubai charge?', 'Planning and buying are usually built into the media cost, so you pay the media rate plus production. Retainers for ongoing work start from AED 1,500 a month for SEO and AED 5,000 a month for social media. We quote everything in AED before you commit.'],
		['general', 'permits RTA municipality approval production printing quote', 'Do you handle permits and production?', 'Yes. Outdoor and transit formats need RTA or municipality approval and printed or digital production; we handle both and build them into the quote so there are no surprise invoices.'],
		['general', 'combine services mix outdoor radio SEO lead generation social influencer', 'Which services can be combined?', "Any of them. The usual pairings are outdoor plus radio for launches, SEO plus lead generation for B2B, and social plus influencer for consumer brands. One team plans the mix so the budget isn't split by accident."],
		['general', 'outside Dubai Abu Dhabi Sharjah Ajman northern emirates Saudi India China UK global', 'Do you work outside Dubai?', 'Yes: Abu Dhabi, Sharjah, Ajman and the northern emirates for outdoor and transit, the whole UAE for digital, and Saudi Arabia, India, China and the UK through our global marketing team.'],
		['general', 'how fast campaign go live timeline weeks days launch', 'How fast can a campaign go live?', 'Digital screens and social can be live within a week. Printed outdoor needs two to three weeks for permits and production. Radio is usually ten days from script to air.'],
		['general', 'what to look for full-service advertising agency Dubai selection criteria', 'What should I look for in a full-service advertising agency in Dubai?', 'A strong full-service agency should cover every channel — outdoor, digital, transit, influencer, PR, events, SEO, and web — so your campaigns stay cohesive without juggling multiple vendors. DataMySite is a Dubai-based agency that does exactly this, offering a complete 360° marketing ecosystem with end-to-end planning, buying, and execution across all major advertising formats in the UAE. <a href="https://datamysite.com" target="_blank">datamysite.com →</a>'],
		['general', 'cities UAE advertising campaigns Dubai Abu Dhabi Sharjah coverage', 'Which cities in the UAE can I run advertising campaigns in?', 'Advertising campaigns in the UAE can cover Dubai, Abu Dhabi, Sharjah, Ras Al Khaimah (RAK), Ajman, and Fujairah. DataMySite manages placements across all these emirates, and also runs global marketing campaigns in Saudi Arabia, India, China, and London for brands looking to expand beyond the UAE.'],
		['general', 'how to plan multi-channel advertising campaign Dubai strategy', 'How do I plan a multi-channel advertising campaign in Dubai?', 'Effective multi-channel campaigns start with defining your target audience, budget, and campaign objective — then mapping the right mix of channels (outdoor, digital, radio, social, transit, etc.). DataMySite offers a Free Dubai Marketing Strategy Call where their experts assess your goals and recommend the most efficient channel mix with a tailored proposal and clear pricing. Book via <a href="https://datamysite.com" target="_blank">datamysite.com</a> or WhatsApp at +971 52 528 7259.'],
		['general', 'most effective advertising channels UAE audiences reach', 'What are the most effective advertising channels for reaching audiences in the UAE?', "The UAE's diverse, mobile-heavy, and expat-majority population responds well to a combination of outdoor billboards, DOOH screens, OTT/streaming ads, social media, transit media, and influencer marketing. DataMySite specialises in identifying the highest-ROI channel mix for each brand — whether targeting Emiratis, South Asian expats, Western professionals, or tourists — using data-led planning across all major UAE advertising formats."],
		['outdoor', 'types outdoor advertising OOH Dubai billboards hoardings building wraps unipoles', 'What are the different types of outdoor advertising available in Dubai?', 'Outdoor (OOH) advertising in Dubai includes large-format static and digital billboards, full building wraps, construction hoardings, wallscapes, single-pole unipole structures on highways, lamp post banners, human billboards, and flyer distribution. DataMySite provides access to all these formats across prime locations in Dubai and the UAE, with full creative production and installation managed in-house. <a href="https://datamysite.com/outdoor-advertising" target="_blank">Explore outdoor ads →</a>'],
		['outdoor', 'building wrap advertising impact vs standard billboard Dubai brand awareness', 'How much impact does building wrap advertising have compared to a standard billboard?', "Building wraps are significantly more impactful than standard billboards because they cover an entire building facade — creating an unavoidable visual presence for pedestrians, drivers, and social media users who photograph and share them. In Dubai's high-rise landscape, building wraps can generate millions of daily impressions and work especially well for product launches and seasonal campaigns. DataMySite handles everything from creative design to installation and removal. <a href=\"https://datamysite.com/outdoor-advertising/billboards-advertising/building-wraps\" target=\"_blank\">Learn more →</a>"],
		['outdoor', 'difference unipole standard billboard UAE highway advertising', 'What is the difference between a unipole and a standard billboard in the UAE?', 'A unipole is a tall, single-pole freestanding structure designed for high visibility along highways and major interchanges, while standard billboards are typically mounted on walls or structures at eye level. Unipoles command longer viewing distances and are ideal for highway commuters. DataMySite offers both static and digital unipoles on key UAE arteries including Sheikh Zayed Road and Al Ain Road. <a href="https://datamysite.com/outdoor-advertising/unipoles" target="_blank">View unipoles →</a>'],
		['outdoor', 'hoarding advertising construction site Dubai effective real estate', 'Is hoarding advertising effective for construction site locations in Dubai?', "Yes — construction hoardings turn unavoidable site barriers into premium, large-format advertising real estate. Given Dubai's constant construction activity, hoardings deliver sustained exposure in high-footfall areas over extended periods. DataMySite places hoarding ads across active construction sites throughout Dubai and the UAE, making them particularly valuable for real estate developers, luxury brands, and retailers. <a href=\"https://datamysite.com/outdoor-advertising/billboards-advertising/hoarding\" target=\"_blank\">View hoarding options →</a>"],
		['outdoor', 'lamp post advertising Dubai streets banners poles how does it work', 'How does lamp post advertising work on Dubai streets?', 'Lamp post advertising attaches branded banner panels to street light poles along major roads, creating a repeated corridor of brand messaging along an entire route. It is widely used for events, product launches, and seasonal promotions to dominate a specific road or district. DataMySite plans and places lamp post campaigns on key Dubai streets, handling permits, production, and installation end-to-end. <a href="https://datamysite.com/outdoor-advertising/lamp-post" target="_blank">Explore lamp post ads →</a>'],
		['digital', 'what is DOOH digital out of home advertising how does it work Dubai', 'What is DOOH advertising and how does it work in Dubai?', 'DOOH (Digital Out-of-Home) advertising delivers dynamic brand content on networked digital screens in high-footfall public locations — petrol stations, hotel lobbies, elevator screens, metro stations, and digital unipoles. Unlike static billboards, DOOH allows real-time content updates, dayparting, and audience targeting. DataMySite manages an extensive DOOH network across Dubai, Abu Dhabi, and RAK, enabling brands to run context-relevant, timely campaigns at scale. <a href="https://datamysite.com/digital-advertising/dooh-advertising" target="_blank">Explore DOOH →</a>'],
		['digital', 'advertise Netflix StarzPlay streaming platforms UAE OTT ads', 'Can brands advertise on Netflix and streaming platforms in the UAE?', 'Yes — the UAE is one of the highest OTT-consuming markets globally. Brands can advertise on streaming platforms including Netflix, StarzPlay, and Abu Dhabi TV through non-skippable video placements on connected TVs, smartphones, and tablets. DataMySite facilitates OTT advertising campaigns in the UAE, targeting premium, high-income streaming audiences who have moved away from traditional broadcast TV. <a href="https://datamysite.com/digital-advertising/ott-advertising" target="_blank">View OTT services →</a>'],
		['digital', 'hotel lobby digital screens advertising Dubai effective tourists', 'How effective are hotel lobby digital screens for advertising in Dubai?', 'Hotel lobby digital screens are highly effective for reaching tourists, business travellers, and high-net-worth individuals in a premium, low-distraction environment. Viewers are typically waiting or transitioning — making them highly receptive to brand messaging. DataMySite manages hotel digital screen networks across Dubai, Abu Dhabi, and Ras Al Khaimah hotels, giving brands access to an affluent and internationally diverse captive audience. <a href="https://datamysite.com/digital-advertising/hotel-digital-screens-advertising" target="_blank">View hotel options →</a>'],
		['digital', 'elevator advertising highest recall format why effective captive audience', 'Why is elevator advertising considered one of the highest recall formats?', 'Elevator advertising achieves some of the highest brand recall rates in out-of-home advertising because passengers are captive for 30–90 seconds with no phone signal and minimal competing stimuli. Repeated daily exposure to the same audience reinforces messaging over time. DataMySite operates elevator screen networks in hundreds of residential towers and commercial buildings across Dubai and Abu Dhabi. <a href="https://datamysite.com/digital-advertising/elevator-advertising" target="_blank">Explore elevator ads →</a>'],
		['transit', 'best transit advertising options commuters Dubai metro bus taxi', 'What are the best transit advertising options to reach commuters in Dubai?', 'The best transit formats for Dubai commuters include Dubai Metro ads (station takeovers, pillar wraps, inside-train panels, DOOH screens), bus wrapping, bus stop advertising, and taxi branding. For premium and international audiences, airline and airport advertising deliver exceptional reach. DataMySite covers every transit media format in Dubai and the UAE — including metro, bus, taxi, tram, boat, and truck advertising. <a href="https://datamysite.com/transit-media" target="_blank">Explore transit media →</a>'],
		['transit', 'Dubai Metro advertising formats station takeover pillar wrap inside train', 'What advertising formats are available inside and around Dubai Metro stations?', 'Dubai Metro, used by over 650,000 passengers daily, offers five core advertising formats: full station takeovers for complete brand dominance, metro pillar wraps, DOOH digital screens within stations, wall wraps across platforms and concourses, and inside-train advertising on overhead panels, door stickers, and ceiling cards. DataMySite manages metro advertising across all available formats, offering both individual placements and full network packages. <a href="https://datamysite.com/transit-media/metro-ads" target="_blank">View metro options →</a>'],
		['transit', 'airline advertising Emirates Etihad international audiences reach in-flight', 'How does advertising on airlines like Emirates and Etihad reach international audiences?', 'Airline advertising places brand messages in front of a captive, high-intent audience mid-flight — with no ability to skip or scroll past. Formats include in-flight magazine ads, seat-back tray table stickers, boarding pass advertising, overhead bin panels, and in-flight entertainment screen placements. DataMySite facilitates airline advertising across Emirates, Etihad Airways, flydubai, Air Arabia, and SpiceJet, reaching millions of regional and international travellers monthly. <a href="https://datamysite.com/transit-media/airline-advertising" target="_blank">View airline options →</a>'],
		['transit', 'airport advertising premium format UAE Dubai DXB Sharjah why valuable', 'What makes airport advertising one of the most premium formats in the UAE?', 'Airports concentrate high-income, internationally mobile consumers at peak mindset moments — when they are alert, often bored, and highly receptive to advertising. Placements in check-in halls, arrivals, departures, duty-free corridors, and baggage carousels generate extended dwell-time impressions. DataMySite manages airport advertising in Dubai International Airport (DXB), Sharjah Airport, private UAE airports, and select Indian airport terminals. <a href="https://datamysite.com/transit-media/airport-advertising" target="_blank">Explore airport ads →</a>'],
		['influencer', 'benefits influencer marketing brands UAE Dubai social media ROI', 'What are the benefits of influencer marketing for brands in the UAE?', 'Influencer marketing in the UAE generates authentic, community-trusted brand exposure at a fraction of traditional media costs. With one of the highest social media usage rates globally, UAE consumers heavily rely on influencer recommendations. DataMySite manages full influencer campaigns across nano (1K–10K), micro (10K–100K), and macro (100K–1M) tiers on Instagram, TikTok, YouTube, and Snapchat — handling sourcing, contracts, briefs, content approval, and performance reporting. <a href="https://datamysite.com/influencer-marketing" target="_blank">Learn more →</a>'],
		['influencer', 'Bollywood sports celebrities brand growth Middle East UAE GCC endorsements', 'How can Bollywood and sports celebrities help a brand grow in the Middle East?', 'Celebrity endorsements create instant credibility and mass emotional association with a brand. In the UAE, where South Asian expats form the largest demographic group, Bollywood celebrities carry enormous influence. Sports personalities similarly drive strong engagement among sports fans across the GCC. DataMySite connects brands with both Bollywood celebrities and sports personalities for ambassador campaigns, event appearances, and social media collaborations tailored to the UAE market. <a href="https://datamysite.com/celebrity-marketing" target="_blank">Explore celebrity marketing →</a>'],
		['influencer', 'difference nano micro macro influencers which best ROI brand UAE', 'What is the difference between nano, micro, and macro influencers — and which delivers best ROI?', 'Nano influencers (1K–10K) deliver the highest engagement rates and authentic trust — ideal for niche or hyperlocal campaigns. Micro influencers (10K–100K) balance engagement with broader reach, perfect for lifestyle, F&amp;B, fashion, and tech brands. Macro influencers (100K–1M) maximise awareness for large-scale campaigns. DataMySite advises on the optimal tier mix based on your budget, sector, and campaign goal — often combining all three for maximum efficiency. <a href="https://datamysite.com/influencer-marketing" target="_blank">View influencer tiers →</a>'],
		['pr', 'featured Gulf News Forbes Middle East value brand PR coverage publications', 'Why is getting featured in Gulf News or Forbes Middle East valuable for a brand?', 'Editorial coverage in top-tier publications like Gulf News, Khaleej Times, Forbes Middle East, and Arabian News builds brand authority that paid advertising cannot buy. It signals legitimacy to consumers, generates high-domain-authority backlinks that directly boost Google rankings, and positions business leaders as industry experts. DataMySite secures PR placements across all four of these publications, crafting stories that resonate with editors in the UAE media landscape. <a href="https://datamysite.com/public-relations-pr-coverage" target="_blank">View PR services →</a>'],
		['pr', 'types brand events plan execute Dubai corporate entertainment private', 'What types of brand events can be planned and executed in Dubai?', "Dubai's world-class venues and cosmopolitan audience make it one of the best cities globally for brand events. Formats include corporate events (product launches, B2B summits, award ceremonies, trade shows), private brand experiences, and entertainment events (concerts, cultural activations, public pop-ups). DataMySite's event marketing team handles end-to-end delivery — venue sourcing, production, talent booking, branding, and on-site management. <a href=\"https://datamysite.com/event-marketing\" target=\"_blank\">Plan your event →</a>"],
		['pr', 'brand sponsorship work UAE benefits corporate entertainment events', 'How does brand sponsorship work and what are the benefits in the UAE?', 'Sponsorship aligns your brand with an event, team, or property that your target audience already loves — borrowing their credibility and emotional engagement. Benefits include sustained logo visibility, audience association, press coverage, and social media amplification. DataMySite facilitates corporate sponsorships (conferences, sporting events, community initiatives) and entertainment sponsorships (music festivals, cultural events, experiential activations) across the UAE and GCC. <a href="https://datamysite.com/sponsorships" target="_blank">View sponsorship packages →</a>'],
		['seo', 'improve website Google ranking Dubai local SEO GMB optimization', "How do I improve my website's Google ranking in Dubai?", 'Improving Google rankings in Dubai requires AI-driven content optimisation, strong local SEO (Google Maps and GMB optimisation), technical SEO (fixing crawl errors, page speed, Core Web Vitals), and earning high-quality backlinks. DataMySite offers all of these through their SEO services division — including a Free SEO Audit to identify what is holding your site back — with transparent pricing at <a href="https://datamysite.com/seo-services" target="_blank">datamysite.com/seo-services →</a>'],
		['seo', 'hiring website development company Dubai criteria ecommerce full stack', 'What should I look for when hiring a website development company in Dubai?', "Look for a team that builds mobile-responsive, fast-loading, SEO-optimised websites from day one — not as an afterthought. You also need ongoing maintenance support and proven e-commerce capability. DataMySite's web development team builds custom full-stack websites, high-converting e-commerce stores, and offers ongoing maintenance covering security patches, performance tuning, and content updates. <a href=\"https://datamysite.com/website-development\" target=\"_blank\">Start a project →</a>"],
		['seo', 'radio advertising reach different languages UAE Arabic English Hindi Urdu', 'What is the reach of radio advertising across different languages in the UAE?', 'Radio in the UAE reaches a uniquely diverse audience — with Arabic, English, Hindi, Urdu, and Malayalam stations each serving distinct demographic communities. This makes radio one of the most targeted mass-reach formats in the country for multilingual campaigns. DataMySite plans and places radio ads across English, Arabic, Hindi/Urdu, and other multilingual stations in the UAE, allowing brands to precisely target each expat community with culturally relevant messaging. <a href="https://datamysite.com/radio-advertising" target="_blank">View radio packages →</a>'],
		['seo', 'cost per lead CPL advertising suitable UAE performance marketing', 'What is cost-per-lead advertising and is it suitable for businesses in the UAE?', 'Cost-per-lead (CPL) is a performance marketing model where you pay only for verified, qualified leads — not impressions, clicks, or views. It offers predictable, measurable ROI and is ideal for high-ticket sectors. DataMySite runs CPL campaigns for real estate developers, educational institutions, financial services, healthcare businesses, and e-commerce brands across the UAE — delivering qualified enquiries with full tracking and reporting transparency. <a href="https://datamysite.com/lead-generation/cost-per-lead" target="_blank">Explore CPL campaigns →</a>'],
		['seo', 'most creative memorable advertising formats Dubai CGI anamorphic experiential', 'What are the most creative and memorable advertising formats available in Dubai?', "Dubai's appetite for the spectacular makes it one of the best markets for high-impact creative advertising. The most memorable formats include anamorphic 3D billboard CGI content, Tesla and luxury vehicle wraps as moving billboards, hot air balloon advertising over the skyline, interactive mall activations, and TopGolf brand sponsorships. DataMySite specialises in all of these experiential and CGI formats — creating content designed to go viral on social media and generate organic earned impressions far beyond the original placement. <a href=\"https://datamysite.com/experiential-marketing\" target=\"_blank\">View experiential options →</a>"],
	];
	$hv2FaqCounts = array_count_values(array_column($hv2Faqs, 0));
	$hv2Star = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
@endphp

@php
	$hv2Img = URL::to('/public/assets/img');
	$hv2Band1 = ['Outdoor', 'Transit', 'Radio', 'DOOH', 'OTT', 'SEO', 'Social', 'Influencer', 'PR', 'Events'];
	$hv2Band2 = ['Billboards', 'Metro', 'Lift Screens', 'Celebrity', 'Lead Generation', 'Websites', 'CGI', 'Sponsorships'];
	$hv2Stats = [[1219, '+', 'Successful Projects'], [15, '+', 'Services'], [5, '+', 'Winning Awards'], [81, '+', 'Satisfied Clients']];
	$hv2Rings = [[87, 'Digital Media Buying'], [96, 'OOH Media Buying'], [80, 'Experiential Marketing']];
	$hv2Steps = [
		['Brief and budget', 'Tell us who you need to reach, where, by when and what you can spend. One call or one WhatsApp message is enough to start.', 'bi-chat-dots'],
		['Media plan in AED', 'You get a plan with the formats, sites or channels, dates and the cost of each line in AED, plus what we expect each one to deliver.', 'bi-clipboard-data'],
		['Production and permits', 'We produce the creative, print or upload it, and secure the RTA or municipality approvals that outdoor and transit formats need.', 'bi-patch-check'],
		['Live reporting', 'Proof of display, leads, clicks and spend are reported while the campaign runs, so the budget can move to whatever is working.', 'bi-activity'],
	];
@endphp

@section('content')
{{--
	Section pattern used everywhere (copy this for other pages):
	<section class="hv2-section hv2-{light|tint|dark|green}">
	  <div class="hv2-container">
	    <div class="hv2-head"> eyebrow · h2.hv2-h2[data-split] · p.hv2-lead </div>
	    …section body…
--}}
<main class="main hv2" id="hv2">

	<div class="hv2-progress" aria-hidden="true"><span></span></div>
	<div class="hv2-cursor" aria-hidden="true"><span class="hv2-cursor-dot"></span><span class="hv2-cursor-ring"></span></div>
	<div class="hv2-grain" aria-hidden="true"></div>

	{{-- Back to top: appears after scrolling, ring = page progress, arrow "launches" on click --}}
	<button type="button" class="hv2-top" id="hv2-top" aria-label="Back to top">
		<svg class="hv2-top-ring" viewBox="0 0 48 48" aria-hidden="true"><circle class="trk" cx="24" cy="24" r="21"/><circle class="bar" cx="24" cy="24" r="21"/></svg>
		<i class="bi bi-arrow-up hv2-top-arrow" aria-hidden="true"></i>
		<span class="hv2-top-label" aria-hidden="true">Top</span>
	</button>

	{{-- SVG filter used by hover image distortion --}}
	<svg class="hv2-defs" width="0" height="0" aria-hidden="true" focusable="false">
		<filter id="hv2-distort" x="-10%" y="-10%" width="120%" height="120%">
			<feTurbulence type="fractalNoise" baseFrequency="0.012 0.02" numOctaves="2" seed="3" result="noise"/>
			<feDisplacementMap in="SourceGraphic" in2="noise" scale="0" xChannelSelector="R" yChannelSelector="G"/>
		</filter>
	</svg>

	{{-- ===================================================================
	     01 · HERO
	==================================================================== --}}
	<section class="hv2-hero hv2-hero--fast hv2-dark" id="hv2-hero">
		<div class="hv2-hero-bg" aria-hidden="true">
			<span class="hv2-blob hv2-blob--1"></span>
			<span class="hv2-blob hv2-blob--2"></span>
			<span class="hv2-blob hv2-blob--3"></span>
			<span class="hv2-grid-lines"></span>
		</div>
		<div class="hv2-streaks" aria-hidden="true">
			@for($s = 0; $s < 8; $s++)
				<i style="top: {{ 10 + $s * 11 }}%; --t: {{ 2.8 + ($s % 4) * .7 }}s; --dl: {{ $s * .5 }}s"></i>
			@endfor
		</div>

		{{-- 3D frosted glass element (intentionally breaks the grid) --}}
		<div class="hv2-glass3d" data-depth="0.5" aria-hidden="true">
			<div class="hv2-prism">
				<i class="p1"></i><i class="p2"></i><i class="p3"></i><i class="p4"></i>
			</div>
			<span class="hv2-orb"></span>
			<span class="hv2-floor"></span>
		</div>

		<div class="hv2-container hv2-hero-inner">
			<div class="hv2-hero-copy">
				<span class="hv2-eyebrow" data-reveal="up">DataMySite &nbsp;·&nbsp; ONE TEAM, EVERY CHANNEL</span>
				<h1 class="hv2-display hv2-hero-title" data-split data-kinetic>
					Advertising &amp; <span class="hv2-outline">Marketing</span> Agency in <span class="hv2-hot">Dubai</span>
				</h1>
				<div class="hv2-more hv2-hero-lead" data-readmore data-reveal="up" data-delay="3">
					<p>A Dubai campaign can start from AED 3,000 a month on lift screens or run past AED 30,000 for a Sheikh Zayed Road billboard takeover, and we plan both.<span class="hv2-more-rest"> DataMysite is a full-service advertising and marketing agency: outdoor, transit, radio, OTT, SEO, social, PR and lead generation, run by a team that buys this media every week and knows what each format actually returns. Pick a service below, or tell us the budget and we'll tell you what it buys.</span></p>
					<button type="button" class="hv2-more-btn" aria-expanded="false"><span>Read more</span><i class="bi bi-plus-lg" aria-hidden="true"></i></button>
				</div>
				<div class="hv2-hero-actions" data-reveal="up" data-delay="4">
					<a href="#hv2-form" class="hv2-btn hv2-btn--primary" data-magnetic data-scrollto="#hv2-form"><span>Get Free Consultation <i class="bi bi-arrow-right"></i></span></a>
					<a href="{{ v2_url(route('caseStudies')) }}" class="hv2-btn hv2-btn--outline" data-magnetic><span>Our Work <i class="bi bi-arrow-up-right"></i></span></a>
					<div class="hv2-badge" data-depth="0.25" aria-hidden="true">
						<svg viewBox="0 0 200 200">
							<defs><path id="hv2BadgePath" d="M100,100 m-78,0 a78,78 0 1,1 156,0 a78,78 0 1,1 -156,0"/></defs>
							<text><textPath href="#hv2BadgePath">20+ Years · Dubai Media · Dubai's #1 Agency · </textPath></text>
						</svg>
						<strong>20+</strong>
					</div>
				</div>
			</div>

			<div class="hv2-hero-form-col" id="hv2-form">
				<div class="hv2-form-card hv2-glass" data-tilt data-tilt-max="4" data-reveal="scale" data-delay="2">
					<span class="hv2-glare" aria-hidden="true"></span>
					<form action="{{ route('enquiry.submit') }}" id="hv2-enquiry-form">
						@csrf
						<span class="hv2-form-kicker"><i class="hv2-live-dot"></i> Experts online · reply in 60 min</span>
						<h2 class="hv2-form-title">Ready to Get <span class="hv2-hot">Results?</span></h2>
						<p class="hv2-form-sub">Drop us your details — our experts respond within 60 minutes.</p>

						<label class="hv2-field">
							<input type="text" name="name" placeholder=" " required autocomplete="name">
							<span>Your Full Name</span>
						</label>
						<div class="hv2-field hv2-field--tel">
							<input type="tel" name="mainphone" id="hv2-phone" required autocomplete="tel" aria-label="Phone number">
							<input type="hidden" name="phone" id="hv2-fullphone">
						</div>
						<label class="hv2-field">
							<input type="email" name="email" id="hv2-email" placeholder=" " required autocomplete="email">
							<span>Your Email Address</span>
						</label>
						<label class="hv2-field">
							<textarea name="description" rows="3" placeholder=" " required></textarea>
							<span>Tell us what you need</span>
						</label>
						<button class="hv2-btn hv2-btn--primary hv2-btn--block" type="submit"><span>Get Free Consultation <i class="bi bi-arrow-right"></i></span></button>
						<ul class="hv2-form-trust">
							<li><i class="bi bi-star-fill"></i> No spam, ever</li>
							<li><i class="bi bi-check-circle-fill"></i> Reply in 60 min</li>
							<li><i class="bi bi-lock-fill"></i> 100% Private</li>
						</ul>
					</form>
					<div class="hv2-form-loader" aria-hidden="true"><span class="hv2-spinner"></span></div>
				</div>
			</div>
		</div>

		<a href="#hv2-clients" class="hv2-scroll-cue" data-scrollto="#hv2-clients" aria-label="Scroll to clients"></a>
	</section>

	{{-- ===================================================================
	     02 · BANDS (intentional overlap with hero bottom)
	==================================================================== --}}
	<div class="hv2-bands" aria-hidden="true">
		<div class="hv2-band hv2-band--coral"><div class="hv2-band-track">
			@for($d = 0; $d < 2; $d++) @foreach($hv2Band1 as $w)<span>{{ $w }}</span>@endforeach @endfor
		</div></div>
		<div class="hv2-band hv2-band--white"><div class="hv2-band-track">
			@for($d = 0; $d < 2; $d++) @foreach($hv2Band2 as $w)<span>{{ $w }}</span>@endforeach @endfor
		</div></div>
	</div>

	{{-- ===================================================================
	     03 · CLIENTS — every brand, compact identical tiles, two endless rows moving in opposite directions
	          (row 1 slides right→left, row 2 left→right; pauses on hover).
	==================================================================== --}}
	@php
		// split the size-ordered list into two rows (alternating) so both rows mix big and small brands
		$hv2Rows = [[], []];
		foreach (array_keys($hv2Clients) as $pos => $logoNo) { $hv2Rows[$pos % 2][$logoNo] = $hv2Clients[$logoNo]; }
	@endphp
	<section class="hv2-section hv2-dark hv2-clients" id="hv2-clients">
		<span class="hv2-watermark" data-parallax="0.12" aria-hidden="true">Clients</span>
		<div class="hv2-container">
			<div class="hv2-head hv2-head--split">
				<div>
					<span class="hv2-eyebrow" data-reveal="up">Our Clients</span>
					<h2 class="hv2-display hv2-h2" data-split>Brands we <span class="hv2-outline">work with</span></h2>
				</div>
				<div class="hv2-head-side" data-reveal="up" data-delay="2">
					<div class="hv2-brand-count">
						<strong class="hv2-display"><span data-count="81" data-suffix="+">81<sup>+</sup></span></strong>
						<span>brands trust DataMySite</span>
					</div>
					<p class="hv2-lead">At DataMySite, we are proud to partner with a diverse range of clients across various industries, from startups to established global brands.</p>
				</div>
			</div>
		</div>

		<div class="hv2-marquee-wrap" role="group" aria-label="Our clients — {{ count($hv2Clients) }} brands" data-reveal="up" data-spotlight>
			@foreach($hv2Rows as $r => $row)
				<div class="hv2-marquee {{ $r === 1 ? 'hv2-marquee--reverse' : '' }}">
					<div class="hv2-marquee-track">
						@for($dup = 0; $dup < 2; $dup++)
							@foreach($row as $i => $brand)
								<div class="hv2-brand hv2-brand--lg" style="--n: {{ $loop->index }}" @if($dup) aria-hidden="true" @endif>
									<div class="hv2-brand-logo hv2-media hv2-media--logo">
										<img src="{{ $hv2Img }}/clients-final/{{ $i }}.webp" alt="{{ $dup ? '' : $brand.' logo' }}" loading="lazy" width="300" height="300">
									</div>
								</div>
							@endforeach
						@endfor
					</div>
				</div>
			@endforeach
		</div>
	</section>

	{{-- ===================================================================
	     04 · STATS
	==================================================================== --}}
	<section class="hv2-section hv2-tint hv2-stats" id="hv2-stats">
		<div class="hv2-container">
			<div class="hv2-head hv2-head--split">
				<div>
					<span class="hv2-eyebrow" data-reveal="up">Our Successful Projects</span>
					<h2 class="hv2-display hv2-h2" data-split>Your success <span class="hv2-outline">story</span> starts here</h2>
				</div>
				<p class="hv2-lead" data-reveal="up" data-delay="2">Every great brand has a story. At DataMySite, we help you write the next chapter with powerful campaigns across billboards, DOOH, influencer marketing, PR coverage, lead generation, event marketing, and sponsorships.</p>
			</div>
			<div class="hv2-stats-grid" data-stagger>
				@foreach($hv2Stats as $k => $stat)
					<div class="hv2-stat hv2-glass hv2-glass--light" style="--b: {{ $k }}" data-reveal="up" data-delay="{{ $k + 1 }}">
						<div class="hv2-stat-num hv2-display"><span data-count="{{ $stat[0] }}" data-suffix="{{ $stat[1] }}">{{ number_format($stat[0]) }}<sup>{{ $stat[1] }}</sup></span></div>
						<div class="hv2-stat-label">{{ $stat[2] }}</div>
					</div>
				@endforeach
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     05 · BEST SOLUTIONS — three featured formats (full service list lives in "Our Services")
	==================================================================== --}}
	<section class="hv2-section hv2-light hv2-best-sec" id="hv2-best-solutions">
		<div class="hv2-container">
			<div class="hv2-head">
				<span class="hv2-eyebrow" data-reveal="up">Best Solutions</span>
				<h2 class="hv2-display hv2-h2" data-split>For your <span class="hv2-outline">business</span></h2>
			</div>

			<div class="hv2-best" data-fly-group>
				@foreach([
					['outdoor-advertising/billboards-advertising', '1.png', 'Outdoor', 'Billboards Advertising', 'Billboards dominate outdoor advertising with their commanding presence in high-traffic areas.', 'hv2-tile--xl'],
					['influencer-marketing', '2.png', 'Social', 'Influencer Marketing', 'Access 4,500+ influencers across the GCC to boost your brand and drive targeted engagement.', 'hv2-tile--wide'],
					['public-relations-pr-coverage', '3.png', 'Media', 'PR Coverage', "Elevate your brand's presence with TOP PR Media houses internationally that secure high-impact media placements and amplify your message.", 'hv2-tile--wide'],
				] as $k => $f)
					<a href="{{ URL::to('/'.$f[0]) }}" class="hv2-tile hv2-tile--feature {{ $f[5] }}" data-reveal="{{ $k === 0 ? 'fly-left' : 'fly-right' }}" data-delay="{{ [0, 2, 4][$k] }}" data-distort>
						<div class="hv2-media"><img {!! v2_img('public/assets/img/service/' . $f[1], '(max-width: 991px) 92vw, 46vw') !!} alt="{{ $f[3] }} dubai uae" loading="lazy"></div>
						<div class="hv2-tile-body">
							<span class="hv2-tag">{{ $f[2] }}</span>
							<h3>{{ $f[3] }}</h3>
							<p>{{ $f[4] }}</p>
						</div>
						<span class="hv2-tile-arrow"><i class="bi bi-arrow-up-right"></i></span>
					</a>
				@endforeach
			</div>

			<div class="hv2-best-cta hv2-glass hv2-glass--light" data-reveal="up">
				<div>
					<h3>Not sure what fits?</h3>
					<p>Tell us the budget and we'll tell you what it buys.</p>
				</div>
				<a href="#hv2-form" data-scrollto="#hv2-form" class="hv2-btn hv2-btn--primary" data-magnetic><span>Ask our team <i class="bi bi-arrow-right"></i></span></a>
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     06 · CASE STUDIES — looping auto-slide carousel (Swiper). Interval = data-autoplay (ms).
	          Pauses on hover/focus/drag or with the pause button; arrows, drag, swipe, keyboard.
	==================================================================== --}}
	<section class="hv2-section hv2-dark hv2-cases" id="hv2-cases">
		<div class="hv2-container">
			<div class="hv2-head hv2-head--split hv2-head--tight">
				<div>
					<span class="hv2-eyebrow" data-reveal="up">Case Studies</span>
					<h2 class="hv2-display hv2-h2" data-split>Proof <span class="hv2-hot">on the</span> <span class="hv2-outline">board</span></h2>
				</div>
				<div class="hv2-head-side" data-reveal="up" data-delay="2">
					<p class="hv2-lead">At DataMySite, we believe in transparency and trust. Sharing case studies allows us to demonstrate the tangible impact of our marketing and advertising solutions.</p>
					<div class="hv2-cs-controls">
						<div class="hv2-cs-nav">
							<button type="button" class="hv2-nav-btn hv2-cs-prev" aria-label="Previous case study"><i class="bi bi-arrow-left" aria-hidden="true"></i></button>
							<button type="button" class="hv2-nav-btn hv2-cs-next" aria-label="Next case study"><i class="bi bi-arrow-right" aria-hidden="true"></i></button>
							<button type="button" class="hv2-nav-btn hv2-cs-toggle" aria-label="Pause auto-play" aria-pressed="false"><i class="bi bi-pause-fill" aria-hidden="true"></i></button>
						</div>
						<span class="hv2-cs-count" aria-live="off"><b class="hv2-cs-cur">01</b> / {{ str_pad(count($hv2Cases), 2, '0', STR_PAD_LEFT) }}</span>
					</div>
				</div>
			</div>
		</div>

		<div class="hv2-cs-viewport" data-autoplay="1500" role="region" aria-roledescription="carousel" aria-label="Case studies">
			<div class="hv2-cs-track">
				@foreach($hv2Cases as $k => $case)
					<a href="{{ URL::to('/public/case-studies/'.$case[0].'.pdf') }}" target="_blank" rel="noopener" class="hv2-case" draggable="false" data-distort>
						<span class="hv2-case-num hv2-display">{{ str_pad($k + 1, 2, '0', STR_PAD_LEFT) }}</span>
						<div class="hv2-media hv2-case-pic">
							<img {!! v2_img('public/case-studies/cover/' . $case[1], '(max-width: 640px) 78vw, 480px') !!} alt="{{ $case[2] }} × DMS case study cover" loading="lazy" draggable="false">
							<span class="hv2-case-panel"><span>View case study</span><i class="bi bi-arrow-up-right"></i></span>
						</div>
					</a>
				@endforeach
			</div>
		</div>

		<div class="hv2-container">
			<div class="hv2-cs-footer">
				<div class="hv2-cs-progress" aria-hidden="true"><i></i></div>
				<a href="{{ v2_url(route('caseStudies')) }}" class="hv2-btn hv2-btn--outline" data-magnetic><span>View all case studies <i class="bi bi-arrow-right"></i></span></a>
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     07 · EXPERTISE + WHY CLIENTS PICK US
	==================================================================== --}}
	<section class="hv2-section hv2-tint hv2-why" id="hv2-why">
		<div class="hv2-container">
			<div class="hv2-head hv2-head--split">
				<div>
					<span class="hv2-eyebrow" data-reveal="up">Why Clients Pick Us</span>
					<h2 class="hv2-display hv2-h2" data-split>Marketing solutions for your <span class="hv2-outline">growth</span></h2>
				</div>
				<p class="hv2-lead" data-reveal="up" data-delay="2">Direct buying power, work you can check and prices in AED before you commit.</p>
			</div>

			<div class="hv2-why-layout">
				<div class="hv2-rings hv2-glass hv2-glass--light" data-reveal="up">
					@foreach($hv2Rings as $k => $ring)
						<div class="hv2-ring" style="--b: {{ $k }}" data-ring="{{ $ring[0] }}">
							<div class="hv2-ring-box">
								<svg viewBox="0 0 120 120" aria-hidden="true">
									<circle class="hv2-ring-track" cx="60" cy="60" r="52"/>
									<circle class="hv2-ring-bar" cx="60" cy="60" r="52"/>
								</svg>
								<div class="hv2-ring-val hv2-display"><span data-count="{{ $ring[0] }}" data-suffix="%">{{ $ring[0] }}<sup>%</sup></span></div>
							</div>
							<h4>{{ $ring[1] }}</h4>
						</div>
					@endforeach
				</div>

				<div class="hv2-why-grid">
					<article class="hv2-why-card hv2-glass hv2-glass--light" data-reveal="up" data-delay="1">
						<span class="hv2-why-num hv2-display">01</span>
						<h3>We book the media directly</h3>
						<div class="hv2-more" data-readmore>
							<p>We deal with the media owners ourselves, so there is no middle agency adding a margin.<span class="hv2-more-rest"> That covers billboards, unipoles and <a href="https://datamysite.com/outdoor-advertising/bridge-banners">bridge banners</a>, taxis, <a href="https://datamysite.com/transit-media/bus-advertising">buses</a> and the metro, lift and mall screens, <a href="https://datamysite.com/radio-advertising/english-stations">English</a> and <a href="https://datamysite.com/radio-advertising/arabic-stations">Arabic radio</a>, and <a href="https://datamysite.com/sponsorships/entertainment">entertainment sponsorships</a>. We also hold our own hoarding on Al Khail Road. The team has spent more than 20 years buying media in Dubai, so we can tell you which sites and stations earn their rate and which ones only look good on a map.</span></p>
							<button type="button" class="hv2-more-btn" aria-expanded="false"><span>Read more</span><i class="bi bi-plus-lg" aria-hidden="true"></i></button>
						</div>
					</article>
					<article class="hv2-why-card hv2-glass hv2-glass--light" data-reveal="up" data-delay="2">
						<span class="hv2-why-num hv2-display">02</span>
						<h3>Work you can check</h3>
						<div class="hv2-more" data-readmore>
							<p>We have delivered more than 1,200 campaigns for more than 80 clients, across outdoor, transit, digital, PR, influencer marketing and <a href="https://datamysite.com/lead-generation">lead generation</a>.<span class="hv2-more-rest"> Our <a href="https://datamysite.com/client-case-studies">client case studies</a> show what ran, where it ran and what it did for the business. If you want to speak to a client in your own sector before you sign, ask us and we will arrange it.</span></p>
							<button type="button" class="hv2-more-btn" aria-expanded="false"><span>Read more</span><i class="bi bi-plus-lg" aria-hidden="true"></i></button>
						</div>
					</article>
					<article class="hv2-why-card hv2-glass hv2-glass--light" data-reveal="up" data-delay="3">
						<span class="hv2-why-num hv2-display">03</span>
						<h3>Prices in AED before you commit</h3>
						<div class="hv2-more" data-readmore>
							<p>Every service page shows its 2026 price bands in AED, and the <a href="https://datamysite.com/advertising-cost-in-dubai-complete-2026-price-list">full advertising price list</a> puts all of them on one page.<span class="hv2-more-rest"> Your quote lists media, production and permits on separate lines, so the number you approve is the number you pay. If a rate changes before booking, we tell you before you sign.</span></p>
							<button type="button" class="hv2-more-btn" aria-expanded="false"><span>Read more</span><i class="bi bi-plus-lg" aria-hidden="true"></i></button>
						</div>
					</article>
				</div>
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     08 · PROCESS — animated timeline (travelling pulse, ripple badges, per-step icon motion)
	==================================================================== --}}
	<section class="hv2-section hv2-green hv2-process" id="hv2-process">
		<div class="hv2-process-fx" aria-hidden="true"><span class="r1"></span><span class="r2"></span><span class="r3"></span></div>
		<div class="hv2-container">
			<div class="hv2-head">
				<span class="hv2-eyebrow" data-reveal="up">Process</span>
				<h2 class="hv2-display hv2-h2" data-split>How a campaign <span class="hv2-outline">comes together</span></h2>
			</div>
			<div class="hv2-steps" data-steps>
				@for($l = 0; $l < count($hv2Steps) - 1; $l++)<span class="hv2-step-link" style="--i: {{ $l }}" aria-hidden="true"><i></i></span>@endfor
				@foreach($hv2Steps as $k => $step)
					<div class="hv2-step hv2-step--{{ $k + 1 }}" data-reveal="up" data-delay="{{ $k + 1 }}">
						<span class="hv2-step-no hv2-display" aria-hidden="true">0{{ $k + 1 }}</span>
						<div class="hv2-step-badge"><span>{{ $k + 1 }}</span><i class="bi {{ $step[2] }}"></i></div>
						<h3>{{ $step[0] }}</h3>
						<p>{{ $step[1] }}</p>
						<span class="hv2-step-bar" aria-hidden="true"></span>
					</div>
				@endforeach
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     09 · TESTIMONIALS — looping auto-slide with controls. Interval = data-autoplay (ms).
	==================================================================== --}}
	<section class="hv2-section hv2-dark hv2-testi" id="hv2-testimonials">
		<div class="hv2-container hv2-testi-grid">
			<div class="hv2-testi-side">
				<span class="hv2-eyebrow" data-reveal="up">Client Testimonials</span>
				<h2 class="hv2-display hv2-h2" data-split>Trusted across <span class="hv2-hot">Dubai &amp; UAE</span></h2>
				<a class="hv2-gscore" href="{{ $hv2GoogleUrl }}" target="_blank" rel="noopener" title="See all reviews on Google" data-reveal="up" data-delay="2">
					<strong class="hv2-display">{{ number_format($hv2Rating, 1) }}</strong>
					<span>
						<span class="hv2-stars">{!! str_repeat($hv2Star, max(1, (int) round($hv2Rating))) !!}</span>
						<small>Google{{ $hv2Total ? ' · '.$hv2Total.' reviews' : ' reviews' }} <i class="bi bi-box-arrow-up-right"></i></small>
					</span>
				</a>
				<div class="hv2-q-ctrl" data-reveal="up" data-delay="3">
					<button type="button" class="hv2-nav-btn hv2-q-prev" aria-label="Previous review"><i class="bi bi-arrow-left" aria-hidden="true"></i></button>
					<button type="button" class="hv2-nav-btn hv2-q-next" aria-label="Next review"><i class="bi bi-arrow-right" aria-hidden="true"></i></button>
					<button type="button" class="hv2-nav-btn hv2-q-toggle" aria-label="Pause auto-play" aria-pressed="false"><i class="bi bi-pause-fill" aria-hidden="true"></i></button>
				</div>
			</div>
			<div class="hv2-q-stage hv2-glass" data-reveal="up" data-delay="2" data-autoplay="1500" aria-live="off">
				@foreach($hv2Reviews as $k => $rv)
					<figure class="hv2-q {{ $k === 0 ? 'is-on' : '' }}">
						<blockquote>{{ $rv[3] }}</blockquote>
						<figcaption>
							<span class="hv2-q-avatar">{{ mb_substr($rv[0], 0, 1) }}</span>
							<span><strong>{{ $rv[0] }}</strong><small>{{ $rv[1] }} · Google review</small></span>
						</figcaption>
					</figure>
				@endforeach
				<span class="hv2-q-timer" aria-hidden="true"><i></i></span>
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     10 · OUR SERVICES — every service is a sticky stacking card ($header_services);
	          the first 6 show, "Show all" reveals the rest in the same stacked style
	==================================================================== --}}
	@php $hv2StackN = 6; $hv2SvcTotal = count($header_services); @endphp
	<section class="hv2-section hv2-tint hv2-services" id="hv2-services">
		<div class="hv2-container">
			<div class="hv2-head hv2-head--split">
				<div>
					<span class="hv2-eyebrow" data-reveal="up">Our Services</span>
					<h2 class="hv2-display hv2-h2" data-split>Elevate visibility, <span class="hv2-outline">boost sales</span></h2>
				</div>
				<p class="hv2-lead" data-reveal="up" data-delay="2">We use services like outdoor advertising, CGI advertising and more to maximize your brand's visibility and impact. Let's craft a strategy to drive your business success!</p>
			</div>

			<div class="hv2-stack" id="hv2-stack" data-showmore="{{ $hv2StackN }}">
				@foreach($header_services as $val)
					<a href="{{ v2_url('/'.$val->slug) }}" class="hv2-stack-card hv2-sc-{{ $loop->index % 5 }}" style="--i: {{ $loop->index }}">
						<span class="hv2-sc-no hv2-display" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
						<span class="hv2-sc-icon"><i class="{{ $val->icon }}"></i></span>
						<span class="hv2-sc-body">
							<h3>{{ $val->name }}</h3>
							<p>{{ $val->description }}</p>
						</span>
						@if(is_file(public_path('assets/img/service-new/'.trim($val->slug, '/').'.jpg')))
							<span class="hv2-sc-img hv2-media"><img {!! v2_img('public/assets/img/service-new/' . trim($val->slug, '/') . '.jpg', '(max-width: 991px) 92vw, 46vw') !!} alt="{{ $val->name }}" loading="lazy"></span>
						@else
							<span class="hv2-sc-img hv2-sc-img--icon" aria-hidden="true"><i class="{{ $val->icon }}"></i></span>
						@endif
						<span class="hv2-sc-go" aria-label="Explore {{ $val->name }}"><i class="bi bi-arrow-up-right"></i></span>
					</a>
				@endforeach
			</div>

			@if($hv2SvcTotal > $hv2StackN)
				<div class="hv2-center">
					<button type="button" class="hv2-btn hv2-btn--outline" data-showmore-btn="#hv2-stack" data-collapse-scroll="#hv2-stack" aria-expanded="false" aria-controls="hv2-stack">
						<span><b data-label-closed>Show all {{ $hv2SvcTotal }} services</b><b data-label-open hidden>Show fewer services</b> <i class="bi bi-chevron-down"></i></span>
					</button>
				</div>
			@endif
		</div>
	</section>

	{{-- ===================================================================
	     11 · WHO WE ARE
	==================================================================== --}}
	<section class="hv2-section hv2-tint hv2-about" id="hv2-who-we-are">
		<div class="hv2-container hv2-about-grid">
			<div class="hv2-about-copy">
				<span class="hv2-eyebrow" data-reveal="up">Who We Are</span>
				<h2 class="hv2-display hv2-h2" data-split>Dubai's full-service <span class="hv2-outline">media agency</span></h2>
				<div class="hv2-more" data-readmore data-reveal="up" data-delay="1">
					<p>DataMySite is an advertising and marketing agency in Dubai, founded and led by Abhishek, Founder and Growth Strategy Lead. We help UAE businesses get seen and get enquiries through outdoor and transit media, digital campaigns, SEO and PR, all managed by one team.</span></p>
					<button type="button" class="hv2-more-btn" aria-expanded="false"><span>Read more</span><i class="bi bi-plus-lg" aria-hidden="true"></i></button>
				</div>
				<div class="hv2-contact-cards" data-reveal="up" data-delay="2">
					<div class="hv2-contact"><i class="bi bi-geo-alt"></i><span>Office 205, Al Bayan Building, Dubai Investment Park, Dubai</span></div>
					<a class="hv2-contact" href="https://api.whatsapp.com/send/?phone=971525287259" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i><span>Call or WhatsApp +971 52 528 7259</span></a>
					<a class="hv2-contact" href="mailto:contact@datamysite.com"><i class="bi bi-envelope"></i><span>contact@datamysite.com</span></a>
				</div>
			</div>
			<div class="hv2-team hv2-glass hv2-glass--light" data-reveal="up" data-delay="2">
				<h3 class="hv2-team-title">The team leads <span class="hv2-team-count">{{ count($hv2Team) }}</span></h3>

				{{-- First entry is featured; everyone else flows into a compact grid. Names only — designations are intentionally not shown. --}}
				@foreach($hv2Team as $lead)
					@if($loop->first)
						<div class="hv2-team-card hv2-team-card--founder">
							<span class="hv2-team-avatar"><span>{{ mb_substr($lead[0], 0, 1) }}</span></span>
							<div><strong>{{ $lead[0] }}</strong></div>
						</div>
						<div class="hv2-team-leads">
					@else
						<div class="hv2-team-card">
							<span class="hv2-team-avatar"><span>{{ mb_substr($lead[0], 0, 1) }}</span></span>
							<div><strong>{{ $lead[0] }}</strong></div>
						</div>
					@endif
					@if($loop->last)
						</div>
					@endif
				@endforeach
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     12 · BLOGS
	==================================================================== --}}
	<section class="hv2-section hv2-light hv2-blogs" id="hv2-blogs">
		<div class="hv2-container">
			<div class="hv2-head hv2-head--split">
				<div>
					<span class="hv2-eyebrow" data-reveal="up">Blogs</span>
					<h2 class="hv2-display hv2-h2" data-split>What's going on <span class="hv2-outline">in the industry?</span></h2>
				</div>
				<a href="{{ URL::to('/blogs/pr-and-media-relations') }}" class="hv2-btn hv2-btn--dark" target="_blank" data-magnetic data-reveal="up" data-delay="2"><span>See All Blogs <i class="bi bi-arrow-right"></i></span></a>
			</div>
			<div class="hv2-blog-grid">
				@foreach($blogs as $val)
					<a href="{{ v2_blog_url($val->slug) }}" class="hv2-blog" data-reveal="up" data-delay="{{ $loop->index + 1 }}" data-distort>
						<div class="hv2-media hv2-media--blog">
							<img {!! v2_img('public/storage/blogs/' . $val->banner, '(max-width: 640px) 92vw, 31vw') !!} alt="{{ $val->banner_alt ?: $val->heading }}" loading="lazy">
						</div>
						<time>{{ date('F d, Y', strtotime($val->created_at)) }}</time>
						<h3 title="{{ $val->heading }}">{{ $val->heading }}</h3>
						<p>{{ $val->short_description }}</p>
						<small class="hv2-blog-meta">by <b>{{ optional($val->author)->name }}</b> · Read article <i class="bi bi-arrow-right"></i></small>
					</a>
				@endforeach
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     13 · FAQ — topic rail + search + accordion with "load more"
	==================================================================== --}}
	@php
		$hv2FaqIcons = ['all' => 'bi-collection', 'general' => 'bi-grid-1x2', 'outdoor' => 'bi-signpost-split', 'digital' => 'bi-display', 'transit' => 'bi-bus-front', 'influencer' => 'bi-stars', 'pr' => 'bi-megaphone', 'seo' => 'bi-search'];
	@endphp
	<section class="hv2-section hv2-tint hv2-faq" id="hv2-faq">
		<div class="hv2-container">
			<div class="hv2-head hv2-head--split">
				<div>
					<span class="hv2-eyebrow" data-reveal="up">Got Questions?</span>
					<h2 class="hv2-display hv2-h2" data-split>Frequently <span class="hv2-outline">asked</span> questions</h2>
				</div>
				<div class="hv2-head-side" data-reveal="up" data-delay="2">
					<p class="hv2-lead">Everything you need to know about advertising and marketing in Dubai and the UAE.</p>
					<span class="hv2-faq-total"><b>{{ count($hv2Faqs) }}</b> answers · <b>{{ count($hv2FaqCats) }}</b> topics</span>
				</div>
			</div>

			<div class="hv2-faq-layout">
				<aside class="hv2-faq-side">
					<div class="hv2-faq-tabs hv2-glass hv2-glass--light" role="tablist" aria-orientation="vertical" aria-label="FAQ topics" data-reveal="up">
						<button type="button" role="tab" class="hv2-faq-cat" data-cat="all" aria-selected="false" aria-controls="hv2-faq-list" tabindex="-1">
							<i class="bi {{ $hv2FaqIcons['all'] }}" aria-hidden="true"></i><span class="hv2-faq-cat-l">All questions</span><span class="hv2-faq-cat-n">{{ count($hv2Faqs) }}</span>
						</button>
						@foreach($hv2FaqCats as $key => $label)
							<button type="button" role="tab" class="hv2-faq-cat {{ $key === 'general' ? 'is-active' : '' }}" data-cat="{{ $key }}" aria-selected="{{ $key === 'general' ? 'true' : 'false' }}" aria-controls="hv2-faq-list" tabindex="{{ $key === 'general' ? '0' : '-1' }}">
								<i class="bi {{ $hv2FaqIcons[$key] ?? 'bi-question-circle' }}" aria-hidden="true"></i><span class="hv2-faq-cat-l">{{ $label }}</span><span class="hv2-faq-cat-n">{{ $hv2FaqCounts[$key] ?? 0 }}</span>
							</button>
						@endforeach
					</div>
					<div class="hv2-faq-cta hv2-glass hv2-glass--light" data-reveal="up" data-delay="2">
						<p><strong>Still have questions? Let's talk.</strong> Book a free strategy call with our UAE advertising experts — no commitment, no pressure.</p>
						<a href="javascript:void(0)" class="hv2-btn hv2-btn--3d open-newsletter" data-magnetic><span>Book Free Strategy Call <i class="bi bi-arrow-right"></i></span></a>
					</div>
				</aside>

				<div class="hv2-faq-main" data-reveal="up" data-delay="1">
					<div class="hv2-faq-search">
						<i class="bi bi-search" aria-hidden="true"></i>
						<input type="text" id="hv2-faq-search" placeholder="Search any question…" autocomplete="off" aria-label="Search FAQs">
						<button type="button" id="hv2-faq-clear" aria-label="Clear search">×</button>
					</div>
					<div class="hv2-faq-meta">
						<span id="hv2-faq-count" aria-live="polite"></span>
						<span class="hv2-faq-bar" aria-hidden="true"><i></i></span>
					</div>
					<div class="hv2-faq-empty" id="hv2-faq-empty">
						<i class="bi bi-search"></i>
						<h4>No results found</h4>
						<p>Try a different keyword or pick a topic.</p>
					</div>
					<div class="hv2-faq-list" id="hv2-faq-list" role="tabpanel">
						@foreach($hv2Faqs as $k => $faq)
							<div class="hv2-faq-item" data-cat="{{ $faq[0] }}" data-q="{{ $faq[1] }}" @if($faq[0] !== 'general') hidden @endif>
								<h3 class="hv2-faq-h">
									<button type="button" class="hv2-faq-q" id="hv2-faq-q{{ $k }}" aria-expanded="false" aria-controls="hv2-faq-a{{ $k }}">
										<span class="hv2-faq-num">{{ str_pad($k + 1, 2, '0', STR_PAD_LEFT) }}</span>
										<span class="hv2-faq-text">{{ $faq[2] }}</span>
										<span class="hv2-faq-tog" aria-hidden="true"></span>
									</button>
								</h3>
								<div class="hv2-faq-a" id="hv2-faq-a{{ $k }}" role="region" aria-labelledby="hv2-faq-q{{ $k }}">
									<div><p>{!! $faq[3] !!}</p></div>
								</div>
							</div>
						@endforeach
					</div>
					<div class="hv2-faq-more">
						<button type="button" class="hv2-btn hv2-btn--outline" id="hv2-faq-more" hidden>
							<span><b id="hv2-faq-more-label">Load more questions</b> <i class="bi bi-plus-lg" aria-hidden="true"></i></span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- ===================================================================
	     14 · FINAL CTA — copy on the left, big animated 3D globe on the right
	==================================================================== --}}
	<section class="hv2-cta hv2-green" id="hv2-cta">
		<div class="hv2-cta-bg" aria-hidden="true"><img src="{{ $hv2Img }}/background/call-to.webp" alt="" loading="lazy"></div>
		<div class="hv2-container hv2-cta-grid">
			<div class="hv2-cta-copy">
				<span class="hv2-eyebrow hv2-eyebrow--white" data-reveal="up">Schedule Your Appointment Today</span>
				<h2 class="hv2-display hv2-cta-title" data-split>Ready to get <span class="hv2-outline">results?</span></h2>
				<p data-reveal="up" data-delay="2">Don't miss out on this opportunity to elevate your advertising game. <strong>Smarter Marketing, Better Outcomes.</strong> Boost your brand's visibility, enhance campaign performance.</p>
				<div class="hv2-cta-btns" data-reveal="up" data-delay="3">
					<a class="hv2-btn hv2-btn--primary hv2-btn--pulse" href="{{ v2_url(route('contact')) }}" data-magnetic><span>Get Started - Free Consultation <i class="bi bi-arrow-right"></i></span></a>
					<a class="hv2-btn hv2-btn--outline" href="https://api.whatsapp.com/send/?phone=971525287259" target="_blank" rel="noopener" data-magnetic><span><i class="bi bi-whatsapp"></i> WhatsApp +971 52 528 7259</span></a>
				</div>
			</div>

			{{-- 3D globe: rotating wireframe sphere + glow, pulsing pins and two orbiting satellites (pure CSS) --}}
			<div class="hv2-globe" aria-hidden="true" data-reveal="scale" data-delay="2">
				<div class="hv2-globe-glow"></div>
				<div class="hv2-globe-stage">
					<div class="hv2-globe-core"></div>
					<div class="hv2-globe-tilt">
						<div class="hv2-globe-spin">
							@for($m = 0; $m < 6; $m++)<i class="hv2-g-m" style="--k: {{ $m }}"></i>@endfor
							@foreach([-.4, -.25, 0, .25, .4] as $gy)<i class="hv2-g-l {{ $gy == 0 ? 'is-eq' : '' }}" style="--y: {{ $gy }}; --s: {{ round(sqrt(1 - pow(2 * $gy, 2)), 3) }}"></i>@endforeach
							<b class="hv2-g-pin" style="--lon: 0deg; --lat: 25deg"></b>
							<b class="hv2-g-pin" style="--lon: 118deg; --lat: -8deg"></b>
							<b class="hv2-g-pin" style="--lon: -104deg; --lat: 38deg"></b>
							<b class="hv2-g-pin" style="--lon: 205deg; --lat: 12deg"></b>
						</div>
						<div class="hv2-g-orbit hv2-g-orbit--1"><i></i></div>
						<div class="hv2-g-orbit hv2-g-orbit--2"><i></i></div>
					</div>
				</div>
			</div>
		</div>
	</section>

</main>
@endsection

@section('addScript')
	<script src="{{ URL::to('/public/assets/vendor/lenis/lenis-1.1.13.min.js') }}" defer></script>
	<script src="{{ v2_asset('assets/js/home-v2.js') }}" defer></script>
@endsection
