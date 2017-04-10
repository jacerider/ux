/**
 * @file
 * Global ux_form javascript.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.UxForm.models.checkbox = new Drupal.UxForm.ElementModel({
    selector: '.ux-form-checkbox input',
    events: {
      change: 'onChange'
    },

    onChange: function (e) {
      var $element = $(e.currentTarget);
      var $checkbox = $element.closest('.js-form-type-checkbox');
      if ($element.is(':checkzed')) {
        $checkbox.addClass('active');
      }
      else {
        $checkbox.removeClass('active');
      }
    },

    /*
    On render.
     */
    onRender: function () {
      var _this = this;
      _this.$el.each(function (index, element) {
        var $element = $(this);
        var $wrapper = $element.closest('.js-form-type-checkbox');
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
  Drupal.UxForm.collection.add(Drupal.UxForm.models.checkbox);

})(jQuery, Drupal);
