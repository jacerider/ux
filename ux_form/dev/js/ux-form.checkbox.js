
(function ($, Drupal, window, document) {

  'use strict';

  var pluginName = 'uxFormCheckbox';

  function Plugin(element, options) {
    this.element = element;
    this._name = pluginName;
    this._defaults = $.fn.uxFormCheckbox.defaults;
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
      if (this.$element.hasClass('form-no-label')) {
        var label = this.$element.find('label');
        label.removeClass('visually-hidden');
        label.html('<span class="visually-hidden">' + label.html() + '</span>');
      }
      if (this.$field.is(':checked')) {
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
      this.$element = $(this.element);
      this.$field = this.$element.find('input.form-checkbox');
    },

    /*
    Bind events that trigger methods.
    */
    bindEvents: function () {
      var _this = this;
      _this.$field.on('change' + '.' + _this._name, function () {
        _this.onChange.call(_this);
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
      if (this.$field.is(':checked')) {
        this.$element.addClass('active');
      }
      else {
        this.$element.removeClass('active');
      }
    }

  });

  $.fn.uxFormCheckbox = function (options) {
    this.each(function () {
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new Plugin(this, options));
      }
    });
    return this;
  };

  $.fn.uxFormCheckbox.defaults = {};

  Drupal.behaviors.uxFormCheckbox = {
    attach: function (context) {
      var $context = $(context);
      $context.find('.ux-form-checkbox').once('ux-form-checkbox').uxFormCheckbox();
    },
    detach: function (context) {
      $(context).find('.ux-form-checkbox').each(function () {
        var plugin = $(this).data('uxFormCheckbox');
        if (plugin) {
          plugin.destroy();
        }
      });
    }
  };

})(jQuery, Drupal, window, document);
