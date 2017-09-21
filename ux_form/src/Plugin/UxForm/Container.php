<?php

namespace Drupal\ux_form\Plugin\UxForm;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "container",
 *   label = @Translation("Container"),
 *   element_types = {
 *     "fieldset",
 *     "details",
 *     "container",
 *   }
 * )
 */
class Container extends UxFormBase {

  /**
   * {@inheritdoc}
   */
  public function process(&$element, FormStateInterface $form_state, &$complete_form) {
    parent::process($element, $form_state, $complete_form);
    if ($element['#type'] !== 'container') {
      $element['#ux_form_attributes']['class'][] = 'ux-form-container';
    }
    $element['#ux_form_attributes']['class'][] = 'ux-form-container-js';
    if (!empty($element['#description'])) {
      $element['#description'] = '<div class="ux-form-element-container-description">' . $element['#description'] . '</div>';
    }
  }

}
