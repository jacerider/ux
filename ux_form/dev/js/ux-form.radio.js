
(function ($, Drupal, window, document) {

  'use strict';

  var pluginName = 'uxFormRadio';

  function Plugin(element, options) {
    this.element = element;
    this._name = pluginName;
    this._defaults = $.fn.uxFormRadio.defaults;
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
      this.$field = this.$element.find('input');
    },

    /*
    Bind events that trigger methods.
    */
    bindEvents: function () {
      var _this = this;
      _this.$field.on('change' + '.' + _this._name, function () {
        _this.onChange.call(_this);
      }).on('focus' + '.' + _this._name, function () {
        _this.$element.addClass('focused');
      }).on('blur' + '.' + _this._name, function () {
        _this.$element.removeClass('focused');
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
      // .form-wrapper is used as sometimes radios are wrapped in other
      // elements.
      this.$element.closest('.ux-form-radios, .form-wrapper').find('.ux-form-radio.active').removeClass('active');
      if (this.$field.is(':checked')) {
        this.$element.addClass('active');
      }
    }

  });

  $.fn.uxFormRadio = function (options) {
    this.each(function () {
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new Plugin(this, options));
      }
    });
    return this;
  };

  $.fn.uxFormRadio.defaults = {};

  Drupal.behaviors.uxFormRadio = {
    attach: function (context) {
      var $context = $(context);
      $context.find('.ux-form-radio').once('ux-form-radio').uxFormRadio();
    },
    detach: function (context, setting, trigger) {
      if (trigger === 'unload') {
        $(context).find('.ux-form-radio').each(function () {
          var plugin = $(this).data('uxFormRadio');
          if (plugin) {
            plugin.destroy();
          }
        });
      }
    }
  };

})(jQuery, Drupal, window, document);
