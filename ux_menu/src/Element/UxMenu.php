<?php

namespace Drupal\ux_menu\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a render element for a menu.
 *
 * @RenderElement("ux_menu")
 */
class UxMenu extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#theme' => 'ux_menu',
      '#menu' => NULL,
      '#header' => NULL,
      '#footer' => NULL,
      '#attributes' => [
        'class' => []
      ],
      '#pre_render' => [
        [get_class(), 'preRenderMenu'],
      ],
    ];
  }

  /**
   * Builds the aside as a structured array ready for drupal_render().
   *
   * @param array $element
   *   A renderable array.
   *
   * @return array
   *   A renderable array.
   */
  public static function preRenderMenu(array $element) {
    ksm($element);
    return $element;
  }

}
