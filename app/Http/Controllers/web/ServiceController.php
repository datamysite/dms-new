<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Services;
use App\Models\Publications;
use App\Models\Blogs;
use App\Models\ServiceSection;

class ServiceController extends Controller
{
    protected function renderService($view, array $data)
    {
        $page = $data['subservice'] ?? $data['service'] ?? null;

        if ($page && ServiceSection::where('service_id', $page->id)->where('is_active', 1)->exists()) {
            return view('web.services-new.db-page')->with($data);
        }

        return view($view)->with($data);
    }
    
    public function outdoorAdvertising(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'outdoor-advertising')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '1')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
        public function outdoorAdvertisingAillboardsAdvertising(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'outdoor-advertising')->first();
            $data['subservice'] = Services::where('slug', 'billboards-advertising')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '21')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function outdoorAdvertisingAillboardsAdvertisingBuildingWraps(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'outdoor-advertising')->first();
                $data['service'] = Services::where('slug', 'billboards-advertising')->first();
                $data['subservice'] = Services::where('slug', 'building-wraps')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '21')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function outdoorAdvertisingAillboardsAdvertisingHoarding(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'outdoor-advertising')->first();
                $data['service'] = Services::where('slug', 'billboards-advertising')->first();
                $data['subservice'] = Services::where('slug', 'hoarding')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '21')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function outdoorAdvertisingAillboardsAdvertisingWallscapes(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'outdoor-advertising')->first();
                $data['service'] = Services::where('slug', 'billboards-advertising')->first();
                $data['subservice'] = Services::where('slug', 'wallscapes')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '21')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function outdoorAdvertisingAillboardsAdvertisingBridgePanels(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'outdoor-advertising')->first();
                $data['service'] = Services::where('slug', 'billboards-advertising')->first();
                $data['subservice'] = Services::where('slug', 'bridge-panels')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '21')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }

        public function outdoorAdvertisingFlyersDistribution(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'outdoor-advertising')->first();
            $data['subservice'] = Services::where('slug', 'flyers-distribution')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '22')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function outdoorAdvertisingHumanBillboards(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'outdoor-advertising')->first();
            $data['subservice'] = Services::where('slug', 'human-billboards')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '1')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function outdoorAdvertisingUnipoles(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'outdoor-advertising')->first();
            $data['subservice'] = Services::where('slug', 'unipoles')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '1')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function outdoorAdvertisingLampPost(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'outdoor-advertising')->first();
            $data['subservice'] = Services::where('slug', 'lamp-post')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '1')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function outdoorAdvertisingBridgeBanners(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'outdoor-advertising')->first();
            $data['subservice'] = Services::where('slug', 'bridge-banners')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '1')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }


