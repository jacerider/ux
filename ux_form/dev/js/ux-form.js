/**
 * @file
 * Global ux_form javascript.
 */
(function ($, Drupal) {

  'use strict';

  function UxForm($form) {
  }

  /**
   * Global form object.
   */
  $.extend(UxForm, /** @lends Drupal.UxForm */{

    /**
     * The collection instance.
     *
     * @type Backbone.Collection
     */
    collection: null,

    /**
     * A hash of View instances.
     *
     * @type {object.<string, Backbone.View>}
     */
    forms: {},

    /**
     * A hash of Model instances.
     *
     * @type {object.<string, Backbone.Model>}
     */
    models: {}
  });

  Drupal.behaviors.uxForm = {
    attach: function (context, settings) {
      $('form.ux-form').once('ux-form').each(function () {
        var id = $(this).attr('id');
        Drupal.UxForm.forms[id] = new Drupal.UxForm.FormView({
          el: this
        });
      });
      for (var id in Drupal.UxForm.forms) {
        if (typeof Drupal.UxForm.forms[id] === 'object') {
          Drupal.UxForm.forms[id].render();
        }
      }
    },
    detach: function (context, settings, trigger) {
      if (trigger === 'unload') {
        for (var id in Drupal.UxForm.forms) {
          if (typeof Drupal.UxForm.forms[id] === 'object') {
            Drupal.UxForm.forms[id].remove();
          }
        }
      }
    }
  };

  // Expose constructor in the public space.
  Drupal.UxForm = UxForm;

})(jQuery, Drupal);
