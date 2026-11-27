<?php

namespace App\Http\Controllers\web;


use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Services;
use App\Models\Blogs;

class WebController extends Controller
{
    public function index(){
        $data['nav'] = 'home';
        $data['sub_footer'] = 'visible';
        $data['blogs'] = Blogs::where('status', '1')->orderBy('created_at', 'desc')->limit(3)->get();
        
        return view('web.index-beta')->with($data);
    }
    
    public function indexNew(){
        $data['nav'] = 'home';
        $data['sub_footer'] = 'visible';
        $data['blogs'] = Blogs::with('author')->where('status', '1')->orderBy('created_at', 'desc')->limit(3)->get();

        // Live Google reviews (cached, shared with every page's footer). On failure the view falls back to its built-in reviews.
        $data['googleReviews'] = \App\Services\GooglePlaces::cached();

        $placeId = env('GOOGLE_PLACE_ID');
        $data['googleReviewsUrl'] = 'https://www.google.com/maps/place/?q=place_id:' . $placeId;
        $data['googleWriteUrl']   = 'https://search.google.com/local/writereview?placeid=' . $placeId;

        return view('web.home-v2')->with($data);
    }

    // FAQ / Privacy / Terms V2 previews (same data as faq(), privacyPolicy(), termsCondition())
    public function faqNew(){
        $data['nav'] = 'faq';
        $data['sub_footer'] = 'visible';

        return view('web.faq-v2')->with($data);
    }

    public function privacyNew(){
        $data['sub_footer'] = 'visible';

        return view('web.privacy-v2')->with($data);
    }

    public function termsNew(){
        $data['sub_footer'] = 'visible';

        return view('web.terms-v2')->with($data);
    }

    // Contact V2 preview (same data as contact(), redesigned template)
    public function contactNew(){
        $data['nav'] = 'contact';
        $data['sub_footer'] = 'visible';

        return view('web.contact-v2')->with($data);
    }

    // About V2 preview (same data as about(), redesigned template)
    public function aboutNew(){
        $data['nav'] = 'about';
        $data['sub_footer'] = 'visible';

        return view('web.about-v2')->with($data);
    }

    // Case studies landing V2 preview (same data as caseStudies(), redesigned template)
    public function caseStudiesNew(){
        $data['nav'] = 'case-studies';
        $data['sub_footer'] = 'visible';

        return view('web.case-studies-v2')->with($data);
    }

    // Services landing V2 preview (same data as services(), redesigned template)
    public function servicesNew(){
        $data['nav'] = 'services';
        $data['sub_footer'] = 'visible';
        $data['services'] = Services::where('parent_id', '0')->get();

        return view('web.services-landing-v2')->with($data);
    }


    public function services(){
        $data['nav'] = 'services';
        $data['titleImg'] = 'services.jpg';
        $data['title'] = 'Services';
        $data['services'] = Services::where('parent_id', '0')->get();
        $data['sub_footer'] = 'visible';
        

        return view('web.services')->with($data);
    }


    public function faq(){
        $data['nav'] = 'faq';
        $data['titleImg'] = 'faqs.png';
        $data['title'] = 'FAQ';
        
        return view('web.faq')->with($data);
    }


    public function about(){
        $data['nav'] = 'about';
        $data['titleImg'] = 'about.png';
        $data['title'] = 'About Us';
        $data['sub_footer'] = 'visible';
        
        return view('web.about')->with($data);
    }


    public function contact(){
        $data['nav'] = 'contact';
        $data['titleImg'] = 'contact.jpg';
        $data['title'] = 'Contact Us';
        $data['sub_footer'] = 'visible';
        
        return view('web.contact')->with($data);
    }


    public function privacyPolicy(){
        $data['titleImg'] = 'faqs.png';
        $data['title'] = 'Privacy Policy';
        
        return view('web.privacy-policy')->with($data);
    }


    public function termsCondition(){
        $data['titleImg'] = 'faqs.png';
        $data['title'] = 'Terms & Conditions';
        
        return view('web.terms')->with($data);
    }


    public function caseStudies(){
        $data['nav'] = 'case-studies';
        $data['titleImg'] = 'faqs.png';
        $data['title'] = 'Client Case Studies';
        
        return view('web.case-studies')->with($data);
    }


    //Get Aside
    public function getAside(){

        $data['services'] = '';
        
        return view('web.includes.elements.aside')->with($data);
    }
}
