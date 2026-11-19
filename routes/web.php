<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\adminAuth;
use App\Models\Blogs;
use App\Helpers\Mailer;
use App\Http\Controllers\ChatWidgetController;
use App\Http\Controllers\ChatDashboardController;


require __DIR__.'/redirects.php';

//ChatBot

Route::post('/api/chat', [ChatWidgetController::class, 'chat']);
Route::post('/api/lead', [ChatWidgetController::class, 'lead']);
Route::post('/api/track', [ChatWidgetController::class, 'track']);



//sitemap
Route::get('/update-sitemap', function () {
    Artisan::call('app:generate-sitemap');
    $response['success'] = 'success';
    $response['message'] = 'Success! Sitemap Successfully Updated.';
    
    return response()->json($response, 200);
});
//Clear Cache

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');

    return response()->json([
        'status' => 'success',
        'message' => 'All caches cleared successfully!'
    ]);
});

Route::get('/route-list', function () {
    Artisan::call('route:list', ['--path' => 'redirects']);

    return response()->json([
        'status' => 'success',
        'routes' => Artisan::output(),
    ]);
});

Route::get('/broker-growth-mailer', function(){

    $email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
    $data = array('name', $_GET['name']);
    $mail = Mailer::sendMail('Thank you for confirming your attendance for the Business Networking Session | DMS x Forex.com', array($email), 'DMS', 'web.emails.broker-mailer', $data);

    return redirect('https://broker-growth.datamysite.com/');
});

//Web 

