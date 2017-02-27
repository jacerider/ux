/**
 * @file
 * Global ux_form javascript.
 */

(function ($, Modernizr, Drupal) {

  'use strict';

  Drupal.behaviors.uxFormDate = {
    attach: function (context, settings) {
      var $context = $(context);
      // Skip if date are supported by the browser.
      if (Modernizr.inputtypes.date === true) {
        return;
      }
      $context.find('input[data-drupal-date-format]').once('datePicker').each(function () {
        var $input = $(this);
        var datepickerSettings = {};
        var dateFormat = $input.data('drupalDateFormat');
        datepickerSettings.format = 'mmmm d, yyyy';
        // The date format is saved in PHP style, we need to convert to jQuery
        // datepicker.
        datepickerSettings.formatSubmit = dateFormat
          .replace('Y', 'yyyy')
          .replace('m', 'mm')
          .replace('d', 'dd');
        // Add min and max date if set on the input.
        if ($input.attr('min')) {
          datepickerSettings.min = $input.attr('min');
        }
        if ($input.attr('max')) {
          datepickerSettings.max = $input.attr('max');
        }
        $input.pickadate(datepickerSettings);
      });
    },
    detach: function (context, settings, trigger) {
      if (trigger === 'unload') {
        $(context).find('input[data-drupal-date-format]').findOnce('datePicker').pickadate('destroy');
      }
    }
  };

})(jQuery, Modernizr, Drupal);
