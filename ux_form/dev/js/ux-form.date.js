(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.uxFormDate = {
    configDefaults: {
      mode: 'button',
      container: '#ux-content',
      format: 'yyyy-mm-dd'
    },

    attach: function (context, settings) {
      var self = this;
      if (settings.ux && settings.ux.date && settings.ux.date.items) {
        for (var id in settings.ux.date.items) {
          if (settings.ux.date.items[id]) {
            var $elements = $('#' + id, context).once('ux-form-date');
            for (var i = 0; i < $elements.length; i++) {
              self.init($elements[i], settings.ux.date.items[id]);
            }
          }
        }
      }
    },

    init: function (wrapper, settings) {
      var $wrapper = $(wrapper);
      var $input = $wrapper.find('.form-date');
      var config = $.extend(true, {}, this.configDefaults, settings);
      var mode = config.mode;
      $input.data('value', $input.val());
      $input.pickadate(config);

      switch (mode) {
        case 'button':
          var $button = $wrapper.find('.ux-form-date-button');
          $input.attr('type', 'date');
          $button.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $input.pickadate('picker').open();
          });
          break;
      }
    }

    // @see https://www.drupal.org/node/2692453
    // detach: function (context, setting, trigger) {
    //   if (trigger === 'unload') {
    //     $(context).find('.ux-form-date input.form-date').each(function () {
    //       var $element = $(this);
    //       $element.off('.ux-form-date');
    //       var plugin = $element.pickadate('picker');
    //       if (typeof plugin === 'object') {
    //         plugin.$node.val($(plugin._hidden).val());
    //         plugin.stop();
    //       }
    //     });
    //   }
    // }

  };

})(jQuery, Drupal, drupalSettings);
