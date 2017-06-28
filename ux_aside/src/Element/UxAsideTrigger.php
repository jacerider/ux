<?php

namespace Drupal\ux_aside\Element;

use Drupal\Core\Render\Element\RenderElement;
use Drupal\ux_aside\UxAsideInterface;

/**
 * Provides a render element for an aside trigger.
 *
 * @RenderElement("ux_aside_trigger")
 */
class UxAsideTrigger extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#theme' => 'ux_aside_trigger',
      '#aside' => NULL,
      '#pre_render' => [
        [get_class(), 'preRenderAsideTrigger'],
      ],
    ];
  }

  /**
   * Builds the aside trigger as a structured array ready for drupal_render().
   *
   * @param array $element
   *   A renderable array.
   *
   * @return array
   *   A renderable array.
   */
  public static function preRenderAsideTrigger(array $element) {
    $aside = $element['#aside'];

    if ($aside instanceof UxAsideInterface) {
      $aside->prepareToRender();
      $element['#cache']['keys'] = ['ux_asides', 'ux_aside', $aside->id()];
      $element['#cache']['tags'] = $aside->getCacheTags();
      $element['#cache']['contexts'] = $aside->getCacheContexts();
      $element['#attributes']['data-ux-aside'] = $aside->id();
      $element['#attributes']['class'][] = 'ux-aside-trigger';
      $element['#attributes']['class'][] = 'ux-aside-trigger-' . $aside->id();
      $element['#label'] = $aside->getTrigger();
    }

    return $element;
  }

}
