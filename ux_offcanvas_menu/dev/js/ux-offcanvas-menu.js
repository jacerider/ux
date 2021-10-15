/**
 * @file
 * Global ux_offcanvas javascript.
 */

/* eslint-disable no-alert, no-console */
(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.uxOffcanvasMenu = {
    attach: function (context, settings) {
      if (settings.ux && settings.ux.offcanvasMenu && settings.ux.offcanvasMenu.items) {
        for (var id in settings.ux.offcanvasMenu.items) {
          if (settings.ux.offcanvasMenu.items[id]) {
            var $element = $('#ux-offcanvas-' + id);
            if ($element.length) {
              $element.ux_offcanvas_menu(settings.ux.offcanvasMenu.items[id]);
            }
          }
        }
      }
    }
  };

})(jQuery, Drupal, drupalSettings, document);
