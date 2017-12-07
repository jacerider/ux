/**
 * @file ux_media.ux_aside_selection.js
 *
 * Propagates selected entities from ux_aside display.
 */

(function (drupalSettings) {

  'use strict';

  // We need to access parent window, get it's jquery and find correct ux_aside
  // element to trigger event on.
  parent.jQuery(parent.document)
    .find(':input[data-uuid*=' + drupalSettings.entity_browser.ux_aside.uuid + ']')
    .trigger('entities-selected', [drupalSettings.entity_browser.ux_aside.uuid, drupalSettings.entity_browser.ux_aside.entities])
    .unbind('entities-selected').show();

  // This is a silly solution, but works fo now. We should close the ux_aside
  // via ajax commands.
  parent.jQuery(parent.document).find('.uxAside-button-close').trigger('mousedown').trigger('mouseup').trigger('click');

}(drupalSettings));