//-------------------------------------------------------------------------------------------------------
    public function digitalAdvertising(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'digital-advertising')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
        public function digitalAdvertisingOTTAdvertising(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'digital-advertising')->first();
            $data['subservice'] = Services::where('slug', 'ott-advertising')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function digitalAdvertisingOTTAdvertisingNetflix(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'digital-advertising')->first();
                $data['service'] = Services::where('slug', 'ott-advertising')->first();
                $data['subservice'] = Services::where('slug', 'netflix-advertising')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function digitalAdvertisingOTTAdvertisingStarzPlay(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'digital-advertising')->first();
                $data['service'] = Services::where('slug', 'ott-advertising')->first();
                $data['subservice'] = Services::where('slug', 'starzplay-advertising')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function digitalAdvertisingOTTAdvertisingAbuDhabiTV(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'digital-advertising')->first();
                $data['service'] = Services::where('slug', 'ott-advertising')->first();
                $data['subservice'] = Services::where('slug', 'abudhabitv-advertising')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }


        public function digitalAdvertisingDOOHAdvertising(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'digital-advertising')->first();
            $data['subservice'] = Services::where('slug', 'dooh-advertising')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function digitalAdvertisingDOOHAdvertisingUnipole(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'digital-advertising')->first();
                $data['service'] = Services::where('slug', 'dooh-advertising')->first();
                $data['subservice'] = Services::where('slug', 'digital-unipole')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function digitalAdvertisingDOOHAdvertisingPetrolStation(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'digital-advertising')->first();
                $data['service'] = Services::where('slug', 'dooh-advertising')->first();
                $data['subservice'] = Services::where('slug', 'petrol-station-digital-screen')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }

            
        public function digitalAdvertisingHotelAdvertising(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'digital-advertising')->first();
            $data['subservice'] = Services::where('slug', 'hotel-digital-screens-advertising')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function digitalAdvertisingHotelAdvertisingDubai(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'digital-advertising')->first();
                $data['service'] = Services::where('slug', 'hotel-digital-screens-advertising')->first();
                $data['subservice'] = Services::where('slug', 'dubai-hotels-advertising')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function digitalAdvertisingHotelAdvertisingAbuDhabi(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'digital-advertising')->first();
                $data['service'] = Services::where('slug', 'hotel-digital-screens-advertising')->first();
                $data['subservice'] = Services::where('slug', 'abu-dhabi-hotels-advertising')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function digitalAdvertisingHotelAdvertisingRAK(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'digital-advertising')->first();
                $data['service'] = Services::where('slug', 'hotel-digital-screens-advertising')->first();
                $data['subservice'] = Services::where('slug', 'rak-hotels-advertising')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }


        public function digitalAdvertisingElevetorAdvertising(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'digital-advertising')->first();
            $data['subservice'] = Services::where('slug', 'elevator-advertising')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function digitalAdvertisingElevetorAdvertisingResidential(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'digital-advertising')->first();
                $data['service'] = Services::where('slug', 'elevator-advertising')->first();
                $data['subservice'] = Services::where('slug', 'residential-lift')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function digitalAdvertisingElevetorAdvertisingCommercial(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'digital-advertising')->first();
                $data['service'] = Services::where('slug', 'elevator-advertising')->first();
                $data['subservice'] = Services::where('slug', 'commercial-lift')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }



//-------------------------------------------------------------------------------------------------------
    public function digitalMarketing(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'digital-marketing')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['publications'] = Publications::all();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
        public function digitalMarketingSocialMedia(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'digital-marketing')->first();
            $data['subservice'] = Services::where('slug', 'social-media-marketing')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function digitalMarketingContentMarketing(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'digital-marketing')->first();
            $data['subservice'] = Services::where('slug', 'content-marketing')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '2')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }


//-------------------------------------------------------------------------------------------------------
    public function prCoverage(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'public-relations-pr-coverage')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['publications'] = Publications::all();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '6')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
    public function prCoverageNew(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'public-relations-pr-coverage')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['publications'] = Publications::all();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '6')->orderBy('created_at', 'desc')->limit(3)->get();
        $data['sub_footer'] = 'visible';
        
        return view('web.services-new.master-service')->with($data);
    }
        public function prCoverageGulfNews(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'public-relations-pr-coverage')->first();
            $data['subservice'] = Services::where('slug', 'gulf-news')->first();
            $data['publications'] = Publications::all();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '6')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function prCoverageForbes(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'public-relations-pr-coverage')->first();
            $data['subservice'] = Services::where('slug', 'forbes')->first();
            $data['publications'] = Publications::all();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '6')->orderBy('created_at', 'desc')->limit(3)->get();


            return view('web.services-new.master-service')->with($data);
        }
        public function prCoverageKhaleejTime(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'public-relations-pr-coverage')->first();
            $data['subservice'] = Services::where('slug', 'khaleej-time')->first();
            $data['publications'] = Publications::all();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '6')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function prCoverageArabianNews(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'public-relations-pr-coverage')->first();
            $data['subservice'] = Services::where('slug', 'arabian-news')->first();
            $data['publications'] = Publications::all();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '6')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }


//-------------------------------------------------------------------------------------------------------

    public function celebrityMarketing(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'celebrity-marketing')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '7')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
        public function celebrityMarketingSport(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'celebrity-marketing')->first();
            $data['subservice'] = Services::where('slug', 'sport-personalities')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '7')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function celebrityMarketingBollywood(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'celebrity-marketing')->first();
            $data['subservice'] = Services::where('slug', 'bollywood-celebrities')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '7')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }


//-------------------------------------------------------------------------------------------------------

    public function influencerMarketing(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'influencer-marketing')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '8')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
        public function influencerMarketingNano(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'influencer-marketing')->first();
            $data['subservice'] = Services::where('slug', 'nano-influencer')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '8')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function influencerMarketingMicro(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'influencer-marketing')->first();
            $data['subservice'] = Services::where('slug', 'micro-influencer')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '8')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function influencerMarketingMacro(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'influencer-marketing')->first();
            $data['subservice'] = Services::where('slug', 'macro-influencer')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '8')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }



