{{--
|==========================================================================
| HOME V2 MASTER — standalone layout for the redesigned home page.
| Same contract as web.includes.master (yields: metaAddition, addStyle,
| content, addScript) but uses its OWN header + footer:
|   web.home-v2.header · web.home-v2.footer
|   public/assets/css/home-v2-shell.css · public/assets/js/home-v2-shell.js
| Nothing here is shared with the old header/footer.
|==========================================================================
--}}
@php
  require_once resource_path('views/web/home-v2/helpers.php');
  /* LITE MODE — a page opts in with @section('v2lite', '1'):
     third-party / legacy scripts (jQuery, sweetalert, intl-tel-input, analytics snippets, chat widget …) are NOT run during load;
     they are released on the first user interaction (or 10 s after window load), so first paint and the main thread stay free.
     swiper / glightbox / intl-tel-input CSS load non-blocking. Other pages are unaffected. */
  $v2Lite = $__env->hasSection('v2lite');
  $v2Delay = function ($html) use ($v2Lite) {
    if (!$v2Lite) return $html;
    return preg_replace_callback('#<script\b([^>]*)>(.*?)</script>#is', function ($m) {
      if (preg_match('#type\s*=\s*["\']?(application/(ld\+)?json|text/template|text/x-template)#i', $m[1])) return $m[0];
      return '<script type="text/v2-delayed"' . preg_replace('#\stype\s*=\s*("[^"]*"|\'[^\']*\'|\S+)#i', '', $m[1]) . '>' . $m[2] . '</script>';
    }, (string) $html);
  };
  $v2Css = function ($href) use ($v2Lite) {
    return $v2Lite
      ? '<link rel="stylesheet" href="' . $href . '" media="print" onload="this.media=\'all\'"><noscript><link rel="stylesheet" href="' . $href . '"></noscript>'
      : '<link rel="stylesheet" href="' . $href . '">';
  };
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>{{@$metaTags->title}}{{@$ametaTags['title']}}{{empty($metaTags->title) && empty($ametaTags['title']) ? env('APP_NAME') : ''}}</title>
  <meta name="description" content="{{@$metaTags->description}}{{@$ametaTags['description']}}">
  <meta name="keywords" content="{{@$metaTags->keywords}}{{@$ametaTags['keywords']}}">
  @yield('metaAddition')
  <!-- Basic OG Tags -->
  <meta property="og:title" content="{{@$metaTags->title}}{{@$ametaTags['title']}}{{empty($metaTags->title) && empty($ametaTags['title']) ? env('APP_NAME') : ''}}" />
  <meta property="og:description" content="{{@$metaTags->description}}{{@$ametaTags['description']}}" />
  <meta property="og:url" content="{{@URL::current()}}" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="DataMySite" />

  <!-- Image OG Tag -->
  <meta property="og:image" content="{{empty($og_img) ? URL::to('/public/og-logo.jpeg') : $og_img}}" />
  <meta property="og:image:alt" content="Image - {{@$metaTags->title}}{{@$ametaTags['title']}}{{empty($metaTags->title) && empty($ametaTags['title']) ? env('APP_NAME') : ''}}" />
  <meta property="og:image:type" content="image/*" />
  <meta property="og:image:width" content="620" />
  <meta property="og:image:height" content="340" />

  <!-- Additional Tags for Social Platforms -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="{{@$metaTags->title}}{{@$ametaTags['title']}}{{empty($metaTags->title) && empty($ametaTags['title']) ? env('APP_NAME') : ''}}" />
  <meta name="twitter:description" content="{{@$metaTags->description}}{{@$ametaTags['description']}}" />
  <meta name="twitter:image" content="{{empty($og_img) ? URL::to('/public/og-logo.jpeg') : $og_img}}" />
  <meta name="twitter:site" content="@DataMySite" />

  <meta property="fb:app_id" content="1234567890" />
  
  <link rel="canonical" href="{{ (int) request('page') > 1 ? URL::current().'?page='.(int) request('page') : URL::current() }}" />

  <meta name="home_url" content="{{@URL::to('/')}}">

  <link rel="alternate" type="text/markdown" href="{{URL::to('/')}}/llms.txt" title="AI-optimized site summary">

  {{-- shared vendor + site CSS (the OLD header.css is intentionally not loaded) --}}
  <link rel="icon" href="{{URL::to('/public')}}/favicon.png">
  {!! $v2Lite ? '' : $v2Css(URL::to('/public') . '/assets/vendor/swiper/swiper-bundle.min.css') !!}
  <link rel="stylesheet" href="{{URL::to('/public')}}/assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{URL::to('/public')}}/assets/vendor/bootstrap-icons/bootstrap-icons.css">
  {!! $v2Lite ? '' : $v2Css(URL::to('/public') . '/assets/vendor/glightbox/css/glightbox.min.css') !!}
  <link href="{{URL::to('/public')}}/assets/css/main.css" rel="stylesheet">
  <link href="{{URL::to('/public')}}/assets/css/custom.css" rel="stylesheet">
  <link href="{{URL::to('/public')}}/assets/css/responsive.css" rel="stylesheet">
  {!! $v2Css(URL::to('/public') . '/assets/input-master/css/intlTelInput.css') !!}
  {{-- new standalone header + footer --}}
  <link href="{{ v2_asset('assets/css/home-v2-shell.css', $v2Lite) }}" rel="stylesheet">
  @yield('addStyle')
  
  @foreach($headSnippet as $val)
    @if($val->position == 'Head')
      <!-- {{$val->name}} // Start -->
          {!! $v2Delay($val->snippet_code) !!}
      <!-- {{$val->name}} // End -->
    @endif
  @endforeach
