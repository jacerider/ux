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
    delta: 50,
    direction: '',
    fixed: false,
    offset: {},

    initialize: function () {
      var _this = this;
      _this.$header.wrap('<div class="ux-header-wrapper"></div>');
      _this.$wrapper = _this.$header.parent();
      _this.reset();

      // Setup resizing.
      Drupal.Ux.addResizeCallback($.proxy(function () {
        _this.onResize();
      }, _this));

      // Setup resizing.
      Drupal.Ux.addScrollCallback($.proxy(function () {
        _this.scroll();
      }, _this));

      // Position floating bar.
      _this.$header.addClass('ux-header');
      setTimeout(function () {
        _this.onResize();
      }, 100);
    },

    reset: function () {
      var _this = this;
      _this.$wrapper.removeAttr('style');
      _this.$header.removeAttr('style');
      _this.$header.removeClass('ux-header-float ux-header-hide');
    },

    calcSize: function () {
      var _this = this;
      _this.offset = _this.$header.offset();
      _this.width = _this.$header.outerWidth();
      _this.height = _this.$header.outerHeight();
    },

    setSize: function () {
      var _this = this;
      _this.$wrapper.css({width: _this.width, height: _this.height});
    },

    float: function () {
      var _this = this;
      _this.$header.css({
        display: 'none',
        position: 'fixed',
        marginLeft: (_this.offset.left - displace.offsets.left),
        marginRight: (_this.offset.left - displace.offsets.right),
        maxWidth: _this.width,
        top: displace.offsets.top,
        left: displace.offsets.left,
        right: displace.offsets.right
      });
      _this.$header.addClass('ux-header-hide ux-header-float ux-header-alt');
      setTimeout(function () {
        _this.$header.css({display: ''});
      }, 10);
    },

    onResize: function () {
      var _this = this;
      _this.reset();
      _this.calcSize();
      _this.setSize();

    },

    scroll: function () {
      var _this = this;
      var scrollTop = Math.abs(_this.$window.scrollTop());
      var lastScrollTop = _this.lastScrollTop;
      var currentDirection = '';
      var endFloat = (_this.offset.top - displace.offsets.top);
      endFloat = endFloat >= 0 ? endFloat : 0;
      var startFloat = endFloat + _this.height;

      if (scrollTop > startFloat) {
        if (_this.fixed === false) {
          _this.fixed = true;
          _this.float();
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

      }
      else if (_this.fixed === true) {
        _this.$header.removeClass('ux-header-alt');
        if (scrollTop <= endFloat) {
          _this.fixed = false;
          _this.reset();
          _this.setSize();
        }
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
