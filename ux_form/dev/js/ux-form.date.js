(function ($, Drupal, window, document) {

  'use strict';

  Drupal.behaviors.uxFormDate = {
    attach: function (context) {
      var _this = this;
      var $context = $(context);
      $context.find('.ux-form-date input.form-date').once('ux-form-date').each(function () {
        var $element = $(this);
        var datepickerSettings = {};
        var dateFormat = $element.data('drupalDateFormat');
        datepickerSettings.format = 'mmmm d, yyyy';
        // The date format is saved in PHP style, we need to convert to jQuery
        // datepicker.
        datepickerSettings.formatSubmit = _this.formatDateAsString(dateFormat);
        // Add min and max date if set on the input.
        if ($element.attr('min')) {
          datepickerSettings.min = _this.formatDateAsArray($element.attr('min'));
        }
        if ($element.attr('max')) {
          datepickerSettings.max = _this.formatDateAsArray($element.attr('max'));
        }
        datepickerSettings.container = '#ux-content';
        // Set default value.
        $element.data('value', $element.val());
        $element.pickadate(datepickerSettings);
      });
    },
    formatDateAsString: function (string) {
      return string.replace('Y', 'yyyy')
        .replace('m', 'mm')
        .replace('d', 'dd');
    },
    formatDateAsArray: function (string) {
      return string.split('-');
    },
    detach: function (context) {
      $(context).find('.ux-form-date input.form-date').each(function () {
        var plugin = $(this).pickadate('picker');
        if (typeof plugin === 'object') {
          plugin.$node.val($(plugin._hidden).val());
          plugin.stop();
        }
      });
    }
  };

})(jQuery, Drupal, window, document);
