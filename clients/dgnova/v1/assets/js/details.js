/* ============================================================
   dgnova — details.js
   Detail page loaders. Page declares data-detail="property|car|
   tour|product" on <body>. Reads ?id=, renders detail content,
   gallery (mosaic + mobile strip + lightbox) and related items.
   Invalid/missing IDs get a polished "Listing Not Found" state.
   ============================================================ */

(function ($) {
  "use strict";

  window.DL = window.DL || {};

  var DATASET = { property: "properties", car: "cars", tour: "tours", product: "products" };
  var LIST_PAGE = {
    property: { href: "real-estate.html", label: "Real Estate" },
    car: { href: "car-rental.html", label: "Car Rental" },
    tour: { href: "tourism.html", label: "Travel & Tourism" },
    product: { href: "general-trading.html", label: "General Trading" }
  };

  /* ---------------- Gallery ---------------- */

  var galleryImages = [];
  var lbIndex = 0;

  function renderGallery(images, title, kind) {
    galleryImages = images && images.length ? images : [];
    if (!galleryImages.length) return;

    // Mosaic (desktop)
    var $mosaic = $(".gallery-mosaic");
    if ($mosaic.length) {
      var main = '<button class="gm-item gm-main" data-lb="0" aria-label="Open photo 1 of ' + galleryImages.length + '">' +
        DL.imgTag(galleryImages[0], title + " — photo 1", kind) + "</button>";
      var tiles = galleryImages.slice(1, 3).map(function (src, i) {
        return '<button class="gm-item" data-lb="' + (i + 1) + '" aria-label="Open photo ' + (i + 2) + '">' +
          DL.imgTag(src, title + " — photo " + (i + 2), kind) + "</button>";
      }).join("");
      var viewAll = galleryImages.length > 1
        ? '<button class="gallery-viewall" data-lb="0"><i class="bi bi-images"></i> View All Photos (' + galleryImages.length + ")</button>"
        : "";
      $mosaic.html(main + tiles + viewAll);
    }

    // Strip (mobile)
    var $strip = $(".gallery-strip");
    if ($strip.length) {
      $strip.html(galleryImages.map(function (src, i) {
        return '<button class="gs-item" data-lb="' + i + '" aria-label="Open photo ' + (i + 1) + '">' +
          DL.imgTag(src, title + " — photo " + (i + 1), kind) + "</button>";
      }).join(""));
    }
  }

  function openLightbox(i) {
    lbIndex = i;
    var $lb = $(".lightbox");
    if (!$lb.length) return;
    updateLightbox();
    $lb.addClass("is-open").attr("aria-hidden", "false");
    $("body").css("overflow", "hidden");
  }

  function closeLightbox() {
    $(".lightbox").removeClass("is-open").attr("aria-hidden", "true");
    $("body").css("overflow", "");
  }

  function updateLightbox() {
    var $lb = $(".lightbox");
    $lb.find(".lb-img").attr("src", galleryImages[lbIndex]);
    $lb.find(".lb-counter").text((lbIndex + 1) + " / " + galleryImages.length);
  }

  function stepLightbox(dir) {
    lbIndex = (lbIndex + dir + galleryImages.length) % galleryImages.length;
    updateLightbox();
  }

  /* ---------------- Not found ---------------- */

  function showNotFound(kind) {
    var lp = LIST_PAGE[kind];
    $("[data-detail-root]").html(
      '<div class="container-xl"><div class="empty-state notfound-inline">' +
        '<span class="es-icon"><i class="bi bi-search"></i></span>' +
        "<h1 class=\"h3\">Listing Not Found</h1>" +
        "<p>The " + kind + " you&rsquo;re looking for may have been removed or the link may be incorrect.</p>" +
        '<div class="d-flex gap-2 justify-content-center flex-wrap mt-3">' +
          '<a class="btn btn-navy" href="' + lp.href + '">Browse ' + lp.label + "</a>" +
          '<a class="btn btn-outline-navy" href="index.html">Back to Home</a>' +
        "</div>" +
      "</div></div>"
    );
    document.title = "Listing Not Found | dgnova";
  }

  /* ---------------- Shared fills ---------------- */

  function fill(sel, html) { var $el = $(sel); if ($el.length) $el.html(html); }
  function fillText(sel, text) { var $el = $(sel); if ($el.length) $el.text(text); }

  function listHTML(arr, icon) {
    return (arr || []).map(function (x) {
      return "<li><i class=\"bi " + (icon || "bi-check2") + "\"></i> " + DL.esc(x) + "</li>";
    }).join("");
  }

  function setBreadcrumbCurrent(name) {
    $(".breadcrumb-bar [aria-current]").text(name);
  }

  function setMeta(title, desc) {
    document.title = title + " | dgnova";
    var m = document.querySelector('meta[name="description"]');
    if (m && desc) m.setAttribute("content", desc.slice(0, 158));
    var og = document.querySelector('meta[property="og:title"]');
    if (og) og.setAttribute("content", title + " | dgnova");
  }

  function setItemContext(item, name, category, sourceType) {
    var ctx = {
      source_type: sourceType, source_section: "detail",
      item_id: item.id, item_name: name, item_category: category || "",
      current_page: window.location.pathname.split("/").pop()
    };
    DL.setEnquiryContext(ctx);
    $("[data-wa-item]").attr("data-item-name", name).attr("data-item-id", item.id);
    $("[data-enquire]").attr("data-item-name", name).attr("data-item-id", item.id)
      .attr("data-source-type", sourceType);
    $(".ec-ref").text("Enquiring about: " + name);
    $('input[name="ctx_item_id"]').val(item.id);
    $('input[name="ctx_item_name"]').val(name);
  }

  function renderRelated(items, current, keys, type) {
    var rel = DL.related(items, current, keys, 3);
    if (!rel.length) { $("[data-related-section]").addClass("d-none"); return; }
    DL.renderInto("[data-related-grid]", rel, type);
  }

  /* ---------------- Renderers ---------------- */

  function renderProperty(p, all) {
    setMeta(p.title, p.summary);
    setBreadcrumbCurrent(p.title);
    fillText("[data-d-title]", p.title);
    fill("[data-d-loc]", '<i class="bi bi-geo-alt"></i> ' + DL.esc(p.location));
    fillText("[data-d-price]", p.priceLabel);
    fill("[data-d-badges]",
      (p.purpose === "sale" ? '<span class="badge-status badge-navy">For Sale</span>' : '<span class="badge-status badge-soft">For Rent</span>') +
      (p.featured ? ' <span class="badge-status badge-green">Featured</span>' : "") +
      (p.new ? ' <span class="badge-status badge-soft-green">New</span>' : ""));

    fill("[data-d-specs]",
      specItem("bi-house-door", "Type", p.type) +
      specItem("bi-door-closed", "Bedrooms", p.bedrooms > 0 ? p.bedrooms : "Studio") +
      specItem("bi-droplet", "Bathrooms", p.bathrooms) +
      specItem("bi-arrows-fullscreen", "Area", Number(p.area).toLocaleString() + " sq ft") +
      specItem("bi-lamp", "Furnishing", p.furnishing) +
      specItem("bi-bookmark", "Purpose", p.purpose === "sale" ? "For Sale" : "For Rent"));

    fill("[data-d-desc]", p.description.split("\n").map(function (para) { return "<p>" + DL.esc(para) + "</p>"; }).join(""));
    fill("[data-d-amenities]", listHTML(p.amenities, "bi-check2-circle"));

    renderGallery(p.gallery, p.title, "property");
    setItemContext(p, p.title, p.type, "real-estate");
    renderRelated(all, p, ["purpose", "type"], "property");
  }

  function renderCar(c, all) {
    setMeta(c.name + " Rental", c.description);
    setBreadcrumbCurrent(c.name);
    fillText("[data-d-title]", c.name);
    fill("[data-d-loc]", '<i class="bi bi-tag"></i> ' + DL.esc(c.category) + " &middot; " + DL.esc(String(c.year)));
    fill("[data-d-badges]",
      (c.available ? '<span class="badge-status badge-soft-green">Available</span>' : '<span class="badge-status badge-dark-soft">Currently Unavailable</span>') +
      (c.featured ? ' <span class="badge-status badge-green">Featured</span>' : ""));

    fill("[data-d-rates]",
      rateCard("Daily", c.pricePerDay, false) +
      rateCard("Weekly", c.pricePerWeek, true) +
      rateCard("Monthly", c.pricePerMonth, false));

    fill("[data-d-specs]",
      specItem("bi-gear-wide-connected", "Transmission", c.transmission) +
      specItem("bi-fuel-pump", "Fuel", c.fuel) +
      specItem("bi-people", "Seats", c.seats) +
      specItem("bi-briefcase", "Luggage", c.luggage + " bags") +
      specItem("bi-door-open", "Doors", c.doors) +
      specItem("bi-stars", "Highlight", c.keyFeature));

    fill("[data-d-desc]", "<p>" + DL.esc(c.description) + "</p>");
    fill("[data-d-features]", listHTML(c.features, "bi-check2-circle"));
    fill("[data-d-conditions]", listHTML(c.conditions, "bi-info-circle"));
    fill("[data-d-documents]", (c.documents || []).map(function (d) {
      return '<div class="doc-item"><i class="bi bi-file-earmark-text"></i><span>' + DL.esc(d) + "</span></div>";
    }).join(""));

    if (!c.available) {
      $("[data-d-unavailable]").removeClass("d-none");
    }

    renderGallery(c.gallery, c.name, "car");
    setItemContext(c, c.name, c.category, "car-rental");
    renderRelated(all, c, ["category", "brand"], "car");
  }

  function renderTour(t, all) {
    setMeta(t.title, t.overview);
    setBreadcrumbCurrent(t.title);
    fillText("[data-d-title]", t.title);
    fill("[data-d-loc]", '<i class="bi bi-geo-alt"></i> ' + DL.esc(t.destination) + ", " + DL.esc(t.country) +
      ' &nbsp;&middot;&nbsp; <i class="bi bi-calendar3"></i> ' + DL.esc(t.durationLabel));
    fillText("[data-d-price]", "Price on Request");
    fill("[data-d-badges]",
      (t.featured ? '<span class="badge-status badge-green">Featured</span>' : "") +
      ' <span class="badge-status badge-soft">' + DL.esc(t.packageType) + "</span>" +
      (t.styles || []).map(function (s) { return ' <span class="badge-status badge-soft">' + DL.esc(s) + "</span>"; }).join(""));

    fill("[data-d-highlights]", listHTML(t.highlights, "bi-star"));
    fill("[data-d-desc]", "<p>" + DL.esc(t.overview) + "</p>");

    fill("[data-d-itinerary]", (t.itinerary || []).map(function (d) {
      return '<li><span class="it-day" aria-label="Day ' + d.day + '">' + d.day + "</span>" +
        "<h3>Day " + d.day + " — " + DL.esc(d.title) + "</h3><p>" + DL.esc(d.detail) + "</p></li>";
    }).join(""));

    fill("[data-d-included]", listHTML(t.included, "bi-check2-circle"));
    fill("[data-d-excluded]", listHTML(t.excluded, "bi-x-circle"));
    fill("[data-d-travelinfo]", listHTML(t.travelInfo, "bi-info-circle"));

    renderGallery(t.gallery, t.title, "tour");
    setItemContext(t, t.title, t.packageType, "tourism");
    renderRelated(all, t, ["country", "packageType"], "tour");
  }

  function renderProduct(p, all) {
    setMeta(p.name, p.shortDescription);
    setBreadcrumbCurrent(p.name);
    fillText("[data-d-title]", p.name);
    fill("[data-d-loc]", '<i class="bi bi-grid"></i> ' + DL.esc(p.category) + ' &nbsp;&middot;&nbsp; <i class="bi bi-globe2"></i> Origin: ' + DL.esc(p.origin));
    fillText("[data-d-price]", "Price on Request");
    fill("[data-d-badges]",
      (p.featured ? '<span class="badge-status badge-green">Featured</span>' : "") +
      (p.availability === "Available" ? ' <span class="badge-status badge-soft-green">Available</span>' : "") +
      (p.availability === "On Request" ? ' <span class="badge-status badge-soft">On Request</span>' : "") +
      (p.availability === "Unavailable" ? ' <span class="badge-status badge-dark-soft">Unavailable</span>' : ""));

    fill("[data-d-desc]", "<p>" + DL.esc(p.description) + "</p>");
    fill("[data-d-specs]", (p.specs || []).map(function (s) {
      return '<div class="spec-item"><i class="bi bi-check2"></i><div><span class="spec-label">' + DL.esc(s.label) + '</span><span class="spec-value">' + DL.esc(s.value) + "</span></div></div>";
    }).join(""));
    fillText("[data-d-moq]", p.moq);

    renderGallery(p.gallery, p.name, "product");
    setItemContext(p, p.name, p.category, "trading");
    renderRelated(all, p, ["category"], "product");
  }

  function specItem(icon, label, value) {
    return '<div class="spec-item"><i class="bi ' + icon + '"></i><div><span class="spec-label">' + DL.esc(label) + '</span><span class="spec-value">' + DL.esc(String(value)) + "</span></div></div>";
  }

  function rateCard(label, price, highlight) {
    return '<div class="rate-card' + (highlight ? " is-highlight" : "") + '">' +
      '<span class="rate-period">' + label + "</span>" +
      '<div class="rate-value">' + DL.formatAED(price) + "</div>" +
    "</div>";
  }

  /* ---------------- Boot ---------------- */

  $(function () {
    var kind = $("body").data("detail");
    if (!kind) return;

    var id = DL.getParam("id");
    var dataset = DATASET[kind];

    if (!id) { showNotFound(kind); return; }

    DL.loadData(dataset).done(function (items) {
      var item = null;
      for (var i = 0; i < items.length; i++) if (items[i].id === id) { item = items[i]; break; }
      if (!item) { showNotFound(kind); return; }

      $("[data-detail-loading]").remove();
      $("[data-detail-content]").removeClass("d-none");

      if (kind === "property") renderProperty(item, items);
      if (kind === "car") renderCar(item, items);
      if (kind === "tour") renderTour(item, items);
      if (kind === "product") renderProduct(item, items);

      $(".reveal").addClass("is-in");
    }).fail(function () { showNotFound(kind); });

    /* Lightbox events */
    $(document).on("click", "[data-lb]", function () { openLightbox(Number($(this).data("lb"))); });
    $(document).on("click", ".lb-close, .lightbox-backdrop", closeLightbox);
    $(document).on("click", ".lb-prev", function (e) { e.stopPropagation(); stepLightbox(-1); });
    $(document).on("click", ".lb-next", function (e) { e.stopPropagation(); stepLightbox(1); });
    $(document).on("keydown", function (e) {
      if (!$(".lightbox").hasClass("is-open")) return;
      if (e.key === "Escape") closeLightbox();
      if (e.key === "ArrowLeft") stepLightbox(-1);
      if (e.key === "ArrowRight") stepLightbox(1);
    });
  });

})(jQuery);
