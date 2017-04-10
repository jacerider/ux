/**
 * @file
 * Global ux_offcanvas javascript.
 */

/* eslint-disable no-alert, no-console */
(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.uxOffcanvasMenu = {
    attach: function (context, settings) {
      $('.ux-offcanvas-menu-wrapper').once('ux_offcanvas_menu').each(function () {
        var options = {};
        options.trailType = $(this).find('.ux-offcanvas-menu').attr('data-trail');
        $(this).ux_offcanvas_menu(options);
      });
    }
  };

})(jQuery, Drupal, drupalSettings, document);
