(function ($, Drupal, once) {

  'use strict';

  Drupal.behaviors.uxFormTel = {
    attach: function (context) {
      if (!$.fn.inputmask) {
        return;
      }
      $(once('ux-inputmask', 'input.ux-form-inputmask-js', context)).inputmask();
    }
  };

})(jQuery, Drupal, once);
