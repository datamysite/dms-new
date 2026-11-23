/* ==========================================================================
   BLOGS V2 — page script (runs after the home-v2.js engine is loaded)
   Used only by resources/views/web/blogs-v2.blade.php
   Independent of other V2 pages.
   - pointer-follow glow on the case cards
   - whitespace decoration: scroll parallax + pointer tilt of the 3D cluster (decorative only)
   ========================================================================== */
(function () {
  'use strict';
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fine = window.matchMedia && matchMedia('(hover: hover) and (pointer: fine)').matches;

  /* glow that follows the pointer on the post cards */
  var grid = document.getElementById('blv-grid');
  if (grid && fine && !reduce) {
    grid.addEventListener('pointermove', function (e) {
      var card = e.target.closest && e.target.closest('.blv-card');
      if (!card) return;
      var r = card.getBoundingClientRect();
      card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
      card.style.setProperty('--my', (e.clientY - r.top) + 'px');
    }, { passive: true });
  }

  if (reduce) return;
  var els = [].slice.call(document.querySelectorAll('[data-blv-depth]'));
  if (!els.length) return;
  var ticking = false, vh = window.innerHeight, inView = new Set();
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { e.target.classList.toggle('is-paused', !e.isIntersecting); if (e.isIntersecting) inView.add(e.target); else inView.delete(e.target); }); }, { rootMargin: '150px' });
    els.forEach(function (el) { io.observe(el); });
  } else { els.forEach(function (el) { inView.add(el); }); }
  /* positions are measured on load / resize / page-height change and cached, so each frame only WRITES (reading a position
     per decoration, after other scripts had written styles in the same frame, forced a layout every time) */
  var geo = new Map();
  function measure() {
    var sy = window.pageYOffset;
    geo.clear();
    els.forEach(function (el) {
      var r = el.parentElement.getBoundingClientRect();
      geo.set(el, { mid: r.top + sy + r.height / 2, d: parseFloat(el.getAttribute('data-blv-depth')) || 0 });
    });
  }
  function paint() {
    ticking = false;
    var sy = window.pageYOffset, half = vh / 2;
    inView.forEach(function (el) {
      var g = geo.get(el); if (!g) return;
      var t = '0 ' + ((g.mid - sy - half) * -g.d).toFixed(1) + 'px';
      if (el.__t !== t) { el.__t = t; el.style.translate = t; }
    });
  }
  if ('ResizeObserver' in window) new ResizeObserver(function () { measure(); req(); }).observe(document.body);
  addEventListener('load', function () { measure(); req(); }, { once: true });
  measure();
  function req() { if (!ticking) { ticking = true; requestAnimationFrame(paint); } }
  addEventListener('scroll', req, { passive: true });
  addEventListener('resize', function () { vh = innerHeight; measure(); req(); });
  req();

  /* the first 3D cluster leans toward the pointer (desktop only) */
  var stage = document.querySelector('.blv-latest .blv-stage');
  if (stage && fine && innerWidth > 991) {
    var sec = stage.closest('section'), base = getComputedStyle(stage).transform;
    sec.addEventListener('pointermove', function (e) {
      var r = sec.getBoundingClientRect();
      var x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      stage.style.transform = 'scale(.55) rotateY(' + (x * 18).toFixed(1) + 'deg) rotateX(' + (-y * 14).toFixed(1) + 'deg)';
    }, { passive: true });
    sec.addEventListener('pointerleave', function () { stage.style.transform = ''; });
  }
})();

/* start the decoration animations after load + one painted frame */
(function () { var r = document.documentElement; function go() { requestAnimationFrame(function () { setTimeout(function () { r.classList.add('blv-ready'); }, 150); }); } if (document.readyState === 'complete') go(); else addEventListener('load', go, { once: true }); })();
