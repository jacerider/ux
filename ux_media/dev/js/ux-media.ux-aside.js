/**
 * @file ux-media.ux-aside.js
 *
 * Defines the behavior of the entity browser's UX aside display.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.entityBrowserAside = {};

  Drupal.AjaxCommands.prototype.select_entities = function (ajax, response, status) {
    var uuid = drupalSettings.entity_browser.ux_aside.uuid;

    $(':input[data-uuid="' + uuid + '"]').trigger('entities-selected', [uuid, response.entities])
      .removeClass('entity-browser-processed').unbind('entities-selected');
  };

  /**
   * Registers behaviours related to ux_aside display.
   */
  Drupal.behaviors.entityBrowserAside = {
    attach: function (context) {
      _.each(drupalSettings.entity_browser.ux_aside, function (instance) {
        _.each(instance.js_callbacks, function (callback) {
          // Get the callback.
          callback = callback.split('.');
          var fn = window;

          for (var j = 0; j < callback.length; j++) {
            fn = fn[callback[j]];
          }

          if (typeof fn === 'function') {
            $(':input[data-uuid="' + instance.uuid + '"]').not('.entity-browser-processed')
              .bind('entities-selected', fn).addClass('entity-browser-processed');
          }
        });
        if (instance.auto_open) {
          $('input[data-uuid="' + instance.uuid + '"]').click();
        }
      });
    }
  };

}(jQuery, Drupal, drupalSettings));
