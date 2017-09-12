// $("#myList").sapling();
/**
 * @file
 * Global ux_menu_tree javascript.
 */

/* eslint-disable no-alert, no-console */
(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.uxMenuTree = {
    _defaults: {},

    attach: function (context, settings) {
      $('.uxMenuTree:visible > .menu', context).uxMenuTree({
        animation: true
      });
    },

    detach: function (context, setting, trigger) {
      if (trigger === 'unload') {
        $(context).find('.uxMenuTree').each(function () {
          var plugin = $(this).data('uxMenuTree');
          if (plugin) {
            plugin.destroy();
          }
        });
      }
    }
  };

})(jQuery, Drupal, drupalSettings);
