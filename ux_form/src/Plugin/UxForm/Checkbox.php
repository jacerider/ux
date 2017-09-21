<?php

namespace Drupal\ux_form\Plugin\UxForm;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "checkbox",
 *   label = @Translation("Checkbox"),
 *   element_types = {
 *     "checkbox",
 *   }
 * )
 */
class Checkbox extends UxFormBase {

  /**
   * {@inheritdoc}
   */
  public function process(&$element, FormStateInterface $form_state, &$complete_form) {
    // The property #checkboxes_child will be true if this checkbox is part
    // of a checkboxes element.
    // @see Drupal\ux_form\Plugin\UxForm\Checkboxes
    if (!empty($element['#checkboxes_child'])) {
      // Do not wrap this element in ux_form_element_container because it is
      // part of a checkbox collection and the collection will be wrapped.
      $this->disableWrapper();
    }

    parent::process($element, $form_state, $complete_form);

    $element['#wrapper_attributes']['class'][] = 'ux-form-checkbox';
    $element['#attached']['library'][] = 'ux_form/ux_form.checkbox';
    if (!empty($element['#title'])) {
      $element['#title'] = '<div class="ux-ripple"></div>' . $element['#title'];
    }
  }

}
