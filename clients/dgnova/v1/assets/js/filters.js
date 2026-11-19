/* ============================================================
   dgnova — filters.js
   Generic listing engine. Each listing page declares
   data-listing="properties|cars|tours|products" on the results
   grid; this module loads data, wires the filter panel (desktop)
   and offcanvas (mobile), applies filters + sort, renders cards,
   keeps the result count updated, and handles empty state.

   Convention: every filter control carries data-filter="key".
   Desktop panel controls and offcanvas controls share the same
   keys and are kept in sync.
   ============================================================ */

(function ($) {
  "use strict";

  window.DL = window.DL || {};

  var PAGE_TYPES = {
    properties: { type: "property", noun: "properties" },
    cars:       { type: "car",      noun: "cars" },
    tours:      { type: "tour",     noun: "packages" },
    products:   { type: "product",  noun: "products" }
  };

  /* ---------------- Filter predicates per dataset ---------------- */

  var PREDICATES = {

    properties: function (item, f) {
      if (f.purpose && f.purpose !== "all" && item.purpose !== f.purpose) return false;
      if (f.type && f.type !== "all" && item.type !== f.type) return false;
      if (f.location && f.location !== "all" && item.location.indexOf(f.location) === -1) return false;
      if (f.priceMin && Number(item.price) < Number(f.priceMin)) return false;
      if (f.priceMax && Number(item.price) > Number(f.priceMax)) return false;
      if (f.bedrooms && f.bedrooms !== "all") {
        if (f.bedrooms === "4+") { if (item.bedrooms < 4) return false; }
        else if (String(item.bedrooms) !== f.bedrooms) return false;
      }
      if (f.bathrooms && f.bathrooms !== "all") {
        if (f.bathrooms === "4+") { if (item.bathrooms < 4) return false; }
        else if (String(item.bathrooms) !== f.bathrooms) return false;
      }
      if (f.furnishing && f.furnishing !== "all" && item.furnishing !== f.furnishing) return false;
      if (f.areaMin && Number(item.area) < Number(f.areaMin)) return false;
      if (f.areaMax && Number(item.area) > Number(f.areaMax)) return false;
      return true;
    },

    cars: function (item, f) {
      if (f.category && f.category !== "all" && item.category !== f.category) return false;
      if (f.brand && f.brand !== "all" && item.brand !== f.brand) return false;
      if (f.transmission && f.transmission !== "all" && item.transmission !== f.transmission) return false;
      if (f.fuel && f.fuel !== "all" && item.fuel !== f.fuel) return false;
      if (f.seats && f.seats !== "all") {
        if (f.seats === "7+") { if (item.seats < 7) return false; }
        else if (String(item.seats) !== f.seats) return false;
      }
      if (f.priceMax && Number(item.pricePerDay) > Number(f.priceMax)) return false;
      return true;
    },

    tours: function (item, f) {
      if (f.country && f.country !== "all" && item.country !== f.country) return false;
      if (f.duration && f.duration !== "all") {
        var d = Number(item.durationDays);
        if (f.duration === "short" && d > 4) return false;
        if (f.duration === "medium" && (d < 5 || d > 7)) return false;
        if (f.duration === "long" && d < 8) return false;
      }
      if (f.packageType && f.packageType !== "all" && item.packageType !== f.packageType) return false;
      if (f.style && f.style !== "all" && (item.styles || []).indexOf(f.style) === -1) return false;
      return true;
    },

    products: function (item, f) {
      if (f.category && f.category !== "all" && item.category !== f.category) return false;
      if (f.availability && f.availability !== "all" && item.availability !== f.availability) return false;
      if (f.keyword) {
        var hay = (item.name + " " + item.category + " " + item.shortDescription).toLowerCase();
        if (hay.indexOf(f.keyword.toLowerCase()) === -1) return false;
      }
      return true;
    }
  };

  /* ---------------- Sorters ---------------- */

  function priceOf(item, dataset) {
    if (dataset === "cars") return Number(item.pricePerDay) || 0;
    return Number(item.price) || 0;
  }

  var SORTERS = {
    newest: function (a, b) { return new Date(b.addedOn) - new Date(a.addedOn); },
    priceAsc: null,   // built per dataset below
    priceDesc: null
  };

  function sortItems(items, sortKey, dataset) {
    var arr = items.slice();
    if (sortKey === "priceAsc") arr.sort(function (a, b) { return priceOf(a, dataset) - priceOf(b, dataset); });
    else if (sortKey === "priceDesc") arr.sort(function (a, b) { return priceOf(b, dataset) - priceOf(a, dataset); });
    else arr.sort(SORTERS.newest);
    return arr;
  }

  /* ---------------- Dynamic option population ---------------- */

  function uniqueValues(items, key, splitLocation) {
    var set = {};
    items.forEach(function (it) {
      var v = it[key];
      if (splitLocation && typeof v === "string") {
        // "Dubai Marina, Dubai" → use last segment (emirate/city)
        v = v.split(",").pop().trim();
      }
      if (Array.isArray(v)) v.forEach(function (x) { set[x] = 1; });
      else if (v !== undefined && v !== null && v !== "") set[v] = 1;
    });
    return Object.keys(set).sort();
  }

  function populateOptions($ctx, dataset, items) {
    var maps = {
      properties: [
        { key: "type",       attr: "type" },
        { key: "location",   attr: "location", split: true },
        { key: "furnishing", attr: "furnishing" }
      ],
      cars: [
        { key: "category",     attr: "category" },
        { key: "brand",        attr: "brand" },
        { key: "transmission", attr: "transmission" },
        { key: "fuel",         attr: "fuel" }
      ],
      tours: [
        { key: "country",     attr: "country" },
        { key: "packageType", attr: "packageType" },
        { key: "styles",      attr: "style" }
      ],
      products: [
        { key: "category",     attr: "category" },
        { key: "availability", attr: "availability" }
      ]
    };
    (maps[dataset] || []).forEach(function (m) {
      var vals = uniqueValues(items, m.key, m.split);
      $ctx.find('select[data-filter="' + m.attr + '"]').each(function () {
        var $sel = $(this);
        if ($sel.data("populated")) return;
        vals.forEach(function (v) {
          $sel.append('<option value="' + DL.esc(v) + '">' + DL.esc(v) + "</option>");
        });
        $sel.data("populated", true);
      });
    });
  }

  /* ---------------- Engine ---------------- */

  function ListingPage($grid) {
    this.$grid = $grid;
    this.dataset = $grid.data("listing");
    this.meta = PAGE_TYPES[this.dataset];
    this.items = [];
    this.sortKey = "newest";
    this.$count = $("[data-result-count]");
    this.$empty = $("[data-empty-state]");
    this.$panels = $("[data-filter-scope]"); // desktop panel + offcanvas
  }

  ListingPage.prototype.currentFilters = function () {
    var f = {};
    // Read from the desktop panel as source of truth
    var $src = $('[data-filter-scope="desktop"]');
    if (!$src.length) $src = this.$panels.first();
    $src.find("[data-filter]").each(function () {
      var $el = $(this);
      var key = $el.data("filter");
      if ($el.is(":radio")) {
        if ($el.is(":checked")) f[key] = $el.val();
      } else {
        var v = $.trim($el.val());
        if (v !== "" && v !== "all") f[key] = v;
        else if (v === "all") f[key] = "all";
      }
    });
    // Purpose toggle (buttons)
    var $pt = $(".purpose-toggle button.is-active");
    if ($pt.length) f.purpose = $pt.data("purpose");
    return f;
  };

  ListingPage.prototype.apply = function () {
    var self = this;
    var f = this.currentFilters();
    var pred = PREDICATES[this.dataset];
    var filtered = this.items.filter(function (it) { return pred(it, f); });
    filtered = sortItems(filtered, this.sortKey, this.dataset);

    if (!filtered.length) {
      this.$grid.hide();
      this.$empty.removeClass("d-none");
    } else {
      this.$empty.addClass("d-none");
      this.$grid.show();
      var card = DL.cardFor(this.meta.type);
      var col = this.$grid.data("col") || "col-md-6 col-lg-4";
      this.$grid.html(filtered.map(function (it) {
        return '<div class="' + col + '">' + card(it) + "</div>";
      }).join(""));
      this.$grid.find(".reveal").addClass("is-in");
    }

    var n = filtered.length;
    this.$count.text(n + " " + (n === 1 ? this.meta.noun.replace(/ies$/, "y").replace(/s$/, "") : this.meta.noun) + " found");
  };

  ListingPage.prototype.reset = function () {
    this.$panels.find("select[data-filter]").val("all");
    this.$panels.find('input[data-filter][type="number"], input[data-filter][type="text"], input[data-filter][type="search"]').val("");
    $(".purpose-toggle button").removeClass("is-active").first().addClass("is-active");
    this.sortKey = "newest";
    $('[data-sort]').val("newest");
    this.apply();
  };

  ListingPage.prototype.syncFromOffcanvas = function () {
    // Copy offcanvas control values into desktop panel, then apply
    var $oc = $('[data-filter-scope="mobile"]');
    var $desk = $('[data-filter-scope="desktop"]');
    $oc.find("[data-filter]").each(function () {
      var key = $(this).data("filter");
      $desk.find('[data-filter="' + key + '"]').val($(this).val());
    });
    this.apply();
  };

  ListingPage.prototype.syncToOffcanvas = function () {
    var $oc = $('[data-filter-scope="mobile"]');
    var $desk = $('[data-filter-scope="desktop"]');
    $desk.find("[data-filter]").each(function () {
      var key = $(this).data("filter");
      $oc.find('[data-filter="' + key + '"]').val($(this).val());
    });
  };

  ListingPage.prototype.init = function () {
    var self = this;
    this.$grid.html('<div class="col-12"><div class="row g-4">' + "</div></div>");
    this.$grid.html(
      ["col-md-6 col-lg-4", "col-md-6 col-lg-4", "col-md-6 col-lg-4"].map(function (c) {
        return '<div class="' + c + '">' + DL.skeletons(1) + "</div>";
      }).join("")
    );

    DL.loadData(this.dataset).done(function (items) {
      self.items = items;
      populateOptions($(document), self.dataset, items);

      // Pre-apply from query params (e.g. real-estate.html?purpose=rent, general-trading.html?category=X)
      var qpKeys = ["purpose", "category", "type", "country", "style", "availability"];
      qpKeys.forEach(function (k) {
        var v = DL.getParam(k);
        if (!v) return;
        if (k === "purpose") {
          $(".purpose-toggle button").removeClass("is-active")
            .filter('[data-purpose="' + v + '"]').addClass("is-active");
        } else {
          self.$panels.find('[data-filter="' + k + '"]').val(v);
        }
      });

      self.apply();
    }).fail(function () {
      self.$grid.html('<div class="col-12"><div class="empty-state"><span class="es-icon"><i class="bi bi-exclamation-circle"></i></span>' +
        "<h3>We couldn&rsquo;t load the listings</h3><p>Please refresh the page or try again shortly.</p></div></div>");
    });

    /* Events */
    $(document).on("change", '[data-filter-scope="desktop"] [data-filter]', function () { self.apply(); });
    $(document).on("input", '[data-filter-scope="desktop"] input[data-filter]', DL.debounce(function () { self.apply(); }, 350));

    $(document).on("click", ".purpose-toggle button", function () {
      var v = $(this).data("purpose");
      $(".purpose-toggle button").removeClass("is-active")
        .filter('[data-purpose="' + v + '"]').addClass("is-active");
      self.apply();
    });

    $(document).on("change", "[data-sort]", function () {
      self.sortKey = $(this).val();
      $("[data-sort]").val(self.sortKey);
      self.apply();
    });

    $(document).on("click", "[data-filter-reset]", function (e) {
      e.preventDefault();
      self.reset();
      var oc = document.getElementById("filtersOffcanvas");
      if (oc) { var inst = bootstrap.Offcanvas.getInstance(oc); if (inst) inst.hide(); }
    });

    $(document).on("click", "[data-filter-apply]", function (e) {
      e.preventDefault();
      self.syncFromOffcanvas();
      var oc = document.getElementById("filtersOffcanvas");
      if (oc) { var inst = bootstrap.Offcanvas.getInstance(oc); if (inst) inst.hide(); }
    });

    var ocEl = document.getElementById("filtersOffcanvas");
    if (ocEl) ocEl.addEventListener("show.bs.offcanvas", function () { self.syncToOffcanvas(); });

    // "More filters" toggle
    $(document).on("click", "[data-more-filters]", function (e) {
      e.preventDefault();
      var $adv = $(".filter-advanced");
      var open = $adv.toggleClass("d-none").hasClass("d-none");
      $(this).find("span").text(open ? "More Filters" : "Fewer Filters");
      $(this).find("i").toggleClass("bi-sliders bi-chevron-up");
    });
  };

  /* Small debounce helper */
  DL.debounce = function (fn, wait) {
    var t;
    return function () {
      var args = arguments, ctx = this;
      clearTimeout(t);
      t = setTimeout(function () { fn.apply(ctx, args); }, wait);
    };
  };

  $(function () {
    var $grid = $("[data-listing]");
    if ($grid.length) new ListingPage($grid).init();
  });

})(jQuery);
