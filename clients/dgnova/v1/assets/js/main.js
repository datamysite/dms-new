/* ============================================================
   dgnova — main.js
   Core behaviour: config binding, sticky header, offcanvas nav,
   data loader (fetch → embedded fallback), image fallback,
   reveal-on-scroll, back-to-top, shared utilities.
   ============================================================ */

(function ($) {
  "use strict";

  var CFG = window.dgnova_CONFIG || {};

  /* ---------------- Utilities ---------------- */

  window.DL = window.DL || {};

  DL.getParam = function (name) {
    var m = new RegExp("[?&]" + name + "=([^&#]*)").exec(window.location.search);
    return m ? decodeURIComponent(m[1].replace(/\+/g, " ")) : null;
  };

  DL.formatAED = function (n) {
    if (n === null || n === undefined || isNaN(n)) return "";
    return "AED " + Number(n).toLocaleString("en-AE");
  };

  DL.esc = function (s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
  };

  DL.placeholderFor = function (kind) {
    var map = { property: "property", car: "car", tour: "tour", product: "product" };
    return "assets/images/placeholder-" + (map[kind] || "general") + ".svg";
  };

  DL.imgTag = function (src, alt, kind, cls) {
    return '<img src="' + DL.esc(src) + '" alt="' + DL.esc(alt) + '" loading="lazy" ' +
      'onerror="this.onerror=null;this.src=\'' + DL.placeholderFor(kind) + '\';" ' +
      (cls ? 'class="' + cls + '"' : "") + ">";
  };

  DL.waLink = function (message) {
    var num = CFG.whatsapp || "";
    return "https://wa.me/" + num + "?text=" + encodeURIComponent(message || "Hi dgnova, I have an enquiry.");
  };

  /* ---------------- Data loader ---------------- */
  // Tries fetch (works over http). Falls back to embedded data.js
  // (window.dgnova_DATA) so the site also works when opened via file://.

  var dataCache = {};

  DL.loadData = function (key) {
    // key: properties | cars | tours | products
    if (dataCache[key]) return dataCache[key];

    var dfd = $.Deferred();
    dataCache[key] = dfd.promise();

    // Accept either a bare array or an { items: [...] } wrapper
    function normalize(d) {
      if (Array.isArray(d)) return d;
      if (d && Array.isArray(d.items)) return d.items;
      return [];
    }

    var embedded = window.dgnova_DATA && window.dgnova_DATA[key];

    if (window.location.protocol === "file:" && embedded) {
      dfd.resolve(normalize(embedded));
      return dataCache[key];
    }

    $.getJSON("assets/data/" + key + ".json")
      .done(function (json) { dfd.resolve(normalize(json)); })
      .fail(function () {
        if (embedded) { dfd.resolve(normalize(embedded)); }
        else { dfd.reject(); }
      });

    return dataCache[key];
  };

  /* ---------------- Config binding ---------------- */

  function bindConfig() {
    $("[data-cfg-text]").each(function () {
      var k = $(this).data("cfg-text");
      if (CFG[k]) $(this).text(CFG[k]);
    });
    $("[data-cfg-tel]").attr("href", "tel:" + (CFG.phoneHref || ""));
    $("[data-cfg-mail]").attr("href", "mailto:" + (CFG.email || ""));
    $("[data-cfg-wa]").each(function () {
      var msg = $(this).data("wa-msg") || "Hi dgnova, I have an enquiry.";
      $(this).attr("href", DL.waLink(msg)).attr("target", "_blank").attr("rel", "noopener");
    });
    var soc = CFG.socialLinks || {};
    $("[data-cfg-social]").each(function () {
      var k = $(this).data("cfg-social");
      if (soc[k]) $(this).attr("href", soc[k]);
    });
    $("[data-year]").text(new Date().getFullYear());
  }

  /* ---------------- Header ---------------- */

  function initHeader() {
    var $header = $(".site-header");
    if (!$header.length) return;
    var onScroll = function () {
      $header.toggleClass("is-stuck", window.scrollY > 40);
    };
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
  }

  function markActiveNav() {
    var page = (window.location.pathname.split("/").pop() || "index.html").toLowerCase();
    if (!page) page = "index.html";
    $(".nav-primary a, .oc-list a, .utility-nav a").each(function () {
      var href = ($(this).attr("href") || "").toLowerCase();
      if (href === page) $(this).addClass("is-active").attr("aria-current", "page");
    });
  }

  /* ---------------- Reveal on scroll ---------------- */

  function initReveal() {
    var els = document.querySelectorAll(".reveal");
    if (!els.length) return;
    if (!("IntersectionObserver" in window) ||
        window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      els.forEach(function (el) { el.classList.add("is-in"); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add("is-in"); io.unobserve(e.target); }
      });
    }, { threshold: 0.12 });
    els.forEach(function (el) { io.observe(el); });
  }

  /* ---------------- Back to top ---------------- */

  function initBackTop() {
    var $bt = $(".back-top");
    if (!$bt.length) return;
    var onScroll = function () { $bt.toggleClass("is-visible", window.scrollY > 600); };
    window.addEventListener("scroll", onScroll, { passive: true });
    $bt.on("click", function () { window.scrollTo({ top: 0, behavior: "smooth" }); });
  }

  /* ---------------- Global image fallback ---------------- */

  function initImageFallback() {
    // Safety net for images written directly in HTML
    document.addEventListener("error", function (e) {
      var t = e.target;
      if (t && t.tagName === "IMG" && !t.dataset.fbDone) {
        t.dataset.fbDone = "1";
        t.src = DL.placeholderFor(t.dataset.kind || "general");
      }
    }, true);
  }

  /* ---------------- FAQ deep link ---------------- */

  function initFaqAnchors() {
    if (window.location.hash) {
      var $target = $(window.location.hash);
      if ($target.hasClass("accordion-collapse")) {
        new bootstrap.Collapse($target[0], { show: true });
      }
    }
  }

  /* ---------------- Boot ---------------- */

  $(function () {
    bindConfig();
    initHeader();
    markActiveNav();
    initReveal();
    initBackTop();
    initImageFallback();
    initFaqAnchors();
  });

})(jQuery);
