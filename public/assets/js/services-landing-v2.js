/* ==========================================================================
   SERVICES LANDING V2 — page script (runs after the home-v2.js engine is loaded)
   Used only by resources/views/web/services-landing-v2.blade.php
   Independent of service-v2.js.
   Performance notes: decoration animations start only after window load + a first frame (html.svl-ready);
   off-screen pieces are paused; the parallax caches section geometry (no layout reads per frame).
   ========================================================================== */
(function () {
  'use strict';
  var root = document.documentElement;
  var fine = window.matchMedia && matchMedia('(hover: hover) and (pointer: fine)').matches;
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* decoration animations are held back until the page has loaded and painted once */
  function ready() { requestAnimationFrame(function () { setTimeout(function () { root.classList.add('svl-ready'); }, 150); }); }
  if (document.readyState === 'complete') ready(); else addEventListener('load', ready, { once: true });

  /* pointer-follow glow on the service cards (mouse devices only) */
  var stack = document.getElementById('svl-grid');
  if (stack && fine && !reduce) {
    stack.addEventListener('pointermove', function (e) {
      var card = e.target.closest && e.target.closest('.svl-card');
      if (!card) return;
      var r = card.getBoundingClientRect();
      card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
      card.style.setProperty('--my', (e.clientY - r.top) + 'px');
    }, { passive: true });
  }

  /* scroll parallax of the decoration (decorative only) */
  if (reduce || !fine) return;
  var els = [].slice.call(document.querySelectorAll('[data-svl-depth]'));
  if (!els.length) return;
  var vh = window.innerHeight, ticking = false, inView = new Set(), geo = new Map();

  function measure() {
    /* one geometry read per parent section, cached; refreshed on resize / size change */
    var sy = window.pageYOffset;
    els.forEach(function (el) {
      var p = el.parentElement, r = p.getBoundingClientRect();
      geo.set(el, { mid: r.top + sy + r.height / 2, d: parseFloat(el.getAttribute('data-svl-depth')) || 0 });
    });
  }
  function paint() {
    ticking = false;
    var sy = window.pageYOffset, half = vh / 2;
    inView.forEach(function (el) {
      var g = geo.get(el); if (!g) return;
      el.style.translate = '0 ' + ((g.mid - sy - half) * -g.d).toFixed(1) + 'px';
    });
  }
  function req() { if (!ticking) { ticking = true; requestAnimationFrame(paint); } }

  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (e) {
        e.target.classList.toggle('is-paused', !e.isIntersecting);
        if (e.isIntersecting) inView.add(e.target); else inView.delete(e.target);
      });
      req();
    }, { rootMargin: '150px' });
    els.forEach(function (el) { io.observe(el); });
  } else { els.forEach(function (el) { inView.add(el); }); }

  var secs = els.map(function (e) { return e.parentElement; }).filter(function (v, i, a) { return a.indexOf(v) === i; });
  if ('ResizeObserver' in window) {
    var ro = new ResizeObserver(function () { measure(); req(); });
    secs.forEach(function (s) { ro.observe(s); });
  }
  addEventListener('load', function () { measure(); req(); }, { once: true });
  addEventListener('resize', function () { vh = innerHeight; measure(); req(); });
  addEventListener('scroll', req, { passive: true });
  measure(); req();

  /* the first 3D piece leans toward the pointer (desktop only) */
  var stage = document.querySelector('.svl-services .svl-stage');
  if (stage && innerWidth > 991) {
    var sec = stage.closest('section');
    sec.addEventListener('pointermove', function (e) {
      var r = sec.getBoundingClientRect();
      var x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      stage.style.transform = 'rotateY(' + (x * 18).toFixed(1) + 'deg) rotateX(' + (-y * 14).toFixed(1) + 'deg)';
    }, { passive: true });
    sec.addEventListener('pointerleave', function () { stage.style.transform = ''; });
  }
})();
