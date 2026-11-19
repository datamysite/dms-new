/* ==========================================================================
   ABOUT V2 — page script (runs after the home-v2.js engine is loaded)
   Used only by resources/views/web/about-v2.blade.php
   Independent of every other V2 page script.
   - Mission / Vision / Values accordion (one open at a time, same content as the live page)
   - decoration: scroll parallax of the page's custom 3D pieces (decorative only)
   ========================================================================== */
(function () {
  'use strict';

  /* accordion */
  var acc = document.getElementById('abv-acc');
  if (acc) {
    var items = [].slice.call(acc.querySelectorAll('.abv-acc-item'));
    acc.addEventListener('click', function (e) {
      var btn = e.target.closest && e.target.closest('.abv-acc-q button');
      if (!btn) return;
      var item = btn.closest('.abv-acc-item'), open = item.classList.contains('is-open');
      items.forEach(function (it) { it.classList.remove('is-open'); it.querySelector('button').setAttribute('aria-expanded', 'false'); });
      if (!open) { item.classList.add('is-open'); btn.setAttribute('aria-expanded', 'true'); }
    });
  }

  /* parallax of the decoration */
  if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  var els = [].slice.call(document.querySelectorAll('[data-abv-depth]'));
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
      var d = parseFloat(el.getAttribute('data-abv-depth')) || 0;
      el.style.translate = '0 ' + ((r.top + r.height / 2 - vh / 2) * -d).toFixed(1) + 'px';
    });
  }
  function req() { if (!ticking) { ticking = true; requestAnimationFrame(paint); } }
  addEventListener('scroll', req, { passive: true });
  addEventListener('resize', function () { vh = innerHeight; req(); });
  req();
})();

/* == Modern dropdown for "Select Service" ===================================
   Enhances the native <select name="service"> (kept in the form, so the submitted value is unchanged):
   icon + label button, animated panel, search box, arrow/Home/End/Enter/Esc/type-ahead keys, opens upward when low on space. */
