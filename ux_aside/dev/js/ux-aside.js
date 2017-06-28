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
      var self = this;
      if (settings.ux && settings.ux.aside && settings.ux.aside.items) {
        $(document).once('ux-aside').on('drupalViewportOffsetChange.ux-aside', self.resize());
        self.resize();

        for (var id in settings.ux.aside.items) {
          if (settings.ux.aside.items[id]) {
            var $element = $('#ux-aside-' + id).once('ux-aside');
            if ($element.length) {
              var options = self.getOptions(settings, id);
              $element.uxAside(options);
            }
            var $trigger = $('.ux-aside-trigger-' + id).once('ux-aside');
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
      var self = this;
      var options = $.extend({}, self._defaults, settings.ux.aside.options, settings.ux.aside.items[id]);

      // Callbacks
      options.onOpening = self.onOpening;
      options.onClosed = self.onClosed;
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
