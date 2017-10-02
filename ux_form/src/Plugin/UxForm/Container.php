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
    $children = Element::children($element);
    $element['#ux_form_attributes']['class'][] = 'ux-form-container';
    $element['#ux_form_attributes']['class'][] = 'ux-form-container-js';
    if (count($children) || !empty($element['#ux_wrapper_supported'])) {
      parent::process($element);
    }
  }

}
