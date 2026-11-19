/* ============================================================
   dgnova — listings.js
   Card renderers for properties, cars, tours and trading
   products, plus related-item selection. Used by filters.js
   (listing pages), details.js (related sections) and the
   homepage featured sections.
   ============================================================ */

(function ($) {
  "use strict";

  window.DL = window.DL || {};

  /* ---------------- Badges ---------------- */

  function statusBadges(item, type) {
    var out = [];
    if (type === "property") {
      if (item.purpose === "sale") out.push('<span class="badge-status badge-navy">For Sale</span>');
      if (item.purpose === "rent") out.push('<span class="badge-status badge-white">For Rent</span>');
      (item.status || []).forEach(function (s) {
        if (s === "Sold" || s === "Rented") out.push('<span class="badge-status badge-dark-soft">' + DL.esc(s) + "</span>");
      });
      if (item.featured) out.push('<span class="badge-status badge-green">Featured</span>');
      if (item.new) out.push('<span class="badge-status badge-soft-green">New</span>');
    }
    if (type === "car") {
      if (item.featured) out.push('<span class="badge-status badge-green">Featured</span>');
      out.push(item.available
        ? '<span class="badge-status badge-white">Available</span>'
        : '<span class="badge-status badge-dark-soft">Unavailable</span>');
    }
    if (type === "tour") {
      if (item.featured) out.push('<span class="badge-status badge-green">Featured</span>');
      out.push('<span class="badge-status badge-white">' + DL.esc(item.durationLabel) + "</span>");
    }
    if (type === "product") {
      if (item.featured) out.push('<span class="badge-status badge-green">Featured</span>');
      if (item.availability === "Available") out.push('<span class="badge-status badge-white">Available</span>');
      if (item.availability === "On Request") out.push('<span class="badge-status badge-navy">On Request</span>');
      if (item.availability === "Unavailable") out.push('<span class="badge-status badge-dark-soft">Unavailable</span>');
    }
    return out.join("");
  }

  /* ---------------- Card templates ---------------- */

  DL.cardProperty = function (p) {
    return '' +
      '<article class="d-card reveal">' +
        '<a class="d-card-img" href="property-details.html?id=' + DL.esc(p.id) + '" aria-label="' + DL.esc(p.title) + '">' +
          DL.imgTag(p.image, p.title, "property") +
          '<div class="d-card-badges">' + statusBadges(p, "property") + "</div>" +
        "</a>" +
        '<div class="d-card-body">' +
          '<p class="d-card-price">' + DL.esc(p.priceLabel) + "</p>" +
          '<h3 class="d-card-title"><a href="property-details.html?id=' + DL.esc(p.id) + '">' + DL.esc(p.title) + "</a></h3>" +
          '<p class="d-card-loc"><i class="bi bi-geo-alt"></i> ' + DL.esc(p.location) + "</p>" +
          '<div class="d-card-meta">' +
            "<span><i class=\"bi bi-house-door\"></i> " + DL.esc(p.type) + "</span>" +
            (p.bedrooms > 0 ? "<span><i class=\"bi bi-door-closed\"></i> " + p.bedrooms + " Bed</span>" : "<span><i class=\"bi bi-door-closed\"></i> Studio</span>") +
            "<span><i class=\"bi bi-droplet\"></i> " + p.bathrooms + " Bath</span>" +
            "<span><i class=\"bi bi-arrows-fullscreen\"></i> " + Number(p.area).toLocaleString() + " sq ft</span>" +
          "</div>" +
          '<div class="d-card-foot">' +
            '<a class="link-arrow" href="property-details.html?id=' + DL.esc(p.id) + '">View Details <i class="bi bi-arrow-right"></i></a>' +
          "</div>" +
        "</div>" +
      "</article>";
  };

  DL.cardCar = function (c) {
    return '' +
      '<article class="d-card reveal">' +
        '<a class="d-card-img" href="car-details.html?id=' + DL.esc(c.id) + '" aria-label="' + DL.esc(c.name) + '">' +
          DL.imgTag(c.image, c.name, "car") +
          '<div class="d-card-badges">' + statusBadges(c, "car") + "</div>" +
        "</a>" +
        '<div class="d-card-body">' +
          '<p class="d-card-price">' + DL.formatAED(c.pricePerDay) + ' <span class="price-unit">/ day</span></p>' +
          '<h3 class="d-card-title"><a href="car-details.html?id=' + DL.esc(c.id) + '">' + DL.esc(c.name) + "</a></h3>" +
          '<p class="d-card-loc"><i class="bi bi-tag"></i> ' + DL.esc(c.category) + " &middot; " + DL.esc(String(c.year)) + "</p>" +
          '<div class="d-card-meta">' +
            "<span><i class=\"bi bi-gear-wide-connected\"></i> " + DL.esc(c.transmission) + "</span>" +
            "<span><i class=\"bi bi-fuel-pump\"></i> " + DL.esc(c.fuel) + "</span>" +
            "<span><i class=\"bi bi-people\"></i> " + c.seats + " Seats</span>" +
          "</div>" +
          '<div class="d-card-foot">' +
            '<a class="link-arrow" href="car-details.html?id=' + DL.esc(c.id) + '">View Details <i class="bi bi-arrow-right"></i></a>' +
          "</div>" +
        "</div>" +
      "</article>";
  };

  DL.cardTour = function (t) {
    return '' +
      '<article class="d-card reveal">' +
        '<a class="d-card-img" href="tour-details.html?id=' + DL.esc(t.id) + '" aria-label="' + DL.esc(t.title) + '">' +
          DL.imgTag(t.image, t.title, "tour") +
          '<div class="d-card-badges">' + statusBadges(t, "tour") + "</div>" +
        "</a>" +
        '<div class="d-card-body">' +
          '<p class="d-card-price">Price on Request</p>' +
          '<h3 class="d-card-title"><a href="tour-details.html?id=' + DL.esc(t.id) + '">' + DL.esc(t.title) + "</a></h3>" +
          '<p class="d-card-loc"><i class="bi bi-geo-alt"></i> ' + DL.esc(t.destination) + ", " + DL.esc(t.country) + "</p>" +
          '<div class="d-card-meta">' +
            "<span><i class=\"bi bi-calendar3\"></i> " + DL.esc(t.durationLabel) + "</span>" +
            "<span><i class=\"bi bi-suit-heart\"></i> " + DL.esc((t.styles || []).slice(0, 2).join(", ")) + "</span>" +
          "</div>" +
          '<div class="d-card-foot">' +
            '<a class="link-arrow" href="tour-details.html?id=' + DL.esc(t.id) + '">View Package <i class="bi bi-arrow-right"></i></a>' +
          "</div>" +
        "</div>" +
      "</article>";
  };

  DL.cardProduct = function (p) {
    return '' +
      '<article class="d-card reveal">' +
        '<a class="d-card-img" href="product-details.html?id=' + DL.esc(p.id) + '" aria-label="' + DL.esc(p.name) + '">' +
          DL.imgTag(p.image, p.name, "product") +
          '<div class="d-card-badges">' + statusBadges(p, "product") + "</div>" +
        "</a>" +
        '<div class="d-card-body">' +
          '<p class="d-card-price">Price on Request</p>' +
          '<h3 class="d-card-title"><a href="product-details.html?id=' + DL.esc(p.id) + '">' + DL.esc(p.name) + "</a></h3>" +
          '<p class="d-card-loc"><i class="bi bi-grid"></i> ' + DL.esc(p.category) + "</p>" +
          '<p class="d-card-desc">' + DL.esc(p.shortDescription) + "</p>" +
          '<div class="d-card-foot">' +
            '<a class="link-arrow" href="product-details.html?id=' + DL.esc(p.id) + '">View Product <i class="bi bi-arrow-right"></i></a>' +
          "</div>" +
        "</div>" +
      "</article>";
  };

  DL.cardFor = function (type) {
    return { property: DL.cardProperty, car: DL.cardCar, tour: DL.cardTour, product: DL.cardProduct }[type];
  };

  /* ---------------- Related items ---------------- */

  DL.related = function (items, current, keys, limit) {
    limit = limit || 3;
    var scored = items.filter(function (it) { return it.id !== current.id; }).map(function (it) {
      var score = 0;
      (keys || []).forEach(function (k) {
        if (it[k] !== undefined && String(it[k]) === String(current[k])) score += 1;
      });
      return { it: it, score: score };
    });
    scored.sort(function (a, b) { return b.score - a.score; });
    return scored.slice(0, limit).map(function (s) { return s.it; });
  };

  /* ---------------- Skeletons ---------------- */

  DL.skeletons = function (n) {
    var one = '<div class="skeleton-card"><div class="sk sk-img"></div><div class="sk-pad">' +
      '<div class="sk sk-line" style="width:40%"></div>' +
      '<div class="sk sk-line" style="width:85%"></div>' +
      '<div class="sk sk-line" style="width:60%"></div></div></div>';
    return new Array(n || 6).fill(one).join("");
  };

  /* ---------------- Simple grid renderer (homepage) ---------------- */

  DL.renderInto = function (sel, items, type, colClass) {
    var $wrap = $(sel);
    if (!$wrap.length) return;
    var card = DL.cardFor(type);
    var html = items.map(function (it) {
      return '<div class="' + (colClass || "col-md-6 col-lg-4") + '">' + card(it) + "</div>";
    }).join("");
    $wrap.html(html);
    if (window.DL && typeof DL._reinitReveal === "function") DL._reinitReveal($wrap);
    $wrap.find(".reveal").addClass("is-in");
  };

})(jQuery);
