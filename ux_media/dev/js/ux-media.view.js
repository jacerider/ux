/**
 * @file
 * Defines the behavior of the media entity browser view.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Attaches the behavior of the media entity browser view.
   */
  Drupal.behaviors.uxMediaView = {
    attach: function (context, settings) {
      var cardinality = parent && parent.drupalSettings && parent.drupalSettings.ux_media_aside && parent.drupalSettings.ux_media_aside.cardinality ? parent.drupalSettings.ux_media_aside.cardinality : 0;
      $('.views-row', context).once('ux-media-view').each(function () {
        var $row = $(this);
        var $input = $row.find('.views-field-entity-browser-select input');
        $('<div class="ux-media-check"><svg class="ux-media-checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="ux-media-checkmark--circle" cx="26" cy="26" r="25" fill="none"/><path class="ux-media-checkmark--check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg></div>').appendTo($row);
        if ($input.prop('checked')) {
          $row.addClass('checked');
        }
      }).click(function () {
        var selector = '.views-field-entity-browser-select input';
        var $row = $(this);
        var $input = $row.find(selector);
        var checked = !$input.prop('checked');
        $input.prop('checked', checked);
        if (checked) {
          if (cardinality > 0) {
            var $rowChecked = $('.views-row.checked');
            if ($rowChecked.length >= cardinality) {
              $rowChecked.first().removeClass('checked').addClass('unchecked');
              $rowChecked.first().find(selector).prop('checked', false);
            }
          }
          $row.removeClass('unchecked').addClass('checked');
        }
        else {
          $row.removeClass('checked').addClass('unchecked');
        }
      });

      if (parent) {
        $(document).once('ux-media-view').on('ajaxSuccess', function () {
          // Tell parent to resize.
          parent.jQuery(parent.document).trigger('uxAsideResize');
        });
      }
    }
  };

}(jQuery, Drupal));
