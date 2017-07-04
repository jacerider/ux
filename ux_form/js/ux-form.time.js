(function ($, Drupal, window, document) {

  'use strict';

  Drupal.behaviors.uxFormTime = {
    attach: function (context) {
      var $context = $(context);
      $context.find('.ux-form-time input.form-time').once('ux-form-time').each(function () {
        var $element = $(this);
        $element.on('focus.ux-form-time', function (e) {
          // We blur as soon as the focus happens to avoid the cursor showing
          // momentarily within the field.
          $(this).blur();
        });
        var timepickerSettings = {};
        timepickerSettings.formatSubmit = 'HH:i:00';
        timepickerSettings.container = '#ux-content';
        $element.data('value', $element.val());
        $element.pickatime(timepickerSettings);
      });
    },
    detach: function (context, setting, trigger) {
      if (trigger === 'unload') {
        $(context).find('.ux-form-time input.form-time').each(function () {
          var $element = $(this);
          $element.off('.ux-form-time');
          var plugin = $element.pickatime('picker');
          if (typeof plugin === 'object') {
            plugin.stop();
          }
        });
      }
    }
  };

})(jQuery, Drupal, window, document);
