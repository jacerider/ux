<?php

namespace Drupal\ux_aside\Element;

use Drupal\Core\Render\Element\RenderElementBase;
use Drupal\ux_aside\UxAsideInterface;
use Drupal\Component\Utility\NestedArray;

/**
 * Provides a render element for an aside trigger.
 *
 * @RenderElement("ux_aside_trigger")
 */
class UxAsideTrigger extends RenderElementBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#theme' => 'ux_aside_trigger',
      '#aside' => NULL,
      '#pre_render' => [
        [static::class, 'preRenderAsideTrigger'],
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
      $element['#attributes'] = isset($element['#attributes']) ? $element['#attributes'] : [];
      $element['#attributes'] = NestedArray::mergeDeep($aside->getTriggerAttributes(), $element['#attributes']);
      $element['#attributes']['class'][] = 'ux-aside-trigger-' . $aside->id();
      $element['#cache']['keys'] = ['ux_asides', 'ux_aside', $aside->id()];
      $element['#cache']['tags'] = $aside->getCacheTags();
      $element['#cache']['contexts'] = $aside->getCacheContexts();
      $element['#label'] = $aside->getTrigger();
    }

    return $element;
  }

}
