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
    fixed: false,

    initialize: function () {
      var _this = this;
      _this.$header.wrap('<div class="ux-header-wrapper"></div>');
      _this.$wrapper = _this.$header.parent();

      // Setup resizing.
      Drupal.Ux.addResizeCallback($.proxy(function () {
        _this.resize();
      }, _this));

      // Setup resizing.
      Drupal.Ux.addScrollCallback($.proxy(function () {
        _this.scroll();
      }, _this));

      // Position floating bar.
      _this.$header.addClass('ux-header');
      _this.resize();
    },

    resize: function () {
      var _this = this;
      _this.$header.removeAttr('style');

      var height = _this.$header.outerHeight();
      var width = _this.$header.outerWidth();
      _this.$wrapper.css({width: width, height: height});

      var offset = _this.$header.offset();
      _this.$header.css({
        position: 'fixed',
        marginLeft: (offset.left - displace.offsets.left),
        marginRight: (offset.left - displace.offsets.right),
        top: displace.offsets.top,
        left: displace.offsets.left,
        right: displace.offsets.right
      });
    },

    scroll: function () {
      var _this = this;
      var scrollTop = Math.abs(_this.$window.scrollTop());
      var lastScrollTop = _this.lastScrollTop;
      var currentDirection = '';

      // Toggle float class.
      if (scrollTop >= _this.delta) {
        if (!_this.fixed) {
          _this.fixed = true;
          _this.$header.addClass('ux-header-float');
        }
      }
      else if (_this.fixed) {
        _this.fixed = false;
        _this.$header.removeClass('ux-header-float');
      }

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
        // Toggle hide class.
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
      $('#ux-content header').first().once('ux-header').each(function (e) {
        new UxHeader($(this));
      });
    }
  };

})(jQuery, Drupal, Drupal.displace);
