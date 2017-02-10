<?php

namespace Drupal\ux_dialog\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Defines an AJAX command to open content in a dialog in a off-canvas tray.
 *
 * @ingroup ajax
 */
class CloseUxDialogCommand implements CommandInterface {

  /**
   * {@inheritdoc}
   */
  public function render() {
    return array(
      'command' => 'closeUxDialog',
      'selector' => '#ux-dialog',
    );
  }

}
