/**
 * @file
 * Global ux_menu javascript.
 */

/* eslint-disable no-alert, no-console */
(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.uxMenu = {
    _defaults: {},

    attach: function (context, settings) {
      var _this = this;
      if (settings.ux && settings.ux.menu && settings.ux.menu.items) {
        console.log(settings.ux.menu);
        for (var id in settings.ux.menu.items) {
          if (settings.ux.menu.items[id]) {
            var $element = $('#' + id).once('ux-menu');
            if ($element.length) {
              var options = _this.getOptions(settings.ux.menu, id);
              $element.uxMenu(options);
            }
          }
        }
      }
    },

    detach: function (context, setting, trigger) {
      if (trigger === 'unload') {
        $(context).find('.ux-menu').each(function () {
          var plugin = $(this).data('uxMenu');
          if (plugin) {
            plugin.destroy();
          }
        });
      }
    },

    getOptions: function (settings, id) {
      var _this = this;
      var options = $.extend({}, _this._defaults, settings.options, settings.items[id]);
      return options;
    }
  };

})(jQuery, Drupal, drupalSettings);