(function () {
  'use strict';
  var root = document.querySelector('[data-abv-dd]');
  var sel = root && root.querySelector('select');
  if (!sel) return;
  var opts = [].slice.call(sel.options);
  var uid = 'abv-dd-' + Math.random().toString(36).slice(2, 7);

  var btn = document.createElement('button');
  btn.type = 'button'; btn.className = 'abv-dd-btn';
  btn.setAttribute('aria-haspopup', 'listbox'); btn.setAttribute('aria-expanded', 'false'); btn.setAttribute('aria-controls', uid);
  btn.innerHTML = '<span class="abv-dd-ico"><i class="bi bi-grid-3x3-gap"></i></span><span class="abv-dd-label"></span><i class="bi bi-chevron-down abv-dd-caret" aria-hidden="true"></i>';
  var label = btn.querySelector('.abv-dd-label'), btnIco = btn.querySelector('.abv-dd-ico i');

  var panel = document.createElement('div'); panel.className = 'abv-dd-panel';
  var search = null;
  if (opts.length > 8) {
    var sw = document.createElement('div'); sw.className = 'abv-dd-search';
    sw.innerHTML = '<i class="bi bi-search" aria-hidden="true"></i><input type="text" placeholder="Search services…" aria-label="Search services" autocomplete="off">';
    panel.appendChild(sw); search = sw.querySelector('input');
  }
  var list = document.createElement('ul'); list.className = 'abv-dd-list'; list.id = uid; list.setAttribute('role', 'listbox'); list.setAttribute('aria-label', 'Select Service');
  panel.appendChild(list);
  var empty = document.createElement('div'); empty.className = 'abv-dd-empty'; empty.textContent = 'No services found'; empty.hidden = true; panel.appendChild(empty);

  function esc(t) { return String(t).replace(/&/g, '&amp;').replace(/</g, '&lt;'); }
  var items = opts.filter(function (o) { return o.value !== ''; }).map(function (o, i) {
    var li = document.createElement('li');
    li.className = 'abv-dd-opt'; li.setAttribute('role', 'option'); li.id = uid + '-' + i; li.setAttribute('aria-selected', 'false');
    li.dataset.value = o.value;
    li.innerHTML = '<span class="abv-dd-ico"><i class="' + (o.getAttribute('data-icon') || 'bi bi-dot') + '"></i></span><span>' + esc(o.text) + '</span>';
    list.appendChild(li);
    return li;
  });

  root.appendChild(btn); root.appendChild(panel);
  var active = -1, placeholder = (opts[0] && opts[0].text) || 'Select Service';

  function visible() { return items.filter(function (li) { return !li.hidden; }); }
  function paint() {
    var o = sel.options[sel.selectedIndex];
    var has = o && o.value !== '';
    label.textContent = has ? o.text : placeholder;
    root.classList.toggle('has-value', !!has);
    btnIco.className = has ? (o.getAttribute('data-icon') || 'bi bi-check2') : 'bi bi-grid-3x3-gap';
    items.forEach(function (li) { li.setAttribute('aria-selected', has && li.dataset.value === o.value ? 'true' : 'false'); });
  }
  function setActive(i) {
    var v = visible(); if (!v.length) return;
    active = Math.max(0, Math.min(v.length - 1, i));
    items.forEach(function (li) { li.classList.remove('is-active'); });
    v[active].classList.add('is-active'); btn.setAttribute('aria-activedescendant', v[active].id);
    v[active].scrollIntoView({ block: 'nearest' });
  }
  function filter(q) {
    q = q.trim().toLowerCase(); var n = 0;
    items.forEach(function (li) { var m = li.textContent.toLowerCase().indexOf(q) !== -1; li.hidden = !m; if (m) n++; });
    empty.hidden = n !== 0; active = -1; if (n) setActive(0);
  }
  function open() {
    var r = btn.getBoundingClientRect();
    root.classList.toggle('is-up', window.innerHeight - r.bottom < 380 && r.top > 380);
    root.classList.add('is-open'); btn.setAttribute('aria-expanded', 'true');
    if (search) { search.value = ''; filter(''); }
    var sIdx = visible().findIndex(function (li) { return li.getAttribute('aria-selected') === 'true'; });
    setActive(sIdx < 0 ? 0 : sIdx);
    /* the panel is still fading in, so focus is retried until the search box accepts it */
    if (search) { var tries = 0, f = function () { search.focus({ preventScroll: true }); if (document.activeElement !== search && ++tries < 8 && root.classList.contains('is-open')) setTimeout(f, 60); }; setTimeout(f, 40); }
  }
  function close(focusBtn) { root.classList.remove('is-open'); btn.setAttribute('aria-expanded', 'false'); if (focusBtn) btn.focus(); }
  function choose(li) {
    sel.value = li.dataset.value;
    sel.dispatchEvent(new Event('change', { bubbles: true }));
    paint(); close(true);
  }

  btn.addEventListener('click', function () { root.classList.contains('is-open') ? close() : open(); });
  list.addEventListener('click', function (e) { var li = e.target.closest('.abv-dd-opt'); if (li) choose(li); });
  list.addEventListener('mousemove', function (e) { var li = e.target.closest('.abv-dd-opt'); if (li) { var i = visible().indexOf(li); if (i !== active) setActive(i); } });
  if (search) search.addEventListener('input', function () { filter(search.value); });
  document.addEventListener('click', function (e) { if (!root.contains(e.target)) close(); });

  function keys(e) {
    var openNow = root.classList.contains('is-open'), v = visible();
    if (e.key === 'Escape') { if (openNow) { e.preventDefault(); close(true); } return; }
    if (e.key === 'Tab') { close(); return; }
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') { e.preventDefault(); if (!openNow) { open(); return; } setActive(active + (e.key === 'ArrowDown' ? 1 : -1)); return; }
    if (e.key === 'Home' || e.key === 'End') { if (openNow && e.target !== search) { e.preventDefault(); setActive(e.key === 'Home' ? 0 : v.length - 1); } return; }
    if (e.key === 'Enter' || (e.key === ' ' && e.target === btn)) { e.preventDefault(); if (!openNow) open(); else if (v[active]) choose(v[active]); return; }
    /* type-ahead on the button (when the search box is not focused) */
    if (e.target === btn && e.key.length === 1 && /\S/.test(e.key)) {
      if (!openNow) open();
      var k = e.key.toLowerCase();
      var i = v.findIndex(function (li, idx) { return idx > active && li.textContent.trim().toLowerCase().indexOf(k) === 0; });
      if (i < 0) i = v.findIndex(function (li) { return li.textContent.trim().toLowerCase().indexOf(k) === 0; });
      if (i >= 0) setActive(i);
    }
  }
  btn.addEventListener('keydown', keys);
  if (search) search.addEventListener('keydown', keys);
  if (sel.form) sel.form.addEventListener('reset', function () { setTimeout(paint, 0); });
  paint();
})();

