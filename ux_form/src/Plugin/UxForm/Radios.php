<?php

namespace Drupal\ux_form\Plugin\UxForm;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "radios",
 *   label = @Translation("Radios"),
 *   element_types = {
 *     "radios",
 *     "webform_entity_radios",
 *   }
 * )
 */
class Radios extends UxFormBase {

  /**
   * {@inheritdoc}
   */
  public function process(&$element) {
    parent::process($element);
    $element['#attributes']['class'][] = 'ux-form-radios';
    if (!empty($element['#inline'])) {
      $element['#attributes']['class'][] = 'ux-form-radios-inline';
    }
  }

}
