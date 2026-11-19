/* ==========================================================================
   SERVICE PAGE V2 — service-specific behaviour
   Used only by resources/views/web/services-v2/page.blade.php
   Loaded BEFORE home-v2.js: it tags each page's own (legacy) content with the
   [data-reveal] hooks so the shared Home V2 motion engine animates it.
   ========================================================================== */
(function () {
  'use strict';
  var root = document.getElementById('hv2');
  if (!root || !root.classList.contains('sv2')) return;
  var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || root).querySelectorAll(sel)); };

  /* == 0 · Backgrounds: reset only the old LIGHT section stripes; mark DARK blocks so their text stays light == */
  var lum = function (c) {
    var m = c && c.match(/rgba?\(([^)]+)\)/); if (!m) return null;
    var p = m[1].split(',').map(parseFloat); if (p.length > 3 && p[3] < 0.5) return null;
    var f = function (v) { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); };
    return 0.2126 * f(p[0]) + 0.7152 * f(p[1]) + 0.0722 * f(p[2]);
  };
  var bgLum = function (el) {
    var cs = window.getComputedStyle(el), l = lum(cs.backgroundColor);
    if (l === null && cs.backgroundImage && cs.backgroundImage !== 'none') { var g = cs.backgroundImage.match(/rgba?\([^)]+\)/); if (g) l = lum(g[0]); }
    return l;
  };
  $$('[data-sv2-legacy]').forEach(function (block) {
    $$(':scope > section, :scope > .section, :scope > section .section', block).forEach(function (sec) {
      var l = bgLum(sec);
      if (l === null || l > 0.6) sec.classList.add('sv2-bg-reset'); else if (l < 0.2) sec.classList.add('sv2-on-dark');
    });
    $$('div, section, aside, article, a, li', block).forEach(function (el) {
      if (el.closest('.sv2-on-dark')) return;
      var l = bgLum(el); if (l !== null && l < 0.2) el.classList.add('sv2-on-dark');
    });
  });

  /* == 1 · Motion tags for the legacy sections ============================ */
  $$('[data-sv2-legacy]').forEach(function (block) {
    /* the old AOS attributes are not used here: the V2 reveal engine takes over */
    $$('[data-aos]', block).forEach(function (el) { el.removeAttribute('data-aos'); el.removeAttribute('data-aos-delay'); });

    $$('.row', block).forEach(function (row) {
      var cols = $$(':scope > [class*="col-"]', row).filter(function (c) { return c.children.length; });
      cols.forEach(function (col, i) {
        var media = col.querySelector(':scope > img, :scope > iframe, :scope > a > img, :scope > .side-image');
        var onlyMedia = media && col.children.length <= 2 && !col.querySelector('h4, p');
        if (onlyMedia) {
          /* image / video columns glide in from their side */
          var left = i === 0 && cols.length > 1;
          col.setAttribute('data-reveal', left ? 'fly-left' : (cols.length > 1 ? 'fly-right' : 'scale'));
          if (cols.length > 1) row.setAttribute('data-fly-group', '');
        } else {
          /* text columns: headings, paragraphs, lists, tables and accordions rise in sequence */
          var d = 0;
          $$(':scope > h2, :scope > h3, :scope > h4, :scope > h5, :scope > p, :scope > ul, :scope > ol, :scope > .table-responsive, :scope > .faq, :scope > iframe, :scope > img, :scope > div:not(.faq)', col).forEach(function (el) {
            if (el.hasAttribute('data-reveal')) return;
            el.setAttribute('data-reveal', (el.tagName === 'IMG' || el.tagName === 'IFRAME') ? 'scale' : 'up');
            el.setAttribute('data-delay', String(Math.min(d++, 5)));
          });
        }
      });
    });

    /* location chips pop in one after another */
    $$('.badge-location', block).forEach(function (b, i) { b.style.setProperty('animation-delay', (i * 60) + 'ms'); b.classList.add('sv2-chip'); });
  });

  /* == 2 · Legacy accordions: mirror the open state on the card (for the V2 styling) ==
        The site's faq.js still does the actual toggling (.collapse.show). */
  var syncCards = function () {
    $$('[data-sv2-legacy] .faq .card').forEach(function (card) {
      var c = card.querySelector('.collapse');
      card.classList.toggle('is-open', !!(c && c.classList.contains('show')));
    });
  };
  root.addEventListener('click', function (e) {
    if (e.target.closest && e.target.closest('[data-sv2-legacy] .faq .faq-title')) setTimeout(syncCards, 0);
  });
  syncCards();
  $$('[data-sv2-legacy] .faq .faq-title').forEach(function (t) {
    t.setAttribute('role', 'button'); t.setAttribute('tabindex', '0');
    t.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); t.click(); } });
  });

  /* == 3 · Page FAQ (V2 accordion, one open at a time) ===================== */
  var faq = root.querySelector('[data-sv2-faq]');
  if (faq) {
    var items = $$('.hv2-faq-item', faq);
    faq.addEventListener('click', function (e) {
      var q = e.target.closest('.hv2-faq-q'); if (!q) return;
      var item = q.closest('.hv2-faq-item'), open = item.classList.contains('is-open');
      items.forEach(function (it) { it.classList.remove('is-open'); it.querySelector('.hv2-faq-q').setAttribute('aria-expanded', 'false'); });
      if (!open) { item.classList.add('is-open'); q.setAttribute('aria-expanded', 'true'); }
    });
    faq.addEventListener('keydown', function (e) {
      var q = e.target.closest('.hv2-faq-q'); if (!q || !/^(ArrowDown|ArrowUp|Home|End)$/.test(e.key)) return;
      e.preventDefault();
      var qs = items.map(function (it) { return it.querySelector('.hv2-faq-q'); }), i = qs.indexOf(q), n = qs.length;
      qs[e.key === 'Home' ? 0 : e.key === 'End' ? n - 1 : (i + (e.key === 'ArrowDown' ? 1 : -1) + n) % n].focus();
    });
  }

  /* == 3b · "See More" strips (publications, uni strips): the site's main.js expands them; here we
        (a) hide the strip when the list already fits (nothing hidden), (b) make sure it disappears once expanded */
  var smSel = '.pub-see-more, .uni-strip-see-more, .ser-see-more';
  var smCheck = function () {
    $$('[data-sv2-legacy] ' + smSel.split(', ').join(', [data-sv2-legacy] ')).forEach(function (sm) {
      var list = sm.previousElementSibling; if (!list) return;
      if (list.scrollHeight <= list.clientHeight + 4) { sm.classList.add('sv2-sm-done'); list.classList.add('sv2-sm-open'); }
    });
  };
  root.addEventListener('click', function (e) {
    var btn = e.target.closest && e.target.closest(smSel.split(', ').map(function (s) { return s + ' button'; }).join(', '));
    if (!btn) return;
    var sm = btn.closest(smSel), list = sm && sm.previousElementSibling;
    if (sm) sm.classList.add('sv2-sm-done');
    if (list) list.classList.add('sv2-sm-open');
  });
  (document.fonts && document.fonts.ready ? document.fonts.ready : Promise.resolve()).then(function () { setTimeout(smCheck, 80); });
  window.addEventListener('load', smCheck);

  /* == 4 · Whitespace decoration ==============================================
        Light sections get themed CSS-3D objects placed into their REAL empty areas
        (measured after fonts/images load), plus an ambient layer. Nothing is stored in the DB:
        the page's service group (data-sv2-group on <main>) picks the motif pair.
        Self-contained: own IntersectionObserver + rAF parallax (home-v2.js collects elements
        only once and its data-parallax would overwrite the 3D transforms). */
  var DECO = {
    /* every service page (themed by its service group); PILOT kept only for reference */
    ALL: true,
    PILOT: ['sport-personalities', 'lamp-post', 'full-stack-web-development'],
    SKIP: [],                       /* section_keys that must never be decorated */
    MIN_W: 992, PAGE_CAP: 10, PER_BLOCK: 2,
    THEMES: {
      'outdoor-advertising': ['billboard', 'prism'],
      'digital-advertising': ['screen', 'pixelcube'],
      'transit-media': ['route', 'wheel'],
      'radio-advertising': ['waves', 'orb'],
      'seo-services': ['bars', 'magnifier'],
      'digital-marketing': ['screen', 'bars'],
      'website-development': ['brackets', 'cube'],
      'influencer-marketing': ['avatars', 'orb'],
      'celebrity-marketing': ['stars', 'spotlight'],
      'public-relations-pr-coverage': ['pages', 'ring'],
      'event-marketing': ['calendar', 'orbs'],
      'sponsorships': ['links', 'ring'],
      'lead-generation': ['funnel', 'orb'],
      'creative-and-cgi-advertising': ['prism', 'cube'],
      'experiential-marketing': ['orb', 'ring'],
      'global-marketing': ['globe', 'arc'],
      '_': ['prism', 'orb']
    }
  };
  var page = root.getAttribute('data-sv2-page') || '', group = root.getAttribute('data-sv2-group') || '';
  var decoOn = DECO.ALL || DECO.PILOT.indexOf(page) > -1 || /[?&]deco=1\b/.test(window.location.search);
  if (decoOn) {
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    var motifs = DECO.THEMES[group] || DECO.THEMES._;

    /* -- motif markup (pure CSS 3D; each built from a few nested elements) -- */
    var rep = function (n, f) { var s = ''; for (var i = 0; i < n; i++) s += f(i); return s; };
    var face = function (c) { return '<i class="' + c + '"></i>'; };
    var MOTIF = {
      prism: function () { return '<div class="sv2-m sv2-m--prism"><div class="sv2-m-spin">' + rep(4, function (i) { return face('f f' + i); }) + '</div><span class="sv2-m-shadow"></span></div>'; },
      cube: function () { return '<div class="sv2-m sv2-m--cube"><div class="sv2-m-spin">' + rep(6, function (i) { return face('f f' + i); }) + '</div><span class="sv2-m-shadow"></span></div>'; },
      pixelcube: function () { return '<div class="sv2-m sv2-m--cube sv2-m--pixel"><div class="sv2-m-spin">' + rep(6, function (i) { return face('f f' + i); }) + '</div><span class="sv2-m-shadow"></span></div>'; },
      orb: function () { return '<div class="sv2-m sv2-m--orb"><span class="sv2-m-ball"></span><span class="sv2-m-floor"></span></div>'; },
      orbs: function () { return '<div class="sv2-m sv2-m--orbs"><span class="sv2-m-ball b1"></span><span class="sv2-m-ball b2"></span><span class="sv2-m-floor"></span></div>'; },
      ring: function () { return '<div class="sv2-m sv2-m--ring"><div class="sv2-m-tilt"><span class="r r1"></span><span class="r r2"></span><span class="sat"></span></div></div>'; },
      wheel: function () { return '<div class="sv2-m sv2-m--wheel"><div class="sv2-m-tilt"><span class="r r1"></span>' + rep(6, function (i) { return '<span class="spoke" style="--k:' + i + '"></span>'; }) + '<span class="hub"></span></div></div>'; },
      links: function () { return '<div class="sv2-m sv2-m--links"><div class="sv2-m-tilt"><span class="r l1"></span><span class="r l2"></span></div></div>'; },
      billboard: function () { return '<div class="sv2-m sv2-m--billboard"><div class="sv2-m-sway"><span class="board"><b></b><b></b><b></b></span><span class="pole"></span><span class="base"></span></div><span class="sv2-m-shadow"></span></div>'; },
      screen: function () { return '<div class="sv2-m sv2-m--screen"><div class="sv2-m-sway"><span class="scr"><b class="px"></b><b class="bar"></b></span><span class="stand"></span></div><span class="sv2-m-shadow"></span></div>'; },
      bars: function () { return '<div class="sv2-m sv2-m--bars"><div class="sv2-m-iso">' + rep(4, function (i) { return '<span class="bar" style="--k:' + i + '"><i class="ft"></i><i class="tp"></i><i class="sd"></i></span>'; }) + '</div><span class="sv2-m-shadow"></span></div>'; },
      magnifier: function () { return '<div class="sv2-m sv2-m--magnifier"><div class="sv2-m-sway"><span class="lens"></span><span class="handle"></span></div><span class="sv2-m-shadow"></span></div>'; },
      brackets: function () { return '<div class="sv2-m sv2-m--brackets"><div class="sv2-m-sway"><span class="glyph">&lt;/&gt;</span></div><span class="sv2-m-shadow"></span></div>'; },
      avatars: function () { return '<div class="sv2-m sv2-m--avatars"><div class="sv2-m-tilt"><span class="r r1"></span><span class="orbit">' + rep(3, function (i) { return '<span class="av" style="--k:' + i + '"></span>'; }) + '</span></div><span class="hash">#</span></div>'; },
      stars: function () { return '<div class="sv2-m sv2-m--stars">' + rep(3, function (i) { return '<span class="st" style="--k:' + i + '"></span>'; }) + '</div>'; },
      spotlight: function () { return '<div class="sv2-m sv2-m--spotlight"><span class="lamp"></span><span class="beam"></span><span class="pool"></span></div>'; },
      pages: function () { return '<div class="sv2-m sv2-m--pages"><div class="sv2-m-sway">' + rep(3, function (i) { return '<span class="pg" style="--k:' + i + '"><b></b><b></b><b></b></span>'; }) + '</div><span class="sv2-m-shadow"></span></div>'; },
      calendar: function () { return '<div class="sv2-m sv2-m--calendar"><div class="sv2-m-sway"><span class="cal"><b class="top"></b><b class="flip"></b>' + rep(6, function (i) { return '<i style="--k:' + i + '"></i>'; }) + '</span></div><span class="sv2-m-shadow"></span></div>'; },
      funnel: function () { return '<div class="sv2-m sv2-m--funnel"><div class="sv2-m-tilt">' + rep(3, function (i) { return '<span class="fr" style="--k:' + i + '"></span>'; }) + '</div>' + rep(3, function (i) { return '<span class="drop" style="--k:' + i + '"></span>'; }) + '</div>'; },
      route: function () { return '<div class="sv2-m sv2-m--route"><div class="sv2-m-tilt"><svg viewBox="0 0 200 200" aria-hidden="true"><path class="rt" d="M20 160 C 60 40, 140 170, 180 40"/><circle class="pt a" cx="20" cy="160" r="7"/><circle class="pt b" cx="180" cy="40" r="7"/></svg><span class="mover"></span></div></div>'; },
      arc: function () { return '<div class="sv2-m sv2-m--route sv2-m--arc"><div class="sv2-m-tilt"><svg viewBox="0 0 200 200" aria-hidden="true"><path class="rt" d="M20 150 Q 100 -10 180 150"/><circle class="pt a" cx="20" cy="150" r="7"/><circle class="pt b" cx="180" cy="150" r="7"/></svg><span class="mover"></span></div></div>'; },
      globe: function () { return '<div class="sv2-m sv2-m--globe"><span class="core"></span><div class="sv2-m-spin">' + rep(4, function (i) { return '<span class="mer" style="--k:' + i + '"></span>'; }) + '</div><div class="sv2-m-tilt"><span class="r r1"></span><span class="sat"></span></div></div>'; },
      waves: function () { return '<div class="sv2-m sv2-m--waves">' + rep(3, function (i) { return '<span class="wv" style="--k:' + i + '"></span>'; }) + '<span class="sv2-m-ball"></span></div>'; }
    };

    /* deterministic random from the section key, so a section always gets the same look */
    var hash = function (s) { var h = 2166136261; for (var i = 0; i < s.length; i++) { h ^= s.charCodeAt(i); h = Math.imul(h, 16777619); } return h >>> 0; };
    var rng = function (seed) { return function () { seed = (seed + 0x6D2B79F5) | 0; var t = Math.imul(seed ^ (seed >>> 15), 1 | seed); t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t; return ((t ^ (t >>> 14)) >>> 0) / 4294967296; }; };

    var CONTENT_SEL = 'h1, h2, h3, h4, h5, h6, p, ul, ol, table, img, iframe, video, .faq, .card, .btn, a[class*="btn"], .badge, blockquote, form, [class*="col-"] > div';
    var blocks = $$('[data-sv2-legacy]').filter(function (b) {
      if (b.querySelector('.sv2-on-dark')) return false;
      var key = (b.id || '').replace(/^sv2-/, '');
      return DECO.SKIP.indexOf(key) === -1;
    });
    var anchors = [], marks = [], total = 0;

    var rectIn = function (r, br) { return { l: r.left - br.left, t: r.top - br.top, r: r.right - br.left, b: r.bottom - br.top }; };
    var hit = function (a, b) { return a.l < b.r && a.r > b.l && a.t < b.b && a.b > b.t; };

    var buildBlock = function (block, idx) {
      var old = block.querySelector(':scope > .sv2-deco'); if (old) old.remove();
      var br = block.getBoundingClientRect();
      if (br.height < 260) return;
      var W = br.width, H = br.height, wide = window.innerWidth >= DECO.MIN_W;
      var key = (block.id || 'b' + idx), rand = rng(hash(key));
      var layer = document.createElement('div');
      layer.className = 'sv2-deco'; layer.setAttribute('aria-hidden', 'true');

      /* content rectangles (padded) */
      var pad = 24, boxes = [];
      $$(CONTENT_SEL, block).forEach(function (el) {
        if (el.closest('.sv2-deco')) return;
        var r = el.getBoundingClientRect(); if (r.width < 2 || r.height < 2) return;
        var q = rectIn(r, br); boxes.push({ l: q.l - pad, t: q.t - pad, r: q.r + pad, b: q.b + pad });
      });
      if (!boxes.length) return;
      var minL = Math.min.apply(null, boxes.map(function (b) { return b.l + pad; }));
      var maxR = Math.max.apply(null, boxes.map(function (b) { return b.r - pad; }));
      var top = Math.max(32, Math.min.apply(null, boxes.map(function (b) { return b.t + pad; })) - 20);
      var bottom = Math.min(H - 32, Math.max.apply(null, boxes.map(function (b) { return b.b - pad; })) + 20);

      /* ambient: second glow + dotted grid on the emptier side */
      var side = (W - maxR) >= minL ? 'r' : 'l';
      layer.innerHTML = '<span class="sv2-amb-glow sv2-amb-glow--' + side + '"></span><span class="sv2-amb-dots sv2-amb-dots--' + side + '"></span>';

      if (wide && total < DECO.PAGE_CAP) {
        /* candidate slots: [x, y, w, h, kind] in block coords */
        var slots = [];
        /* (b) empty spacer columns */
        $$('[class*="col-"]', block).forEach(function (c) {
          var empty = !c.textContent.trim() && !c.querySelector('img, iframe, video, svg, table');
          var r = c.getBoundingClientRect(); if (!empty || r.width < 90) return;
          var q = rectIn(r, br); slots.push([q.l, Math.max(q.t, top), q.r - q.l, Math.max(0, Math.min(q.b, bottom) - Math.max(q.t, top)), 'col']);
        });
        /* (c) free area under a column much shorter than its row */
        $$('.row', block).forEach(function (row) {
          var cols = $$(':scope > [class*="col-"]', row).filter(function (c) { return c.children.length; });
          if (cols.length < 2) return;
          var rr = rectIn(row.getBoundingClientRect(), br);
          cols.forEach(function (c) {
            var kids = Array.prototype.slice.call(c.children).filter(function (k) { return k.getBoundingClientRect().height > 0; });
            if (!kids.length) return;
            var cb = Math.max.apply(null, kids.map(function (k) { return k.getBoundingClientRect().bottom; })) - br.top;
            var cr = rectIn(c.getBoundingClientRect(), br);
            if (rr.b - cb > 220) slots.push([cr.l, cb + 24, cr.r - cr.l, rr.b - cb - 48, 'under']);
          });
        });
        /* (a) side gutters (may bleed 40% off-screen) */
        if (minL > 70) slots.push([-0.4 * 200, top, minL + 0.4 * 200 - 16, bottom - top, 'gutter']);
        if (W - maxR > 70) slots.push([maxR + 16, top, W - maxR - 16 + 0.4 * 200, bottom - top, 'gutter']);
        /* (d) corner bleed zones */
        slots.push([W - 140, 0, 220, Math.min(260, top + 120), 'corner']);
        slots.push([-80, H - 260, 220, 260, 'corner']);

        /* fit a square of the largest size that is free of content and other anchors */
        var placed = [], want = Math.min(DECO.PER_BLOCK, DECO.PAGE_CAP - total);
        var order = { col: 0, under: 1, gutter: 2, corner: 3 };
        slots.sort(function (a, b) { return order[a[4]] - order[b[4]] || (b[2] * b[3]) - (a[2] * a[3]); });
        var sizes = window.innerWidth >= 1200 ? [200, 160, 120] : [150, 120, 100];
        slots.forEach(function (s) {
          if (placed.length >= want) return;
          for (var si = 0; si < sizes.length; si++) {
            var S = sizes[si];
            var bleed = s[4] === 'gutter' || s[4] === 'corner';
            if (s[2] < (bleed ? S * 0.6 : S) || s[3] < S) continue;
            /* try a few vertical positions inside the slot (seeded) */
            var tries = [rand(), 0.5, 0.2, 0.8, 0.05, 0.95];
            for (var ti = 0; ti < tries.length; ti++) {
              var x = s[4] === 'gutter' ? (s[0] < 0 ? s[0] + s[2] - S : s[0]) : s[0] + (s[2] - S) / 2;
              if (bleed) x = Math.max(-0.4 * S, Math.min(W - 0.6 * S, x));
              var y = s[1] + (s[3] - S) * tries[ti];
              var box = { l: x, t: y, r: x + S, b: y + S };
              var clash = boxes.some(function (b) { return hit(box, b); }) || placed.some(function (p) { return hit(box, { l: p.l - 40, t: p.t - 40, r: p.r + 40, b: p.b + 40 }); });
              if (!clash) { placed.push(box); return; }
            }
          }
        });

        placed.forEach(function (p, k) {
          var name = motifs[(idx + k) % motifs.length];
          var a = document.createElement('div');
          a.className = 'sv2-d';
          a.style.cssText = 'left:' + p.l.toFixed(0) + 'px;top:' + p.t.toFixed(0) + 'px;--s:' + (p.r - p.l).toFixed(0) + 'px;--dl:' + (k * 0.25 + rand() * 0.3).toFixed(2) + 's;--sp:' + (0.8 + rand() * 0.5).toFixed(2);
          a.innerHTML = '<div class="sv2-d-lean"><div class="sv2-d-in">' + MOTIF[name]() + '</div></div>';
          a._dir = (k % 2 ? -1 : 1) * (0.7 + rand() * 0.6);
          layer.appendChild(a); anchors.push({ el: a, host: block }); total++;
        });
      }
      block.insertBefore(layer, block.firstChild);
      return layer;
    };

    /* watermark of the page name on the 1–2 tallest decorated blocks */
    var addMarks = function () {
      var name = root.getAttribute('data-sv2-name') || '';
      if (!name) return;
      blocks.slice().sort(function (a, b) { return b.offsetHeight - a.offsetHeight; }).slice(0, 2).forEach(function (b, i) {
        var layer = b.querySelector(':scope > .sv2-deco'); if (!layer || b.offsetHeight < 420) return;
        var m = document.createElement('span');
        m.className = 'sv2-amb-mark sv2-amb-mark--' + (i ? 'l' : 'r'); m.textContent = name;
        layer.appendChild(m); marks.push({ el: m, host: b });
      });
    };

    var build = function () {
      anchors = []; marks = []; total = 0;
      /* measure the FINAL layout: elements still waiting to slide/rise in are offset by their reveal transform,
         so switch those transforms off while measuring (no transition, so nothing visibly jumps) */
      root.classList.add('sv2-measuring');
      void root.offsetHeight;
      blocks.forEach(buildBlock);
      root.classList.add('sv2-measure-end'); root.classList.remove('sv2-measuring');
      void root.offsetHeight;
      root.classList.remove('sv2-measure-end');
      addMarks();
      blocks.forEach(function (b) { if (b._live) b.classList.add('is-live'); });
      tick();
    };

    /* in-view: entry animation once, loops only while visible */
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (en) {
        en.forEach(function (e) { e.target._live = e.isIntersecting; e.target.classList.toggle('is-live', e.isIntersecting); if (e.isIntersecting) e.target.classList.add('sv2-d-in-view'); });
      }, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });
      blocks.forEach(function (b) { io.observe(b); });
    } else { blocks.forEach(function (b) { b._live = true; b.classList.add('is-live', 'sv2-d-in-view'); }); }

    /* scroll parallax (own rAF loop, transforms only) */
    var clampN = function (v, a, b) { return Math.min(Math.max(v, a), b); };
    var ticking = false;
    var tick = function () {
      ticking = false; if (reduce) return;
      var vh = window.innerHeight;
      anchors.forEach(function (a) {
        if (!a.host._live) return;
        var r = a.host.getBoundingClientRect(), p = clampN((vh - r.top) / (vh + r.height), 0, 1);
        a.el.style.transform = 'translate3d(0,' + ((p * 2 - 1) * -40 * a.el._dir).toFixed(1) + 'px,0)';
      });
      marks.forEach(function (m) {
        var r = m.host.getBoundingClientRect(), p = clampN((vh - r.top) / (vh + r.height), 0, 1);
        m.el.style.transform = 'translate3d(' + ((p - 0.5) * -120).toFixed(1) + 'px,' + ((p * 2 - 1) * -60).toFixed(1) + 'px,0)';
      });
    };
    window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(tick); } }, { passive: true });

    /* cursor lean (desktop): one CSS variable pair on <main> */
    if (fine && !reduce) {
      var lf = null;
      window.addEventListener('mousemove', function (e) {
        if (lf) return;
        lf = requestAnimationFrame(function () {
          lf = null;
          root.style.setProperty('--sv2-lx', ((e.clientX / window.innerWidth) - 0.5).toFixed(3));
          root.style.setProperty('--sv2-ly', ((e.clientY / window.innerHeight) - 0.5).toFixed(3));
        });
      }, { passive: true });
    }

    /* build once layout is final (fonts + images), rebuild on resize / content size changes */
    var rb = null, rebuild = function () { clearTimeout(rb); rb = setTimeout(build, 220); };
    /* don't wait for window.load (YouTube / chat embeds can delay it a lot): build when fonts are ready,
       then refresh once everything has loaded (images may have changed the layout) */
    var fontsReady = document.fonts && document.fonts.ready ? document.fonts.ready : Promise.resolve();
    fontsReady.then(function () { setTimeout(build, 60); });
    if (document.readyState !== 'complete') window.addEventListener('load', rebuild);
    var lastW = window.innerWidth;
    window.addEventListener('resize', function () { if (window.innerWidth !== lastW) { lastW = window.innerWidth; rebuild(); } });
    /* lazy images change the layout when they arrive: re-measure so nothing ends up over an image */
    blocks.forEach(function (b) {
      $$('img', b).forEach(function (img) { if (!img.complete) img.addEventListener('load', rebuild, { once: true }); });
      $$('iframe', b).forEach(function (f) { f.addEventListener('load', rebuild, { once: true }); });
    });
    if ('ResizeObserver' in window) {
      var seen = new WeakMap();
      var ro = new ResizeObserver(function (en) {
        var changed = en.some(function (e) { var h = Math.round(e.contentRect.height), was = seen.get(e.target); seen.set(e.target, h); return was !== undefined && Math.abs(was - h) > 40; });
        if (changed) rebuild();
      });
      blocks.forEach(function (b) { ro.observe(b); });
    }
  }
})();
