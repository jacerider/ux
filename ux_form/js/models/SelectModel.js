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
  Drupal.UxForm.SelectModel = Backbone.Model.extend(/** @lends Drupal.toolbar.ToolbarModel# */{
    defaults: {
      $select: null,
      $selectChildren: null,
      label: '',
      sanitizedLabelHtml: '',
      multiple: false,
      disabled: false,
      valuesSelected: [],
      optionsHover: false
    },

    initialize: function () {
      var $select = this.get('$select');
      this.set('$selectChildren', $select.children('option, optgroup'));
      this.set('label', $select.find('option:selected').html() || $select.find('option:first').html() || '');
      this.set('sanitizedLabelHtml', this.get('label').replace(/"/g, '&quot;'));
      this.set('multiple', $select.attr('multiple') ? true : false);
      this.set('disabled', $select.is(':disabled') ? true : false);
    }
  });

}(Backbone, Drupal));
