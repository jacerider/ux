/**
 * @file
 * Global ux_form javascript.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.UxForm.models.input = new Drupal.UxForm.ElementModel({
    selector: '.ux-form-input input, .ux-form-input textarea',
    events: {
      change: 'onChange',
      focus: 'onFocus',
      blur: 'onBlur'
    },

    /*
    On change.
     */
    onChange: function (e) {
      var $element = $(e.currentTarget);
      if ($element.val().length !== 0 || this.hasPlaceholder($element)) {
        $element.closest('.js-form-item').addClass('active');
      }
      this.validate($element);
    },

    /*
    On focus.
     */
    onFocus: function (e) {
      var $element = $(e.currentTarget);
      if (!this.isReadonly($element)) {
        $element.closest('.js-form-item').addClass('active focus');
      }
    },

    /*
    On blur.
     */
    onBlur: function (e) {
      var $element = $(e.currentTarget);
      var classes = 'focus';
      if ($element.val().length === 0 && $element[0].validity.badInput !== true && !this.hasPlaceholder($element)) {
        classes += ' active';
      }
      $element.closest('.js-form-item').removeClass(classes);
      this.validate($element);
    },

    /*
    On render.
     */
    onRender: function () {
      var _this = this;
      _this.$el.each(function (index, element) {
        var $wrapper = $(this).closest('.js-form-item');
        if ($(element).val().length > 0 || element.autofocus || _this.hasPlaceholder($(this)) || $(element)[0].validity.badInput === true) {
          $wrapper.addClass('active');
        }
        else {
          $wrapper.removeClass('active');
        }
        setTimeout(function () {
          $wrapper.addClass('ready');
        });
      });
    },

    /*
    On validate.
     */
    onValidate: function ($element) {
      var $wrapper = $element.closest('.js-form-item');
      var hasLength = typeof $element.attr('length') !== 'undefined';
      var lenAttr = hasLength ? parseInt($element.attr('length')) : 0;
      var len = $element.val().length;
      if ($element.val().length === 0 && $element[0].validity.badInput === false) {
        $wrapper.removeClass('valid');
        $wrapper.removeClass('invalid');
        $wrapper.removeAttr('data-error');
        // Check if field is required
        if ($element[0].validity.valueMissing === true) {
          $wrapper.addClass('invalid');
          $wrapper.attr('data-error', $element[0].validationMessage);
        }
      }
      else {
        // Check for character counter attributes
        if (($element[0].validity.valid && hasLength && (len <= lenAttr)) || ($element[0].validity.valid && !hasLength)) {
          $wrapper.removeClass('invalid');
          $wrapper.addClass('valid');
          $wrapper.removeAttr('data-error');
        }
        else {
          $wrapper.removeClass('valid');
          $wrapper.addClass('invalid');
          $wrapper.attr('data-error', $element[0].validationMessage);
        }
      }
    }
  });

  // Add to collection.
  Drupal.UxForm.collection.add(Drupal.UxForm.models.input);

})(jQuery, Drupal);