Route::namespace('App\Http\Controllers\web')->group(function(){
    Route::get('/', 'WebController@index')->name('home');
    Route::get('/home-new', 'WebController@indexNew')->name('home.new');

    // Service pages V2 preview: /v2/<service-url> runs the SAME service controller action, but
    // renders it with the new template (resources/views/web/services-v2/page.blade.php).
    // Live service URLs are untouched.
    Route::get('/v2/{path}', function ($path) {
        $sub = \Illuminate\Http\Request::create('/' . trim($path, '/'), 'GET');
        try { $route = app('router')->getRoutes()->match($sub); } catch (\Throwable $e) { abort(404); }
        abort_unless(str_contains($route->getActionName(), 'ServiceController@'), 404);
        \Illuminate\Support\Facades\View::getFinder()->prependLocation(resource_path('views/_v2-overrides'));
        return $route->bind($sub)->run();
    })->where('path', '.*')->name('services.v2');
    // Route::get('/beta', 'WebController@index_beta');
    Route::get('/services', 'WebController@services')->name('services');
    // Services landing V2 preview (same data as /services, new template)
    Route::get('/services-new', 'WebController@servicesNew')->name('services.new');
    // FAQ / Privacy / Terms V2 previews. Live pages: /faq, /privacy-policy, /terms-conditions.
    Route::get('/faq-new', 'WebController@faqNew')->name('faq.new');
    Route::get('/privacy-policy-new', 'WebController@privacyNew')->name('privacy.new');
    Route::get('/terms-conditions-new', 'WebController@termsNew')->name('terms.new');
    // Contact V2 preview. Live page: /contact-us (route name 'contact').
    Route::get('/contact-us-new', 'WebController@contactNew')->name('contact.new');
    // About V2 preview. Live page: /about-us (route name 'about').
    Route::get('/about-us-new', 'WebController@aboutNew')->name('about.new');
    // Case studies landing V2 preview. Live page: /client-case-studies (route name 'caseStudies').
    Route::get('/client-case-studies-new', 'WebController@caseStudiesNew')->name('caseStudies.new');
    // Blogs V2 previews: landing (/blogs-new) + post page (/blogs-new/post/<slug>). Live: /blogs and /<blog-slug>.
    Route::get('/blogs-new', 'BlogController@indexNew')->name('blogs.new');
    Route::get('/blogs-new/post/{blog_slug}', 'BlogController@detailsNew')->name('blogs.new.detail');
    Route::get('/blogs-new/category/{slug}', 'BlogController@categoryNew')->name('blogs.new.category');
    Route::get('/blogs-new/tag/{slug}', 'BlogController@tagsNew')->name('blogs.new.tag');
    Route::get('/blogs/categories-new', 'BlogController@allCategoriesNew')->name('blogs.categories.new');
    Route::get('/faq', 'WebController@faq')->name('faq');
    Route::get('/about-us', 'WebController@about')->name('about');
    Route::get('/contact-us', 'WebController@contact')->name('contact');
    Route::get('/privacy-policy', 'WebController@privacyPolicy')->name('privacy');
    Route::get('/terms-conditions', 'WebController@termsCondition')->name('terms');
    Route::get('/client-case-studies', 'WebController@caseStudies')->name('caseStudies');


    Route::get('/get-google-reviews', 'ReviewController@index');

    //Services
        Route::prefix('outdoor-advertising')->group(function(){
            Route::get('/', 'ServiceController@outdoorAdvertising');
            Route::prefix('billboards-advertising')->group(function(){
                
                Route::get('/', 'ServiceController@outdoorAdvertisingAillboardsAdvertising');
                Route::get('/building-wraps', 'ServiceController@outdoorAdvertisingAillboardsAdvertisingBuildingWraps');
                Route::get('/hoarding', 'ServiceController@outdoorAdvertisingAillboardsAdvertisingHoarding');
                Route::get('/wallscapes', 'ServiceController@outdoorAdvertisingAillboardsAdvertisingWallscapes');
                Route::get('/bridge-panels', 'ServiceController@outdoorAdvertisingAillboardsAdvertisingBridgePanels');
                

            });
            Route::get('/flyers-distribution', 'ServiceController@outdoorAdvertisingFlyersDistribution');
            Route::get('/human-billboards', 'ServiceController@outdoorAdvertisingHumanBillboards');
            Route::get('/unipoles', 'ServiceController@outdoorAdvertisingUnipoles');
            Route::get('/lamp-post', 'ServiceController@outdoorAdvertisingLampPost');
            Route::get('/bridge-banners', 'ServiceController@outdoorAdvertisingBridgeBanners');
        });


        Route::prefix('/digital-marketing')->group(function(){

            Route::get('/', 'ServiceController@digitalMarketing');
            Route::get('/social-media-marketing', 'ServiceController@digitalMarketingSocialMedia');
            Route::get('/content-marketing', 'ServiceController@digitalMarketingContentMarketing');
        });

        Route::prefix('digital-advertising')->group(function(){
            Route::get('/', 'ServiceController@digitalAdvertising');
            Route::prefix('ott-advertising')->group(function(){
                
                Route::get('/', 'ServiceController@digitalAdvertisingOTTAdvertising');
                Route::get('/netflix-advertising', 'ServiceController@digitalAdvertisingOTTAdvertisingNetflix');
                Route::get('/starzplay-advertising', 'ServiceController@digitalAdvertisingOTTAdvertisingStarzPlay');
                Route::get('/abudhabitv-advertising', 'ServiceController@digitalAdvertisingOTTAdvertisingAbuDhabiTV');

            });
            Route::prefix('/dooh-advertising')->group(function(){

                Route::get('/', 'ServiceController@digitalAdvertisingDOOHAdvertising');
                Route::get('/digital-unipole', 'ServiceController@digitalAdvertisingDOOHAdvertisingUnipole');
                Route::get('/petrol-station-digital-screen', 'ServiceController@digitalAdvertisingDOOHAdvertisingPetrolStation');
            });
            Route::prefix('hotel-digital-screens-advertising')->group(function(){

                Route::get('/', 'ServiceController@digitalAdvertisingHotelAdvertising');
                Route::get('/dubai-hotels-advertising', 'ServiceController@digitalAdvertisingHotelAdvertisingDubai');
                Route::get('/abu-dhabi-hotels-advertising', 'ServiceController@digitalAdvertisingHotelAdvertisingAbuDhabi');
                Route::get('/rak-hotels-advertising', 'ServiceController@digitalAdvertisingHotelAdvertisingRAK');
            });

            Route::prefix('elevator-advertising')->group(function(){

                Route::get('/', 'ServiceController@digitalAdvertisingElevetorAdvertising');
                Route::get('/residential-lift', 'ServiceController@digitalAdvertisingElevetorAdvertisingResidential');
                Route::get('/commercial-lift', 'ServiceController@digitalAdvertisingElevetorAdvertisingCommercial');
            });
        });

        Route::prefix('public-relations-pr-coverage')->group(function(){
            Route::get('/', 'ServiceController@prCoverage');
            Route::get('/gulf-news', 'ServiceController@prCoverageGulfNews');
            Route::get('/forbes', 'ServiceController@prCoverageForbes');
            Route::get('/khaleej-time', 'ServiceController@prCoverageKhaleejTime');
            Route::get('/arabian-news', 'ServiceController@prCoverageArabianNews');
        });

        Route::prefix('celebrity-marketing')->group(function(){
            Route::get('/', 'ServiceController@celebrityMarketing');
            Route::get('/sport-personalities', 'ServiceController@celebrityMarketingSport');
            Route::get('/bollywood-celebrities', 'ServiceController@celebrityMarketingBollywood');
        });

        Route::prefix('influencer-marketing')->group(function(){
            Route::get('/', 'ServiceController@influencerMarketing');
            Route::get('/nano-influencer', 'ServiceController@influencerMarketingNano');
            Route::get('/micro-influencer', 'ServiceController@influencerMarketingMicro');
            Route::get('/macro-influencer', 'ServiceController@influencerMarketingMacro');
        });

        Route::prefix('event-marketing')->group(function(){
            Route::get('/', 'ServiceController@eventMarketing');
            Route::get('/corporate-events', 'ServiceController@eventMarketingCorporate');
            Route::get('/private-events', 'ServiceController@eventMarketingPrivate');
            Route::get('/entertainment-events', 'ServiceController@eventMarketingEntertainment');
        });

        Route::prefix('transit-media')->group(function(){
            Route::get('/', 'ServiceController@transitMedia');
            Route::get('/boat-advertising', 'ServiceController@transitMediaBoat');
            Route::get('/tram-advertising', 'ServiceController@transitMediaTram');
            Route::get('/truck-advertising', 'ServiceController@transitMediaTruck');
            Route::prefix('airline-advertising')->group(function(){
                
                Route::get('/', 'ServiceController@transitMediaAirline');
                Route::get('/emirates-airline', 'ServiceController@transitMediaAirlineEmirates');
                Route::get('/etihad-airline', 'ServiceController@transitMediaAirlineEtihad');
                Route::get('/fly-dubai-airline', 'ServiceController@transitMediaAirlineFlyDubai');
                Route::get('/spicejet-airline', 'ServiceController@transitMediaAirlineSpicejet');
                Route::get('/air-arabia-airline', 'ServiceController@transitMediaAirlineAirArabia');

            });
            Route::prefix('bus-advertising')->group(function(){
                
                Route::get('/', 'ServiceController@transitMediaBus');
                Route::get('/bus-wrapping', 'ServiceController@transitMediaBusWrapping');
                Route::get('/bus-stop-wrap', 'ServiceController@transitMediaBusStopWrapping');

            });
            Route::prefix('metro-ads')->group(function(){
                
                Route::get('/', 'ServiceController@transitMediaMetroAds');
                Route::get('/metro-station-takeover', 'ServiceController@transitMediaMetroAdsStation');
                Route::get('/metro-pillars', 'ServiceController@transitMediaMetroAdsPillers');
                Route::get('/dooh-in-metro', 'ServiceController@transitMediaMetroAdsDOOH');
                Route::get('/metro-station-wall', 'ServiceController@transitMediaMetroWall');
                Route::get('/inside-metro-train', 'ServiceController@transitMediaInsideMetroTrain');
            });
            Route::prefix('taxi-advertising')->group(function(){
                
                Route::get('/', 'ServiceController@transitMediaTaxi');
                Route::get('/abu-dhabi-taxi', 'ServiceController@transitMediaTaxiAbuDhabi');
                Route::get('/pink-taxi', 'ServiceController@transitMediaTaxiPink');
                Route::get('/blue-taxi', 'ServiceController@transitMediaTaxiBlue');
                Route::get('/red-airport-taxi', 'ServiceController@transitMediaTaxiRedAirport');
                Route::get('/red-taxi', 'ServiceController@transitMediaTaxiRed');
            });
            Route::prefix('airport-advertising')->group(function(){
                
                Route::get('/', 'ServiceController@transitMediaAirport');
                Route::get('/private-airport', 'ServiceController@transitMediaAirportPrivate');
                Route::get('/dubai-airport', 'ServiceController@transitMediaAirportDubai');
                Route::get('/sharjah-airport', 'ServiceController@transitMediaAirportSharjah');
                Route::get('/indian-airport', 'ServiceController@transitMediaAirportIndian');
            });
        });

        Route::prefix('lead-generation')->group(function(){
            Route::get('/', 'ServiceController@leadGeneration');
            Route::get('/cost-per-lead', 'ServiceController@leadGenerationCPL');
        });

        Route::prefix('sponsorships')->group(function(){
            Route::get('/', 'ServiceController@sponsorships');
            Route::get('/corporate', 'ServiceController@sponsorshipsCorporate');
            Route::get('/entertainment', 'ServiceController@sponsorshipsEntertainment');
        });

        Route::prefix('creative-and-cgi-advertising')->group(function(){
            Route::get('/', 'ServiceController@cgiAdvertising');
            Route::get('/creative', 'ServiceController@cgiAdvertisingCreative');
        });


        Route::prefix('experiential-marketing')->group(function(){

            Route::get('/', 'ServiceController@experientialMarketing');
            Route::get('/top-golf', 'ServiceController@experientialMarketingGolf');
            Route::get('/mall-activations-dubai', 'ServiceController@experientialMarketingMall');
            Route::get('/hot-air-balloon', 'ServiceController@experientialMarketingBalloon');
            Route::get('/tesla-wrap', 'ServiceController@experientialMarketingTesla');
        });


        Route::prefix('radio-advertising')->group(function(){

            Route::get('/', 'ServiceController@radioAdvertising');
            Route::prefix('english-stations')->group(function(){
                Route::get('/', 'ServiceController@radioAdvertisingEnglish');
                Route::get('/tag-91-1', 'ServiceController@radioAdvertisingEnglishtag911');
                Route::get('/hit-96-7', 'ServiceController@radioAdvertisingEnglishhit967');
                Route::get('/city-101-6', 'ServiceController@radioAdvertisingEnglishcity1016');
                Route::get('/dubai-eye-103-8', 'ServiceController@radioAdvertisingEnglishdubaieye1038');
                Route::get('/virgin-radio-dubai-104-4', 'ServiceController@radioAdvertisingEnglishvirginradio');
            });
            Route::prefix('arabic-stations')->group(function(){
                Route::get('/', 'ServiceController@radioAdvertisingArabic');
                Route::get('/dubai-92', 'ServiceController@radioAdvertisingArabicdubai92');
                Route::get('/al-arabiya-99', 'ServiceController@radioAdvertisingArabicalarabiya99');
                Route::get('/al-khaleejiya-100-9', 'ServiceController@radioAdvertisingArabicalkhaleejiya1009');
            });
            Route::prefix('hindi-urdu-stations')->group(function(){

                Route::get('/', 'ServiceController@radioAdvertisingHindiUrdu');
                Route::get('/radio-shoma-93-4', 'ServiceController@radioAdvertisingHindiUrduradioshoma');
            });
            Route::prefix('other-stations')->group(function(){
                
                Route::get('/', 'ServiceController@radioAdvertisingOther');
                Route::get('/mpn', 'ServiceController@radioAdvertisingOthermpn');
            });
        });

        Route::prefix('global-marketing')->group(function(){
            Route::get('/', 'ServiceController@globalMarketing');
            Route::get('/china-marketing', 'ServiceController@globalMarketingChina');
            Route::get('/saudi-marketing', 'ServiceController@globalMarketingSaudi');
            Route::get('/india-marketing', 'ServiceController@globalMarketingIndia');
            Route::get('/london-marketing', 'ServiceController@globalMarketingLondon');
        });

        Route::get('/seo-services-dubai', 'ServiceController@seoServicesDubai');

        Route::prefix('seo-services')->group(function(){
            Route::get('/', 'ServiceController@seoServices');
            Route::get('/local-seo', 'ServiceController@localSEO');/*
            Route::get('/ai-seo-optimization', 'ServiceController@aiOptimization');
            Route::get('/tech-support', 'ServiceController@techSupport');
            Route::get('/free-seo-audit', 'ServiceController@freeAudit');*/
            Route::get('/case-studies', 'ServiceController@caseStudies');
            Route::get('/price-and-package', 'ServiceController@pricePackage');
            
            Route::get('/abu-dhabi', 'ServiceController@abuDhabi');
            Route::get('/seo-audit', 'ServiceController@seoAudit');
            Route::get('/technical-seo', 'ServiceController@technicalSEO');
            Route::get('/ecommerce-seo', 'ServiceController@ecommerceSEO');
            Route::get('/ai-seo-geo', 'ServiceController@aiSEOgeo');
            Route::get('/arabic-seo', 'ServiceController@arabicSEO');
            Route::get('/seo-consultant', 'ServiceController@seoConsultant');
            Route::get('/small-business-seo', 'ServiceController@smallBusinessSEO');
        });


        Route::prefix('website-development')->group(function(){
            Route::get('/', 'ServiceController@websiteDevelopment');
            Route::get('/full-stack-web-development', 'ServiceController@fullStackDevelopment');
            Route::get('/e-commerce-website-development', 'ServiceController@eCommerceDevelopment');
            Route::get('/website-maintenance-services', 'ServiceController@websiteMaintenance');
        });


    //Newsletter
    Route::post('/subscribe', 'NewsletterController@subscribe')->name('newsletter.subscribe');
    Route::get('/unsubscribe', 'NewsletterController@unsubscribe')->name('newsletter.unsubscribe');

    //Enquiry
    Route::post('/enquiry', 'EnquiryController@enquiry')->name('enquiry.submit');
    Route::post('/enquiryHelp', 'EnquiryController@enquiryHelp')->name('enquiry.help.submit');
    Route::post('/enquiryLite', 'EnquiryController@enquiryLite')->name('enquiry.lite.submit');

    //Aside
    Route::get('/get-aside', 'WebController@getAside');

    //Blogs
    Route::get('/blogs', 'BlogController@index')->name('blogs');
    Route::get('/blogs/categories', 'BlogController@allCategories')->name('blogs.categories');
    Route::get('/blogs/{slug}', 'BlogController@category');
    Route::get('/{blog_slug}', 'BlogController@details')->name('blogs.detail');
    Route::get('/tag/{slug}', 'BlogController@tags');
});


