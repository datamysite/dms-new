/* ============================================================
   dgnova — config.js
   Central site configuration. Replace placeholder values before
   going live. Everything else reads from this object.
   ============================================================ */

window.dgnova_CONFIG = {
  companyName: "dgnova",
  legalName: "dgnova General Trading L.L.C", // placeholder — confirm legal entity name

  // Contact (PLACEHOLDERS — replace with real details)
  phone: "+971 X XXX XXXX",
  phoneHref: "+9710000000000",          // tel: link, digits only after +
  whatsapp: "9710000000000",            // wa.me number, digits only, no +
  email: "info@dgnova.com",          // placeholder

  address: "Office 000, Business Bay, Dubai, United Arab Emirates", // placeholder
  businessHours: "Monday – Saturday, 9:00 AM – 6:00 PM (GST)",

  socialLinks: {
    instagram: "#",
    facebook: "#",
    linkedin: "#",
    x: "#"
  },

  // Enquiry behaviour
  redirectToThankYou: true,   // true: redirect to thank-you.html after submit; false: inline success state
  simulateDelayMs: 900        // simulated network delay for demo submission
};
