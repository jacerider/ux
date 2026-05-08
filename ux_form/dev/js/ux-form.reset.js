/**
 * @file
 * Reset form on back javascript.
 */

(function ($, Drupal, once) {

  'use strict';

  Drupal.behaviors.uxFormReset = {
    attach: function (context) {
      $(once('ux-form-reset', window)).on('pageshow', function () {
        // Form elements with data-ux-form-reset will be reset to the value
        // of the data element when the page is returned to.
        $('form.ux-form :input[data-ux-form-reset]').each(function () {
          var newValue = $(this).data('ux-form-reset');
          if (newValue !== $(this).val()) {
            $(this).val($(this).data('ux-form-reset')).trigger('change');
          }
        });
      });
    }
  };

}(jQuery, Drupal, once));
