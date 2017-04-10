/**
 * @file
 * A Backbone Model for form elements.
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
  Drupal.UxForm.ElementModel = Backbone.Model.extend(/** @lends Drupal.toolbar.ToolbarModel# */{
    defaults: {
      selector: '',
      events: {},
      onRender: null,
      onRemove: null,
      onValidate: null,
      onChange: null,
      onFocus: null,
      onBlur: null
    }
  });

}(Backbone, Drupal));