/* == "See More" for the data-driven lists (services, case studies): shows the items past the limit == */
(function () {
  'use strict';
  [].slice.call(document.querySelectorAll('[data-abv-more]')).forEach(function (btn) {
    var grid = document.querySelector(btn.getAttribute('data-abv-more'));
    if (!grid) return;
    var t = btn.querySelector('.abv-more-t');
    btn.addEventListener('click', function () {
      var open = grid.classList.toggle('is-expanded');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (t) t.textContent = open ? 'See Less' : 'See More';
      if (open) {
        [].slice.call(grid.querySelectorAll('.abv-extra [data-reveal], .abv-extra[data-reveal]')).forEach(function (n) { n.classList.add('is-in'); });
      } else {
        var top = grid.getBoundingClientRect().top;
        if (top < 0) window.scrollBy({ top: top - 120, behavior: 'smooth' });
      }
    });
  });
})();

/* == Copy email: real button, copies without moving the page (replaces the legacy copyEmail() helper,
      which focused a temporary element appended to the end of <body> and scrolled the page there) == */
(function () {
  'use strict';
  function fallback(text) {
    var ta = document.createElement('textarea');
    ta.value = text; ta.setAttribute('readonly', '');
    ta.style.cssText = 'position:fixed;top:0;left:0;width:1px;height:1px;opacity:0;pointer-events:none';
    document.body.appendChild(ta); ta.focus({ preventScroll: true }); ta.select();
    var ok = false; try { ok = document.execCommand('copy'); } catch (e) {}
    document.body.removeChild(ta); return ok;
  }
  document.addEventListener('click', function (e) {
    var btn = e.target.closest && e.target.closest('.copy-email[data-copy]');
    if (!btn) return;
    e.preventDefault();
    var text = btn.getAttribute('data-copy'), orig = btn.getAttribute('data-orig') || btn.innerHTML;
    btn.setAttribute('data-orig', orig);
    var done = function () {
      btn.innerHTML = '<span class="bi bi-check"></span>&nbsp;&nbsp;Copied.';
      btn.classList.add('is-copied');
      clearTimeout(btn._t);
      btn._t = setTimeout(function () { btn.innerHTML = orig; btn.classList.remove('is-copied'); }, 3000);
    };
    if (navigator.clipboard && window.isSecureContext) navigator.clipboard.writeText(text).then(done, function () { fallback(text); done(); });
    else { fallback(text); done(); }
  });
})();

/* start the decoration animations after load + one painted frame */
(function () { var r = document.documentElement; function go() { requestAnimationFrame(function () { setTimeout(function () { r.classList.add('abv-ready'); }, 150); }); } if (document.readyState === 'complete') go(); else addEventListener('load', go, { once: true }); })();
