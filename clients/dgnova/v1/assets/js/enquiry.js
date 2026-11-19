/* ============================================================
   dgnova — enquiry.js
   One enquiry architecture for the whole site.

   Context object carried with every enquiry:
     { source_type, source_section, item_id, item_name,
       item_category, current_page }

   Triggers:
     [data-enquire]                 → opens the global modal
     [data-enquire-item="id"]      → modal pre-filled for an item
     .enquiry-form (inline/detail) → validated + simulated submit
     [data-wa-item]                → WhatsApp with context message
   ============================================================ */

(function ($) {
  "use strict";

  var CFG = window.dgnova_CONFIG || {};
  window.DL = window.DL || {};

  var currentContext = null;

  /* ---------------- Context ---------------- */

  function buildContext($trigger) {
    var page = window.location.pathname.split("/").pop() || "index.html";
    return {
      source_type: $trigger.data("source-type") || $("body").data("division") || "general",
      source_section: $trigger.data("source-section") || $trigger.closest("section").attr("id") || "page",
      item_id: $trigger.data("item-id") || DL.getParam("id") || "",
      item_name: $trigger.data("item-name") || "",
      item_category: $trigger.data("item-category") || "",
      current_page: page
    };
  }

  function contextLine(ctx) {
    if (ctx.item_name) return "Enquire About: " + ctx.item_name;
    var labels = {
      "real-estate": "Real Estate Enquiry",
      "car-rental": "Car Rental Enquiry",
      "import-export": "Car Import & Export Enquiry",
      "tourism": "Travel & Tourism Enquiry",
      "trading": "General Trading Enquiry",
      "general": "General Enquiry"
    };
    return labels[ctx.source_type] || "General Enquiry";
  }

  /* ---------------- Modal ---------------- */

  function openModal(ctx) {
    currentContext = ctx;
    var $modal = $("#enquiryModal");
    if (!$modal.length) return;

    $modal.find(".me-context h2").text(contextLine(ctx));
    $modal.find(".me-context .me-service").text(ctx.item_id ? "Reference: " + ctx.item_id : "We usually respond within one business day.");
    $modal.find('[name="ctx_item_id"]').val(ctx.item_id);
    $modal.find('[name="ctx_item_name"]').val(ctx.item_name);
    $modal.find('[name="ctx_source_type"]').val(ctx.source_type);

    // Preselect service type where it maps
    var svcMap = {
      "real-estate": "Real Estate", "car-rental": "Car Rental",
      "import-export": "Car Import & Export", "tourism": "Travel & Tourism",
      "trading": "General Trading"
    };
    if (svcMap[ctx.source_type]) $modal.find('select[name="service"]').val(svcMap[ctx.source_type]);

    // Reset state
    var $form = $modal.find("form");
    resetForm($form);

    bootstrap.Modal.getOrCreateInstance($modal[0]).show();
  }

  /* ---------------- Validation ---------------- */

  function validEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v); }
  function validPhone(v) { return /^[+]?[\d\s\-()]{7,18}$/.test(v); }

  function validateForm($form) {
    var ok = true;

    function check($el, valid, msg) {
      var $fb = $el.closest(".form-group, .mb-3, .col-12, .col-md-6").find(".invalid-feedback").first();
      if (!valid) {
        $el.addClass("is-invalid");
        if (msg && $fb.length) $fb.text(msg);
        ok = false;
      } else {
        $el.removeClass("is-invalid");
      }
    }

    var $name = $form.find('[name="name"]');
    check($name, $.trim($name.val()).length >= 2, "Please enter your full name.");

    var $email = $form.find('[name="email"]');
    check($email, validEmail($.trim($email.val())), "Please enter a valid email address.");

    var $phone = $form.find('[name="phone"]');
    check($phone, validPhone($.trim($phone.val())), "Please enter a valid phone number.");

    var $svc = $form.find('select[name="service"], select[name="enquiryType"]').first();
    if ($svc.length) check($svc, $svc.val() !== "" && $svc.val() !== null, "Please choose an option.");

    var $msg = $form.find('[name="message"]');
    if ($msg.length) check($msg, $.trim($msg.val()).length >= 10, "Please add a short message (at least 10 characters).");

    var $consent = $form.find('[name="consent"]');
    if ($consent.length) {
      var cOk = $consent.is(":checked");
      $consent.toggleClass("is-invalid", !cOk);
      if (!cOk) ok = false;
    }

    return ok;
  }

  function resetForm($form) {
    $form[0].reset();
    $form.find(".is-invalid").removeClass("is-invalid");
    $form.find(".form-success").addClass("d-none");
    $form.find("fieldset, .form-body").removeClass("d-none");
    $form.find('[type="submit"]').removeClass("is-loading").prop("disabled", false);
  }

  /* ---------------- Submit (simulated) ---------------- */

  function handleSubmit($form) {
    if (!validateForm($form)) {
      $form.find(".is-invalid").first().trigger("focus");
      return;
    }

    var $btn = $form.find('[type="submit"]');
    $btn.addClass("is-loading").prop("disabled", true);

    var payload = {
      name: $form.find('[name="name"]').val(),
      email: $form.find('[name="email"]').val(),
      phone: $form.find('[name="phone"]').val(),
      service: ($form.find('[name="service"], [name="enquiryType"]').val() || ""),
      message: $form.find('[name="message"]').val(),
      preferredContact: $form.find('[name="preferredContact"]:checked').val() || "Phone",
      context: currentContext || buildContext($form)
    };

    // Extra import/export fields if present
    ["ieDirection", "vehicleMake", "vehicleModel", "vehicleYear", "origin", "destination"].forEach(function (k) {
      var $el = $form.find('[name="' + k + '"]');
      if ($el.length) payload[k] = $el.val();
    });

    // Simulated network call — replace with a real endpoint on launch
    setTimeout(function () {
      try { sessionStorage.setItem("dgnova_last_enquiry", JSON.stringify(payload)); } catch (e) {}

      if (CFG.redirectToThankYou && !$form.data("inline-success")) {
        window.location.href = "thank-you.html";
        return;
      }

      // Inline success state
      $btn.removeClass("is-loading").prop("disabled", false);
      $form.find("fieldset, .form-body").addClass("d-none");
      $form.find(".form-success").removeClass("d-none");
    }, CFG.simulateDelayMs || 900);
  }

  /* ---------------- WhatsApp context messages ---------------- */

  function waMessageFor($el) {
    var name = $el.data("item-name");
    var id = $el.data("item-id");
    if (name) {
      var msg = "Hi dgnova, I'm interested in " + name + ".";
      if (id) msg += " Reference: " + id + ".";
      msg += " Please share more details.";
      return msg;
    }
    var division = $el.data("source-type") || $("body").data("division");
    var byDivision = {
      "real-estate": "Hi dgnova, I'd like to enquire about your real estate listings.",
      "car-rental": "Hi dgnova, I'd like to enquire about renting a car.",
      "import-export": "Hi dgnova, I'd like to discuss a vehicle import/export requirement.",
      "tourism": "Hi dgnova, I'd like to plan a trip and get a quote.",
      "trading": "Hi dgnova, I'd like to request a trade quote."
    };
    return byDivision[division] || "Hi dgnova, I have an enquiry.";
  }

  /* ---------------- Wire-up ---------------- */

  $(function () {

    // Modal triggers
    $(document).on("click", "[data-enquire]", function (e) {
      e.preventDefault();
      openModal(buildContext($(this)));
    });

    // WhatsApp contextual triggers
    $(document).on("click", "[data-wa-item]", function (e) {
      e.preventDefault();
      window.open(DL.waLink(waMessageFor($(this))), "_blank", "noopener");
    });

    // All enquiry forms
    $(document).on("submit", "form.enquiry-form", function (e) {
      e.preventDefault();
      currentContext = currentContext || buildContext($(this));
      handleSubmit($(this));
    });

    // Live-clear invalid state
    $(document).on("input change", "form.enquiry-form .is-invalid", function () {
      $(this).removeClass("is-invalid");
    });

    // Reset modal on close
    var modalEl = document.getElementById("enquiryModal");
    if (modalEl) modalEl.addEventListener("hidden.bs.modal", function () {
      resetForm($(modalEl).find("form"));
      currentContext = null;
    });
  });

  // Expose for details.js (sets context for inline detail forms)
  DL.setEnquiryContext = function (ctx) { currentContext = ctx; };
  DL.openEnquiryModal = openModal;
  DL.buildEnquiryContext = buildContext;

})(jQuery);
