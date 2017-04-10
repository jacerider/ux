
(function ($, Drupal, window, document) {

  'use strict';

  // Add posibility to scroll to selected option
  // usefull for select for example
  $.fn.scrollTo = function (element) {
    $(this).scrollTop($(this).scrollTop() - $(this).offset().top + $(element).offset().top);
    return this;
  };

  var pluginName = 'uxFormDropdown';

  function Plugin(element, options) {
    this.element = element;
    this._name = pluginName;
    this._defaults = $.fn.uxFormDropdown.defaults;
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
      this.buildElement();
      this.bindEvents();
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
      this.$element = $(this.element);
      this.$activates = $('#' + this.$element.attr('data-activates'));
      this.isFocused = false;
      this.updateOptions();
      // Attach dropdown to its activator
      this.$element.after(this.$activates);

      setTimeout(function () {
        _this.$element.addClass('ready');
      });
    },

    /*
    Cache DOM nodes for performance.
     */
    buildCache: function () {
      this.$element = $(this.element);
    },

    /*
    Bind events that trigger methods.
    */
    bindEvents: function () {
      var _this = this;
      _this.$element.on('open' + '.' + _this._name, function (e, eventType) {
        _this.show.call(_this, eventType);
      });
      _this.$element.on('close' + '.' + _this._name, function (e, eventType) {
        _this.hide.call(_this);
      });
    },

    /*
    Unbind events that trigger methods.
    */
    unbindEvents: function () {
      this.$element.off('.' + this._name);
    },

    /*
    Show dropdown.
     */
    show: function (eventType) {
      var _this = this;
      // Check for simultaneous focus and click events.
      if (eventType === 'focus') {
        this.isFocused = true;
      }

      // Constrain width
      if (this.options.constrain_width === true) {
        this.$activates.css('width', this.$element.outerWidth());
      }
      else {
        this.$activates.css('white-space', 'nowrap');
      }

      // Offscreen detection
      var windowHeight = window.innerHeight;
      var elementHeight = this.$element.innerHeight();
      var offsetLeft = this.$element.offset().left;
      var offsetTop = this.$element.offset().top - $(window).scrollTop();
      var currAlignment = this.options.alignment;
      var gutterSpacing = 0;
      var leftPosition = 0;

      // Below Origin
      var verticalOffset = 0;
      if (this.options.belowOrigin === true) {
        verticalOffset = elementHeight;
      }

      // Check for scrolling positioned container.
      var scrollYOffset = 0;
      var scrollXOffset = 0;
      var wrapper = this.$element.parent();
      if (!wrapper.is('body')) {
        if (wrapper[0].scrollHeight > wrapper[0].clientHeight) {
          scrollYOffset = wrapper[0].scrollTop;
        }
        if (wrapper[0].scrollWidth > wrapper[0].clientWidth) {
          scrollXOffset = wrapper[0].scrollLeft;
        }
      }

      if (offsetLeft + this.$activates.innerWidth() > $(window).width()) {
        // Dropdown goes past screen on right, force right alignment
        currAlignment = 'right';
      }
      else if (offsetLeft - this.$activates.innerWidth() + this.$element.innerWidth() < 0) {
        // Dropdown goes past screen on left, force left alignment
        currAlignment = 'left';
      }
      // Vertical bottom offscreen detection
      if (offsetTop + this.$activates.innerHeight() > windowHeight) {
        // If going upwards still goes offscreen, just crop height of dropdown.
        if (offsetTop + elementHeight - this.$activates.innerHeight() < 0) {
          var adjustedHeight = windowHeight - offsetTop - verticalOffset;
          this.$activates.css('max-height', adjustedHeight);
        }
        else {
          // Flow upwards.
          if (!verticalOffset) {
            verticalOffset += elementHeight;
          }
          verticalOffset -= this.$activates.innerHeight();
        }
      }

      // Handle edge alignment
      if (currAlignment === 'left') {
        gutterSpacing = this.options.gutter;
        leftPosition = this.$element.position().left + gutterSpacing;
      }
      else if (currAlignment === 'right') {
        var offsetRight = this.$element.position().left + this.$element.outerWidth() - this.$activates.outerWidth();
        gutterSpacing = -this.options.gutter;
        leftPosition = offsetRight + gutterSpacing;
      }

      // Position dropdown
      this.$activates.css({
        position: 'absolute',
        top: this.$element.position().top + this.$element.outerHeight() + verticalOffset + scrollYOffset,
        left: leftPosition + scrollXOffset
      });

      // Set Dropdown state
      this.$element.addClass('active');
      this.$activates.addClass('active');
      setTimeout(function () {
        _this.$activates.addClass('animate');
      }, 50);
    },

    /*
    Hide dropdown.
     */
    hide: function () {
      var _this = this;
      // Check for simultaneous focus and click events.
      this.isFocused = false;
      this.$element.removeClass('active');
      this.$activates.removeClass('animate');
      setTimeout(function () {
        _this.$activates.removeClass('active');
      }, this.options.duration);
    },

    /*
    Update options using optional data attributes.
     */
    updateOptions: function () {
      if (typeof this.$element.data('duration') !== 'undefined') {
        this.options.duration = this.$element.data('outduration');
      }
      if (typeof this.$element.data('constrainwidth') !== 'undefined') {
        this.options.constrain_width = this.$element.data('constrainwidth');
      }
      if (typeof this.$element.data('hover') !== 'undefined') {
        this.options.hover = this.$element.data('hover');
      }
      if (typeof this.$element.data('gutter') !== 'undefined') {
        this.options.gutter = this.$element.data('gutter');
      }
      if (typeof this.$element.data('beloworigin') !== 'undefined') {
        this.options.belowOrigin = this.$element.data('beloworigin');
      }
      if (typeof this.$element.data('alignment') !== 'undefined') {
        this.options.alignment = this.$element.data('alignment');
      }
      if (typeof this.$element.data('stoppropagation') !== 'undefined') {
        this.options.stopPropagation = this.$element.data('stoppropagation');
      }
    }

  });

  $.fn.uxFormDropdown = function (options) {
    this.each(function () {
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new Plugin(this, options));
      }
    });
    return this;
  };

  $.fn.uxFormDropdown.defaults = {
    duration: 300,
    constrain_width: true, // Constrains width of dropdown to the activator
    hover: false,
    gutter: 0, // Spacing from edge
    belowOrigin: false,
    alignment: 'left',
    stopPropagation: false
  };

  // Drupal.behaviors.uxFormDropdown = {
  //   attach: function (context) {
  //     var $context = $(context);
  //     $context.find('.ux-form-radio').once('ux-form-radio').uxFormDropdown();
  //   },
  //   detach: function (context) {
  //     $(context).find('.ux-form-radio').each(function () {
  //       var plugin = $(this).data('uxFormDropdown');
  //       if (plugin) {
  //         plugin.destroy();
  //       }
  //     });
  //   }
  // };

})(jQuery, Drupal, window, document);
