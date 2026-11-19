/* dgnova shared scripts */
(function () {
  'use strict';
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  // Mobile menu
  var burger = $('.burger'), nav = $('#nav');
  if (burger && nav) {
    burger.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      burger.setAttribute('aria-expanded', open);
    });
  }

  // Business area tabs
  var tabs = $$('.tabs [role=tab]'), panels = $$('.panel');
  tabs.forEach(function (t) {
    t.addEventListener('click', function () {
      tabs.forEach(function (b) { b.setAttribute('aria-selected', b === t); });
      panels.forEach(function (p) { p.hidden = p.getAttribute('data-panel') !== t.getAttribute('data-tab'); });
    });
  });

  // FAQ accordion
  $$('.acc button').forEach(function (b) {
    b.addEventListener('click', function () {
      var open = b.getAttribute('aria-expanded') === 'true';
      b.setAttribute('aria-expanded', !open);
      document.getElementById(b.getAttribute('aria-controls')).hidden = open;
    });
  });

  // Enquiry modal: keeps source reference automatically
  var modal = $('#modal'), last;
  function openModal(src, trigger) {
    last = trigger;
    $('#source').value = src;
    $('#srcTxt').textContent = src;
    $('#ok').hidden = true;
    modal.hidden = false;
    $('input[name=name]', modal).focus();
  }
  function closeModal() { modal.hidden = true; if (last) last.focus(); }
  $$('[data-enquiry]').forEach(function (b) {
    b.addEventListener('click', function () { openModal(b.getAttribute('data-enquiry'), b); });
  });
  $('.mclose').addEventListener('click', closeModal);
  modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) closeModal(); });
  $('#enqForm').addEventListener('submit', function (e) {
    e.preventDefault();
    // TODO: connect to the existing enquiry endpoint, sending the "source" field with the form data.
    $('#ok').hidden = false;
    e.target.reset();
    $('#source').value = $('#srcTxt').textContent;
  });
})();
