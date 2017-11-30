(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.uxFormTel = {
    attach: function (context) {
      if (!$.fn.inputmask) {
        return;
      }
      $(context).find('input.ux-form-inputmask-js').once('ux-inputmask').inputmask();
    }
  };

})(jQuery, Drupal);
