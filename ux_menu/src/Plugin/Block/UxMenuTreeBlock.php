<?php

namespace Drupal\ux_menu\Plugin\Block;

/**
 * Provides a 'UxMenuBlock' block.
 *
 * @Block(
 *   id = "ux_menu_tree",
 *   admin_label = @Translation("UX | Menu Tree"),
 *   category = @Translation("User Experience"),
 * )
 */
class UxMenuTreeBlock extends UxMenuBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    if (!empty($build['#items'])) {
      $build['#attributes']['class'][] = 'uxMenuTree';
      $build['#attached']['library'][] = 'ux_menu/ux_menu_tree';
    }
    return $build;
  }

}
