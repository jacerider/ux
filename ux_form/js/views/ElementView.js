/**
 * @file
 * A Backbone view for the body element.
 */

(function ($, Drupal, Backbone) {

  'use strict';

  Drupal.UxForm.ElementView = Backbone.View.extend(/** @lends Drupal.UxForm.ElementView# */{

    events: function () {
      var events = {};
      var modelEvents = this.model.get('events');
      for (var id in modelEvents) {
        if (typeof modelEvents[id] === 'string') {
          var callback = this.model.get(modelEvents[id]);
          if (callback) {
            events[id] = callback;
          }
        }
      }
      return events;
    },

    /**
     * Adjusts the body element with the UxForm position and dimension changes.
     *
     * @constructs
     *
     * @augments Backbone.View
     */
    initialize: function () {
      // console.log('elementView', this.$el);
      this.render();
    },

    render: function () {
      var callback = this.model.get('onRender');
      if (callback) {
        $.proxy(callback, this)();
      }
    },

    remove: function () {
      var callback = this.model.get('onRemove');
      if (callback) {
        $.proxy(callback, this)();
      }
    },

    /*
    Validate element
     */
    validate: function ($element) {
      var _this = this;
      var callback = this.model.get('onValidate');
      if (callback) {
        $.proxy(callback, _this)($element);
      }
    },

    /*
    Check if element has a placeholder.
     */
    hasPlaceholder: function ($element) {
      var placeholder = $element.attr('placeholder');
      return typeof placeholder !== 'undefined' && placeholder.length > 0;
    },

    /*
    Check if element has a placeholder.
     */
    isReadonly: function ($element) {
      var readonly = $element.attr('readonly');
      return typeof readonly !== 'undefined';
    }
  });

}(jQuery, Drupal, Backbone));
