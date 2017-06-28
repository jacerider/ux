<?php

namespace Drupal\ux_aside\Element;

use Drupal\Core\Render\Element\RenderElement;
use Drupal\ux_aside\UxAsideInterface;

/**
 * Provides a render element for an aside..
 *
 * @RenderElement("ux_aside")
 */
class UxAside extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#theme' => 'ux_aside',
      '#aside' => NULL,
      '#pre_render' => [
        [get_class(), 'preRenderAside'],
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
  public static function preRenderAside(array $element) {
    $aside = $element['#aside'];

    if ($aside instanceof UxAsideInterface) {
      $element['#cache'] = [
        'keys' => ['ux-asides', 'ux-aside', $aside->id()],
        'tags' => $aside->getCacheTags(),
        'contexts' => $aside->getCacheContexts(),
        'max-age' => $aside->getCacheMaxAge(),
      ];
      $element['#attributes']['id'] = 'ux-aside-' . $aside->id();
      $element['#content'] = $aside->getContent();
      $element['#content_attributes']['class'][] = 'ux-aside-inner';
      $element['#attached']['drupalSettings']['ux']['aside']['items'][$aside->id()] = $aside->getOptions('content', TRUE);
    }
    return $element;
  }

}
