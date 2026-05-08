/**
 * @file
 * Select as links javascript.
 */

(function ($, Drupal, once) {

  'use strict';

  Drupal.behaviors.uxButton = {
    attach: function (context) {
      $(once('ux-button', '.ux-button-trigger', context)).on('click', function (e) {
        e.preventDefault();
        $(this).closest('.ux-button').find('input[type="submit"]').trigger('mousedown').trigger('mouseup').trigger('click');
      });
    }
  };

}(jQuery, Drupal, once));
