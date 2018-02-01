(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.uxFormTime = {
    configDefaults: {
      mode: 'button',
      container: '#ux-content',
      format: 'HH:i:00',
      formatSubmit: 'HH:i:00',
      formatLabel: 'h:i A'
    },

    attach: function (context, settings) {
      var self = this;
      if (settings.ux && settings.ux.time && settings.ux.time.items) {
        for (var id in settings.ux.time.items) {
          if (settings.ux.time.items[id]) {
            var $elements = $('#' + id, context).once('ux-form-time');
            for (var i = 0; i < $elements.length; i++) {
              self.init($elements[i], settings.ux.time.items[id]);
            }
          }
        }
      }
    },

    init: function (wrapper, settings) {
      var $wrapper = $(wrapper);
      var $input = $wrapper.find('.form-time');
      var config = $.extend(true, {}, this.configDefaults, settings);
      var mode = config.mode;
      $input.data('value', $input.val());
      $input.pickatime(config);

      switch (mode) {
        case 'button':
          var $button = $wrapper.find('.ux-form-time-button');
          $input.attr('type', 'time');
          $button.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $input.pickatime('picker').open();
          });
          break;
      }
    }

    // @see https://www.drupal.org/node/2692453
    // detach: function (context, setting, trigger) {
    //   if (trigger === 'unload') {
    //     $(context).find('.ux-form-time input.form-time').each(function () {
    //       var $element = $(this);
    //       $element.off('.ux-form-time');
    //       var plugin = $element.pickatime('picker');
    //       if (typeof plugin === 'object') {
    //         plugin.stop();
    //       }
    //     });
    //   }
    // }
  };

})(jQuery, Drupal, drupalSettings);