//-------------------------------------------------------------------------------------------------------
    public function eventMarketing(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'event-marketing')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '9')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
        public function eventMarketingCorporate(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'event-marketing')->first();
            $data['subservice'] = Services::where('slug', 'corporate-events')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '9')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function eventMarketingPrivate(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'event-marketing')->first();
            $data['subservice'] = Services::where('slug', 'private-events')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '9')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function eventMarketingEntertainment(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'event-marketing')->first();
            $data['subservice'] = Services::where('slug', 'entertainment-events')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '9')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }



//-------------------------------------------------------------------------------------------------------

    public function globalMarketing(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'global-marketing')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('status', '1')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
        public function globalMarketingChina(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'global-marketing')->first();
            $data['subservice'] = Services::where('slug', 'china-marketing')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '9')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function globalMarketingSaudi(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'global-marketing')->first();
            $data['subservice'] = Services::where('slug', 'saudi-marketing')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '9')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function globalMarketingIndia(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'global-marketing')->first();
            $data['subservice'] = Services::where('slug', 'india-marketing')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '9')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function globalMarketingLondon(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'global-marketing')->first();
            $data['subservice'] = Services::where('slug', 'london-marketing')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '9')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }

//-------------------------------------------------------------------------------------------------------

    public function transitMedia(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'transit-media')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }

        public function transitMediaBoat(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'transit-media')->first();
            $data['subservice'] = Services::where('slug', 'boat-advertising')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }

        public function transitMediaTram(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'transit-media')->first();
            $data['subservice'] = Services::where('slug', 'tram-advertising')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }

        public function transitMediaTruck(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'transit-media')->first();
            $data['subservice'] = Services::where('slug', 'truck-advertising')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }

        public function transitMediaAirline(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'transit-media')->first();
            $data['subservice'] = Services::where('slug', 'airline-advertising')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function transitMediaAirlineEmirates(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'airline-advertising')->first();
                $data['subservice'] = Services::where('slug', 'emirates-airline')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaAirlineEtihad(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'airline-advertising')->first();
                $data['subservice'] = Services::where('slug', 'etihad-airline')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaAirlineFlyDubai(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'airline-advertising')->first();
                $data['subservice'] = Services::where('slug', 'fly-dubai-airline')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaAirlineSpicejet(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'airline-advertising')->first();
                $data['subservice'] = Services::where('slug', 'spicejet-airline')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaAirlineAirArabia(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'airline-advertising')->first();
                $data['subservice'] = Services::where('slug', 'air-arabia-airline')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }


        public function transitMediaAirport(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'transit-media')->first();
            $data['subservice'] = Services::where('slug', 'airport-advertising')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function transitMediaAirportPrivate(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'airport-advertising')->first();
                $data['subservice'] = Services::where('slug', 'private-airport')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaAirportDubai(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'airport-advertising')->first();
                $data['subservice'] = Services::where('slug', 'dubai-airport')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaAirportSharjah(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'airport-advertising')->first();
                $data['subservice'] = Services::where('slug', 'sharjah-airport')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaAirportIndian(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'airport-advertising')->first();
                $data['subservice'] = Services::where('slug', 'indian-airport')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }

        public function transitMediaTaxi(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'transit-media')->first();
            $data['subservice'] = Services::where('slug', 'taxi-advertising')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function transitMediaTaxiAbuDhabi(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'taxi-advertising')->first();
                $data['subservice'] = Services::where('slug', 'abu-dhabi-taxi')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaTaxiPink(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'taxi-advertising')->first();
                $data['subservice'] = Services::where('slug', 'pink-taxi')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaTaxiBlue(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'taxi-advertising')->first();
                $data['subservice'] = Services::where('slug', 'blue-taxi')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaTaxiRedAirport(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'taxi-advertising')->first();
                $data['subservice'] = Services::where('slug', 'red-airport-taxi')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaTaxiRed(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'taxi-advertising')->first();
                $data['subservice'] = Services::where('slug', 'red-taxi')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }

        public function transitMediaBus(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'transit-media')->first();
            $data['subservice'] = Services::where('slug', 'bus-advertising')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function transitMediaBusWrapping(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'bus-advertising')->first();
                $data['subservice'] = Services::where('slug', 'bus-wrapping')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaBusStopWrapping(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'bus-advertising')->first();
                $data['subservice'] = Services::where('slug', 'bus-stop-wrap')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }

        public function transitMediaMetroAds(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'transit-media')->first();
            $data['subservice'] = Services::where('slug', 'metro-ads')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function transitMediaMetroAdsStation(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'metro-ads')->first();
                $data['subservice'] = Services::where('slug', 'metro-station-takeover')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaMetroAdsPillers(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'metro-ads')->first();
                $data['subservice'] = Services::where('slug', 'metro-pillars')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaMetroAdsDOOH(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'metro-ads')->first();
                $data['subservice'] = Services::where('slug', 'dooh-in-metro')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaMetroWall(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'metro-ads')->first();
                $data['subservice'] = Services::where('slug', 'metro-station-wall')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function transitMediaInsideMetroTrain(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'transit-media')->first();
                $data['service'] = Services::where('slug', 'metro-ads')->first();
                $data['subservice'] = Services::where('slug', 'inside-metro-train')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '10')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }


