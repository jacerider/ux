/**
 * @file
 * Drupal's off-canvas library.
 *
 * @todo This functionality should extracted into a new core library or a part
 *  of the current drupal.dialog.ajax library.
 *  https://www.drupal.org/node/2784443
 */

(function ($, Drupal, debounce, displace) {

  'use strict';

  $(window).on({
    'dialog:beforecreate': function (event, dialog, $element, settings) {
      if ($element.is('#ux-dialog')) {
        var $uxContent = $('#ux-content');
        $('body').addClass('has-ux-dialog');
        settings.dialogClass = 'ux-dialog';
        settings.show = false;
        settings.hide = {
          effect: 'none',
          delay: 600
        };
        settings.position = {my: 'center', at: 'center', of: $uxContent};
        settings.beforeClose = function () {
          Drupal.Ux.focusContent();
          Drupal.Ux.hideShadow();
          $('body').removeClass('ux-dialog-animate');
          setTimeout(function () {
            $('body').removeClass('ux-dialog-active');
          }, 400);
        };
      }
    },
    'dialog:aftercreate': function (event, dialog, $element, settings) {
      if ($element.is('#ux-dialog')) {
        // Give ui.dialog time to position item.
        setTimeout(function () {
          $('body').addClass('ux-dialog-active');
        }, 0);
        // Trigger animations.
        setTimeout(function () {
          Drupal.Ux.blurContent();
          Drupal.Ux.showShadow(function (e) {
            $element.dialog('close');
          });
          $('body').addClass('ux-dialog-animate');
        }, 100);
      }
    },
    'dialog:beforeclose': function (event, dialog, $element) {
      if ($element.is('#ux-dialog')) {
        $('body').removeClass('has-ux-dialog');
      }
    }
  });

  /**
   * Command to close a dialog.
   *
   * If no selector is given, it defaults to trying to close the modal.
   *
   * @param {Drupal.Ajax} [ajax]
   *   The ajax object.
   * @param {object} response
   *   Object holding the server response.
   * @param {string} response.selector
   *   The selector of the dialog.
   * @param {bool} response.persist
   *   Whether to persist the dialog element or not.
   * @param {number} [status]
   *   The HTTP status code.
   */
  Drupal.AjaxCommands.prototype.closeUxDialog = function (ajax, response, status) {
    var $dialog = $(response.selector);
    if ($dialog.length) {
      $dialog.dialog('close');
    }

    // Unbind dialogButtonsChange.
    $dialog.off('dialogButtonsChange');
  };

})(jQuery, Drupal, Drupal.debounce, Drupal.displace);
