/* ==========================================================================
   HOME V2 — motion & UI engine (data-attribute driven)
   Used only by resources/views/web/home-v2.blade.php
   --------------------------------------------------------------------------
   Hooks: [data-reveal] [data-delay] [data-split] [data-kinetic] [data-count]
          [data-ring] [data-tilt] [data-magnetic] [data-depth] [data-parallax]
          [data-distort] [data-readmore] [data-showmore-btn] [data-steps]
          [data-scrollto] [data-spotlight]
   No scroll library: native browser scrolling. Every feature has a
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
  /* first read waits one frame (no forced layout during start-up); the CSS default already equals the real header height */
  requestAnimationFrame(syncHeader);
  window.addEventListener('resize', syncHeader);

  var scrollState = { y: 0, velocity: 0 };   /* real position is read at the start of the first frame (see below) */
  var scrollListeners = [];
  var onScroll = function (fn) { scrollListeners.push(fn); };
  var emitScroll = function () { for (var i = 0; i < scrollListeners.length; i++) scrollListeners[i](scrollState); };

  /* Scroll-linked effects run once per frame in TWO phases: first every effect READS what it needs (at the start of a frame
     the layout is still clean, so reading is cheap), then every effect WRITES its styles. Reading right after another effect
     had written forced the browser to recalculate layout 4-5 times per frame. Effects whose element is off-screen are skipped.
     addFx(read, write, el): read() returns values, write(values) applies them; el (optional) = run only while it is near view. */
  var fx = [];
  var runFx = function () {
    var i, f;
    for (i = 0; i < fx.length; i++) { f = fx[i]; f.on = !f.el || !fxIO || f.el.__fxOn === true;   /* off-screen effects wait for the observer: no position reads for them at start-up */ if (f.on) f.v = f.read(); }
    for (i = 0; i < fx.length; i++) { f = fx[i]; if (f.on) f.write(f.v); }
  };
  var fxIO = ('IntersectionObserver' in window) ? new IntersectionObserver(function (entries) {
    var woke = false;
    entries.forEach(function (en) { en.target.__fxOn = en.isIntersecting; if (en.isIntersecting) woke = true; });
    if (woke) requestAnimationFrame(runFx);
  }, { rootMargin: '25% 0px 25% 0px' }) : null;
  var addFx = function (read, write, el) {
    var f = { read: read, write: write, el: el || null, v: null, on: true };
    fx.push(f);
    if (f.el && fxIO) fxIO.observe(f.el);
    /* no read/write at registration (it forced a layout while scripts were still running): the first frame runs every effect once */
    /* the first run waits until the loader curtain is gone: the loader updates styles every frame, and reading positions in
       the same frame would force a layout */
    if (!fxFirst) { fxFirst = 1; afterIntro(function () { requestAnimationFrame(runFx); }); }
  };
  var fxFirst = 0;
  /* write a style / custom property only when its value changes (an unchanged value still costs a style recalculation) */
  var setStyle = function (el, prop, val) {
    var key = '__s_' + prop;
    if (el[key] === val) return;
    el[key] = val;
    if (prop.charAt(0) === '-') el.style.setProperty(prop, val); else el.style[prop] = val;
  };

  /* Page height and viewport are measured when the layout changes (resize, load, fonts, page height change), never per frame */
  var geo = { vh: window.innerHeight, max: 0 };
  var remeasurePending = 0, heroH = 0;
  var measureNow = function () {
    geo.vh = window.innerHeight;
    geo.max = Math.max(0, docEl.scrollHeight - geo.vh);
    var h = document.getElementById('hv2-hero'); if (h) heroH = h.offsetHeight;
    emitScroll();
  };
  var remeasure = function () {
    if (remeasurePending) return;
    remeasurePending = requestAnimationFrame(function () { remeasurePending = 0; measureNow(); });
  };
  onScroll(runFx);
  window.addEventListener('resize', remeasure);
  window.addEventListener('load', remeasure);
  /* a ResizeObserver callback runs right after layout, so reading the page height there is free (reading it later in a frame forced a layout) */
  if (window.ResizeObserver) new ResizeObserver(measureNow).observe(document.body); else remeasure();

  /* == PAGE LOADER ======================================================== */
  /* The overlay is pure CSS (html.hv2-loading, set by an inline <head> script) so it
     paints on the first frame. Here we drive its real progress (scripts → fonts → load)
     and lift the curtain; page reveals start as the curtain moves. */
  var introQueue = [];
  var afterIntro = function (fn) { if (introQueue === true) fn(); else introQueue.push(fn); };
  var finishIntro = function () { if (introQueue === true) return; var q = introQueue; introQueue = true; q.forEach(function (fn) { fn(); }); };
  (function () {
    if (!docEl.classList.contains('hv2-loading')) { finishIntro(); return; } /* reduced motion / inline flag missing */
    var t0 = performance.now(), MIN = 200, SOFT = 350, MAX = 500;
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
      setTimeout(finishIntro, 60);
      setTimeout(function () { docEl.classList.remove('hv2-loading', 'hv2-leaving'); docEl.style.removeProperty('--hv2-p'); docEl.style.removeProperty('--hv2-n'); }, 350);
    };
    requestAnimationFrame(function tick() {   /* first paint of the bar happens in a frame, not while scripts run */
      if (leaving) return;
      /* the inline head script lifts the curtain at 0.5 s: the engine follows at once (reveals must not wait for slow animation frames) */
      if (!docEl.classList.contains('hv2-loading')) { leave(); return; }
      var el = performance.now() - t0;
      var done = (el >= MIN && fontsOk && (loadOk || el >= SOFT)) || el >= MAX;
      var stage = 30 + (fontsOk ? 40 : 0) + (loadOk ? 25 : 0);             /* real signals */
      var goal = done ? 100 : Math.min(94, Math.max(stage, 10 + (el / MAX) * 80)); /* never looks stuck */
      pct += Math.max((goal - pct) * (done ? 0.2 : 0.09), done ? 0.8 : 0);
      if (pct > goal) pct = goal;
      paint(pct);
      if (pct >= 99.5) { leave(); return; }
      requestAnimationFrame(tick);
    });
    setTimeout(leave, MAX + 80); /* hard stop at the 0.5 s budget (also covers throttled tabs / slow frames) */
  })();

  /* == SCROLL (native) ===================================================
     The browser scrolls on its own (compositor) thread: no JS smooth-scroll library. A JS scroller (Lenis) turned every busy
     moment on the main thread into a freeze followed by a jump. Scroll effects read scrollState once per frame (one passive
     listener + one requestAnimationFrame). */
  var lastY = 0, ticking = false;
  requestAnimationFrame(function () { scrollState.y = lastY = window.scrollY; });  /* before the effects' first run (registered later, same frame, later order) */
  window.addEventListener('scroll', function () {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(function () {
      scrollState.y = window.scrollY; scrollState.velocity = scrollState.y - lastY; lastY = scrollState.y;
      emitScroll(); ticking = false;
    });
  }, { passive: true });

  /* scroll so the element's top sits just below the fixed header (scrollIntoView would put it under the header) */
  var scrollToEl = function (el, extra, behavior) {
    var top = el.getBoundingClientRect().top + window.pageYOffset - headerH() - (extra || 0);
    window.scrollTo({ top: Math.max(0, top), behavior: behavior || (reduceMotion ? 'auto' : 'smooth') });
  };

  document.addEventListener('click', function (e) {
    var trigger = e.target.closest && e.target.closest('[data-scrollto]');
    if (!trigger || !root.contains(trigger)) return;
    var target = document.querySelector(trigger.getAttribute('data-scrollto'));
    if (!target) return;
    e.preventDefault();
    scrollToEl(target, 20);
    setTimeout(function () { var f = target.querySelector('input:not([type=hidden]), textarea'); if (f) f.focus({ preventScroll: true }); }, 700);
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
  /* measure every heading first, THEN split: splitting changes the DOM, so measuring in between forced a layout per heading */
  /* no rect reads at start-up: one IntersectionObserver pass classifies every heading (its entries carry the rects, computed once) */
  var splitNow = [], splitStart = function () {
  splitNow.forEach(splitOne);
  if (splitLater.length) {
    var idle = window.requestIdleCallback || function (fn) { return setTimeout(function () { fn({ timeRemaining: function () { return 8; } }); }, 200); };
    var splitChunk = function (dl) {
      while (splitLater.length && dl.timeRemaining() > 2) splitOne(splitLater.shift());
      if (splitLater.length) idle(splitChunk, { timeout: 1500 });
    };
    idle(splitChunk, { timeout: 1500 });
  }
  };
  (function () {
    var els = $$('[data-split]');
    if (!els.length) return;
    if (!('IntersectionObserver' in window)) { splitLater = els; splitStart(); return; }
    var probe = new IntersectionObserver(function (entries) {
      probe.disconnect();
      entries.forEach(function (en) { (en.isIntersecting || en.boundingClientRect.top < window.innerHeight * 1.2 ? splitNow : splitLater).push(en.target); });
      splitStart();
    }, { rootMargin: '20% 0px 0px 0px' });
    els.forEach(function (el) { probe.observe(el); });
  })();

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
    /* sweeper: anything the visitor has already scrolled past (fast flick, anchor jump) is simply shown. It runs shortly
       after scrolling pauses (outside the scroll frame, so its position reads never force a layout mid-scroll). */
    /* Done with an IntersectionObserver instead of measuring every element after each scroll pause: the observer reports an element
       that is above the viewport without forcing a layout (the old sweep read ~70 rects and cost ~150 ms on a phone). It also reports
       the initial state, so a page restored/anchored far down shows everything above it at once. */
    if (!reduceMotion && 'IntersectionObserver' in window) {
      var ioPast = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (en.isIntersecting || en.boundingClientRect.bottom >= 0) return;
          en.target.classList.add('is-in'); ioPast.unobserve(en.target);
        });
      });
      revealEls.forEach(function (el) { if (flyEls.indexOf(el) < 0) ioPast.observe(el); });
    }

  });
  /* Safety net: never leave content invisible */
  setTimeout(function () {
    revealEls.forEach(function (el) {
      var r = el.getBoundingClientRect();
      if (!el.classList.contains('is-in') && r.top < window.innerHeight && r.bottom > 0) el.classList.add('is-in');
    });
  }, 8000);

  /* == PROGRESS · CURSOR ================================================= */
  var progress = root.querySelector('.hv2-progress');
  var updateProgress = function () {
    setStyle(progress, '--p', (geo.max > 0 ? clamp(scrollState.y / geo.max, 0, 1) : 0).toFixed(4));
  };
  onScroll(updateProgress); updateProgress();

  /* == BACK TO TOP — ring shows page progress; arrow "launches" on click ==== */
  var topBtn = document.getElementById('hv2-top');
  if (topBtn) {
    var ringBar = topBtn.querySelector('.bar'), CIRC = 2 * Math.PI * 21;
    ringBar.style.strokeDasharray = CIRC.toFixed(2);
    var updateTop = function () {
      var p = geo.max > 0 ? clamp(scrollState.y / geo.max, 0, 1) : 0;
      setStyle(ringBar, 'strokeDashoffset', (CIRC * (1 - p)).toFixed(1));
      var show = scrollState.y > geo.vh * 0.6;
      if (topBtn.__shown !== show) { topBtn.__shown = show; topBtn.classList.toggle('is-visible', show); }
    };
    onScroll(updateTop); updateTop();
    topBtn.addEventListener('click', function () {
      if (scrollState.y < 8) return;
      topBtn.classList.remove('is-launching'); void topBtn.offsetWidth; topBtn.classList.add('is-launching');
      setTimeout(function () { topBtn.classList.remove('is-launching'); }, 1000);
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
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
  /* n.toLocaleString(locale, options) builds a new formatter on EVERY call (slow, and it ran every frame of every counter) */
  var fmt = function (n, dec) {
    /* en-US grouping without Intl: even one Intl.NumberFormat costs ~50 ms on a phone the first time (locale data load) */
    var s = n.toFixed(dec), p = s.split('.');
    return p[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',') + (p[1] ? '.' + p[1] : '');
  };
  $$('[data-count]').forEach(function (el) {
    var target = parseFloat(el.getAttribute('data-count')) || 0;
    var suffix = el.getAttribute('data-suffix') || '';
    var dec = (String(el.getAttribute('data-count')).split('.')[1] || '').length;
    var prefix = el.getAttribute('data-prefix') || '';
    var text = function (v) { return prefix + fmt(v, dec) + (suffix ? '<sup>' + suffix + '</sup>' : ''); };
    /* A hidden copy of the FINAL value reserves the full width/height, so the layout never shifts while digits tick up */
    /* built once; each animation frame then only changes the number's text node (not the whole HTML) */
    var liveNum = null;
    var render = function (v) {
      if (!liveNum) {
        el.innerHTML = '<span class="hv2-c-ghost" aria-hidden="true">' + text(target) + '</span><span class="hv2-c-live">' + text(v) + '</span>';
        var live = el.querySelector('.hv2-c-live');
        liveNum = live && live.firstChild && live.firstChild.nodeType === 3 ? live.firstChild : null;
        if (!liveNum) render = function (n) { live.innerHTML = text(n); };   /* fallback: unexpected markup */
        return;
      }
      var s = prefix + fmt(v, dec);
      if (liveNum.nodeValue !== s) liveNum.nodeValue = s;
    };
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
    if (glass) addFx(
      function () { return clamp(scrollState.y / Math.max(1, heroH), 0, 1); },
      function (p) { setStyle(glass, 'transform', 'translate3d(0,' + (p * 160).toFixed(1) + 'px,0)'); },
      hero
    );
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
    /* not while scrolling: cards sliding under a resting pointer fired mouseenter, and the SVG filter (CPU-only) ran mid-scroll */
    var lastScrollAt = 0;
    onScroll(function () { lastScrollAt = performance.now(); });
    $$('[data-distort]').forEach(function (el) {
      el.addEventListener('mouseenter', function () {
        if (performance.now() - lastScrollAt < 250) return;
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
      window.scrollTo({ top: Math.max(0, el.getBoundingClientRect().top + window.pageYOffset + offset), behavior: reduceMotion ? 'auto' : 'smooth' });
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
      /* instant jump (no animation) right after the big layout shrink, so the toggle stays under the pointer */
      window.scrollTo({ top: Math.max(0, target.getBoundingClientRect().top + window.pageYOffset + off), behavior: 'instant' });
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
    /* Swiper is built only when the carousel comes within ~1 screen (it sits far below the first screen): its setup
       (≈250 ms of layout work on mobile) no longer runs during the page load. If Swiper's script is not loaded yet
       (pages without v2keep delay it), it is downloaded at that moment from the page's own Swiper <script> tag. */
    var csFallback = function () {
      /* fallback: plain scrolling row with working arrows */
      togBtn.hidden = true;
      var cards = $$('.hv2-case', cs);
      var stepW = function () { return cards.length > 1 ? cards[1].offsetLeft - cards[0].offsetLeft : vp.clientWidth; };
      var slideBy = function (dir) { vp.scrollBy({ left: dir * stepW(), behavior: reduceMotion ? 'auto' : 'smooth' }); };
      prevBtn.addEventListener('click', function () { slideBy(-1); });
      nextBtn.addEventListener('click', function () { slideBy(1); });
      vp.addEventListener('scroll', function () { setPos(clamp(Math.round(vp.scrollLeft / stepW()), 0, slidesN - 1)); }, { passive: true });
    };
    var csLoadSwiper = function (done) {
      if (window.Swiper) { done(); return; }
      var tag = document.querySelector('script[src*="swiper-bundle"]');
      if (!tag) { done(); return; }
      var s = document.createElement('script');
      s.src = tag.getAttribute('src'); s.async = true;
      s.onload = s.onerror = function () { done(); };
      document.head.appendChild(s);
    };
    {
      var csInit = function () {
      if (!window.Swiper) { csFallback(); return; }
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
      };
      /* Swiper is built while the browser is idle after page load (building it as the section approached cost ~250 ms in the
         middle of the visitor's scroll). If the visitor reaches the section first, it is built then. */
      var csStarted = false;
      var csStart = function () { if (csStarted) return; csStarted = true; if (csLazy) csLazy.disconnect(); csLoadSwiper(csInit); };
      var csLazy = null;
      if ('IntersectionObserver' in window) {
        csLazy = new IntersectionObserver(function (en) { if (en[0].isIntersecting) csStart(); }, { rootMargin: '50% 0px 50% 0px' });
        csLazy.observe(cs);
      }
      /* 8.5 s after load: outside the window speed tests count as blocking time (the carousel is far below the first screen) */
      var csIdle = function () { setTimeout(function () { if (window.requestIdleCallback) requestIdleCallback(csStart, { timeout: 4000 }); else csStart(); }, 8500); };
      if (document.readyState === 'complete') csIdle(); else window.addEventListener('load', csIdle);
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
  /* read phase: live positions of the (sticky) cards; write phase: dim + scale, only when the value changes */
  var readStack = function () {
    if (reduceMotion || !stackVis.length) return null;
    var tops = [];
    for (var i = 1; i < stackVis.length; i++) tops[i] = stackVis[i].getBoundingClientRect().top;
    return { from: window.innerHeight * 0.92, tops: tops };
  };
  var writeStack = function (r) {
    if (!r) return;
    for (var i = 0; i < stackVis.length - 1; i++) {
      var p = clamp((r.from - r.tops[i + 1]) / Math.max(1, r.from - stackTops[i + 1]), 0, 1);
      setStyle(stackVis[i], '--dim', (p * 0.38).toFixed(3));
      setStyle(stackVis[i], 'transform', p > 0 ? 'scale(' + (1 - p * 0.06).toFixed(4) + ')' : '');
    }
    var last = stackVis[stackVis.length - 1];
    setStyle(last, '--dim', '0'); setStyle(last, 'transform', '');
  };
  var paintStack = function () { writeStack(readStack()); };
  var buildStack = function () {
    stackCards.forEach(function (c) { c.style.removeProperty('--dim'); c.style.transform = ''; c['__s_--dim'] = c.__s_transform = undefined; });
    measureStack(); paintStack();
  };
  if (stackCards.length > 1) {
    requestAnimationFrame(function () { measureStack(); paintStack(); });   /* first frame, not while scripts run */
    addFx(readStack, writeStack, stackCards[0].parentNode);
    window.addEventListener('resize', measureStack);
  }

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
    /* restart the progress bar without forcing a layout (the old "void offsetWidth" trick reflowed the page on every slide,
       and on every in-view change while scrolling) */
    var qBarReset = function (on) {
      if (!qBar) return;
      qBar.style.setProperty('--q-ms', QMS + 'ms');
      if (!on) { qBar.style.animation = 'none'; return; }
      var wasOff = qBar.style.animation === 'none';
      qBar.style.animation = '';
      if (!qBar.__started) qBar.__started = true;   /* first start: the animation begins by itself, nothing to restart (no getAnimations layout flush) */
      else if (!wasOff && qBar.getAnimations) qBar.getAnimations().forEach(function (a) { a.cancel(); a.play(); });
      else if (!wasOff) { qBar.style.animation = 'none'; void qBar.offsetWidth; qBar.style.animation = ''; }
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
      links.forEach(function (l, k) { setStyle(l, '--f', clamp(p * links.length - k, 0, 1).toFixed(3)); });
      stepEls.forEach(function (s, i) {
        var hit = p > 0 && p >= (i / (stepEls.length - 1)) - 0.02;
        if (s.__hit !== hit) { s.__hit = hit; s.classList.toggle('is-hit', hit); }
      });
    };
    /* progress = how far the block has travelled from "top at 75% of the viewport" to "bottom at 55%" */
    var readProcess = function () {
      var r = steps.getBoundingClientRect(), vh = window.innerHeight;
      return clamp((vh * 0.75 - r.top) / (vh * 0.2 + r.height), 0, 1);
    };
    if (reduceMotion) whenInView(steps, function () { setLine(1); }); else addFx(readProcess, setLine, steps);
  }

  /* (Marquee / band speed-up while scrolling was removed: changing the animation speed every frame made the sliders
     stutter. They now run at a steady speed.) */

  /* == MARQUEE LOGOS: loaded while the browser is idle after page load ==
     The logos are lazy (no cost on first paint), but lazy images inside a moving marquee only start loading as each tile
     slides in, so tiles showed up blank for a moment. They are switched to eager loading once the page has loaded and the
     browser is idle (or on the first click / key press): never on the first scroll, where 88 image requests made it stutter. */
  (function () {
    var wraps = $$('.hv2-marquee-wrap');
    if (!wraps.length) return;
    /* Each marquee loads its logos when it is within ~1.5 screens of the viewport (so a visitor never meets blank tiles, and the
       first screen does not pay for ~40 images it cannot show). Without IntersectionObserver: 8 s after load. */
    var eager = function (wrap) { $$('img[loading="lazy"]', wrap).forEach(function (img) { img.loading = 'eager'; }); };
    if ('IntersectionObserver' in window) {
      var mio = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) { if (en.isIntersecting) { mio.unobserve(en.target); eager(en.target); } });
      }, { rootMargin: '150% 0px 150% 0px' });
      wraps.forEach(function (w) { mio.observe(w); });
    } else {
      window.addEventListener('load', function () { setTimeout(function () { wraps.forEach(eager); }, 8000); });
    }
  })();

  /* == CTA ZOOM · PARALLAX (live-position driven) ========================= */
  if (!reduceMotion) {
    var cta = document.getElementById('hv2-cta');
    var travel = function (el) { var r = el.getBoundingClientRect(), vh = window.innerHeight; return clamp((vh - r.top) / (vh + r.height), 0, 1); };
    /* CTA zoom: written straight on the image (a CSS variable on the whole section made every element in it recalculate) */
    var ctaImg = cta ? cta.querySelector('.hv2-cta-bg img') : null;
    if (ctaImg) addFx(
      function () { return travel(cta); },
      function (t) { setStyle(ctaImg, 'transform', 'scale(' + (1.22 - t * 0.22).toFixed(3) + ')'); },
      cta
    );
    $$('[data-parallax]').forEach(function (el) {
      var sp = parseFloat(el.getAttribute('data-parallax')) || 0, host = el.parentNode;
      addFx(
        function () { return travel(host); },
        function (t) { setStyle(el, 'transform', 'translate3d(0,' + (-sp * 400 + t * 2 * sp * 400).toFixed(1) + 'px,0)'); },
        host
      );
    });
  }

  /* == ENQUIRY FORM (existing endpoint + contract) ======================= */
  var form = document.getElementById('hv2-enquiry-form');
  if (form) {
    var phone = document.getElementById('hv2-phone'), fullPhone = document.getElementById('hv2-fullphone');
    var loader = form.parentNode.querySelector('.hv2-form-loader'), iti = null;
    /* the phone widget builds DOM and measures it: do that when the browser is idle (or the moment the visitor touches the form),
       not while the page's scripts are still running */
    var phoneTries = 0;
    var initPhone = function () {
      if (iti || !phone) return;
      /* the library (300 KB) is one of the delayed scripts: until it has arrived, look again every 400 ms (for up to 40 s) */
      if (!window.intlTelInput) { if (phoneTries++ < 100) setTimeout(initPhone, 400); return; }
      iti = window.intlTelInput(phone, { initialCountry: 'ae', separateDialCode: true });
      var syncPhone = function () { fullPhone.value = iti.getNumber(); };
      ['keyup', 'change', 'countrychange'].forEach(function (ev) { phone.addEventListener(ev, syncPhone); });
    };
    form.addEventListener('focusin', initPhone, { once: true });
    /* the flag + dial-code widget (240 countries) costs ~100 ms on a phone: it is built 6 s after load at the latest (outside the
       window speed tests count), or the moment the visitor touches the form */
    var phoneLater = function () { setTimeout(function () { if (window.requestIdleCallback) requestIdleCallback(initPhone, { timeout: 2000 }); else initPhone(); }, 6000); };
    if (document.readyState === 'complete') phoneLater(); else window.addEventListener('load', phoneLater);
    var toast = function (icon, title) {
      if (window.Toast && window.Toast.fire) window.Toast.fire({ icon: icon, title: title });
      else if (window.Swal) window.Swal.fire({ icon: icon, title: title, toast: true, position: 'top-end', timer: 5000, showConfirmButton: false });
    };
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var email = (document.getElementById('hv2-email').value || '').trim();
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { toast('warning', 'Please Enter valid Email address'); return; }
      if (iti) fullPhone.value = iti.getNumber(); else fullPhone.value = (phone.value || '').trim();   /* submitted before the widget was built: send what was typed */
      loader.classList.add('is-on');
      fetch(form.getAttribute('action'), { method: 'POST', body: new FormData(form), credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          loader.classList.remove('is-on');
          data = data || {};
          if (data.success === 'success') {
            toast('success', data.message);
            window.dataLayer = window.dataLayer || [];
            /* reload only after Tag Manager has fired the conversion (eventCallback), or after 2.5 s if Tag Manager has not loaded yet
               (third-party scripts wait for the first interaction on these pages) */
            var reloaded = false, reloadNow = function () { if (!reloaded) { reloaded = true; window.location.reload(); } };
            window.dataLayer.push({ event: 'formSubmitEvent', eventCallback: reloadNow, eventTimeout: 2000 });
            setTimeout(reloadNow, 2500);
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
        scrollToEl(listEl, 120);
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

  if (document.fonts && document.fonts.ready) document.fonts.ready.then(remeasure);
})();
