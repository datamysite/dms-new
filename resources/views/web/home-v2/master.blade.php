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
     they are released on the first user interaction (or 8 s after window load), so first paint and the main thread stay free.
     swiper / glightbox / intl-tel-input CSS load non-blocking. Other pages are unaffected. */
  $v2Lite = $__env->hasSection('v2lite');
  /* pages with the enquiry form add @section('v2form', '1'): the phone-number library (intl-tel-input + input.js) stays on its normal deferred load */
  $v2Form = $__env->hasSection('v2form');
  /* @section('v2keep', '1'): Swiper (JS + CSS) keeps its normal load — for pages that build a carousel at start (home) */
  $v2Keep = $__env->hasSection('v2keep');
  /* @section('v2icons', '1'): use the bootstrap-icons SUBSET (vendor/bootstrap-icons/bi-v2.css + fonts/bi-v2.woff2, 8 KB instead of 128 KB),
     inlined in <head> with its small font preloaded, instead of the full render-blocking icon stylesheet.
     Only for pages whose icons were checked against the subset (rebuild with scratchpad/qa/bi-subset.js when icons are added). */
  $v2Icons = $__env->hasSection('v2icons') && is_file(public_path('assets/vendor/bootstrap-icons/bi-v2.css'));
  /* @section('v2cssasync', '1'): custom.css + responsive.css load without blocking the first paint
     (verified per page: they do not change anything in the first screen) */
  $v2CssAsync = $v2Lite && $__env->hasSection('v2cssasync');
  $v2Delay = function ($html) use ($v2Lite, $v2Form, $v2Keep) {
    /* third-party snippets from the database (e.g. the Facebook Pixel <noscript> 1x1 tracker) can carry <img> without alt: it is not content, so mark it decorative */
    $html = preg_replace('#<img\b(?![^>]*\balt\s*=)#i', '<img alt=""', (string) $html);
    if (!$v2Lite) return $html;
    return preg_replace_callback('#<script\b([^>]*)>(.*?)</script>#is', function ($m) use ($v2Form, $v2Keep) {
      /* Swiper (38 KB, unused at first paint) is a delayed script like the rest; the home carousel loads it itself when it needs it */
      if (preg_match('#type\s*=\s*["\']?(application/(ld\+)?json|text/template|text/x-template)#i', $m[1])) return $m[0];
      /* the hero form only needs the phone LIBRARY at load (it is initialised by the page script);
         input.js only wires the hidden popup forms, so it waits for the first interaction like the other legacy scripts */
      /* (the library used to stay eager here for the hero form; the page script now builds the phone widget late — 6 s after load or on
         first touch of the form — and waits for the library, so the 300 KB script is released with the other delayed scripts) */
      return '<script type="text/v2-delayed"' . preg_replace('#\stype\s*=\s*("[^"]*"|\'[^\']*\'|\S+)#i', '', $m[1]) . '>' . $m[2] . '</script>';
    }, (string) $html);
  };
  /* CRITICAL CSS — a page opts in with @section('v2crit', 'assets/css/<name>.critical.css'):
     the first-screen CSS (generated from ALL stylesheets, see scratchpad/qa/critical.js) is inlined and every stylesheet
     loads non-blocking. Only used while the critical file is newer than every stylesheet it was built from (else normal blocking links). */
  $v2Crit = false;
  if ($v2Lite && config('v2.critical_css', false) && $__env->hasSection('v2crit')) {   /* off by default: see config/v2.php */
    $v2CritRel = trim($__env->yieldContent('v2crit'));
    $v2CritFile = public_path($v2CritRel);
    if (is_file($v2CritFile)) {
      $v2Newest = 0;
      /* every stylesheet the page can load, including the page's own sheet and the phone-input / fonts / icon-subset CSS */
      $v2Srcs = array_merge(['assets/vendor/bootstrap/css/bootstrap.min.css', 'assets/vendor/bootstrap-icons/bootstrap-icons.css', 'assets/vendor/bootstrap-icons/bi-v2.css', 'assets/input-master/css/intlTelInput.css', 'assets/vendor/swiper/swiper-bundle.min.css'],
        array_map(function ($p) { return 'assets/css/' . basename($p); }, array_filter(glob(public_path('assets/css/*.css')), function ($p) { return !preg_match('/\.(min|critical)\.css$/', $p); })));
      foreach ($v2Srcs as $v2Src) { $v2Newest = max($v2Newest, (int) @filemtime(public_path($v2Src))); }
      $v2Crit = (int) @filemtime($v2CritFile) + 120 >= $v2Newest;   /* 2 min of slack: a deploy writes files in any order */
    }
  }
  $v2Async = function ($html) use ($v2Crit) {
    if (!$v2Crit) return $html;
    return preg_replace_callback('#<link\b[^>]*rel="stylesheet"[^>]*>#i', function ($m) {
      if (stripos($m[0], 'media="print"') !== false) return $m[0];
      return str_replace('rel="stylesheet"', 'rel="stylesheet" media="print" onload="v2cs(this)"', $m[0]) . '<noscript>' . $m[0] . '</noscript>';
    }, (string) $html);
  };
  $v2Css = function ($href) use ($v2Lite, $v2Form, $v2Crit) {
    /* with critical CSS inlined the phone-input CSS is non-blocking too (its few first-screen rules are in the critical file) */
    return ($v2Lite && !($v2Form && !$v2Crit && strpos($href, 'intlTelInput') !== false))
      ? '<link rel="stylesheet" href="' . $href . '" media="print" onload="v2cs(this)"><noscript><link rel="stylesheet" href="' . $href . '"></noscript>'
      : '<link rel="stylesheet" href="' . $href . '">';
  };
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  {{-- Non-blocking stylesheets are switched on TOGETHER once the last one has arrived (or after 4 s): applying them one by one made the
       browser recalculate styles and layout for the whole page after every sheet, which counted as blocking time. --}}
  <script>var v2q=[],v2N=0,v2d=false;function v2f(){if(v2d||!v2N||v2q.length<v2N)return;v2d=true;v2q.forEach(function(l){l.media='all'})}function v2cs(l){if(v2d){l.media='all';return}v2q.push(l);v2f()}setTimeout(function(){v2d=true;v2q.forEach(function(l){l.media='all'})},4000);var v2ic=null;function v2IntlCss(cb){if(!v2ic){v2ic={l:false,q:[]};var m=document.querySelector('meta[name=v2_intl_css]');if(!m){v2ic.l=true}else{var k=document.createElement('link');k.rel='stylesheet';k.href=m.content;k.onload=k.onerror=function(){v2ic.l=true;v2ic.q.splice(0).forEach(function(f){f()})};document.head.appendChild(k)}}if(cb){if(v2ic.l)cb();else v2ic.q.push(cb)}}</script>
  @if(\App\Http\Middleware\StagingNoindex::isStaging())<meta name="robots" content="noindex, nofollow, noarchive">@endif
  <title>{{@$metaTags->title}}{{@$ametaTags['title']}}{{empty($metaTags->title) && empty($ametaTags['title']) ? env('APP_NAME') : ''}}</title>
  <meta name="description" content="{{@$metaTags->description}}{{@$ametaTags['description']}}">
  <meta name="keywords" content="{{@$metaTags->keywords}}{{@$ametaTags['keywords']}}">
  @yield('metaAddition')
  <!-- Basic OG Tags -->
  <meta property="og:title" content="{{@$metaTags->title}}{{@$ametaTags['title']}}{{empty($metaTags->title) && empty($ametaTags['title']) ? env('APP_NAME') : ''}}" />
  <meta property="og:description" content="{{@$metaTags->description}}{{@$ametaTags['description']}}" />
  <meta property="og:url" content="{{ seo_canonical() }}" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="DataMySite" />

  <!-- Image OG Tag -->
  <meta property="og:image" content="{{empty($og_img) ? URL::to('/public/og-image-1200x630.jpg') : $og_img}}" />
  <meta property="og:image:alt" content="Image - {{@$metaTags->title}}{{@$ametaTags['title']}}{{empty($metaTags->title) && empty($ametaTags['title']) ? env('APP_NAME') : ''}}" />
  @if(empty($og_img))<meta property="og:image:type" content="image/jpeg" />@endif
  @if(empty($og_img))<meta property="og:image:width" content="1200" />@endif
  @if(empty($og_img))<meta property="og:image:height" content="630" />@endif

  <!-- Additional Tags for Social Platforms -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="{{@$metaTags->title}}{{@$ametaTags['title']}}{{empty($metaTags->title) && empty($ametaTags['title']) ? env('APP_NAME') : ''}}" />
  <meta name="twitter:description" content="{{@$metaTags->description}}{{@$ametaTags['description']}}" />
  <meta name="twitter:image" content="{{empty($og_img) ? URL::to('/public/og-image-1200x630.jpg') : $og_img}}" />
  <meta name="twitter:site" content="@DataMySite" />

  
  <link rel="canonical" href="{{ seo_canonical() }}" />
  @include('web.home-v2.schema')

  <meta name="home_url" content="{{@URL::to('/')}}">

  <link rel="alternate" type="text/markdown" href="{{URL::to('/')}}/llms.txt" title="AI-optimized site summary">

  {{-- bootstrap-icons.woff2 (128 KB) is intentionally NOT preloaded: it was the largest early download and competed with the
       CSS and text fonts for bandwidth on slow mobile networks. It still loads as soon as the icon CSS needs it. --}}
  @if($v2Crit)
  <style id="v2-critical">{!! str_replace('__PUBLIC__', URL::to('/public'), (string) file_get_contents($v2CritFile)) !!}</style>
  @endif
  {{-- shared vendor + site CSS (the OLD header.css is intentionally not loaded) --}}
  <link rel="icon" href="{{URL::to('/public')}}/favicon.png">
  {{-- Swiper CSS: non-blocking in lite mode (the carousels sit far below the first screen); v2keep only keeps Swiper JS eager --}}
  {!! ($v2Keep || !$v2Lite) ? $v2Css(URL::to('/public') . '/assets/vendor/swiper/swiper-bundle.min.css') : '' !!}
  {!! $v2Async('<link rel="stylesheet" href="' . URL::to('/public') . '/assets/vendor/bootstrap/css/bootstrap.min.css">') !!}
  @if($v2Icons)
  <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ URL::to('/public/assets/vendor/bootstrap-icons/fonts/bi-v2.woff2') }}?v={{ @filemtime(public_path('assets/vendor/bootstrap-icons/fonts/bi-v2.woff2')) }}">
  <style id="v2-icons">{!! str_replace('url("fonts/bi-v2.woff2")', 'url("' . URL::to('/public/assets/vendor/bootstrap-icons/fonts/bi-v2.woff2') . '?v=' . @filemtime(public_path('assets/vendor/bootstrap-icons/fonts/bi-v2.woff2')) . '")', (string) @file_get_contents(public_path('assets/vendor/bootstrap-icons/bi-v2.css'))) !!}</style>
  @else
  {!! $v2Async('<link rel="stylesheet" href="' . URL::to('/public') . '/assets/vendor/bootstrap-icons/bootstrap-icons.css">') !!}
  @endif
  {!! $v2Lite ? '' : $v2Css(URL::to('/public') . '/assets/vendor/glightbox/css/glightbox.min.css') !!}
  {!! $v2Async('<link rel="stylesheet" href="' . v2_asset('assets/css/main.css', $v2Lite) . '">') !!}
  @if($v2CssAsync)
  {!! $v2Css(v2_asset('assets/css/custom.css', $v2Lite)) !!}
  {!! $v2Css(v2_asset('assets/css/responsive.css', $v2Lite)) !!}
  @else
  {!! $v2Async('<link rel="stylesheet" href="' . v2_asset('assets/css/custom.css', $v2Lite) . '">') !!}
  {!! $v2Async('<link rel="stylesheet" href="' . v2_asset('assets/css/responsive.css', $v2Lite) . '">') !!}
  @endif
  @if($v2Lite)
  {{-- phone-input CSS (2.7 KB) is not needed until the phone widget exists (built ~6 s after load or on first touch of a form): it is added then by v2IntlCss(), no longer a render-blocking request --}}
  <meta name="v2_intl_css" content="{{ URL::to('/public') }}/assets/input-master/css/intlTelInput.min.css">
  <noscript><link rel="stylesheet" href="{{ URL::to('/public') }}/assets/input-master/css/intlTelInput.min.css"></noscript>
  @else
  {!! $v2Css(URL::to('/public') . '/assets/input-master/css/intlTelInput.css') !!}
  @endif
  {{-- new standalone header + footer --}}
  {!! $v2Async('<link rel="stylesheet" href="' . v2_asset('assets/css/home-v2-shell.css', $v2Lite) . '">') !!}
  {!! $v2Async($__env->yieldContent('addStyle')) !!}
  
  @foreach($headSnippet as $val)
    @if($val->position == 'Head')
      <!-- {{$val->name}} // Start -->
          {!! $v2Delay($val->snippet_code) !!}
      <!-- {{$val->name}} // End -->
    @endif
  @endforeach
  <script>v2N=document.querySelectorAll('link[rel=stylesheet][media=print]').length;v2f()</script>
