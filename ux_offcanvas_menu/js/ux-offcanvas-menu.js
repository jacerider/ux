/**
 * @file
 * Global ux_offcanvas javascript.
 */

/* eslint-disable no-alert, no-console */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.uxOffcanvasMenu = {
    attach: function (context, settings) {
      $('.ux-offcanvas-menu-wrapper').once('ux_offcanvas_menu').ux_offcanvas_menu();
    }
  };

})(jQuery, Drupal, document);