//-------------------------------------------------------------------------------------------------------

    public function leadGeneration(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'lead-generation')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '15')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
        public function leadGenerationCPL(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'lead-generation')->first();
            $data['subservice'] = Services::where('slug', 'cost-per-lead')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '15')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }


//-------------------------------------------------------------------------------------------------------

    public function sponsorships(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'sponsorships')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '17')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
        public function sponsorshipsCorporate(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'sponsorships')->first();
            $data['subservice'] = Services::where('slug', 'corporate')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '17')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function sponsorshipsEntertainment(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'sponsorships')->first();
            $data['subservice'] = Services::where('slug', 'entertainment')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '17')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }


//-------------------------------------------------------------------------------------------------------

    public function cgiAdvertising(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'creative-and-cgi-advertising')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '16')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
        public function cgiAdvertisingCreative(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'creative-and-cgi-advertising')->first();
            $data['subservice'] = Services::where('slug', 'creative')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '16')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }


//-------------------------------------------------------------------------------------------------------

    public function radioAdvertising(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'radio-advertising')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
        public function radioAdvertisingEnglish(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'radio-advertising')->first();
            $data['subservice'] = Services::where('slug', 'english-stations')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function radioAdvertisingEnglishtag911(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'radio-advertising')->first();
                $data['service'] = Services::where('slug', 'english-stations')->first();
                $data['subservice'] = Services::where('slug', 'tag-91-1')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function radioAdvertisingEnglishhit967(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'radio-advertising')->first();
                $data['service'] = Services::where('slug', 'english-stations')->first();
                $data['subservice'] = Services::where('slug', 'hit-96-7')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function radioAdvertisingEnglishcity1016(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'radio-advertising')->first();
                $data['service'] = Services::where('slug', 'english-stations')->first();
                $data['subservice'] = Services::where('slug', 'city-101-6')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function radioAdvertisingEnglishdubaieye1038(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'radio-advertising')->first();
                $data['service'] = Services::where('slug', 'english-stations')->first();
                $data['subservice'] = Services::where('slug', 'dubai-eye-103-8')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function radioAdvertisingEnglishvirginradio(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'radio-advertising')->first();
                $data['service'] = Services::where('slug', 'english-stations')->first();
                $data['subservice'] = Services::where('slug', 'virgin-radio-dubai-104-4')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }

        public function radioAdvertisingArabic(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'radio-advertising')->first();
            $data['subservice'] = Services::where('slug', 'arabic-stations')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function radioAdvertisingArabicdubai92(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'radio-advertising')->first();
                $data['service'] = Services::where('slug', 'arabic-stations')->first();
                $data['subservice'] = Services::where('slug', 'dubai-92')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function radioAdvertisingArabicalarabiya99(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'radio-advertising')->first();
                $data['service'] = Services::where('slug', 'arabic-stations')->first();
                $data['subservice'] = Services::where('slug', 'al-arabiya-99')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            public function radioAdvertisingArabicalkhaleejiya1009(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'radio-advertising')->first();
                $data['service'] = Services::where('slug', 'arabic-stations')->first();
                $data['subservice'] = Services::where('slug', 'al-khaleejiya-100-9')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            
        public function radioAdvertisingHindiUrdu(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'radio-advertising')->first();
            $data['subservice'] = Services::where('slug', 'hindi-urdu-stations')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function radioAdvertisingHindiUrduradioshoma(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'radio-advertising')->first();
                $data['service'] = Services::where('slug', 'hindi-urdu-stations')->first();
                $data['subservice'] = Services::where('slug', 'radio-shoma-93-4')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }
            
        public function radioAdvertisingOther(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'radio-advertising')->first();
            $data['subservice'] = Services::where('slug', 'other-stations')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
            public function radioAdvertisingOthermpn(){
                $data['nav'] = 'services';
                $data['mainservice'] = Services::where('slug', 'radio-advertising')->first();
                $data['service'] = Services::where('slug', 'other-stations')->first();
                $data['subservice'] = Services::where('slug', 'mpn')->first();
                $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

                return view('web.services-new.master-service')->with($data);
            }

