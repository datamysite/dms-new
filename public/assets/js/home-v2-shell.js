/* HOME V2 SHELL — header behaviour (mega menu, scrolled state, mobile drawer). Vanilla JS, no dependencies. */
(function () {
  'use strict';
  var header = document.querySelector('[data-hs-header]');
  if (!header) return;
  var body = document.body, doc = document;
  var $$ = function (s, c) { return Array.prototype.slice.call((c || doc).querySelectorAll(s)); };

  /* scrolled state */
  /* no read at script time (window.scrollY forces the first layout inside script evaluation): check after the first frame, then on scroll */
  var ticking = false;
  var apply = function () { ticking = false; header.classList.toggle('is-scrolled', window.scrollY > 24); };
  var onScroll = function () { if (!ticking) { ticking = true; requestAnimationFrame(apply); } };
  onScroll(); window.addEventListener('scroll', onScroll, { passive: true });

  /* mega menu */
  var mega = header.querySelector('[data-hs-mega]');
  if (mega) {
    var btn = mega.querySelector('.hs-link--btn'), tabs = $$('[data-hs-tab]', mega), panes = $$('.hs-pane', mega), t = null;
    var fine = window.matchMedia && matchMedia('(hover: hover) and (pointer: fine)').matches;
    var setOpen = function (on) { clearTimeout(t); mega.classList.toggle('is-open', on); btn.setAttribute('aria-expanded', on ? 'true' : 'false'); };
    var show = function (id) {
      tabs.forEach(function (a) { a.classList.toggle('is-on', a.getAttribute('data-hs-tab') === id); });
      panes.forEach(function (p) { p.classList.toggle('is-on', p.id === id); });
    };
    btn.addEventListener('click', function (e) {
      var go = btn.getAttribute('data-hs-href');
      /* mouse click on a hover-capable device opens the Services page; keyboard (detail 0) and touch keep toggling the menu */
      if (go && fine && e.detail > 0) { window.location.href = go; return; }
      setOpen(!mega.classList.contains('is-open'));
    });
    if (fine) {
      mega.addEventListener('mouseenter', function () { clearTimeout(t); t = setTimeout(function () { setOpen(true); }, 80); });
      mega.addEventListener('mouseleave', function () { clearTimeout(t); t = setTimeout(function () { setOpen(false); }, 160); });
    }
    tabs.forEach(function (a) {
      var id = a.getAttribute('data-hs-tab');
      a.addEventListener('mouseenter', function () { show(id); });
      a.addEventListener('focus', function () { show(id); });
    });
    doc.addEventListener('click', function (e) { if (!mega.contains(e.target)) setOpen(false); });
    doc.addEventListener('keydown', function (e) { if (e.key === 'Escape' && mega.classList.contains('is-open')) { setOpen(false); btn.focus(); } });
  }

  /* mobile drawer */
  var burger = header.querySelector('.hs-burger'), drawer = doc.getElementById('hs-drawer');
  var toggle = function (on) {
    body.classList.toggle('hs-menu-open', on);
    burger.setAttribute('aria-expanded', on ? 'true' : 'false'); burger.setAttribute('aria-label', on ? 'Close menu' : 'Open menu');
    drawer.setAttribute('aria-hidden', on ? 'false' : 'true');
    if (on) { var x = drawer.querySelector('.hs-drawer-x'); if (x) setTimeout(function () { x.focus(); }, 60); } else burger.focus();
  };
  if (burger && drawer) {
    burger.addEventListener('click', function () { toggle(!body.classList.contains('hs-menu-open')); });
    $$('[data-hs-close]', header).forEach(function (el) { el.addEventListener('click', function () { toggle(false); }); });
    $$('a', drawer).forEach(function (a) { a.addEventListener('click', function () { body.classList.remove('hs-menu-open'); }); });
    doc.addEventListener('keydown', function (e) { if (e.key === 'Escape' && body.classList.contains('hs-menu-open')) toggle(false); });
    window.addEventListener('resize', function () { if (window.innerWidth > 1199 && body.classList.contains('hs-menu-open')) toggle(false); });
  }
})();

