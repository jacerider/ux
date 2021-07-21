(function ($, Drupal, window, document) {

  'use strict';

  Drupal.behaviors.uxFormTime = {
    attach: function (context) {
      var $context = $(context);
      $context.find('.ux-form-time input.form-time').once('ux-form-time').each(function () {
        var $element = $(this);
        var timepickerSettings = {};
        timepickerSettings.formatSubmit = 'HH:i:00';
        timepickerSettings.container = '#ux-content';
        $element.data('value', $element.val());
        $element.pickatime(timepickerSettings);
      });
    },
    detach: function (context) {
      var $context = $(context);
      var plugin = $context.find('.ux-form-time input.form-time').pickatime('picker');
      if (typeof plugin === 'object') {
        plugin.stop();
      }
    }
  };

})(jQuery, Drupal, window, document);
