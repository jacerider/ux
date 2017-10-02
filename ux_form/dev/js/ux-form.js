(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.uxForm = {
    attach: function (context) {
      this.setLastElement(context);
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
      $(e.target).closest('.ux-form-element-js').toggle(e.value);
      // Drupal.behaviors.uxForm.setLastElement($(e.target).closest('.ux-form'));
    }
  });

})(jQuery, Drupal);
