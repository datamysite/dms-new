/* ==========================================================================
   BLOG CATEGORIES V2 — page script (runs after the home-v2.js engine is loaded)
   Used only by resources/views/web/blogs-categories-v2.blade.php
   Independent of other V2 pages.
   - pointer-follow glow on the case cards
   - whitespace decoration: scroll parallax + pointer tilt of the 3D cluster (decorative only)
   ========================================================================== */
(function () {
  'use strict';
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fine = window.matchMedia && matchMedia('(hover: hover) and (pointer: fine)').matches;

  /* (unused here) */
  var grid = document.getElementById('bcv-grid');
  if (grid && fine && !reduce) {
    grid.addEventListener('pointermove', function (e) {
      var card = e.target.closest && e.target.closest('.bcv-card');
      if (!card) return;
      var r = card.getBoundingClientRect();
      card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
      card.style.setProperty('--my', (e.clientY - r.top) + 'px');
    }, { passive: true });
  }

  if (reduce) return;
  var els = [].slice.call(document.querySelectorAll('[data-bcv-depth]'));
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
      var d = parseFloat(el.getAttribute('data-bcv-depth')) || 0;
      el.style.translate = '0 ' + ((r.top + r.height / 2 - vh / 2) * -d).toFixed(1) + 'px';
    });
  }
  function req() { if (!ticking) { ticking = true; requestAnimationFrame(paint); } }
  addEventListener('scroll', req, { passive: true });
  addEventListener('resize', function () { vh = innerHeight; req(); });
  req();

  /* the first 3D cluster leans toward the pointer (desktop only) */
  var stage = document.querySelector('.bcv-index .bcv-stage');
  if (stage && fine && innerWidth > 991) {
    var sec = stage.closest('section'), base = getComputedStyle(stage).transform;
    sec.addEventListener('pointermove', function (e) {
      var r = sec.getBoundingClientRect();
      var x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      stage.style.transform = 'scale(.45) rotateY(' + (x * 18).toFixed(1) + 'deg) rotateX(' + (-y * 14).toFixed(1) + 'deg)';
    }, { passive: true });
    sec.addEventListener('pointerleave', function () { stage.style.transform = ''; });
  }
})();

/* == Category index: search + A–Z / most published toggle (same behaviour as the live page) == */
(function () {
  'use strict';
  var search = document.getElementById('catSearch');
  var count = document.getElementById('catCount');
  var empty = document.getElementById('catEmpty');
  var azView = document.getElementById('azView');
  var popView = document.getElementById('popularView');
  var rail = document.getElementById('azRail');
  var toggleBtns = document.querySelectorAll('.bcv-toggle-btn');
  if (!azView || !search) return;

  var total = azView.querySelectorAll('.bcv-row').length;
  function activeContainer() { return azView.hidden ? popView : azView; }

  function applySearch() {
    var q = (search.value || '').trim().toLowerCase();
    var visible = 0;
    [azView, popView].forEach(function (container) {
      container.querySelectorAll('.bcv-row').forEach(function (row) {
        var match = row.getAttribute('data-name').indexOf(q) !== -1;
        row.style.display = match ? '' : 'none';
        if (match && container === activeContainer()) visible++;
      });
      container.querySelectorAll('.bcv-letter').forEach(function (group) {
        var any = Array.prototype.some.call(group.querySelectorAll('.bcv-row'), function (r) { return r.style.display !== 'none'; });
        group.style.display = any ? '' : 'none';
      });
    });
    count.textContent = 'Showing ' + visible + ' of ' + total + ' categories';
    empty.hidden = visible !== 0;
  }

  function setView(view) {
    var pop = view === 'popular';
    azView.hidden = pop; popView.hidden = !pop;
    if (rail) rail.style.visibility = pop ? 'hidden' : 'visible';
    applySearch();
  }

  search.addEventListener('input', applySearch);
  toggleBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      toggleBtns.forEach(function (b) { b.classList.remove('is-active'); });
      btn.classList.add('is-active');
      setView(btn.getAttribute('data-view'));
    });
  });
})();
