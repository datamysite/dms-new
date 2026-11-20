/* ==========================================================================
   HOME V2 — motion & UI engine (data-attribute driven)
   Used only by resources/views/web/home-v2.blade.php
   --------------------------------------------------------------------------
   Hooks: [data-reveal] [data-delay] [data-split] [data-kinetic] [data-count]
          [data-ring] [data-tilt] [data-magnetic] [data-depth] [data-parallax]
          [data-distort] [data-readmore] [data-showmore-btn] [data-steps]
          [data-scrollto] [data-spotlight]
   Library (optional, CDN): Lenis smooth scroll. Every feature has a
   fallback so content is never left hidden.
   ========================================================================== */
(function () {
  'use strict';

  /* == SETUP ============================================================= */
  var root = document.getElementById('hv2');
  if (!root) return;

  var docEl = document.documentElement;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || root).querySelectorAll(sel)); };
  var clamp = function (v, a, b) { return Math.min(Math.max(v, a), b); };

  docEl.classList.add('hv2-js');

  /* Fixed site header → expose its height for offsets */
  var siteHeader = document.getElementById('header');
  var headerH = function () { return siteHeader ? siteHeader.offsetHeight : 0; };
  var syncHeader = function () { docEl.style.setProperty('--hv2-header', headerH() + 'px'); };
  syncHeader();
  window.addEventListener('resize', syncHeader);

  var scrollState = { y: window.scrollY, velocity: 0 };
  var scrollListeners = [];
  var onScroll = function (fn) { scrollListeners.push(fn); };
  var emitScroll = function () { for (var i = 0; i < scrollListeners.length; i++) scrollListeners[i](scrollState); };

  /* Scroll-linked effects are plain functions of an element's LIVE position. (A scroll library caches positions, and
     they went stale — page jumped, effects froze — whenever the page height changed, e.g. after "Show all services".) */
  var fx = [];
  var runFx = function () { for (var i = 0; i < fx.length; i++) fx[i](); };
  var addFx = function (fn) { fx.push(fn); fn(); };
  onScroll(runFx);
  window.addEventListener('resize', runFx);
  window.addEventListener('load', runFx);

  /* == PAGE LOADER ======================================================== */
  /* The overlay is pure CSS (html.hv2-loading, set by an inline <head> script) so it
     paints on the first frame. Here we drive its real progress (scripts → fonts → load)
     and lift the curtain; page reveals start as the curtain moves. */
  var introQueue = [];
  var afterIntro = function (fn) { if (introQueue === true) fn(); else introQueue.push(fn); };
  var finishIntro = function () { if (introQueue === true) return; var q = introQueue; introQueue = true; q.forEach(function (fn) { fn(); }); };
  (function () {
    if (!docEl.classList.contains('hv2-loading')) { finishIntro(); return; } /* reduced motion / inline flag missing */
    var t0 = performance.now(), MIN = 450, SOFT = 1400, MAX = 2200;
    var fontsOk = !(document.fonts && document.fonts.load), loadOk = document.readyState === 'complete';
    if (!fontsOk) {
      Promise.all(['800 24px "League Spartan"', '400 16px "Nunito Sans"', '700 16px "Nunito Sans"'].map(function (f) { return document.fonts.load(f).catch(function () {}); }))
        .then(function () { fontsOk = true; });
    }
    window.addEventListener('load', function () { loadOk = true; });
    var pct = 0, leaving = false;
    var paint = function (v) { docEl.style.setProperty('--hv2-p', (v / 100).toFixed(3)); docEl.style.setProperty('--hv2-n', Math.round(v)); };
    var leave = function () {
      if (leaving) return; leaving = true;
      paint(100);
      docEl.classList.add('hv2-leaving');
      setTimeout(finishIntro, 450);
      setTimeout(function () { docEl.classList.remove('hv2-loading', 'hv2-leaving'); docEl.style.removeProperty('--hv2-p'); docEl.style.removeProperty('--hv2-n'); }, 1300);
    };
    (function tick() {
      if (leaving) return;
      var el = performance.now() - t0;
      var done = (el >= MIN && fontsOk && (loadOk || el >= SOFT)) || el >= MAX;
      var stage = 30 + (fontsOk ? 40 : 0) + (loadOk ? 25 : 0);             /* real signals */
      var goal = done ? 100 : Math.min(94, Math.max(stage, 10 + (el / MAX) * 80)); /* never looks stuck */
      pct += Math.max((goal - pct) * (done ? 0.2 : 0.09), done ? 0.8 : 0);
      if (pct > goal) pct = goal;
      paint(pct);
      if (pct >= 99.5) { leave(); return; }
      requestAnimationFrame(tick);
    })();
    setTimeout(leave, 6000); /* safety: also covers throttled background tabs */
  })();

  /* == SMOOTH SCROLL (Lenis) ============================================= */
  var lenis = null;
  var isScrollable = function (node) {
    while (node && node !== document.body && node.nodeType === 1) {
      var s = window.getComputedStyle(node);
      if (/(auto|scroll)/.test(s.overflowY) && node.scrollHeight > node.clientHeight) return true;
      node = node.parentElement;
    }
    return false;
  };
  if (window.Lenis && finePointer && !reduceMotion) {
    lenis = new window.Lenis({
      duration: 1.1, smoothWheel: true,
      easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); },
      prevent: function (node) {
        return !!(node.closest && node.closest('.modal, [data-lenis-prevent], .iti__dropdown-content, .iti__country-list, .swal2-container, #navmenu')) || isScrollable(node);
      }
    });
    lenis.on('scroll', function (e) {
      scrollState.y = e.scroll; scrollState.velocity = e.velocity || 0;
      emitScroll();
    });
    (function raf(t) { lenis.raf(t); requestAnimationFrame(raf); })(performance.now());
    document.addEventListener('show.bs.modal', function () { lenis.stop(); });
    document.addEventListener('hidden.bs.modal', function () { lenis.start(); });
  } else {
    var lastY = window.scrollY, ticking = false;
    window.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () {
        scrollState.y = window.scrollY; scrollState.velocity = scrollState.y - lastY; lastY = scrollState.y;
        emitScroll(); ticking = false;
      });
    }, { passive: true });
  }

  document.addEventListener('click', function (e) {
    var trigger = e.target.closest && e.target.closest('[data-scrollto]');
    if (!trigger || !root.contains(trigger)) return;
    var target = document.querySelector(trigger.getAttribute('data-scrollto'));
    if (!target) return;
    e.preventDefault();
    var focusFirst = function () { var f = target.querySelector('input:not([type=hidden]), textarea'); if (f) f.focus({ preventScroll: true }); };
    if (lenis) lenis.scrollTo(target, { offset: -(headerH() + 20), duration: 1.3, onComplete: focusFirst });
    else { target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' }); setTimeout(focusFirst, 700); }
  });

  /* == SPLIT TEXT ======================================================== */
  var wordIndex;
  var splitNode = function (node) {
    Array.prototype.slice.call(node.childNodes).forEach(function (child) {
      if (child.nodeType === 3) {
        var frag = document.createDocumentFragment();
        child.textContent.split(/(\s+)/).forEach(function (part) {
          if (!part) return;
          if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(' ')); return; }
          var outer = document.createElement('span'), inner = document.createElement('span');
          outer.className = 'hv2-w'; outer.setAttribute('aria-hidden', 'true');
          inner.textContent = part; inner.style.setProperty('--i', wordIndex++);
          outer.appendChild(inner); frag.appendChild(outer);
        });
        child.parentNode.replaceChild(frag, child);
      } else if (child.nodeType === 1) { splitNode(child); }
    });
  };
  /* headings in the first screen are split now (their animation is visible at once); the rest are split when the
     browser is idle, before they scroll into view — keeps the start-up main thread short */
  var splitOne = function (el) {
    if (el.__split) return; el.__split = true;
    wordIndex = 0;
    el.setAttribute('aria-label', el.textContent.replace(/\s+/g, ' ').trim());
    splitNode(el);
  };
  var splitLater = [];
  $$('[data-split]').forEach(function (el) {
    if (el.getBoundingClientRect().top < window.innerHeight * 1.2) splitOne(el); else splitLater.push(el);
  });
  if (splitLater.length) {
    var idle = window.requestIdleCallback || function (fn) { return setTimeout(function () { fn({ timeRemaining: function () { return 8; } }); }, 200); };
    var splitChunk = function (dl) {
      while (splitLater.length && dl.timeRemaining() > 2) splitOne(splitLater.shift());
      if (splitLater.length) idle(splitChunk, { timeout: 1500 });
    };
    idle(splitChunk, { timeout: 1500 });
  }

  /* == REVEAL (GSAP ScrollTrigger.batch, IO fallback) ==================== */
  var revealEls = $$('[data-reveal], [data-split]');
  /* Fly-in tiles sit far outside the viewport before they animate, so an IntersectionObserver could never see
     them. Their (untransformed) container is observed instead and releases all of its fly-in children together. */
  var flyEls = $$('[data-reveal="fly-left"], [data-reveal="fly-right"]'), flyGroups = [];
  flyEls.forEach(function (el) { var g = el.parentElement; if (flyGroups.indexOf(g) < 0) { flyGroups.push(g); g.__flyKids = []; } g.__flyKids.push(el); });
  /* One-shot "scrolled into view" callbacks (counters, rings, process line).
     Works whether registered before or after the intro finishes: on repeat visits
     and with reduced motion the intro ends instantly, so late registrations are
     observed straight away instead of being dropped. */
  var inView = [], ioOnce = null;
  var fireIn = function (h) { if (!h.done) { h.done = true; h.fn(); } };
  if (!reduceMotion && 'IntersectionObserver' in window) {
    ioOnce = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        inView.forEach(function (h) { if (h.el === en.target) fireIn(h); });
        ioOnce.unobserve(en.target);
      });
    }, { threshold: 0.4 });
  }
  /* Starts immediately (these elements sit below the fold, so they never need
     to wait for the intro curtain). Reduced motion / no IO → fire right away. */
  var whenInView = function (el, fn) {
    var h = { el: el, fn: fn, done: false };
    inView.push(h);
    if (!ioOnce) fireIn(h); else ioOnce.observe(el);
  };
  var showAll = function () { revealEls.forEach(function (el) { el.classList.add('is-in'); }); };

  afterIntro(function () {
    /* IntersectionObserver (not ScrollTrigger.batch): it also fires when the visitor lands mid-page —
       anchor links, restored scroll position after a reload, or a programmatic jump. */
    if (reduceMotion || !('IntersectionObserver' in window)) { showAll(); }
    else {
      var io = new IntersectionObserver(function (entries) {
        var ready = entries.filter(function (en) { return en.isIntersecting; });
        ready.forEach(function (en, i) {
          var el = en.target; io.unobserve(el);
          setTimeout(function () { el.classList.add('is-in'); }, i * 80);
        });
      }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
      revealEls.forEach(function (el) { if (flyEls.indexOf(el) < 0) io.observe(el); });
      var ioFly = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (!en.isIntersecting) return;
          ioFly.unobserve(en.target);
          en.target.__flyKids.forEach(function (k) { k.classList.add('is-in'); });
        });
      }, { rootMargin: '0px 0px -5% 0px', threshold: 0.3 });
      flyGroups.forEach(function (g) { ioFly.observe(g); });
    }
    /* sweeper: anything the visitor has already scrolled past (fast flick, anchor jump) is simply shown */
    var lastSweep = 0;
    var sweepReveals = function () {
      revealEls.forEach(function (el) { if (!el.classList.contains('is-in') && el.getBoundingClientRect().bottom < 0) el.classList.add('is-in'); });
    };
    if (!reduceMotion) {
      onScroll(function () { var n = performance.now(); if (n - lastSweep > 200) { lastSweep = n; sweepReveals(); } });
      sweepReveals();
    }

  });
  /* Safety net: never leave content invisible */
  setTimeout(function () {
    revealEls.forEach(function (el) {
      var r = el.getBoundingClientRect();
      if (!el.classList.contains('is-in') && r.top < window.innerHeight && r.bottom > 0) el.classList.add('is-in');
    });
  }, 5000);

  /* == PROGRESS · CURSOR ================================================= */
  var progress = root.querySelector('.hv2-progress');
  var updateProgress = function () {
    var max = docEl.scrollHeight - window.innerHeight;
    progress.style.setProperty('--p', max > 0 ? clamp(scrollState.y / max, 0, 1) : 0);
  };
  onScroll(updateProgress); updateProgress();

  /* == BACK TO TOP — ring shows page progress; arrow "launches" on click ==== */
  var topBtn = document.getElementById('hv2-top');
  if (topBtn) {
    var ringBar = topBtn.querySelector('.bar'), CIRC = 2 * Math.PI * 21;
    ringBar.style.strokeDasharray = CIRC.toFixed(2);
    var updateTop = function () {
      var max = docEl.scrollHeight - window.innerHeight, p = max > 0 ? clamp(scrollState.y / max, 0, 1) : 0;
      ringBar.style.strokeDashoffset = (CIRC * (1 - p)).toFixed(2);
      topBtn.classList.toggle('is-visible', scrollState.y > window.innerHeight * 0.6);
    };
    onScroll(updateTop); updateTop();
    topBtn.addEventListener('click', function () {
      if (scrollState.y < 8) return;
      topBtn.classList.remove('is-launching'); void topBtn.offsetWidth; topBtn.classList.add('is-launching');
      setTimeout(function () { topBtn.classList.remove('is-launching'); }, 1000);
      var dur = clamp(scrollState.y / 3200, 0.9, 2.4);
      if (lenis) lenis.scrollTo(0, { duration: dur, easing: function (t) { return 1 - Math.pow(1 - t, 4); } });
      else window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
  }

  if (finePointer && !reduceMotion) {
    var cursor = root.querySelector('.hv2-cursor');
    var dot = cursor.querySelector('.hv2-cursor-dot'), ring = cursor.querySelector('.hv2-cursor-ring');
    var mx = -100, my = -100, rx = -100, ry = -100;
    docEl.classList.add('hv2-has-cursor');
    /* the ring follows the pointer only while it is catching up (the loop idles when the cursor rests) */
    var cRaf = 0;
    var loop = function () {
      rx += (mx - rx) * 0.18; ry += (my - ry) * 0.18;
      ring.style.transform = 'translate3d(' + rx + 'px,' + ry + 'px,0)';
      cRaf = (Math.abs(mx - rx) > 0.3 || Math.abs(my - ry) > 0.3) ? requestAnimationFrame(loop) : 0;
    };
    window.addEventListener('mousemove', function (e) { mx = e.clientX; my = e.clientY; dot.style.transform = 'translate3d(' + mx + 'px,' + my + 'px,0)'; if (!cRaf) cRaf = requestAnimationFrame(loop); }, { passive: true });
    var hoverSel = 'a, button, [data-tilt], summary, input, textarea';
    root.addEventListener('mouseover', function (e) { if (e.target.closest(hoverSel)) cursor.classList.add('is-hover'); });
    root.addEventListener('mouseout', function (e) { if (e.target.closest(hoverSel)) cursor.classList.remove('is-hover'); });
  }

  /* == COUNTERS & RINGS ================================================== */
  var fmt = function (n, dec) { return n.toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec }); };
  $$('[data-count]').forEach(function (el) {
    var target = parseFloat(el.getAttribute('data-count')) || 0;
    var suffix = el.getAttribute('data-suffix') || '';
    var dec = (String(el.getAttribute('data-count')).split('.')[1] || '').length;
    var prefix = el.getAttribute('data-prefix') || '';
    var text = function (v) { return prefix + fmt(v, dec) + (suffix ? '<sup>' + suffix + '</sup>' : ''); };
    /* A hidden copy of the FINAL value reserves the full width/height, so the layout never shifts while digits tick up */
    var render = function (v) { el.innerHTML = '<span class="hv2-c-ghost" aria-hidden="true">' + text(target) + '</span><span class="hv2-c-live">' + text(v) + '</span>'; };
    el.setAttribute('aria-label', prefix + fmt(target, dec) + suffix);
    if (reduceMotion) { render(target); return; }        /* final value immediately */
    render(0);
    whenInView(el, function () {
      var start = null, dur = 1800;
      (function step(ts) {
        if (!start) start = ts;
        var t = clamp((ts - start) / dur, 0, 1), v = target * (1 - Math.pow(1 - t, 4));
        render(t < 1 ? (dec ? v : Math.round(v)) : target);
        if (t < 1) requestAnimationFrame(step);
      })(performance.now());
    });
  });
  $$('[data-ring]').forEach(function (el) {
    var bar = el.querySelector('.hv2-ring-bar'), circ = 2 * Math.PI * 52;
    whenInView(el, function () { bar.style.strokeDashoffset = circ * (1 - (parseFloat(el.getAttribute('data-ring')) || 0) / 100); });
  });

  /* == IMAGE PLACEHOLDERS ================================================ */
  $$('.hv2-media img').forEach(function (img) {
    var done = function () { img.parentNode.classList.add('is-loaded'); };
    if (img.complete && img.naturalWidth) done();
    else { img.addEventListener('load', done, { once: true }); img.addEventListener('error', done, { once: true }); }
  });

  /* == TILT · MAGNETIC · SPOTLIGHT ======================================= */
  if (finePointer && !reduceMotion) {
    $$('[data-tilt]').forEach(function (el) {
      var max = parseFloat(el.getAttribute('data-tilt-max')) || 6, glare = el.querySelector(':scope > .hv2-glare');
      var frame = null, px = 0.5, py = 0.5;
      var apply = function () {
        frame = null;
        el.style.transform = 'perspective(1000px) rotateX(' + ((0.5 - py) * max * 2).toFixed(2) + 'deg) rotateY(' + ((px - 0.5) * max * 2).toFixed(2) + 'deg)';
        if (glare) { el.style.setProperty('--gx', (px * 100) + '%'); el.style.setProperty('--gy', (py * 100) + '%'); }
      };
      el.addEventListener('mousemove', function (e) { var r = el.getBoundingClientRect(); px = (e.clientX - r.left) / r.width; py = (e.clientY - r.top) / r.height; if (!frame) frame = requestAnimationFrame(apply); });
      el.addEventListener('mouseleave', function () { if (frame) cancelAnimationFrame(frame); frame = null; el.style.transform = ''; });
    });
    $$('[data-magnetic]').forEach(function (btn) {
      btn.addEventListener('mousemove', function (e) {
        var r = btn.getBoundingClientRect();
        btn.style.setProperty('--mx', ((e.clientX - r.left - r.width / 2) * 0.2).toFixed(1) + 'px');
        btn.style.setProperty('--my', ((e.clientY - r.top - r.height / 2) * 0.3).toFixed(1) + 'px');
      });
      btn.addEventListener('mouseleave', function () { btn.style.setProperty('--mx', '0px'); btn.style.setProperty('--my', '0px'); });
    });
    $$('[data-spotlight]').forEach(function (grid) {
      grid.addEventListener('mousemove', function (e) {
        $$('.hv2-tile, .hv2-brand--lg', grid).forEach(function (t) { var r = t.getBoundingClientRect(); t.style.setProperty('--sx', (e.clientX - r.left) + 'px'); t.style.setProperty('--sy', (e.clientY - r.top) + 'px'); });
      });
    });
  }

  /* == HERO: kinetic type, parallax, scroll-out ========================== */
  var hero = document.getElementById('hv2-hero');
  if (hero && finePointer && !reduceMotion) {
    var layers = $$('[data-depth]', hero);
    var kWords = $$('[data-kinetic] .hv2-w > span', hero);
    var kFrame = null, kx = 0, ky = 0;
    var kinetic = function () {
      kFrame = null;
      kWords.forEach(function (w) {
        var r = w.getBoundingClientRect(), cx = r.left + r.width / 2, cy = r.top + r.height / 2;
        var d = Math.hypot(kx - cx, ky - cy), f = clamp(1 - d / 520, 0, 1);
        w.style.setProperty('--wg', Math.round(700 + f * 200));
      });
    };
    hero.addEventListener('mousemove', function (e) {
      var cx = e.clientX / window.innerWidth - 0.5, cy = e.clientY / window.innerHeight - 0.5;
      layers.forEach(function (l) {
        var dd = parseFloat(l.getAttribute('data-depth')) || 0.3;
        l.style.setProperty('--px', (-cx * dd * 60).toFixed(1) + 'px');
        l.style.setProperty('--py', (-cy * dd * 60).toFixed(1) + 'px');
      });
      kx = e.clientX; ky = e.clientY;
      if (!kFrame) kFrame = requestAnimationFrame(kinetic);
    });
    hero.addEventListener('mouseleave', function () { kWords.forEach(function (w) { w.style.removeProperty('--wg'); }); });
  }
  if (hero && !reduceMotion) {
    var glass = hero.querySelector('.hv2-glass3d');
    if (glass) addFx(function () { var p = clamp(scrollState.y / Math.max(1, hero.offsetHeight), 0, 1); glass.style.transform = 'translate3d(0,' + (p * 160).toFixed(1) + 'px,0)'; });
  }

  /* == HOVER IMAGE DISTORTION (SVG displacement) ========================= */
  var disp = document.querySelector('#hv2-distort feDisplacementMap');
  if (disp && finePointer && !reduceMotion) {
    var dAnim = null;
    var pulse = function (el) {
      cancelAnimationFrame(dAnim);
      var start = null;
      el.classList.add('is-distorting');
      (function step(ts) {
        if (!start) start = ts;
        var t = clamp((ts - start) / 750, 0, 1);
        disp.setAttribute('scale', (Math.sin(t * Math.PI) * 26).toFixed(2));
        if (t < 1) dAnim = requestAnimationFrame(step); else el.classList.remove('is-distorting');
      })(performance.now());
    };
    $$('[data-distort]').forEach(function (el) {
      el.addEventListener('mouseenter', function () {
        $$('[data-distort].is-distorting').forEach(function (o) { if (o !== el) o.classList.remove('is-distorting'); });
        pulse(el);
      });
    });
  }

  /* == READ MORE · SHOW MORE ============================================= */
  /* [data-showmore="N"] → children from index N on are tagged .hv2-sm-extra (hidden until .is-all) */
  $$('[data-showmore]').forEach(function (box) {
    var n = parseInt(box.getAttribute('data-showmore'), 10) || 0;
    Array.prototype.slice.call(box.children).forEach(function (c, i) { if (i >= n && !c.hasAttribute('data-keep')) { c.classList.add('hv2-sm-extra'); c.style.setProperty('--k', i - n); } });
  });
  root.addEventListener('click', function (e) {
    var more = e.target.closest('.hv2-more-btn');
    if (more) {
      var rm = more.closest('[data-readmore]'), openRm = !rm.classList.contains('is-open');
      rm.classList.toggle('is-open', openRm);
      more.setAttribute('aria-expanded', openRm ? 'true' : 'false');
      more.querySelector('span').textContent = openRm ? 'Read less' : 'Read more';
      runFx();
      return;
    }
    var sm = e.target.closest('[data-showmore-btn]');
    if (!sm) return;
    var sel = sm.getAttribute('data-showmore-btn'), box = document.querySelector(sel);
    if (!box) return;
    var all = !box.classList.contains('is-all');
    box.classList.toggle('is-all', all);
    if (box.id === 'hv2-stack' && typeof buildStack === 'function') buildStack();
    $$('[data-showmore-btn="' + sel + '"]').forEach(function (b) {
      b.setAttribute('aria-expanded', all ? 'true' : 'false');
      var c = b.querySelector('[data-label-closed]'), o = b.querySelector('[data-label-open]');
      if (c) c.hidden = all; if (o) o.hidden = !all;
    });
    var goTo = function (el, offset) {
      if (lenis) lenis.scrollTo(el, { offset: offset, duration: 1.1 });
      else el.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
    };
    /* collapsing shortens the page a lot: jump (no animation) so the toggle stays under the pointer,
       and only then re-run the scroll-linked effects */
    var settle = function () { requestAnimationFrame(runFx); };
    if (all) {
      $$('[data-reveal]', box).forEach(function (el) { el.classList.add('is-in'); });
      if (sm.hasAttribute('data-scroll-open')) setTimeout(function () { goTo(box, -(headerH() + 24)); }, 60);
      settle();
    } else {
      var backSel = sm.getAttribute('data-collapse-scroll'), back = backSel ? document.querySelector(backSel) : null;
      var target = back || sm, off = back ? -(headerH() + 24) : -Math.max(0, (window.innerHeight - sm.offsetHeight) / 2);
      /* plain native jump: Lenis syncs to native scroll, whereas lenis.scrollTo() right after a big layout shrink snapped back */
      window.scrollTo(0, Math.max(0, target.getBoundingClientRect().top + window.pageYOffset + off));
      settle();
    }
  });

  /* == CASE STUDIES — looping auto-slide carousel (Swiper) =================== */
  /* No pinning / scroll-jacking. Interval comes from data-autoplay (ms) on .hv2-cs-viewport. */
  var cs = document.getElementById('hv2-cases');
  if (cs) {
    var vp = cs.querySelector('.hv2-cs-viewport'), slidesN = $$('.hv2-case', cs).length;
    var prevBtn = cs.querySelector('.hv2-cs-prev'), nextBtn = cs.querySelector('.hv2-cs-next'), togBtn = cs.querySelector('.hv2-cs-toggle');
    var curEl = cs.querySelector('.hv2-cs-cur'), barEl = cs.querySelector('.hv2-cs-progress i');
    var AUTO = parseInt(vp.getAttribute('data-autoplay'), 10) || 1500;
    var pad2 = function (n) { return (n < 10 ? '0' : '') + n; };
    var setPos = function (i) { curEl.textContent = pad2(i + 1); barEl.style.transform = 'scaleX(' + ((i + 1) / slidesN).toFixed(3) + ')'; };
    if (window.Swiper) {
      cs.classList.add('is-swiper');
      var csSwiper = new window.Swiper(vp, {
        wrapperClass: 'hv2-cs-track', slideClass: 'hv2-case',
        slidesPerView: 'auto', centeredSlides: true, spaceBetween: 28, loop: true, speed: 750, grabCursor: true,
        autoplay: reduceMotion ? false : { delay: Math.max(AUTO - 750, 300), disableOnInteraction: false, pauseOnMouseEnter: true }, /* AUTO = full cycle: pause + 750ms slide */
        keyboard: { enabled: true, onlyInViewport: true },
        navigation: { prevEl: prevBtn, nextEl: nextBtn },
        a11y: { enabled: true, prevSlideMessage: 'Previous case study', nextSlideMessage: 'Next case study' },
        on: { slideChange: function (s) { setPos(s.realIndex); } }
      });
      setPos(csSwiper.realIndex);
      var csManual = reduceMotion, csInView = true;
      var csPaintToggle = function () {
        togBtn.setAttribute('aria-pressed', csManual ? 'true' : 'false');
        togBtn.setAttribute('aria-label', csManual ? 'Play auto-play' : 'Pause auto-play');
        togBtn.querySelector('i').className = 'bi ' + (csManual ? 'bi-play-fill' : 'bi-pause-fill');
      };
      var csSync = function () {
        if (!csSwiper.autoplay) return;
        var want = !csManual && csInView && !document.hidden;
        if (want && !csSwiper.autoplay.running) csSwiper.autoplay.start();
        else if (!want && csSwiper.autoplay.running) csSwiper.autoplay.stop();
      };
      togBtn.addEventListener('click', function () { csManual = !csManual; csPaintToggle(); csSync(); });
      /* watchdog: if a browser drops Swiper's "slide finished" event the autoplay can stall — nudge it back */
      var csHover = false, csLast = csSwiper.realIndex, csStuck = 0;
      vp.addEventListener('mouseenter', function () { csHover = true; });
      vp.addEventListener('mouseleave', function () { csHover = false; });
      setInterval(function () {
        var ap = csSwiper.autoplay;
        if (!ap || !ap.running || csHover || document.hidden || !csInView) { csStuck = 0; csLast = csSwiper.realIndex; return; }
        if (csSwiper.realIndex === csLast) csStuck++; else { csStuck = 0; csLast = csSwiper.realIndex; }
        if (csStuck >= 3) { csStuck = 0; ap.paused = false; if (typeof ap.resume === 'function') ap.resume(); else { ap.stop(); ap.start(); } }
      }, Math.max(AUTO, 1500));
      document.addEventListener('visibilitychange', csSync);
      if ('IntersectionObserver' in window) new IntersectionObserver(function (en) { csInView = en[0].isIntersecting; csSync(); }, { threshold: 0.15 }).observe(vp);
      csPaintToggle();
    } else {
      /* fallback: plain scrolling row with working arrows */
      togBtn.hidden = true;
      var cards = $$('.hv2-case', cs);
      var stepW = function () { return cards.length > 1 ? cards[1].offsetLeft - cards[0].offsetLeft : vp.clientWidth; };
      var slideBy = function (dir) { vp.scrollBy({ left: dir * stepW(), behavior: reduceMotion ? 'auto' : 'smooth' }); };
      prevBtn.addEventListener('click', function () { slideBy(-1); });
      nextBtn.addEventListener('click', function () { slideBy(1); });
      vp.addEventListener('scroll', function () { setPos(clamp(Math.round(vp.scrollLeft / stepW()), 0, slidesN - 1)); }, { passive: true });
    }
  }

  /* == SERVICES STACK — earlier cards shrink + dim as the next one slides over ==
        Computed from each card's LIVE position (the cards are position:sticky). "Show all" only changes which
        cards are visible, then buildStack() re-measures. */
  var stackCards = $$('.hv2-stack-card'), stackVis = [], stackTops = [];
  var measureStack = function () {
    stackVis = stackCards.filter(function (c) { return c.offsetParent !== null; });
    stackTops = stackVis.map(function (c) { return parseFloat(window.getComputedStyle(c).top) || 120; });
  };
  var paintStack = function () {
    if (reduceMotion || !stackVis.length) return;
    var from = window.innerHeight * 0.92, i, tops = [];
    /* read all positions first, then write (no forced reflow inside the loop) */
    for (i = 1; i < stackVis.length; i++) tops[i] = stackVis[i].getBoundingClientRect().top;
    for (i = 0; i < stackVis.length - 1; i++) {
      var p = clamp((from - tops[i + 1]) / Math.max(1, from - stackTops[i + 1]), 0, 1);
      stackVis[i].style.setProperty('--dim', (p * 0.38).toFixed(3));
      stackVis[i].style.transform = p > 0 ? 'scale(' + (1 - p * 0.06).toFixed(4) + ')' : '';
    }
    var last = stackVis[stackVis.length - 1];
    last.style.setProperty('--dim', '0'); last.style.transform = '';
  };
  var buildStack = function () {
    stackCards.forEach(function (c) { c.style.removeProperty('--dim'); c.style.transform = ''; });
    measureStack(); paintStack();
  };
  if (stackCards.length > 1) { measureStack(); addFx(paintStack); window.addEventListener('resize', measureStack); }

  /* == TESTIMONIAL SLIDER — looping auto-slide + controls ================== */
  /* Interval comes from data-autoplay (ms) on .hv2-q-stage. Pauses on hover/focus, off-screen,
     hidden tab, or with the pause button. */
  var stage = root.querySelector('.hv2-q-stage');
  if (stage) {
    var quotes = $$('.hv2-q', stage), dots = $$('.hv2-q-dots button');
    var qPrev = root.querySelector('.hv2-q-prev'), qNext = root.querySelector('.hv2-q-next'), qTog = root.querySelector('.hv2-q-toggle');
    var qCur = root.querySelector('.hv2-q-cur'), qBar = stage.querySelector('.hv2-q-timer i');
    var QMS = parseInt(stage.getAttribute('data-autoplay'), 10) || 1500;
    var qi = 0, qTimer = null, qUser = reduceMotion, qHover = false, qSeen = true;
    var qRunning = function () { return !qUser && !qHover && qSeen && !document.hidden && !reduceMotion; };
    var qBarReset = function (on) {
      if (!qBar) return;
      qBar.style.animation = 'none'; void qBar.offsetWidth;
      qBar.style.setProperty('--q-ms', QMS + 'ms');
      qBar.style.animation = on ? '' : 'none';
    };
    var qSchedule = function () {
      clearTimeout(qTimer);
      var on = qRunning();
      qBarReset(on);
      if (on) qTimer = setTimeout(function () { showQ(qi + 1); }, QMS);
    };
    var showQ = function (i) {
      qi = (i + quotes.length) % quotes.length;
      quotes.forEach(function (q, k) { q.classList.toggle('is-on', k === qi); q.setAttribute('aria-hidden', k === qi ? 'false' : 'true'); });
      dots.forEach(function (d, k) { d.classList.toggle('is-on', k === qi); d.setAttribute('aria-current', k === qi ? 'true' : 'false'); });
      if (qCur) qCur.textContent = (qi + 1 < 10 ? '0' : '') + (qi + 1);
      qSchedule();
    };
    var qPaintToggle = function () {
      if (!qTog) return;
      qTog.setAttribute('aria-pressed', qUser ? 'true' : 'false');
      qTog.setAttribute('aria-label', qUser ? 'Play auto-play' : 'Pause auto-play');
      qTog.querySelector('i').className = 'bi ' + (qUser ? 'bi-play-fill' : 'bi-pause-fill');
    };
    dots.forEach(function (d, k) { d.addEventListener('click', function () { showQ(k); }); });
    if (qPrev) qPrev.addEventListener('click', function () { showQ(qi - 1); });
    if (qNext) qNext.addEventListener('click', function () { showQ(qi + 1); });
    if (qTog) qTog.addEventListener('click', function () { qUser = !qUser; qPaintToggle(); qSchedule(); });
    [stage, root.querySelector('.hv2-q-ctrl'), root.querySelector('.hv2-q-dots')].forEach(function (el) {
      if (!el) return;
      el.addEventListener('mouseenter', function () { qHover = true; qSchedule(); });
      el.addEventListener('mouseleave', function () { qHover = false; qSchedule(); });
      el.addEventListener('focusin', function () { qHover = true; qSchedule(); });
      el.addEventListener('focusout', function () { qHover = false; qSchedule(); });
    });
    document.addEventListener('visibilitychange', qSchedule);
    if ('IntersectionObserver' in window) new IntersectionObserver(function (en) { qSeen = en[0].isIntersecting; qSchedule(); }, { threshold: 0.2 }).observe(stage);
    var sx = null;
    stage.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
    stage.addEventListener('touchend', function (e) { if (sx === null) return; var dx = e.changedTouches[0].clientX - sx; if (Math.abs(dx) > 40) showQ(qi + (dx < 0 ? 1 : -1)); sx = null; });
    qPaintToggle(); showQ(0);
  }

  /* == PROCESS CONNECTORS — short bars that join the step badges (never drawn across them) == */
  var steps = root.querySelector('[data-steps]');
  if (steps) {
    var links = $$('.hv2-step-link', steps), stepEls = $$('.hv2-step', steps);
    var setLine = function (p) {
      links.forEach(function (l, k) { l.style.setProperty('--f', clamp(p * links.length - k, 0, 1).toFixed(3)); });
      stepEls.forEach(function (s, i) { s.classList.toggle('is-hit', p > 0 && p >= (i / (stepEls.length - 1)) - 0.02); });
    };
    /* progress = how far the block has travelled from "top at 75% of the viewport" to "bottom at 55%" */
    var updateProcess = function () {
      var r = steps.getBoundingClientRect(), vh = window.innerHeight;
      setLine(clamp((vh * 0.75 - r.top) / (vh * 0.2 + r.height), 0, 1));
    };
    if (reduceMotion) whenInView(steps, function () { setLine(1); }); else addFx(updateProcess);
  }

  /* == MARQUEE / BAND VELOCITY =========================================== */
  if (!reduceMotion) {
    var skewEls = $$('.hv2-marquee-wrap, .hv2-band'), velTracks = $$('.hv2-marquee-track, .hv2-band-track'), anims = null, settle = null;
    var getAnims = function () { if (anims) return anims; anims = []; velTracks.forEach(function (t) { if (t.getAnimations) anims = anims.concat(t.getAnimations()); }); return anims; };
    onScroll(function (s) {
      var v = clamp(s.velocity || 0, -60, 60);
      skewEls.forEach(function (el) { el.style.setProperty('--skew', (-v * 0.1).toFixed(2) + 'deg'); });
      getAnims().forEach(function (a) { a.playbackRate = 1 + Math.min(Math.abs(v) * 0.08, 4); });
      clearTimeout(settle);
      settle = setTimeout(function () { skewEls.forEach(function (el) { el.style.setProperty('--skew', '0deg'); }); getAnims().forEach(function (a) { a.playbackRate = 1; }); }, 140);
    });
  }

  /* == CTA ZOOM · PARALLAX (live-position driven) ========================= */
  if (!reduceMotion) {
    var cta = document.getElementById('hv2-cta');
    var travel = function (el) { var r = el.getBoundingClientRect(), vh = window.innerHeight; return clamp((vh - r.top) / (vh + r.height), 0, 1); };
    /* one pass: read every position first, then write every style (avoids a forced reflow per element) */
    var plx = $$('[data-parallax]').map(function (el) { return { el: el, sp: parseFloat(el.getAttribute('data-parallax')) || 0, host: el.parentNode }; });
    if (cta || plx.length) addFx(function () {
      var tc = cta ? travel(cta) : 0, tv = plx.map(function (o) { return travel(o.host); });
      if (cta) cta.style.setProperty('--z', (1.22 - tc * 0.22).toFixed(3));
      plx.forEach(function (o, k) { o.el.style.transform = 'translate3d(0,' + (-o.sp * 400 + tv[k] * 2 * o.sp * 400).toFixed(1) + 'px,0)'; });
    });
  }

  /* == ENQUIRY FORM (existing endpoint + contract) ======================= */
  var form = document.getElementById('hv2-enquiry-form');
  if (form) {
    var phone = document.getElementById('hv2-phone'), fullPhone = document.getElementById('hv2-fullphone');
    var loader = form.parentNode.querySelector('.hv2-form-loader'), iti = null;
    if (phone && window.intlTelInput) {
      iti = window.intlTelInput(phone, { initialCountry: 'ae', separateDialCode: true });
      var syncPhone = function () { fullPhone.value = iti.getNumber(); };
      ['keyup', 'change', 'countrychange'].forEach(function (ev) { phone.addEventListener(ev, syncPhone); });
    }
    var toast = function (icon, title) {
      if (window.Toast && window.Toast.fire) window.Toast.fire({ icon: icon, title: title });
      else if (window.Swal) window.Swal.fire({ icon: icon, title: title, toast: true, position: 'top-end', timer: 5000, showConfirmButton: false });
    };
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var email = (document.getElementById('hv2-email').value || '').trim();
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { toast('warning', 'Please Enter valid Email address'); return; }
      if (iti) fullPhone.value = iti.getNumber();
      loader.classList.add('is-on');
      fetch(form.getAttribute('action'), { method: 'POST', body: new FormData(form), credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          loader.classList.remove('is-on');
          data = data || {};
          if (data.success === 'success') {
            toast('success', data.message);
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ event: 'formSubmitEvent' });
            setTimeout(function () { window.location.reload(); }, 1000);
          } else {
            var msg = data.message;
            if (data.errors) { var k = Object.keys(data.errors)[0]; if (k) msg = data.errors[k][0]; }
            toast('warning', msg || 'Something went wrong! Try again later.');
          }
        })
        .catch(function () { loader.classList.remove('is-on'); toast('warning', 'Something went wrong! Try again later.'); });
    });
  }

  /* == FAQ: topic rail + search + accordion + "load more" ================ */
  var faq = document.getElementById('hv2-faq');
  if (faq) {
    var PAGE = 5;
    var items = $$('.hv2-faq-item', faq), tabs = $$('.hv2-faq-cat', faq);
    var search = document.getElementById('hv2-faq-search'), clearBtn = document.getElementById('hv2-faq-clear');
    var empty = document.getElementById('hv2-faq-empty'), moreBtn = document.getElementById('hv2-faq-more');
    var moreLbl = document.getElementById('hv2-faq-more-label'), countEl = document.getElementById('hv2-faq-count');
    var faqBar = faq.querySelector('.hv2-faq-bar'), listEl = document.getElementById('hv2-faq-list');
    var st = { cat: 'general', shown: PAGE, q: '' };
    var escRx = function (s) { return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); };
    var escHtml = function (s) { return s.replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };
    var textOf = function (it) { return it.querySelector('.hv2-faq-text'); };
    var closeAll = function () { items.forEach(function (it) { it.classList.remove('is-open'); it.querySelector('.hv2-faq-q').setAttribute('aria-expanded', 'false'); }); };
    var setTab = function (cat, focus) {
      tabs.forEach(function (b) {
        var on = b.getAttribute('data-cat') === cat;
        b.classList.toggle('is-active', on); b.setAttribute('aria-selected', on ? 'true' : 'false'); b.tabIndex = on ? 0 : -1;
        if (on && focus) b.focus();
      });
    };
    var matching = function () {
      if (st.q) { var rx = new RegExp(escRx(st.q), 'i'); return items.filter(function (it) { return rx.test(it.getAttribute('data-q') + ' ' + textOf(it).textContent); }); }
      return items.filter(function (it) { return st.cat === 'all' || it.getAttribute('data-cat') === st.cat; });
    };
    /* render the current page; items from `fromIdx` on animate in with a stagger */
    var render = function (fromIdx) {
      var list = matching(), limit = st.q ? list.length : st.shown, shown = Math.min(limit, list.length);
      var rxg = st.q ? new RegExp(escRx(escHtml(st.q)), 'gi') : null;
      items.forEach(function (it) { var t = textOf(it); t.textContent = t.textContent; it.hidden = true; });
      list.forEach(function (it, i) {
        if (i >= limit) return;
        it.hidden = false;
        if (rxg) { var t = textOf(it); t.innerHTML = escHtml(t.textContent).replace(rxg, function (m) { return '<mark>' + m + '</mark>'; }); }
        if (!reduceMotion && fromIdx !== undefined && i >= fromIdx) {
          it.style.animation = 'none'; void it.offsetWidth;
          it.style.animation = ''; it.style.animationDelay = ((i - fromIdx) * 70) + 'ms';
        } else { it.style.animationDelay = ''; }
      });
      empty.classList.toggle('is-on', list.length === 0);
      countEl.textContent = st.q ? (list.length + (list.length === 1 ? ' matching question' : ' matching questions')) : ('Showing ' + shown + ' of ' + list.length + ' questions');
      faqBar.style.setProperty('--fp', list.length ? (shown / list.length).toFixed(3) : 0);
      faqBar.style.visibility = st.q ? 'hidden' : 'visible';
      var needMore = !st.q && list.length > PAGE, canLoad = limit < list.length;
      moreBtn.hidden = !needMore;
      moreBtn.classList.toggle('is-less', needMore && !canLoad);
      moreLbl.textContent = canLoad ? ('Load more questions (' + (list.length - limit) + ' more)') : 'Show fewer questions';
      runFx();
    };
    var pickCat = function (cat, focus) {
      st.cat = cat; st.shown = PAGE; st.q = ''; search.value = ''; clearBtn.classList.remove('is-on');
      setTab(cat, focus); closeAll(); render(0);
    };

    faq.addEventListener('click', function (e) {
      var q = e.target.closest('.hv2-faq-q');
      if (q) {
        var item = q.closest('.hv2-faq-item'), open = item.classList.contains('is-open');
        closeAll();
        if (!open) { item.classList.add('is-open'); q.setAttribute('aria-expanded', 'true'); }
        return;
      }
      var tab = e.target.closest('.hv2-faq-cat');
      if (tab) pickCat(tab.getAttribute('data-cat'));
    });
    moreBtn.addEventListener('click', function () {
      var total = matching().length;
      if (st.shown < total) { var from = st.shown; st.shown += PAGE; render(from); }
      else {
        st.shown = PAGE; closeAll(); render();
        if (lenis) lenis.scrollTo(listEl, { offset: -(headerH() + 120), duration: 1 }); else listEl.scrollIntoView({ block: 'start' });
      }
    });
    faq.addEventListener('keydown', function (e) {
      var tab = e.target.closest('.hv2-faq-cat');
      if (tab && /^(ArrowLeft|ArrowRight|ArrowUp|ArrowDown|Home|End)$/.test(e.key)) {
        e.preventDefault();
        var i = tabs.indexOf(tab), n = tabs.length, fwd = e.key === 'ArrowRight' || e.key === 'ArrowDown';
        var next = e.key === 'Home' ? 0 : e.key === 'End' ? n - 1 : (i + (fwd ? 1 : -1) + n) % n;
        pickCat(tabs[next].getAttribute('data-cat'), true);
        return;
      }
      var q = e.target.closest('.hv2-faq-q');
      if (q && /^(ArrowDown|ArrowUp|Home|End)$/.test(e.key)) {
        e.preventDefault();
        var vis = $$('.hv2-faq-item:not([hidden]) .hv2-faq-q', faq), j = vis.indexOf(q), m = vis.length;
        var to = e.key === 'Home' ? 0 : e.key === 'End' ? m - 1 : (j + (e.key === 'ArrowDown' ? 1 : -1) + m) % m;
        vis[to].focus();
      }
    });
    search.addEventListener('input', function () {
      st.q = search.value.trim();
      clearBtn.classList.toggle('is-on', st.q.length > 0);
      closeAll();
      if (st.q) { setTab('all'); render(); }
      else { st.shown = PAGE; setTab(st.cat); render(0); }
    });
    clearBtn.addEventListener('click', function () { search.value = ''; search.dispatchEvent(new Event('input')); search.focus(); });
    render();
  }

  if (document.fonts && document.fonts.ready) document.fonts.ready.then(runFx);
})();