//-------------------------------------------------------------------------------------------------------

    public function experientialMarketing(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'experiential-marketing')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('status', '1')->orderBy('created_at', 'desc')->limit(3)->get();

        return view('web.services-new.master-service')->with($data);
    }
        public function experientialMarketingGolf(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'experiential-marketing')->first();
            $data['subservice'] = Services::where('slug', 'top-golf')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function experientialMarketingMall(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'experiential-marketing')->first();
            $data['subservice'] = Services::where('slug', 'mall-activations-dubai')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function experientialMarketingBalloon(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'experiential-marketing')->first();
            $data['subservice'] = Services::where('slug', 'hot-air-balloon')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }
        public function experientialMarketingTesla(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'experiential-marketing')->first();
            $data['subservice'] = Services::where('slug', 'tesla-wrap')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '5')->orderBy('created_at', 'desc')->limit(3)->get();

            return view('web.services-new.master-service')->with($data);
        }


//-------------------------------------------------------------------------------------------------------

    public function websiteDevelopment(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'website-development')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['web_drop'] = '1';
        
        return view('web.services-new.master-service')->with($data);
    }
        public function fullStackDevelopment(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'website-development')->first();
            $data['subservice'] = Services::where('slug', 'full-stack-web-development')->first();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function eCommerceDevelopment(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'website-development')->first();
            $data['subservice'] = Services::where('slug', 'e-commerce-website-development')->first();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function websiteMaintenance(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'website-development')->first();
            $data['subservice'] = Services::where('slug', 'website-maintenance-services')->first();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }


//-------------------------------------------------------------------------------------------------------


    public function seoServicesDubai(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'seo-services')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['web_drop'] = '1';
        $data['noPopup'] = '1';
        
        return view('web.services.seo.index-ads')->with($data);
    }



    public function seoServices(){
        $data['nav'] = 'services';
        $data['service'] = Services::where('slug', 'seo-services')->first();
        $data['subservices'] = Services::where('parent_id', $data['service']->id)->get();
        $data['blogs'] = Blogs::where('status', '1')->where('category_id', '27')->orderBy('created_at', 'desc')->limit(3)->get();
        $data['web_drop'] = '1';
        
        return view('web.services-new.master-service')->with($data);
    }
        public function abuDhabi(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'abu-dhabi')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '34')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function seoAudit(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'seo-audit')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '34')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function technicalSEO(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'technical-seo')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '34')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function ecommerceSEO(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'ecommerce-seo')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '34')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function aiSEOgeo(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'ai-seo-geo')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '34')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function arabicSEO(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'arabic-seo')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '34')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function seoConsultant(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'seo-consultant')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '34')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function smallBusinessSEO(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'small-business-seo')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '34')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
    
    
    
    
        public function aiOptimization(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'ai-seo-optimization')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '34')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function localSEO(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'local-seo')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '27')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function techSupport(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'tech-support')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '27')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function freeAudit(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'free-seo-audit')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '27')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function caseStudies(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'case-studies')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '27')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
        public function pricePackage(){
            $data['nav'] = 'services';
            $data['service'] = Services::where('slug', 'seo-services')->first();
            $data['subservice'] = Services::where('slug', 'price-and-package')->first();
            $data['blogs'] = Blogs::where('status', '1')->where('category_id', '27')->orderBy('created_at', 'desc')->limit(3)->get();
            $data['web_drop'] = '1';
            
            return view('web.services-new.master-service')->with($data);
        }
}
