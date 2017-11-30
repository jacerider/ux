(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.uxForm = {
    attach: function (context) {
      // $('.ux-form-element-js')
      // this.setLastElement(context);
    },

    setLastElement: function (context) {
      // var $context = $(context);
      // $context.find('.ux-form-container-js').each(function () {
      //   $(this).find('.ux-form-element-js').removeClass('ux-form-element-last').filter(':visible:last').addClass('ux-form-element-last');
      // });
    }
  };

  var $document = $(document);
  $document.on('state:visible', function (e) {
    if (e.trigger) {
      // Integrate with state API to hide parent wrapper elements.
      $(e.target).closest('.ux-form-element-js').toggle(e.value);
    }
  });

})(jQuery, Drupal);
