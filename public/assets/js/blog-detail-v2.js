/* ==========================================================================
   BLOG POST V2 — page script (runs after the home-v2.js engine is loaded)
   Used only by resources/views/web/blog-detail-v2.blade.php
   Independent of other V2 pages.
   - pointer-follow glow on the case cards
   - whitespace decoration: scroll parallax + pointer tilt of the 3D cluster (decorative only)
   ========================================================================== */
(function () {
  'use strict';
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fine = window.matchMedia && matchMedia('(hover: hover) and (pointer: fine)').matches;

  /* glow that follows the pointer on the related cards */
  var grid = document.getElementById('bdv-grid');
  if (grid && fine && !reduce) {
    grid.addEventListener('pointermove', function (e) {
      var card = e.target.closest && e.target.closest('.bdv-card');
      if (!card) return;
      var r = card.getBoundingClientRect();
      card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
      card.style.setProperty('--my', (e.clientY - r.top) + 'px');
    }, { passive: true });
  }

  if (reduce) return;
  var els = [].slice.call(document.querySelectorAll('[data-bdv-depth]'));
  if (!els.length) return;
  var ticking = false, vh = window.innerHeight, inView = new Set();
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { e.target.classList.toggle('is-paused', !e.isIntersecting); if (e.isIntersecting) inView.add(e.target); else inView.delete(e.target); }); }, { rootMargin: '150px' });
    els.forEach(function (el) { io.observe(el); });
  } else { els.forEach(function (el) { inView.add(el); }); }
  function paint() {
    ticking = false;
    inView.forEach(function (el) {
      var r = el.parentElement.getBoundingClientRect();
      var d = parseFloat(el.getAttribute('data-bdv-depth')) || 0;
      el.style.translate = '0 ' + ((r.top + r.height / 2 - vh / 2) * -d).toFixed(1) + 'px';
    });
  }
  function req() { if (!ticking) { ticking = true; requestAnimationFrame(paint); } }
  addEventListener('scroll', req, { passive: true });
  addEventListener('resize', function () { vh = innerHeight; req(); });
  req();

  /* the first 3D cluster leans toward the pointer (desktop only) */
  var stage = document.querySelector('.bdv-article-sec .bdv-stage');
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

/* == image preview: click the cover or an article image to enlarge; Esc / click / x to close == */
(function () {
  'use strict';
  var lb = document.createElement('div');
  lb.className = 'bdv-lb'; lb.setAttribute('role', 'dialog'); lb.setAttribute('aria-modal', 'true'); lb.setAttribute('aria-label', 'Image preview');
  lb.innerHTML = '<button type="button" aria-label="Close preview">&times;</button><img alt="">';
  document.body.appendChild(lb);
  var img = lb.querySelector('img');
  function open(src, alt) { img.src = src; img.alt = alt || ''; lb.classList.add('is-open'); }
  function close() { lb.classList.remove('is-open'); }
  document.addEventListener('click', function (e) {
    var t = e.target;
    if (t.matches && t.matches('.bdv-cover img, .bdv-article img') && !t.closest('a')) { e.preventDefault(); open(t.currentSrc || t.src, t.alt); return; }
    if (lb.classList.contains('is-open') && (t === lb || t.closest('.bdv-lb button'))) close();
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
})();

/* start the decoration animations after load + one painted frame */
(function () { var r = document.documentElement; function go() { requestAnimationFrame(function () { setTimeout(function () { r.classList.add('bdv-ready'); }, 150); }); } if (document.readyState === 'complete') go(); else addEventListener('load', go, { once: true }); })();
