<?php

namespace Drupal\ux_form\Plugin\UxForm;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "radio",
 *   label = @Translation("Radio"),
 *   element_types = {
 *     "radio",
 *   }
 * )
 */
class Radio extends UxFormBase {

  /**
   * {@inheritdoc}
   */
  public function process(&$element) {
    $element['#wrapper_attributes']['class'][] = 'ux-form-radio';
    $element['#attached']['library'][] = 'ux_form/ux_form.radio';
    if (!empty($element['#title'])) {
      $element['#title'] = '<div class="ux-ripple"></div>' . $element['#title'];
    }
  }

}
