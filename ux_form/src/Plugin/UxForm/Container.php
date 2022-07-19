<?php

namespace Drupal\ux_form\Plugin\UxForm;

use Drupal\Core\Render\Element;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "container",
 *   label = @Translation("Container"),
 *   element_types = {
 *     "container",
 *   }
 * )
 */
class Container extends UxFormBase {

  /**
   * {@inheritdoc}
   */
  public function process(&$element) {

    // Special langcode field wrapper.
    if (isset($element['widget']['#field_name']) && $element['widget']['#field_name'] === 'langcode') {
      if (!isset($element['widget'][0]['value']['#options'])) {
        // Do not wraper langcode field as it is empty if it has no options.
        // No options means the language module is not enabled or only a single
        // language is enabled.
        $element['#ux_wrapper_supported'] = FALSE;
      }
    }

    $children = Element::children($element);
    $element['#ux_form_attributes']['class'][] = 'ux-form-container';
    $element['#ux_form_attributes']['class'][] = 'ux-form-container-js';
    if (!$this->hasVisibleChildren($element)) {
      $element['#ux_form_attributes']['class'][] = 'ux-form-container-hide';
    }
    if (count($children) || !empty($element['#ux_wrapper_supported']) || isset($element['#group'])) {
      parent::process($element);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function hasVisibleChildren($element) {
    $visible = FALSE;
    if (isset($element['#type']) && !in_array($element['#type'], ['value', 'container'])) {
      $visible = TRUE;
    }
    foreach (Element::children($element) as $key) {
      $child_element = $element[$key];
      if ($this->hasVisibleChildren($child_element)) {
        $visible = TRUE;
      }
    }
    return $visible;
  }

}
