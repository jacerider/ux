/**
 * @file
 * Global ux_aside javascript.
 */

/* eslint-disable no-alert, no-console */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.uxFiltersSummary = {

    attach: function (context, settings) {
      // 'this' references the form element.
      function triggerSubmit(e) {
        $(this).closest('form').find('.form-actions [type="submit"]').first().trigger('click');
      }

      $('.ux-filters-summary-item', context).once().each(function () {
        var $item = $(this);
        var field = $item.data('ux-filters-summary-field');
        var $field = $(':input[name="' + field + '"]');

        $('.ux-filters-summary-value', this).each(function () {
          $('<i class="fa-times"></i>').appendTo($(this));
        }).on('click', function (e) {
          e.preventDefault();
          var value = $(this).data('ux-filters-summary-value');
          switch ($field.get(0).tagName) {
            case 'SELECT':
              $('option[value="' + value + '"]', $field).prop('selected', false);
              triggerSubmit.call($field);
              break;

            case 'INPUT':
              $field.val('');
              triggerSubmit.call($field);
              break;
          }
        });
      });
    }
  };

})(jQuery, Drupal);
