/**
 * @file
 * Global ELYSIUM_NAME javascript.
 */

(function ($, Drupal, debounce, displace) {

  'use strict';

  function Ux() {
    this.$window = $(window);
    this.$document = $(document);
    this.$body = $('body');
    this.$uxWrapper = $('#ux-wrapper');
    this.$uxCanvas = $('#ux-canvas');
    this.$uxContent = $('#ux-content');
    this.setup();
  }

  $.extend(Ux.prototype, /** @lends Drupal.Ux# */{
    onResizeCallbacks: [],
    onScrollCallbacks: [],

    /**
     * Initial setup.
     */
    setup: function () {
      var _this = this;
      // Initialize displace.
      displace();
      // Add document resize callback to resize event stack.
      _this.addResizeCallback($.proxy(_this.onResizeSetDocumentSize, _this));
      // Adjust the document size.
      _this.$document.on('drupalViewportOffsetChange.ux', function (event, offsets) {
        _this.onResizeSetDocumentSize();
      });

      // Watch window for resizing and call onResizeCallbacks.
      _this.onResize();
      _this.$window.on('resize.ux', {}, debounce(function (event) {
        _this.onResize(event);
      }, 100));

      // Watch window for scroll and call onScrollCallbacks.
      _this.onScroll();
      _this.$window.on('scroll.ux', {}, function (event) {
        _this.onScroll(event);
      });
      // _this.$window.on('scroll.ux', {}, function (event) {
      //   requestAnimationFrame(function () {
      //     _this.onScroll(event);
      //   });
      // });
    },

    /**
     * Add a resize callback.
     *
     * @param {Function} callback
     *   The callback to call.
     */
    addResizeCallback: function (callback) {
      var _this = this;
      _this.onResizeCallbacks.push(callback);
    },

    /**
     * Event fired on window resize.
     *
     * @param {Event} event
     *   Triggered event.
     */
    onResize: function (event) {
      var _this = this;
      _this.onResizeCallbacks.forEach(function (callback) {
        callback(event);
      });
    },

    /**
     * Add a scoll callback.
     *
     * @param {Function} callback
     *   The callback to call.
     */
    addScrollCallback: function (callback) {
      var _this = this;
      _this.onScrollCallbacks.push(callback);
    },

    /**
     * Event fired on window scroll.
     *
     * @param {Event} event
     *   Triggered event.
     */
    onScrollTimeout: null,
    onScroll: function (event) {
      var _this = this;
      clearTimeout(_this.onScrollTimeout);
      _this.onScrollTimeout = setTimeout(function () {
        _this.onScrollCallbacks.forEach(function (callback) {
          callback(event);
        });
      }, 10);
    },

    /**
     * Resize the document to fit the available content area.
     *
     * @param {Event} event
     *   Triggered event.
     */
    onResizeSetDocumentSize: function (event) {
      var _this = this;
      var height = _this.$body.height() - (_this.$uxWrapper.outerHeight() - _this.$uxWrapper.height());
      _this.$uxContent.css('min-height', height);
    },

    /**
     * Blur #ux-content.
     */
    blurTimeout: null,
    blurContent: function () {
      var _this = this;
      _this.$uxContent.addClass('ux-blur');
      _this.$window.trigger('ux-content:blur', [_this.$uxContent]);
      clearTimeout(_this.blurTimeout);
      _this.blurTimeout = setTimeout(function () {
        _this.$uxContent.addClass('ux-blur-animate');
      }, 0);
    },

    /**
     * Focus #ux-content.
     */
    focusContent: function () {
      var _this = this;
      _this.$uxContent.removeClass('ux-blur-animate');
      clearTimeout(_this.blurTimeout);
      _this.blurTimeout = setTimeout(function () {
        _this.$uxContent.removeClass('ux-blur');
        _this.$window.trigger('ux-content:focus', [_this.$uxContent]);
      }, 400);
    },

    /**
     * Show shadow overlay.
     *
     * @type {Function} callback
     *   The callback to call when shadow is clicked.
     */
    shadowTimeout: null,
    showShadow: function (callback) {
      var _this = this;
      var $shadow = $('#ux-shadow');
      $shadow.addClass('active');
      clearTimeout(_this.shadowTimeout);
      _this.shadowTimeout = setTimeout(function () {
        $shadow.addClass('animate');
      }, 0);
      if (callback) {
        $shadow.on('click.ux', callback);
      }
    },

    /**
     * Hide shadow overlay.
     */
    hideShadow: function () {
      var _this = this;
      var $shadow = $('#ux-shadow');
      $shadow.removeClass('animate').off('click.ux');
      clearTimeout(_this.shadowTimeout);
      _this.shadowTimeout = setTimeout(function () {
        $shadow.removeClass('active');
      }, 400);
    },

    /**
     * Generate a unique id.
     *
     * @return {string}
     *   A unique id.
     */
    guid: function () {
      function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
          .toString(16)
          .substring(1);
      }
      return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
               s4() + '-' + s4() + s4() + s4();
    }

    /**
     * Set element as fullscreen.
     *
     * @param {Object.<jQuery>} $element
     *   The element to set as fullscreen.
     */
    // setFullscreen: function ($element) {
    //   if ($element.length) {
    //     $element.css(displace.offsets);
    //   }
    // }
  });

  Drupal.behaviors.ux = {
    attach: function (context) {
      // $('.ux-fullscreen', context).once('ux-fullscreen').each(function (e) {
      //   Drupal.Ux.setFullscreen($(this));
      // });
    }
  };

  // Expose constructor in the public space.
  Drupal.Ux = new Ux();

})(jQuery, Drupal, Drupal.debounce, Drupal.displace);
