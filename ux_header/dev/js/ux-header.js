/**
 * @file
 * Global ux_offcanvas javascript.
 */

(function ($, Drupal, displace) {

  'use strict';

  function UxHeader($header) {
    this.$header = $header;
    this.$window = $(window);
    this.$document = $(document);
    this.initialize();
  }

  $.extend(UxHeader.prototype, /** @lends Drupal.UxHeader# */{
    lastScrollTop: 0,
    delta: 100,
    direction: '',

    initialize: function () {
      var _this = this;
      _this.$header.wrap('<div class="ux-header-wrapper"></div>');
      _this.$wrapper = _this.$header.parent();

      // Setup resizing.
      _this.resize();
      Drupal.Ux.addResizeCallback($.proxy(function () {
        _this.resize();
      }, _this));

      // Setup resizing.
      _this.scroll();
      Drupal.Ux.addScrollCallback($.proxy(function () {
        _this.scroll();
      }, _this));

      // Position floating bar.
      displace();
      _this.$header.addClass('ux-header');
      _this.position();
      _this.$document.on('drupalViewportOffsetChange', function () {
        _this.position();
      });
    },

    position: function () {
      var _this = this;
      _this.$header.css({
        top: displace.offsets.top,
        left: displace.offsets.left,
        right: displace.offsets.right,
      });
    },

    resize: function () {
      var _this = this;
      var height = _this.$header.outerHeight();
      var width = _this.$header.outerWidth();
      _this.$wrapper.css({width: width, height: height});
    },

    scroll: function () {
      var _this = this;
      var scrollTop = Math.abs(_this.$window.scrollTop());
      var lastScrollTop = _this.lastScrollTop;
      var currentDirection = '';

      // Make sure scroll is more than delta.
      if (Math.abs(lastScrollTop - scrollTop) <= _this.delta) {
        return;
      }

      // Determine direction.
      if (scrollTop > lastScrollTop) {
        currentDirection = 'down';
      }
      else {
        currentDirection = 'up';
      }

      if (_this.direction !== currentDirection) {
        if (currentDirection === 'down') {
          _this.$header.addClass('ux-header-hide');
        }
        else {
          _this.$header.removeClass('ux-header-hide');
        }
        _this.direction = currentDirection;
      }

      _this.lastScrollTop = scrollTop;
    }
  });

  Drupal.behaviors.uxStickyHeader = {
    attach: function (context) {
      $('header', context).once('ux-header').each(function (e) {
        new UxHeader($(this));
      });
    }
  };

})(jQuery, Drupal, Drupal.displace);
