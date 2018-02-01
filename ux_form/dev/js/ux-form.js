(function ($, Drupal, debounce) {

  'use strict';

  var timer;
  function setLastElement(context) {
    clearTimeout(timer);
    timer = setTimeout(function () {
      $('.form-wrapper > .ux-for-element-last').removeClass('ux-for-element-last');
      $('.field--name-field-ethnicity.form-wrapper').each(function () {
        $(this).find('> :visible').last().addClass('ux-for-element-last');
      });
    }, 100);
  }

  Drupal.behaviors.uxForm = {
    attach: function (context) {
      setLastElement();
    }
  };

  var $document = $(document);
  $document.on('state:visible', function (e) {
    if (e.trigger) {
      setLastElement();
      // Integrate with state API to hide parent wrapper elements.
      $(e.target).closest('.ux-form-element-js').toggle(e.value);
    }
  });

})(jQuery, Drupal, Drupal.debounce);
