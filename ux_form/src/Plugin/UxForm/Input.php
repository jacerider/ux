<?php

namespace Drupal\ux_form\Plugin\UxForm;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "input",
 *   label = @Translation("Input"),
 *   element_types = {
 *     "textfield",
 *     "number",
 *     "tel",
 *     "url",
 *     "email",
 *     "password",
 *     "search",
 *     "entity_autocomplete",
 *     "commerce_number",
 *   }
 * )
 */
class Input extends UxFormBase {

  /**
   * Disable floating for this input field.
   *
   * @var bool
   *   If TRUE floating is allowed for this field.
   */
  protected $floatSupported = TRUE;

  /**
   * {@inheritdoc}
   */
  public function process(&$element) {
    parent::process($element);
    $element['#wrapper_attributes']['class'][] = 'ux-form-input';
    $element['#wrapper_attributes']['class'][] = 'ux-form-input-js';
    $element['#attributes']['class'][] = 'ux-form-input-item';
    $element['#attributes']['class'][] = 'ux-form-input-item-js';
    $element['#attached']['library'][] = 'ux_form/ux_form.input';
    if (!empty($element['#field_prefix'])) {
      $element['#wrapper_attributes']['class'][] = 'has-prefix';
    }
    if (!empty($element['#field_suffix'])) {
      $element['#wrapper_attributes']['class'][] = 'has-suffix';
    }
    if ($this->getConfiguration()['float'] && $this->floatSupported) {
      $element['#ux_form_attributes']['class'][] = 'ux-form-element-float';
    }
  }

  /**
   * Enable floating support for this field.
   */
  protected function enableFloat() {
    $this->floatSupported = TRUE;
  }

  /**
   * Disable floating support for this field.
   */
  protected function disableFloat() {
    $this->floatSupported = FALSE;
  }

}
