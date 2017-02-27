/**
 * @file
 * Global ux_form javascript.
 */

(function ($, Modernizr, Drupal) {

  'use strict';

  Drupal.behaviors.uxFormTime = {
    attach: function (context, settings) {
      var $context = $(context);
      // Skip if date are supported by the browser.
      if (Modernizr.inputtypes.date === true) {
        return;
      }
      $context.find('input[type=time]').once('datePicker').each(function () {
        var $input = $(this);
        var timepickerSettings = {};
        timepickerSettings.formatSubmit = 'HH:i:00';
        $input.pickatime(timepickerSettings);
      });
    },
    detach: function (context, settings, trigger) {
      // if (trigger === 'unload') {
      //   $(context).find('input[data-drupal-date-format]').findOnce('datePicker').pickadate('destroy');
      // }
    }
  };

})(jQuery, Modernizr, Drupal);
