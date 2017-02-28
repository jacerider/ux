/**
 * @file
 * Global ux_form javascript.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.UxForm.models.radio = new Drupal.UxForm.ElementModel({
    selector: '.ux-form-radio input',
    events: {
      change: 'onChange'
    },

    onChange: function (e) {
      var $element = $(e.currentTarget);
      var $wrapper = $element.closest('.js-form-wrapper');
      $wrapper.find('.js-form-type-radio').removeClass('active');
      if ($element.is(':checked')) {
        $element.closest('.js-form-type-radio').addClass('active');
      }
    },

    /*
    On render.
     */
    onRender: function () {
      var _this = this;
      _this.$el.each(function (index, element) {
        var $element = $(this);
        var $wrapper = $element.closest('.js-form-type-radio');
        if ($element.is(':checked')) {
          $wrapper.addClass('active');
        }
        setTimeout(function () {
          $wrapper.addClass('ready');
        });
      });
    }
  });

  // Add to collection.
  Drupal.UxForm.collection.add(Drupal.UxForm.models.radio);

})(jQuery, Drupal);