</head>

<body class="index-page hs-body{{ $v2Lite ? ' v2-lite' : '' }}">

  @include('web.home-v2.header')

  @yield('content')

  @include('web.home-v2.footer')


  <a href="https://api.whatsapp.com/send/?phone=971525287259" aria-label="Whatsapp Chat" id="whatsapp-chat" class="whatsapp-chat" target="_blank">
    <div class="whatsapp-icon-position">
      <img src="{{URL::to('/public/whatsapp-icon2.png')}}" alt="Whatsapp Chat">
    </div>
  </a>


  
  <div class="modal fade" id="newsletter-modal-u" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                  <span aria-hidden="true">&times;</span> 
                </button> 
              </div>
              <div class="modal-body p-0 row">
                  <div class="col-12 col-lg-5 ad p-0"> 
                    <img src="{{URL::to('/public/popup-min.png')}}" class="popup-des" alt="Need a Help?"  loading="lazy" /> 
                    <img src="{{URL::to('/public/popup-m.jpg')}}" class="popup-mob" width="100%" alt="Need a Help?" loading="lazy" /> 
                  </div>
                  <div class="details col-12 col-lg-7">
                    <form action="{{route('enquiry.help.submit')}}" id="enquiry-help-form">
                      @csrf
                      <h2>Need a Help?</h2>
                      <p>
                        <small class="para">
                          Looking for a Custom <strong>Marketing Plan</strong>? 
                        <br>
                          Get expert guidance tailored to your brand’s success!</small>
                      </p>
                      <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Name" required>
                      </div>
                      <div class="form-group">
                        <input type="tel" name="mailphone" id="phone-field3" class="form-control" required>
                        <input type="hidden" name="phone" id="fullphone-field3" required="">
                      </div>
                      <div class="form-group">
                        <input type="email" class="form-control help-email" name="email" placeholder="Email" required>
                      </div>
                      <br>
                      <div class="form-group">
                        <button type="reset" class="btn btn-sm btn-default cancel">&nbsp;&nbsp;Cancel&nbsp;&nbsp;</button>&nbsp;&nbsp;
                        <input type="submit" class="btn btn-sm btn-theme" value="Proceed">
                      </div>
                      <img src="{{URL::to('/public/loader-gif-m.gif')}}"  alt="Loader gif" class="help-loading">
                    </form>
                  </div>
              </div>
          </div>
      </div>
  </div>


  <div class="modal fade" id="seo-cta-modal-u" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                  <span aria-hidden="true">&times;</span> 
                </button> 
              </div>
              <div class="modal-body p-0 row">
                  <div class="col-12 col-lg-5 ad p-0"> 
                    <img src="{{URL::to('/public/popup-min.png')}}" class="popup-des" alt="Need a Help?"  loading="lazy" /> 
                    <img src="{{URL::to('/public/popup-m.jpg')}}" class="popup-mob" width="100%" alt="Need a Help?" loading="lazy" /> 
                  </div>
                  <div class="details col-12 col-lg-7">
                    <form action="{{route('enquiry.help.submit')}}" id="seo-help-form">
                      @csrf
                      <h2>Need a Help?</h2>
                      <p>
                        <small class="para">
                          We’re here to rank your <strong>website higher</strong>! 
                          </small>
                      </p>
                      <p class="seo-prompt-para">
                        Get a FREE SEO Audit Report for your website. Just fill out the form and our experts will send you a detailed audit and help you fix the issues that stop you from ranking on your desired keywords.
                      </p>
                      <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Name" required>
                      </div>
                      <div class="form-group">
                        <input type="tel" name="mailphone" id="phone-field4" class="form-control" required>
                        <input type="hidden" name="phone" id="fullphone-field4" required="">
                      </div>
                      <div class="form-group">
                        <input type="email" class="form-control help-email2" name="email" placeholder="Email" required>
                      </div>
                      <div class="form-group">
                        <input type="text" class="form-control" name="website_link" placeholder="Website" required>
                      </div>

                      <div class="form-group">
                        <button type="reset" class="btn btn-sm btn-default cancel">&nbsp;&nbsp;Cancel&nbsp;&nbsp;</button>&nbsp;&nbsp;
                        <input type="submit" class="btn btn-sm btn-theme" value="Proceed">
                      </div>
                      <img src="{{URL::to('/public/loader-gif-m.gif')}}"  alt="Loader gif" class="help-loading2">
                    </form>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div class="modal fade" id="seo-ads-cta-modal-u" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-body p-0 row">
                  <div class="aside-form">
                  <h3>Zero-Cost SEO Audit</h3>
                  <h4><small><small>Know Your Rank. Improve.</small></small></h4>
                    <form action="{{route('enquiry.help.submit')}}" id="seo-ads-form" >
                        @csrf
                        <input type="text" name="name" placeholder="Name" class="form-control" required>
                        
                        <input type="tel" name="mailphone" id="phone-fieldseo" class="form-control" required>
                        <input type="hidden" name="phone" id="fullphone-fieldseo" required="">
                        <input type="email" name="email" placeholder="Email" class="form-control seo-ads-email" required>
                      
                        <input type="text" class="form-control" name="website_link" placeholder="Website" required>
                        
                        <input type="hidden" name="service" value="SEO Services">
                                                
                        <button class="btn btn-theme">Submit</button>
                        <button type="button" class="btn btn-default close">Close</button>
                        <img src="{{URL::to('/public/loader-gif.gif')}}" alt="loader image" width="80px" class="help-loading2 seo-loading">
                    </form>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div class="ov" id="ov">
    <div class="pop" role="dialog" aria-modal="true">

      <button class="xbtn" onclick="closeExitPopup()">✕</button>

      <!-- HEADER -->
      <div class="hd">
        <div class="pill"><span class="dot"></span> Before You Leave</div>
        <h2>Get a <em>Free Strategy</em><br>From Dubai's Top Ad Agency</h2>
        <p>Our experts respond in 60 min — no commitment, no cost.</p>
      </div>

      <!-- FORM BODY -->
      <div class="bd">

        <form action="{{route('enquiry.lite.submit')}}" id="enquiry-form-exit">
            @csrf
            <div class="urg">
              <span class="ui">⚡</span>
              <span class="ut"><strong>Free slots</strong> available today</span>
              <span class="ucd" id="cd">29:59</span>
            </div>

            <div class="fw" id="fw">
              <div style="display:flex;flex-direction:column;gap:8px">
                <div class="row2">
                  <input type="text" name="name" placeholder="Your Name" required>
                  <div>
                      <input type="tel" name="mainphone" id="phone-field7" required="">
                      <input type="hidden" name="phone" id="fullphone-field7" required="">
                  </div>
                </div>
                <div class="row2 row3">
                  <input type="email" class="exit-email" name="email" placeholder="Your Email" required>
                </div>
                <select name="service" required>
                  <option value="" disabled selected>I need help with…</option>
                  <option>Billboard Advertising</option>
                  <option>Transit Media (Metro, Bus, Taxi, Airport)</option>
                  <option>PR Coverage (Gulf News, Forbes, Khaleej Times)</option>
                  <option>Influencer Marketing</option>
                  <option>Digital Marketing</option>
                  <option>SEO &amp; Website</option>
                  <option>Experiential Marketing</option>
                  <option>Lead Generation</option>
                  <option>Radio Advertising</option>
                  <option>CGI / Creative Ads</option>
                  <option>Celebrity Marketing</option>
                  <option>Event Marketing &amp; Sponsorships</option>
                  <option>Global Marketing (Saudi, India, London)</option>
                  <option>Full 360° Strategy</option>
                </select>
                <button class="cta" type="submit">Claim My Free Strategy Call <span class="ar">→</span></button>
              </div>

              <div style="display:flex;flex-direction:column;gap:6px;margin-top:8px">
                <a href="https://api.whatsapp.com/send/?phone=971525287259" class="wa" target="_blank" rel="noopener">
                  <span class="wad"></span> WhatsApp: +971 52 528 7259
                </a>
                <p class="prv">🔒 No spam · 100% free · ⭐ 5.0 Google rated</p>
                <div class="skip"><button onclick="closeExitPopup()">No thanks, I'll figure it out myself</button></div>

                <div class="loading exit-loading"><img src="{{URL::to('/public/loader-gif.gif')}}" width="80px" class="newsletter-loader"></div>
              </div>
            </div>
          </form>
      </div>
    </div>
  </div>

  @foreach($bodySnippet as $val)
    @if($val->position == 'Body')
      @if(!empty($noPopup) && ($val->name == 'Klaviyo Basecode' || $val->name == 'Tawk.to Live Chat' || $val->name == 'Tawk.to BaseCode'))

      @else
      <!-- {{$val->name}} // Start -->
          {!! $v2Delay($val->snippet_code) !!}
      <!-- {{$val->name}} // End -->
      @endif
    @endif
  @endforeach
  
  @if($v2Lite)
  {!! $v2Delay(view('web.includes.scripts')->render()) !!}
  @else
  @include('web.includes.scripts')
  @endif
  <script src="{{ v2_asset('assets/js/home-v2-shell.js', $v2Lite) }}" defer></script>
  @yield('addScript')
  
  
  @if($v2Lite)
  <template id="v2-chat-tpl">{!! $v2Delay(view('partials.chat-widget')->render()) !!}</template>
  <script>
  /* releases the delayed third-party scripts + chat widget on first interaction (or 10 s after load), in their original order */
  (function () {
    var started = false, evs = ['pointerdown', 'keydown', 'touchstart', 'scroll', 'wheel', 'mousemove'];
    function runList(list, done) {
      (function next(i) {
        if (i >= list.length) { if (done) done(); return; }
        var o = list[i], s = document.createElement('script');
        [].slice.call(o.attributes).forEach(function (a) { if (a.name !== 'type') s.setAttribute(a.name, a.value); });
        s.removeAttribute('defer'); s.async = false;
        if (o.getAttribute('src')) { s.onload = s.onerror = function () { next(i + 1); }; o.parentNode.replaceChild(s, o); }
        else { s.text = o.text; o.parentNode.replaceChild(s, o); next(i + 1); }
      })(0);
    }
    function start() {
      if (started) return; started = true;
      evs.forEach(function (e) { removeEventListener(e, start, true); });
      var list = [].slice.call(document.querySelectorAll('script[type="text/v2-delayed"]'));
      runList(list, function () {
        var tpl = document.getElementById('v2-chat-tpl');
        if (!tpl) return;
        var before = document.body.lastChild;
        document.body.appendChild(tpl.content.cloneNode(true));
        var more = [].slice.call(document.body.querySelectorAll('script[type="text/v2-delayed"]'));
        runList(more);
      });
    }
    evs.forEach(function (e) { addEventListener(e, start, { capture: true, passive: true, once: false }); });
    addEventListener('load', function () { setTimeout(function () { if (window.requestIdleCallback) requestIdleCallback(start, { timeout: 500 }); else start(); }, 10000); });
  })();
  </script>
  @else
  @include('partials.chat-widget')
  @endif
</body>

</html>