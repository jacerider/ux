(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.uxFormAutogrow = {

    attach: function (context, settings) {
      $.each($('textarea[data-autogrow]', context).once(), function () {
        var $element = $(this);
        var offset = this.offsetHeight - this.clientHeight;

        var resizeTextarea = function (el) {
          var $el = $(el);
          var maxHeight = $el.data('autogrow-max');
          var height = el.scrollHeight + offset;
          if (maxHeight && height > maxHeight) {
            $el.css({
              overflow: 'auto',
              resize: 'vertical'
            });
          }
          else {
            $el.css({
              minHeight: 'auto',
              overflow: 'hidden',
              resize: 'none'
            }).css('minHeight', el.scrollHeight + offset);
          }
        };

        resizeTextarea(this);
        $element.on('keyup input', function () {
          resizeTextarea(this);
        });
      });
    }

  };

})(jQuery, Drupal);
