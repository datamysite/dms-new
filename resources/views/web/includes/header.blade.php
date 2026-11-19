<header id="header" class="header fixed-top">
    <div class="header-info">
      <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
        <span class="header-info__phone-wrap">
          <a href="https://api.whatsapp.com/send/?phone=971525287259" class="webCellPhone"><i class="bi bi-telephone"></i>&nbsp; +971 52 528 7259</a>
          <span class="header-info__divider">|</span>
          <span class="header-info__cta-text">Get Your Free Dubai Marketing Strategy Call</span>
        </span>
        <span class="para top-navmenu">
          <ul class="nav-mega">
            <li><a href="{{route('home')}}" class="{{!empty($nav) && $nav == 'home' ? 'active' : ''}}">Home<br></a></li>
            <li><a href="{{route('services')}}" class="{{!empty($nav) && $nav == 'services' ? 'active' : ''}}">Services<br></a></li>
            <li><a href="{{route('caseStudies')}}" class="{{!empty($nav) && $nav == 'case-studies' ? 'active' : ''}}">Case Studies</a></li>
            <li><a href="{{route('blogs')}}" class="{{!empty($nav) && $nav == 'blogs' ? 'active' : ''}}">Blogs</a></li>
            <li><a href="{{route('about')}}" class="{{!empty($nav) && $nav == 'about' ? 'active' : ''}}">About Us</a></li>
            <li><a href="{{route('contact')}}" class="{{!empty($nav) && $nav == 'contact' ? 'active' : ''}}">Contact Us</a></li>
          </ul>
        </span>
        <span>
          <button type="button" class="open-newsletter">Book Now</button>
        </span>
      </div>
    </div>
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between main-nav-row">
      <a href="{{route('home')}}" class="logo d-flex align-items-center" aria-label="Home">
        <img src="{{URL::to('/public')}}/assets/img/logo.png" alt="Logo">
      </a>
      <a href="https://api.whatsapp.com/send/?phone=971525287259" class="m-phone-link"><i class="bi bi-telephone"></i>&nbsp; +971 525 287 259</a>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      <div class="nav-overlay"></div>
      <nav id="navmenu" class="navmenu">
        <ul class="nav-mega nav-mega-new">
          @foreach($header_services as $key => $val)
            @php $show_nav = array('digital-marketing', 'seo-service', 'outdoor-advertising', 'transit-media', 'digital-advertising', 'lead-generation', 'influencer-marketing', 'celebrity-marketing', 'public-relations-pr-coverage', 'radio-advertising', 'seo-services') @endphp
            @if(in_array($val->slug, $show_nav))
              <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">{{$val->sudo}}</button>
                <ul class="dropdown-menu">
                  <li class="dropdown-heading"><a href="{{URL::to('/'.$val->slug)}}">{{$val->name}}</a></li>
                  @foreach($val->subServices as $sub)
                    <li><a href="{{URL::to('/'.$val->slug.'/'.$sub->slug)}}">{{$sub->name}}</a></li>
                  @endforeach
                </ul>
              </div>
            @endif
          @endforeach
        </ul>
      </nav>
    </div>
</header>

<script>
  // Mobile nav toggle
  document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('.mobile-nav-toggle');
    const navmenu = document.getElementById('navmenu');

    if (toggleBtn && navmenu) {
      toggleBtn.addEventListener('click', function () {
        navmenu.classList.toggle('navmenu-active');
        toggleBtn.classList.toggle('bi-list');
        toggleBtn.classList.toggle('bi-x');
        document.body.classList.toggle('nav-open');
      });
    }

    // On mobile, tapping a dropdown button opens/closes its submenu instead of following a hover state
    document.querySelectorAll('#navmenu .dropdown > .dropdown-toggle').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        if (window.innerWidth <= 1199) {
          e.preventDefault();
          const parent = btn.closest('.dropdown');
          const wasOpen = parent.classList.contains('dropdown-open');
          document.querySelectorAll('#navmenu .dropdown.dropdown-open').forEach(function (el) {
            el.classList.remove('dropdown-open');
          });
          if (!wasOpen) parent.classList.add('dropdown-open');
        }
      });
    });

    // Close mobile menu when a link is clicked
    document.querySelectorAll('#navmenu .dropdown-menu a').forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.innerWidth <= 1199) {
          navmenu.classList.remove('navmenu-active');
          toggleBtn.classList.add('bi-list');
          toggleBtn.classList.remove('bi-x');
          document.body.classList.remove('nav-open');
        }
      });
    });
  });
</script>