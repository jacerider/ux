/**
 * @file
 * Global ux_form javascript.
 */

(function ($, Modernizr, Drupal) {

  'use strict';

  Drupal.UxForm.models.date = new Drupal.UxForm.ElementModel({
    selector: '.ux-form-date input.form-date',

    /*
    On render.
     */
    onRender: function () {
      // Skip if date are supported by the browser.
      if (Modernizr.inputtypes.date === true) {
        return;
      }
      var _this = this;
      _this.$el.each(function (index, element) {
        var $element = $(this);
        var datepickerSettings = {};
        var dateFormat = $element.data('drupalDateFormat');
        datepickerSettings.format = 'mmmm d, yyyy';
        // The date format is saved in PHP style, we need to convert to jQuery
        // datepicker.
        datepickerSettings.formatSubmit = dateFormat
          .replace('Y', 'yyyy')
          .replace('m', 'mm')
          .replace('d', 'dd');
        // Add min and max date if set on the input.
        if ($element.attr('min')) {
          datepickerSettings.min = $element.attr('min');
        }
        if ($element.attr('max')) {
          datepickerSettings.max = $element.attr('max');
        }
        $element.pickadate(datepickerSettings);
      });
    },

    onRemove: function () {
      var _this = this;
      _this.$el.findOnce('datePicker').pickadate('stop');
    }
  });

  // Add to collection.
  Drupal.UxForm.collection.add(Drupal.UxForm.models.date);

})(jQuery, Modernizr, Drupal);
