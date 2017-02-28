/**
 * @file
 * Global ux_ui javascript.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.uxUi = {
    attach: function (context, settings) {
      $('select.ux-ui-select').once('ux_ui').material_select();
    }
  };


})(jQuery, Drupal, document);
