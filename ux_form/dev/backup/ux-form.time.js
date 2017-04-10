/**
 * @file
 * Global ux_form javascript.
 */

(function ($, Modernizr, Drupal) {

  'use strict';

  Drupal.UxForm.models.time = new Drupal.UxForm.ElementModel({
    selector: '.ux-form-time input.form-time',

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
        var $input = $(this);
        var timepickerSettings = {};
        timepickerSettings.formatSubmit = 'HH:i:00';
        $input.pickatime(timepickerSettings);
      });
    },

    onRemove: function () {
      var _this = this;
      _this.$el.findOnce('datePicker').pickadate('stop');
    }
  });

  // Add to collection.
  Drupal.UxForm.collection.add(Drupal.UxForm.models.time);

})(jQuery, Modernizr, Drupal);
