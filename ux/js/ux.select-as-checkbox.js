/**
 * @file
 * Select as checkbox javascript.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.uxSelectAsCheckbox = {
    attach: function (context, settings) {
      if (settings.ux && settings.ux.theme && settings.ux.theme.select_as_checkbox) {
        for (var id in settings.ux.theme.select_as_checkbox) {
          if (settings.ux.theme.select_as_checkbox[id]) {
            $('#' + id + '-checkbox', context).once('ux-select-as-checkbox').data('ux-select-as-checkbox', id).change(function () {
              var id = $(this).data('ux-select-as-checkbox');
              var $select = $('#' + id + '-select');
              if ($(this).is(':checked')) {
                $select.val(1);
              }
              else {
                $select.val('All');
              }
              if (typeof Drupal.UxForm !== 'undefined') {
                Drupal.UxForm.updateFields();
              }
            });
          }
        }
      }
    }
  };

}(jQuery, Drupal));
