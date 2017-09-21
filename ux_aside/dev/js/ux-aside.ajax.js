/**
 * @file
 * Extends the Drupal AJAX functionality to integrate the aside API.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Command to open an aside.
   *
   * @param {Drupal.Ajax} ajax
   *   The Drupal Ajax object.
   * @param {object} response
   *   Object holding the server response.
   * @param {number} [status]
   *   The HTTP status code.
   */
  Drupal.AjaxCommands.prototype.uxAsideOpen = function (ajax, response, status) {
    // Because ajax.js expects only 1 root element, when twig hints are nurned
    // on this causes HTML comments to cause Drupal to wrap the contents within
    // and <div>. We need to make sure this does not happen as it breaks
    // styling.
    var $new_content_wrapped = $('<div></div>').html(response.data);
    response.data = $new_content_wrapped.find('.uxAside')[0].outerHTML;

    var insertResponse = $.extend({}, response, {
      selector: '#ux-asides',
      method: 'append'
    });
    this.insert(ajax, insertResponse, status);
  };

})(jQuery, Drupal);
