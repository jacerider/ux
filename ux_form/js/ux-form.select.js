/**
 * @file
 * Global ux_form javascript.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.UxForm.models.select = new Drupal.UxForm.ElementModel({
    selector: '.ux-form-select',
    events: {
      change: 'onChange'
    },

    onChange: function (e) {
      var $element = $(e.currentTarget);
      console.log('change');
    },

    /*
    On render.
     */
    onRender: function () {
      var _this = this;
      _this.$el.each(function (index, element) {
        var $wrapper = $(this);
        var $select = $wrapper.find('select');
        var model = new Drupal.UxForm.SelectModel({
          $select: $select,
        });
        new Drupal.UxForm.SelectView({
          el: $wrapper,
          model: model
        });
        // var multiple = $select.attr('multiple') ? true : false;
        // wrapper.addClass($select.attr('class'));
        // console.log('multiple', multiple);
      });
    }
  });

  // Add to collection.
  Drupal.UxForm.collection.add(Drupal.UxForm.models.select);

})(jQuery, Drupal);
