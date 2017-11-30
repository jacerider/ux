<?php

namespace Drupal\ux_form\Plugin\UxForm;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "tel",
 *   label = @Translation("Telephone"),
 *   element_types = {
 *     "tel",
 *   }
 * )
 */
class Tel extends Input {

  /**
   * {@inheritdoc}
   */
  public function process(&$element) {
    parent::process($element);
    $element['#attached']['library'][] = 'ux_form/ux_form.inputmask';
    $element['#attributes']['class'][] = 'ux-form-inputmask-js';
    $element['#attributes']['data-inputmask-mask'] = '(999) 999-9999';
  }

}
