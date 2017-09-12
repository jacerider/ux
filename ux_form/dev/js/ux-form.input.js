
(function ($, Drupal, window, document) {

  'use strict';

  var pluginName = 'uxFormInput';

  function Plugin(element, options) {
    this.element = element;
    this._name = pluginName;
    this._defaults = $.fn.uxFormInput.defaults;
    this.options = $.extend({}, this._defaults, options);
    this.init();
  }

  // Avoid Plugin.prototype conflicts
  $.extend(Plugin.prototype, {

    /*
    Initialize plugin instance.
     */
    init: function () {
      this.buildCache();
      this.bindEvents();
      this.buildElement();
    },

    /*
    Remove plugin instance complete.
     */
    destroy: function () {
      this.unbindEvents();
      this.$element.removeData();
    },

    /*
    Process fields.
     */
    buildElement: function () {
      var _this = this;
      if (this.hasValue() || this.isAutofocus() || this.hasPlaceholder() || this.hasBadInput()) {
        this.$element.addClass('active');
      }
      setTimeout(function () {
        _this.$element.addClass('ready');
      });
    },

    /*
    Cache DOM nodes for performance.
     */
    buildCache: function () {
      var _this = this;
      _this.$element = $(this.element);
      _this.input_selector = '.ux-form-input-item-js';
      _this.$field = _this.$element.find(_this.input_selector);
      _this.$field.each(function (e) {
        var $suffix = _this.$element.find('.field-suffix');
        if ($suffix.length) {
          $suffix.after('<div class="ux-form-input-line" />');
        }
        else {
          $(this).after('<div class="ux-form-input-line" />');
        }
      });
      if (_this.hasError()) {
        _this.$element.addClass('invalid');
      }
    },

    /*
    Bind events that trigger methods.
    */
    bindEvents: function () {
      var _this = this;
      _this.$field.on('change' + '.' + _this._name, function () {
        _this.onChange.call(_this);
      });
      _this.$field.on('focus' + '.' + _this._name, function () {
        _this.onFocus.call(_this);
      });
      _this.$field.on('blur' + '.' + _this._name, function () {
        _this.onBlur.call(_this);
      });
    },

    /*
    Unbind events that trigger methods.
    */
    unbindEvents: function () {
      this.$field.off('.' + this._name);
    },

    /*
    On change event callback.
     */
    onChange: function () {
      if (this.hasValue() || this.hasPlaceholder()) {
        this.$element.addClass('active');
      }
      this.validate();
    },

    /*
    On focus event callback.
     */
    onFocus: function () {
      if (!this.isReadonly()) {
        this.$element.addClass('active focus');
      }
    },

    /*
    On blur event callback.
     */
    onBlur: function () {
      var classes = 'focus';
      if (!this.hasValue() && this.isValid() && !this.hasPlaceholder()) {
        classes += ' active';
      }
      this.$element.removeClass(classes);
      this.validate();
    },

    /*
    Validate the field.
     */
    validate: function () {
      this.$element.removeClass('valid invalid').removeAttr('data-error');
      if (this.isValid()) {
        if (this.hasValue()) {
          this.$element.addClass('valid');
        }
      }
      else {
        this.$element.addClass('invalid').attr('data-error', this.$field[0].validationMessage);
      }
    },

    /*
    Check if element has a placeholder.
     */
    hasPlaceholder: function () {
      var placeholder = this.$field.attr('placeholder');
      return typeof placeholder !== 'undefined' && placeholder.length > 0;
    },

    /*
    Check if element has value.
     */
    hasValue: function () {
      return this.$field.val().length > 0;
    },

    /*
    Check if element has error.
     */
    hasError: function () {
      return this.$field.hasClass('error');
    },

    /*
    Check if element has bad input.
     */
    hasBadInput: function () {
      return this.$field[0].validity.badInput === true;
    },

    /*
    Check if element value is valid.
     */
    isValid: function () {
      return this.$field[0].validity.valid === true;
    },

    /*
    Check if element is set as autofocus..
     */
    isAutofocus: function () {
      var autofocus = this.$field.attr('autofocus');
      return typeof autofocus !== 'undefined';
    },

    /*
    Check if element has a placeholder.
     */
    isReadonly: function () {
      var readonly = this.$field.attr('readonly');
      return typeof readonly !== 'undefined';
    }

  });

  $.fn.uxFormInput = function (options) {
    this.each(function () {
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new Plugin(this, options));
      }
    });
    return this;
  };

  $.fn.uxFormInput.defaults = {};

  Drupal.behaviors.uxFormInput = {
    attach: function (context) {
      var $context = $(context);
      $context.find('.ux-form-input-js').once('ux-form-input').uxFormInput();
    }
    // @see https://www.drupal.org/node/2692453
    // detach: function (context, setting, trigger) {
    //   if (trigger === 'unload') {
    //     $(context).find('.ux-form-input-js').each(function () {
    //       var plugin = $(this).data('uxFormInput');
    //       if (plugin) {
    //         plugin.destroy();
    //       }
    //     });
    //   }
    // }
  };

})(jQuery, Drupal, window, document);
