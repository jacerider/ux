/**
 * @file
 * JavaScript behaviors for jquery.inputmask integration.
 */

(function ($, Drupal, once) {

  'use strict';

  /**
   * Initialize input masks.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.webformInputMask = {
    attach: function (context) {
      if (!$.fn.inputmask) {
        return;
      }

      $(once('webform-input-mask', 'input.js-webform-input-mask', context)).inputmask({showMaskOnHover: false});
    }
  };

})(jQuery, Drupal, once);
