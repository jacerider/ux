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
        [get_class(), 'generateLazyBuilder'],
      ],
    ];
  }

  /**
   * Build lazy builder.
   *
   * @param array $element
   *   A renderable array.
   *
   * @return array
   *   The updated renderable array containing the placeholder.
   */
  public static function generateLazyBuilder(array $element) {
    $aside_manager = static::uxAsideManager();
    $renderer = static::renderer();
    $element = [
      '#cache' => [
        'keys' => ['ux-asides'],
        'tags' => $aside_manager->getCacheTags(),
        'contexts' => $aside_manager->getCacheContexts(),
        'max-age' => $aside_manager->getCacheMaxAge(),
      ],
      '#lazy_builder' => [static::class . '::renderAsides', []],
    ];
    foreach ($aside_manager->getAll() as $id => $aside) {
      $element['#cache']['keys'][] = $id;
      $renderer->addCacheableDependency($element, $aside);
    }
    return \Drupal::service('render_placeholder_generator')->createPlaceholder($element);
  }

  /**
   * Builds the asides as a structured array ready for drupal_render().
   *
   * @return array
   *   A renderable array.
   */
  public static function renderAsides() {
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
    // Add base options.
    $element['#attached']['drupalSettings']['ux']['aside']['options'] = $aside_manager->getOptions('content', TRUE);

    foreach ($aside_manager->getAll() as $id => $aside) {
      $element['#content'][$id] = [
        '#type' => 'ux_aside',
        '#aside' => $aside,
      ];
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

  /**
   * Wraps the renderer.
   *
   * @return \Drupal\Core\Render\RendererInterface
   *   The Drupal render service.
   */
  protected static function renderer() {
    return \Drupal::service('renderer');
  }

}
