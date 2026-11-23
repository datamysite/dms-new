/* ==========================================================================
   FQV V2 — page script (runs after the home-v2.js engine is loaded). Independent of every other V2 page script.
   ========================================================================== */
/* FAQ: accordion (one open), category filter, search with highlight — same behaviour as the live page */
(function () {
  'use strict';
  var items = [].slice.call(document.querySelectorAll('.fqv-faq-item'));
  var navBtns = [].slice.call(document.querySelectorAll('.fqv-nav-btn'));
  var groups = [].slice.call(document.querySelectorAll('.fqv-faq-group'));
  var si = document.getElementById('faqSearch'), sc = document.getElementById('sClear'), fe = document.getElementById('faqEmpty');
  document.querySelectorAll('.fqv-faq-q').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('.fqv-faq-item'), isOpen = item.classList.contains('open');
      items.forEach(function (i) { i.classList.remove('open'); i.querySelector('.fqv-faq-q').setAttribute('aria-expanded', 'false'); });
      if (!isOpen) { item.classList.add('open'); btn.setAttribute('aria-expanded', 'true'); }
    });
  });
  navBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      navBtns.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var cat = btn.dataset.cat;
      groups.forEach(function (g) { g.style.display = (cat === 'all' || cat === g.dataset.group) ? '' : 'none'; });
      if (si) { si.value = ''; si.dispatchEvent(new Event('input')); }
    });
  });
  function esc(s) { return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }
  if (si) si.addEventListener('input', function () {
    var q = si.value.trim();
    if (sc) sc.classList.toggle('on', q.length > 0);
    if (q) {
      navBtns.forEach(function (b) { b.classList.remove('active'); });
      var all = document.querySelector('.fqv-nav-btn[data-cat="all"]'); if (all) all.classList.add('active');
      groups.forEach(function (g) { g.style.display = ''; });
    }
    document.querySelectorAll('.fqv-q-text').forEach(function (el) { el.innerHTML = el.innerHTML.replace(/<mark>(.*?)<\/mark>/g, '$1'); });
    if (!q) { items.forEach(function (i) { i.style.display = ''; }); if (fe) fe.classList.remove('show'); return; }
    var found = 0;
    items.forEach(function (item) {
      var text = (item.dataset.q || '') + ' ' + item.querySelector('.fqv-q-text').textContent;
      if (new RegExp(esc(q), 'i').test(text)) {
        item.style.display = ''; found++;
        var el = item.querySelector('.fqv-q-text');
        el.innerHTML = el.textContent.replace(new RegExp(esc(q), 'gi'), function (m) { return '<mark>' + m + '</mark>'; });
      } else { item.style.display = 'none'; }
    });
    groups.forEach(function (g) { var any = [].slice.call(g.querySelectorAll('.fqv-faq-item')).some(function (i) { return i.style.display !== 'none'; }); g.style.display = any ? '' : 'none'; });
    if (fe) fe.classList.toggle('show', found === 0);
  });
  if (sc) sc.addEventListener('click', function () { si.value = ''; si.dispatchEvent(new Event('input')); si.focus(); });
})();

(function () {
  'use strict';
  /* decoration: scroll parallax of the custom 3D pieces (decorative only) */
  if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  var els = [].slice.call(document.querySelectorAll('[data-fqv-depth]'));
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
      geo.set(el, { mid: r.top + sy + r.height / 2, d: parseFloat(el.getAttribute('data-fqv-depth')) || 0 });
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
})();

/* start the decoration animations after load + one painted frame */
(function () { var r = document.documentElement; function go() { requestAnimationFrame(function () { setTimeout(function () { r.classList.add('fqv-ready'); }, 150); }); } if (document.readyState === 'complete') go(); else addEventListener('load', go, { once: true }); })();
