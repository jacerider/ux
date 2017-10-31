<?php

namespace Drupal\ux_aside\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a render element for the aside collection wrapper.
 *
 * @RenderElement("ux_asides")
 */
class UxAsides extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#pre_render' => [
        [get_class(), 'preRenderAsides'],
      ],
    ];
  }

  /**
   * Builds the asides wrapper.
   *
   * @param array $element
   *   A renderable array.
   *
   * @return array
   *   A renderable array.
   */
  public static function preRenderAsides(array $element) {
    $aside_manager = static::uxAsideManager();

    $element = [
      '#theme' => 'ux_asides',
      '#attached' => [
        'library' => [
          'ux_aside/ux_aside',
        ],
      ],
      '#attributes' => [
        'id' => 'ux-asides',
        'class' => [
          'ux-asides',
        ],
      ],
    ];
    $options = $aside_manager->getOptions('content', TRUE, TRUE);
    // Add base options.
    $element['#attached']['drupalSettings']['ux']['aside']['options'] = $options;
    if (!empty($options['theme'])) {
      $element['#attached']['library'][] = 'ux_aside/ux_aside.theme.' . $options['theme'];
    }
    return $element;
  }

  /**
   * Wraps the aside manager.
   *
   * @return \Drupal\ux_aside\UxAsideManagerInterface
   *   The UX Aside manager.
   */
  protected static function uxAsideManager() {
    return \Drupal::service('ux_aside.manager');
  }

}
