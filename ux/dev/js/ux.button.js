/**
 * @file
 * Select as links javascript.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.uxButton = {
    attach: function (context) {
      $('.ux-button-trigger', context).once('ux-button').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.ux-button').find('input[type="submit"]').trigger('mousedown').trigger('mouseup').trigger('click');
      });
    }
  };

}(jQuery, Drupal));
