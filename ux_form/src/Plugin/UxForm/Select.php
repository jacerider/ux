<?php

namespace Drupal\ux_form\Plugin\UxForm;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "select",
 *   label = @Translation("Select"),
 *   element_types = {
 *     "select",
 *     "webform_entity_select",
 *   }
 * )
 */
class Select extends UxFormBase {

  /**
   * {@inheritdoc}
   */
  public function process(&$element) {
    parent::process($element);
    $element['#wrapper_attributes']['class'][] = 'ux-form-select';
    $element['#attached']['library'][] = 'ux_form/ux_form.select';
    if (isset($element['#multiple']) && $element['#multiple']) {
      $element['#attached']['library'][] = 'ux_form/ux_form.checkbox';
    }
  }

}
