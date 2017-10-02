<?php

namespace Drupal\ux_form\Plugin\UxForm;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "wrapper",
 *   label = @Translation("Wrapper"),
 *   element_types = {
 *     "fieldset",
 *     "details",
 *   }
 * )
 */
class Wrapper extends Container {

  /**
   * {@inheritdoc}
   */
  public function process(&$element) {
    parent::process($element);
    $element['#ux_form_attributes']['class'][] = 'ux-form-wrapper';
    $element['#ux_form_attributes']['class'][] = 'ux-form-wrapper-js';
    $element['#ux_form_is_wrapper'] = TRUE;
    if (!empty($element['#description'])) {
      $element['#description'] = '<div class="ux-form-element-wrapper-description">' . $element['#description'] . '</div>';
    }
  }

}