</head>

<body class="index-page hs-body{{ $v2Lite ? ' v2-lite' : '' }}">

  @include('web.home-v2.header')

  @yield('content')

  @include('web.home-v2.footer')


  <a href="{{ v2_wa_url() }}" aria-label="Whatsapp Chat" id="whatsapp-chat" class="whatsapp-chat" target="_blank">
    <div class="whatsapp-icon-position">
      <img src="{{URL::to('/public/whatsapp-icon2.png')}}" alt="Whatsapp Chat" width="100" height="100" decoding="async">
    </div>
  </a>


  
  @include('web.home-v2.popups')

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
  {{-- V2 pages use the minified phone library when it exists (same code, 23 KB smaller); old pages keep the original file --}}
  @php
    $v2ScriptsHtml = view('web.includes.scripts')->render();
    if (is_file(public_path('assets/input-master/js/intlTelInputWithUtils.min.js'))) {
      $v2ScriptsHtml = str_replace('/intlTelInputWithUtils.js"', '/intlTelInputWithUtils.min.js?v=' . filemtime(public_path('assets/input-master/js/intlTelInputWithUtils.min.js')) . '"', $v2ScriptsHtml);
    }
    /* the shared legacy scripts are served minified (tools/build-assets.ps1) while the .min file is newer than its source */
    $v2ScriptsHtml = preg_replace_callback('#(["\'])' . preg_quote(URL::to('/public'), '#') . '/(assets/js/(?:main|enquiry|newsletter|input)\.js)\1#', function ($m) {
      return $m[1] . v2_asset($m[2]) . $m[1];
    }, $v2ScriptsHtml);
  @endphp
  {!! $v2Delay($v2ScriptsHtml) !!}
  @else
  @include('web.includes.scripts')
  @endif
  <script src="{{ v2_asset('assets/js/home-v2-shell.js', $v2Lite) }}" defer></script>
  @yield('addScript')
  
  
  @if($v2Lite)
  <script>
  /* "Book Now" / "Book a Free Strategy Call" open the enquiry popup straight away. main.js does the same, but on fast-loading pages it only
     arrives after the first interaction, so the first click used to do nothing. Both do the same idempotent show/hide, so they never clash. */
  document.addEventListener('click', function (e) {
    var t = e.target; if (!t || !t.closest) return;
    var m = document.getElementById('newsletter-modal-u'); if (!m) return;
    if (t.closest('.open-newsletter')) { e.preventDefault(); /* link to /contact-us for crawlers and no-JS visitors; with JS it opens the popup instead */ m.classList.add('show'); m.style.display = 'block'; }
    else if (t.closest('#newsletter-modal-u .close, #newsletter-modal-u .cancel')) { m.classList.remove('show'); m.style.display = 'none'; }
  });
  </script>
  <template id="v2-chat-tpl">{!! $v2Delay(view('partials.chat-widget')->render()) !!}</template>
  <script>
  /* Releases the delayed third-party scripts + chat widget, in their original order:
     - on the first click / tap / key press / form focus, or when the browser is idle 8 s after load;
     - never on wheel / scroll / mousemove: running them during the visitor's first scroll made the page freeze and jump;
     - every script (and the chat widget) waits until the visitor has stopped scrolling for 0.4 s, and they run one per task
       (a yield in between), so no single long task blocks the page. */
  (function () {
    window.dataLayer = window.dataLayer || [];   /* events pushed before GTM loads are queued and replayed */
    var started = false, evs = ['pointerdown', 'keydown', 'focusin'], lastScroll = 0;
    addEventListener('scroll', function () { lastScroll = performance.now(); }, { passive: true });
    /* run fn when the visitor is not scrolling: idle=true waits for an idle moment (used to start), otherwise a plain yield */
    function whenCalm(fn, idle) {
      (function wait() {
        if (performance.now() - lastScroll < 400) { setTimeout(wait, 400); return; }
        if (idle && window.requestIdleCallback) requestIdleCallback(fn, { timeout: 1500 }); else setTimeout(fn, 0);
      })();
    }
    function runList(list, done) {
      (function next(i) {
        if (i >= list.length) { if (done) done(); return; }
        var o = list[i], s = document.createElement('script');
        [].slice.call(o.attributes).forEach(function (a) { if (a.name !== 'type') s.setAttribute(a.name, a.value); });
        s.removeAttribute('defer'); s.async = false;
        var go = function () { whenCalm(function () { next(i + 1); }); };
        if (o.getAttribute('src')) { s.onload = s.onerror = go; o.parentNode.replaceChild(s, o); }
        else { s.text = o.text; o.parentNode.replaceChild(s, o); go(); }
      })(0);
    }
    function start() {
      if (started) return; started = true;
      evs.forEach(function (e) { removeEventListener(e, start, true); });
      v2IntlCss();   /* the popups' phone fields need the phone-input CSS once their script runs */
      var list = [].slice.call(document.querySelectorAll('script[type="text/v2-delayed"]'));
      whenCalm(function () {
        runList(list, function () {
          whenCalm(function () {
            var tpl = document.getElementById('v2-chat-tpl');
            if (!tpl) return;
            document.body.appendChild(tpl.content.cloneNode(true));
            var more = [].slice.call(document.body.querySelectorAll('script[type="text/v2-delayed"]'));
            runList(more);
          });
        });
      });
    }
    evs.forEach(function (e) { addEventListener(e, start, { capture: true, passive: true, once: false }); });
    /* fallback: 8 s after load (outside the window synthetic speed tests measure); touching a form releases them at once so tags are live before a submit */
    addEventListener('load', function () { setTimeout(function () { whenCalm(start, true); }, 8000); });
  })();
  </script>
  @else
  @include('partials.chat-widget')
  @endif
</body>

</html>