/* Footer: copy-to-clipboard button with visible + announced feedback */
(function () {
  'use strict';
  var fallback = function (text) {
    var ta = document.createElement('textarea');
    ta.value = text; ta.setAttribute('readonly', ''); ta.style.cssText = 'position:fixed;top:-100px;opacity:0';
    document.body.appendChild(ta); ta.select();
    var ok = false; try { ok = document.execCommand('copy'); } catch (e) {}
    document.body.removeChild(ta); return ok;
  };
  document.addEventListener('click', function (e) {
    var btn = e.target.closest && e.target.closest('[data-hs-copy]');
    if (!btn) return;
    var text = btn.getAttribute('data-hs-copy'), label = btn.querySelector('span'), ico = btn.querySelector('i'), live = btn.parentNode.querySelector('.hs-copy-live');
    var done = function (ok) {
      btn.classList.toggle('is-done', ok);
      if (label) label.textContent = ok ? 'Copied' : 'Press Ctrl+C';
      if (ico) ico.className = ok ? 'bi bi-check2' : 'bi bi-copy';
      if (live) { live.textContent = ok ? 'Email copied to clipboard' : 'Copy not available'; live.classList.add('is-on'); }
      clearTimeout(btn._t);
      btn._t = setTimeout(function () {
        btn.classList.remove('is-done');
        if (label) label.textContent = 'Copy';
        if (ico) ico.className = 'bi bi-copy';
        if (live) live.classList.remove('is-on');
      }, 2200);
    };
    if (navigator.clipboard && window.isSecureContext) navigator.clipboard.writeText(text).then(function () { done(true); }, function () { done(fallback(text)); });
    else done(fallback(text));
  });
})();

/* Footer: Google reviews modal (opens from the footer rating chip; slider starts on first open) */
(function () {
  'use strict';
  var modal = document.getElementById('hs-reviews');
  if (!modal) return;
  var body = document.body, panel = modal.querySelector('.hs-rv-panel'), last = null, swiper = null, manual = false, open = false;
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var host = modal.querySelector('.hs-rv-swiper'), tog = modal.querySelector('.hs-rv-toggle');
  manual = reduce;

  var paint = function () {
    if (!tog) return;
    tog.setAttribute('aria-pressed', manual ? 'true' : 'false');
    tog.setAttribute('aria-label', manual ? 'Play auto-play' : 'Pause auto-play');
    tog.querySelector('i').className = 'bi ' + (manual ? 'bi-play-fill' : 'bi-pause-fill');
  };
  var sync = function () {
    if (!swiper || !swiper.autoplay) return;
    var want = open && !manual && !document.hidden;
    if (want && !swiper.autoplay.running) swiper.autoplay.start();
    else if (!want && swiper.autoplay.running) swiper.autoplay.stop();
  };
  var init = function () {
    if (swiper || !window.Swiper) return;
    host.classList.add('is-ready');
    swiper = new window.Swiper(host, {
      wrapperClass: 'hs-rv-track', slideClass: 'hs-rv-slide',
      slidesPerView: 1, spaceBetween: 16, loop: modal.querySelectorAll('.hs-rv-slide').length > 3, speed: 700, grabCursor: true,
      autoplay: reduce ? false : { delay: parseInt(host.getAttribute('data-autoplay'), 10) || 1500, disableOnInteraction: false, pauseOnMouseEnter: true },
      breakpoints: { 640: { slidesPerView: 2, spaceBetween: 18 }, 992: { slidesPerView: 3, spaceBetween: 20 } },
      keyboard: { enabled: true },
      navigation: { prevEl: modal.querySelector('.hs-rv-prev'), nextEl: modal.querySelector('.hs-rv-next') },
      a11y: { enabled: true, prevSlideMessage: 'Previous reviews', nextSlideMessage: 'Next reviews' }
    });
    paint();
  };
  var show = function (trigger) {
    last = trigger || document.activeElement;
    open = true;
    modal.classList.add('is-open'); modal.setAttribute('aria-hidden', 'false'); body.classList.add('hs-rv-open');
    init(); if (swiper) { swiper.update(); } sync();
    panel.setAttribute('tabindex', '-1'); setTimeout(function () { panel.focus({ preventScroll: true }); }, 60);   /* focus the dialog itself: no stray ring on the close button */
  };
  var hide = function () {
    open = false;
    modal.classList.remove('is-open'); modal.setAttribute('aria-hidden', 'true'); body.classList.remove('hs-rv-open');
    sync();
    if (last && last.focus) last.focus();
  };

  document.addEventListener('click', function (e) {
    var t = e.target.closest && e.target.closest('[data-hs-reviews]');
    if (t) { e.preventDefault(); show(t); return; }
    if (e.target.closest && e.target.closest('[data-hs-rv-close]') && open) hide();
  });
  document.addEventListener('keydown', function (e) {
    if (!open) return;
    if (e.key === 'Escape') { hide(); return; }
    if (e.key === 'Tab') {   /* keep focus inside the dialog */
      var f = panel.querySelectorAll('a[href], button:not([disabled])'); if (!f.length) return;
      var first = f[0], lastEl = f[f.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); lastEl.focus(); }
      else if (!e.shiftKey && document.activeElement === lastEl) { e.preventDefault(); first.focus(); }
    }
  });
  if (tog) tog.addEventListener('click', function () { manual = !manual; paint(); sync(); });
  document.addEventListener('visibilitychange', sync);
})();
