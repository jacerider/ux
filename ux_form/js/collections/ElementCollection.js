/**
 * @file
 * A Backbone Collection for form elements.
 */

(function (Backbone, Drupal) {

  'use strict';

  /**
   * Backbone model for form elements.
   *
   * @constructor
   *
   * @augments Backbone.Model
   */
  Drupal.UxForm.ElementCollection = Backbone.Collection.extend(/** @lends Drupal.toolbar.ToolbarModel# */{
    model: Drupal.UxForm.ElementModel,
    initialize: function () {
      // console.log('Element Collection initialized.');
    }
  });

  Drupal.UxForm.collection = new Drupal.UxForm.ElementCollection();

}(Backbone, Drupal));
