
(function ($, Drupal, window, document) {

  'use strict';

  var pluginName = 'uxFormFile';

  function Plugin(element, options) {
    this.element = element;
    this._name = pluginName;
    this._defaults = $.fn.uxFormFile.defaults;
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

      this.$input.insertBefore(this.$field);
      this.$element.addClass('ux-form-input-js').uxFormInput();

      setTimeout(function () {
        _this.$element.addClass('ready');
      });
    },

    /*
    Cache DOM nodes for performance.
     */
    buildCache: function () {
      this.$element = $(this.element);
      this.$field = this.$element.find('input[type=file]');
      this.$input = $('<input class="ux-form-file-path" type="text">');
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
      this.$element.off('.' + this._name);
    },

    /*
    On change event callback.
     */
    onChange: function () {
      var files = this.$field[0].files;
      var file_names = [];
      for (var i = 0; i < files.length; i++) {
        file_names.push(files[i].name);
      }
      // console.log(file_names);
    }

  });

  $.fn.uxFormFile = function (options) {
    this.each(function () {
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new Plugin(this, options));
      }
    });
    return this;
  };

  $.fn.uxFormFile.defaults = {};

  Drupal.behaviors.uxFormFile = {
    attach: function (context) {
      var $context = $(context);
      $context.find('.ux-form-file').once('ux-form-file').uxFormFile();
    },
    detach: function (context, setting, trigger) {
      if (trigger === 'unload') {
        $(context).find('.ux-form-file').each(function () {
          var plugin = $(this).data('uxFormFile');
          if (plugin) {
            plugin.destroy();
          }
        });
      }
    }
  };

})(jQuery, Drupal, window, document);
