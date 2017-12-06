<?php

namespace Drupal\ux_aside\Element;

use Drupal\Core\Render\Element\RenderElement;
use Drupal\ux_aside\UxAsideInterface;

/**
 * Provides a render element for an aside.
 *
 * @RenderElement("ux_aside")
 */
class UxAside extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
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
      $options = $aside->getOptions('content', TRUE);
      $element['#cache'] = [
        'keys' => ['ux-asides', 'ux-aside', $aside->id()],
        'tags' => $aside->getCacheTags(),
        'contexts' => $aside->getCacheContexts(),
        'max-age' => $aside->getCacheMaxAge(),
      ];
      $element['#attributes'] = $aside->getContentAttributes()->toArray();
      $element['#attributes']['id'] = 'ux-aside-' . $aside->id();
      $element['#attributes']['class'][] = 'uxAside';
      $element['#attributes']['class'][] = 'ux-aside-' . $aside->id();
      $element['#content'] = $aside->getContent();
      $element['#content_attributes']['class'][] = 'uxAside-inner';
      $element['#attached']['drupalSettings']['ux']['aside']['items'][$aside->id()] = $options;
      if (!empty($options['theme'])) {
        $element['#attached']['library'][] = 'ux_aside/ux_aside.theme.' . $options['theme'];
      }
    }
    return $element;
  }

}