//Administration

Route::prefix('admin/panel')->namespace('App\Http\Controllers\admin')->group(function () {

    //Authentication
    Route::get('/login', 'LoginController@index')->name('admin.login');
    Route::post('/login', 'LoginController@authenticate');
    Route::get('/logout', 'LoginController@logout')->name('admin.logout');


    //Authenticated
    Route::middleware([adminAuth::class])->group(function () {
        Route::get('/', 'MainController@index')->name('admin.dashboard');

        
        //Chat-Dashboard
        Route::get('/chat-dashboard',       [ChatDashboardController::class, 'dashboard']);
        Route::get('/chat-dashboard/stats', [ChatDashboardController::class, 'stats']);
        
        Route::get('/chat-dashboard/conversations', [ChatDashboardController::class, 'conversations']);
        Route::get('/chat-dashboard/conversation',  [ChatDashboardController::class, 'conversation']);
        
        //Blogs
        Route::prefix('blogs')->group(function () {

            Route::get('/', 'BlogController@index')->name('admin.blog');
            Route::get('/load', 'BlogController@load')->name('admin.blog.load');
            Route::get('/search/{val}', 'BlogController@search');
            Route::post('/create', 'BlogController@create')->name('admin.blog.create');
            Route::get('/delete/{id}', 'BlogController@delete');
            Route::get('/edit/{id}', 'BlogController@edit');
            Route::post('/update', 'BlogController@update_blog')->name('admin.blog.update');

        });


        Route::prefix('author')->group(function () {

            Route::get('/', 'AuthorController@index')->name('admin.author');
            Route::post('/create', 'AuthorController@create')->name('admin.author.create');
            Route::get('/load', 'AuthorController@load')->name('admin.author.load');
            Route::get('/edit/{id}', 'AuthorController@edit');
            Route::get('/delete/{id}', 'AuthorController@delete');
        });


        //SEO Tools
        Route::prefix('seo')->group(function () {
            Route::prefix('meta')->group(function () {
                Route::get('/', 'SeoController@meta')->name('admin.seo.meta');
                Route::post('/check', 'SeoController@meta_check')->name('admin.seo.meta.check');
                Route::post('/update', 'SeoController@meta_update')->name('admin.seo.meta.update');
            });
            Route::prefix('snippet')->group(function () {
                Route::get('/', 'SeoController@snippet')->name('admin.seo.snippet');
                Route::get('/load', 'SeoController@snippet_load')->name('admin.seo.snippet.load');
                Route::post('/create', 'SeoController@snippet_create')->name('admin.seo.snippet.create');
                Route::get('/delete/{id}', 'SeoController@snippet_delete');
                Route::get('/edit/{id}', 'SeoController@snippet_edit');
                Route::post('/update', 'SeoController@snippet_update')->name('admin.seo.snippet.update');
            });
        });


        //Newsletter
        Route::prefix('newsletter')->group(function () {
            Route::get('/', 'NewsletterController@index')->name('admin.newsletter');
            Route::get('/load', 'NewsletterController@load')->name('admin.newsletter.load');
            Route::post('/filter', 'NewsletterController@user_filter')->name('admin.newsletter.filter');
            Route::post('/export', 'NewsletterController@user_export')->name('admin.newsletter.export');
        });
    });
});