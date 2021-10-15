<?php

namespace Drupal\ux_dialog\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Defines an AJAX command to close content in a dialog in a off-canvas tray.
 *
 * @ingroup ajax
 */
class CloseUxDialogCommand implements CommandInterface {

  /**
   * {@inheritdoc}
   */
  public function render() {
    return [
      'command' => 'closeUxDialog',
      'selector' => '#ux-dialog',
    ];
  }

}
