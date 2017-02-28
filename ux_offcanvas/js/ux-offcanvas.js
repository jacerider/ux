/**
 * @file
 * Global ux_offcanvas javascript.
 */

/* eslint-disable no-alert, no-console */
(function ($, Drupal, drupalSettings, displace) {

  'use strict';

  function UxOffcanvas($offcanvas, settings) {
    this.settings = settings;
    this.$window = $(window);
    this.$document = $(document);
    this.$offcanvas = $offcanvas;
    this.$trigger = $('#ux-offcanvas-trigger-' + settings.id);
    this.setup();
  }

  /**
   * Global offcanvas object.
   */
  $.extend(UxOffcanvas, /** @lends Drupal.UxOffcanvas */{
    /**
     * The body element.
     *
     * @type {Object.<jQuery>}
     */
    $body: $('body'),

    /**
     * The #ux-offcanvas element.
     *
     * @type {Object.<jQuery>}
     */
    $wrapper: $('#ux-offcanvas'),

    /**
     * Holds references to instantiated UxOffcanvas objects.
     *
     * @type {Array.<Drupal.UxOffcanvas>}
     */
    instances: {},

    /**
     * The currently active offcanvas item.
     *
     * @type {String}
     */
    currentId: null,

    /**
     * Get the offcanvas item instance.
     *
     * @param {Integer} id
     *   The offcanvas id.
     *
     * @return {Object.<UxOffcanvas>}
     *   The offcanvas object.
     */
    getInstance: function (id) {
      var _this = this;
      return _this.instances[id];
    },

    open: function (id) {
      var _this = this;
      var currentId = _this.currentId;
      var instance = _this.getInstance(id);
      // If we have a currently open offcanvas, close it.
      if (currentId) {
        _this.close();
      }
      // Open offcanvas item if we are requested a new item.
      if (currentId !== id) {
        _this.currentId = id;
        _this.$body.addClass('ux-offcanvas-active ux-offcanvas-' + instance.getPosition());
        instance.open();
        _this.stopBodyScrolling(true);
        Drupal.Ux.blurContent();
        Drupal.Ux.showShadow(function (e) {
          _this.close();
        });
      }
    },

    close: function (id) {
      var _this = this;
      id = id || _this.currentId;
      var instance = _this.getInstance(id);
      _this.currentId = null;
      _this.$body.removeClass('ux-offcanvas-active ux-offcanvas-' + instance.getPosition());
      instance.close();
      _this.stopBodyScrolling(false);
      Drupal.Ux.focusContent();
      Drupal.Ux.hideShadow();
    },

    stopBodyScrolling: function (bool) {
      var _this = this;
      if (bool === true) {
        var freezeVp = function (e) {
          e.preventDefault();
        };
        _this.$body.on('touchmove.ux-offcanvas', freezeVp);
      }
      else {
        _this.$body.off('touchmove.ux-offcanvas');
      }
    }
  });

  /**
   * Individual offcanvas items.
   */
  $.extend(UxOffcanvas.prototype, /** @lends Drupal.UxOffcanvas# */{
    _defaults: {
      position: 'left',
      size: 320
    },
    isActive: false,

    setup: function () {
      var _this = this;
      _this.settings = $.extend(true, {}, _this._defaults, _this.settings);

      switch (_this.settings.position) {
        case 'left':
        case 'right':
          _this.$offcanvas.width(_this.settings.size);
          break;

        default:
          _this.$offcanvas.height(_this.settings.size);
          break;
      }

      if (_this.$trigger.length) {
        _this.$trigger.on('click.ux-offcanvas', function (e) {
          e.preventDefault();
          UxOffcanvas.open(_this.getId());
        });
      }

      _this.$offcanvas.find('.ux-offcanvas-close').on('click.ux-offcanvas', function (e) {
        e.preventDefault();
        UxOffcanvas.close(_this.getId());
      });
    },

    getId: function () {
      var _this = this;
      return _this.settings.id;
    },

    getPosition: function () {
      var _this = this;
      return _this.settings.position;
    },

    getSize: function () {
      var _this = this;
      return _this.settings.size;
    },

    open: function () {
      var _this = this;
      _this.isActive = true;
      _this.$offcanvas.addClass('active').css(_this.getOffset());
      _this.$offcanvas.trigger('ux_offcanvas_item.open');
      _this.$trigger.addClass('active');
      _this.$document.on('keyup.ux-offcanvas', function (e) {
        if (e.keyCode === 27) {
          e.preventDefault();
          UxOffcanvas.close(_this.getId());
        }
      });
    },

    close: function (e, offcanvas) {
      var _this = this;
      _this.isActive = false;
      _this.$offcanvas.removeClass('active');
      _this.$offcanvas.trigger('ux_offcanvas_item.close');
      _this.$trigger.removeClass('active');
      _this.$document.off('keyup.ux-offcanvas');
    },

    getOffset: function () {
      var _this = this;
      var position = _this.settings.position;
      var opposites = {left: 'right', right: 'left', top: 'bottom', bottom: 'top'};
      var values = displace.offsets;
      switch (position) {
        case 'top':
        case 'bottom':
          values.maxHeight = _this.$window.height() - values.top - values.bottom;
          break;
      }
      delete values[opposites[position]];
      return values;
    }
  });

  Drupal.behaviors.uxOffcanvas = {
    attach: function (context, settings) {
      if (settings.ux && settings.ux.offcanvas && settings.ux.offcanvas.items) {
        var $wrapper = $('#ux-offcanvas').once('ux-offcanvas');
        var config;
        var $offcanvas;
        if ($wrapper.length) {
          for (var id in settings.ux.offcanvas.items) {
            if (settings.ux.offcanvas.items[id]) {
              config = settings.ux.offcanvas.items[id];
              $offcanvas = $('#ux-offcanvas-' + config.id, context);
              UxOffcanvas.instances[id] = new UxOffcanvas($offcanvas, settings.ux.offcanvas.items[id]);
            }
          }
        }
      }
    }
  };

  // Expose constructor in the public space.
  Drupal.UxOffcanvas = UxOffcanvas;

})(jQuery, Drupal, drupalSettings, Drupal.displace);
