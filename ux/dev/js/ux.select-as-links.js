/**
 * @file
 * Select as links javascript.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.uxSelectAsLinks = {
    attach: function (context) {
      $('.ux-select-as-links', context).once('ux-select-as-links').each(function () {
        var $select = $(this).find('select').hide();
        var $links = $(this).find('a');
        var $trigger = $select.closest('form').find('.form-submit:visible').first();

        $links.on('click', function (e) {
          e.preventDefault();
          var value = $(this).data('ux-value');
          $select.val(value);
          $select.trigger('change');
          $links.removeClass('active');
          $(this).addClass('active');

          if ($trigger.length) {
            $trigger.trigger('click');
          }
        });
      });
    }
  };

}(jQuery, Drupal));
