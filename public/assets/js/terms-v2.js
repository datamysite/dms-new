/* ==========================================================================
   TMV V2 — page script (runs after the home-v2.js engine is loaded). Independent of every other V2 page script.
   ========================================================================== */
/* contents rail: built from the document's own headings (text untouched) + scroll-spy */
(function () {
  'use strict';
  var art = document.getElementById('tmv-article'), toc = document.getElementById('tmv-toc'), grid = art && art.parentElement;
  if (!art || !toc) return;
  var hs = [].slice.call(art.querySelectorAll('h4')).filter(function (h) { return h.textContent.trim().length > 0 && h.textContent.trim().length < 90 && !/^note:/i.test(h.textContent.trim()); });
  if (hs.length < 3) { toc.remove(); if (grid) grid.classList.add('no-toc'); return; }
  var ol = toc.querySelector('ol'), links = [];
  hs.forEach(function (h, i) {
    h.id = h.id || 'tmv-sec-' + (i + 1);
    var li = document.createElement('li'), a = document.createElement('a');
    a.href = '#' + h.id; a.textContent = h.textContent.replace(/^\s*\d+\.\s*/, ''); li.appendChild(a); ol.appendChild(li); links.push(a);
  });
  toc.hidden = false;
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (e) { if (e.isIntersecting) { links.forEach(function (l) { l.classList.toggle('is-on', l.getAttribute('href') === '#' + e.target.id); }); } });
    }, { rootMargin: '-20% 0px -70% 0px' });
    hs.forEach(function (h) { io.observe(h); });
  }
})();

(function () {
  'use strict';
  /* decoration: scroll parallax of the custom 3D pieces (decorative only) */
  if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  var els = [].slice.call(document.querySelectorAll('[data-tmv-depth]'));
  if (!els.length) return;
  var ticking = false, vh = window.innerHeight, inView = new Set();
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { e.target.classList.toggle('is-paused', !e.isIntersecting); if (e.isIntersecting) inView.add(e.target); else inView.delete(e.target); }); }, { rootMargin: '150px' });
    els.forEach(function (el) { io.observe(el); });
  } else { els.forEach(function (el) { inView.add(el); }); }
  function paint() {
    ticking = false;
    inView.forEach(function (el) {
      var r = el.parentElement.getBoundingClientRect(), d = parseFloat(el.getAttribute('data-tmv-depth')) || 0;
      el.style.translate = '0 ' + ((r.top + r.height / 2 - vh / 2) * -d).toFixed(1) + 'px';
    });
  }
  function req() { if (!ticking) { ticking = true; requestAnimationFrame(paint); } }
  addEventListener('scroll', req, { passive: true });
  addEventListener('resize', function () { vh = innerHeight; req(); });
  req();
})();

/* start the decoration animations after load + one painted frame */
(function () { var r = document.documentElement; function go() { requestAnimationFrame(function () { setTimeout(function () { r.classList.add('tmv-ready'); }, 150); }); } if (document.readyState === 'complete') go(); else addEventListener('load', go, { once: true }); })();
