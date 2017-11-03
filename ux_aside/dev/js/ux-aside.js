/**
 * @file
 * Global ux_aside javascript.
 */

/* eslint-disable no-alert, no-console */
(function ($, Drupal, drupalSettings, displace) {

  'use strict';

  Drupal.behaviors.uxAside = {
    _defaults: {
      overlay: '#ux-asides',
      appendTo: false
    },

    openCount: 0,

    attach: function (context, settings) {
      var _this = this;
      if (settings.ux && settings.ux.aside && settings.ux.aside.items) {
        var $wrapper = $('#ux-asides');
        $(document).once('ux-aside').on('drupalViewportOffsetChange.ux-aside', _this.resize);
        _this.resize();

        for (var id in settings.ux.aside.items) {
          if (settings.ux.aside.items[id]) {
            var $element = $('#ux-aside-' + id).once('ux-aside').appendTo($wrapper);
            if ($element.length) {
              var options = _this.getOptions(settings.ux.aside, id);
              $element.uxAside(options);
            }
            var $trigger = $('[data-ux-aside="' + id + '"').once('ux-aside');
            if ($trigger.length) {
              $trigger.on('click', function (e) {
                e.preventDefault();
                var $element = $('#ux-aside-' + $(this).data('ux-aside'));
                if ($element.length) {
                  $element.uxAside('open');
                }
              });
            }
          }
        }
      }
    },

    detach: function (context, setting, trigger) {
      if (trigger === 'unload') {
        $(document).removeOnce('ux-aside').off('drupalViewportOffsetChange.ux-aside');
      }
    },

    resize: function (event, offsets) {
      $('#ux-asides').css({
        marginTop: displace.offsets.top,
        marginLeft: displace.offsets.left,
        marginRight: displace.offsets.right,
        marginBottom: displace.offsets.bottom
      });
    },

    getOptions: function (settings, id) {
      var _this = this;
      var options = $.extend({}, _this._defaults, settings.options, settings.items[id]);

      // Callbacks
      options.onOpening = _this.onOpening;
      options.onClosed = _this.onClosed;
      if (options.attachTop && options.attachTop !== null && options.attachTop !== false) {
        options.top = 0;
      }
      if (options.attachBottom && options.attachBottom !== null && options.attachBottom !== false) {
        options.bottom = 0;
      }
      if (options.attachLeft && options.attachLeft !== null && options.attachLeft !== false) {
        options.left = 0;
        options.openTall = true;
      }
      if (options.attachRight && options.attachRight !== null && options.attachRight !== false) {
        options.right = 0;
        options.openTall = true;
      }

      options.offsets = displace.offsets;
      return options;
    },

    onOpening: function (uxAside) {
      Drupal.behaviors.uxAside.openCount++;
      $('#ux-asides').addClass('active');

      if (uxAside.options.top === displace.offsets.top) {
        uxAside.$element.css({
          borderTopRightRadius: 0,
          borderTopLeftRadius: 0
        });
      }
      if (uxAside.options.bottom === displace.offsets.bottom) {
        uxAside.$element.css({
          borderBottomRightRadius: 0,
          borderBottomLeftRadius: 0
        });
      }

      if (uxAside.options.restoreDefaultContent) {
        // Wait just a moment before binding so that browser registers
        // elements as visible.
        setTimeout(function () {
          Drupal.attachBehaviors(uxAside.$element.get(0), drupalSettings);
        }, 10);
      }

      uxAside.initialized = true;
    },

    onClosed: function (uxAside) {
      Drupal.behaviors.uxAside.openCount--;
      if (Drupal.behaviors.uxAside.openCount === 0) {
        // If no asides are still open, disable ux-slides.
        $('#ux-asides').removeClass('active');
      }
    }
  };

})(jQuery, Drupal, drupalSettings, Drupal.displace);
