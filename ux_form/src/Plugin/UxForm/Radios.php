<?php

namespace Drupal\ux_form\Plugin\UxForm;

use Drupal\Core\Form\FormStateInterface;

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
  public function process(&$element, FormStateInterface $form_state, &$complete_form) {
    parent::process($element, $form_state, $complete_form);
    $element['#attributes']['class'][] = 'ux-form-radios';
    if (!empty($element['#inline'])) {
      $element['#attributes']['class'][] = 'ux-form-radios-inline';
    }
  }

}
