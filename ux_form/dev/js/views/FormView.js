/**
 * @file
 * A Backbone view for the body element.
 */

(function ($, Drupal, Backbone) {

  'use strict';

  Drupal.UxForm.FormView = Backbone.View.extend(/** @lends Drupal.UxForm.FormView# */{

    instances: {},

    /**
     * Adjusts the body element with the UxForm position and dimension changes.
     *
     * @constructs
     *
     * @augments Backbone.View
     */
    initialize: function () {
      // _this.render(this.$el);
      // console.log('initialize FormView', this.$el);
    },

    render: function () {
      var _this = this;
      // Reduce context to this single form.
      Drupal.UxForm.collection.each(function (model, id) {
        if (!_this.instances[id]) {
          var $element = $(model.get('selector'), _this.$el);
          if ($element.length) {
            _this.instances[id] = new Drupal.UxForm.ElementView({
              el: $element,
              model: model
            });
          }
        }
      });
    },

    remove: function () {
      var _this = this;
      Drupal.UxForm.collection.each(function (model, id) {
        var $element = $(model.get('selector'), _this.$el);
        if ($element.length) {
          _this.instances[id].undelegateEvents();
          _this.instances[id].$el.removeData().unbind();
          _this.instances[id].remove();
          delete _this.instances[id];
        }
      });
    }
  });

  // Drupal.UxForm.views.inputView = new Drupal.UxForm.FormView({
  //   tagName: '.ux-form-input',
  //   model: Drupal.UxForm.models.elementModel
  // });

}(jQuery, Drupal, Backbone));
