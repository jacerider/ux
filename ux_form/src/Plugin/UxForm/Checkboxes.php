<?php

namespace Drupal\ux_form\Plugin\UxForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "checkboxes",
 *   label = @Translation("Checkboxes"),
 *   element_types = {
 *     "checkboxes",
 *     "webform_entity_checkboxes",
 *   }
 * )
 */
class Checkboxes extends UxFormBase {

  /**
   * {@inheritdoc}
   */
  public function process(&$element, FormStateInterface $form_state, &$complete_form) {
    parent::process($element, $form_state, $complete_form);
    $element['#attributes']['class'][] = 'ux-form-checkboxes';
    foreach (Element::children($element) as $key) {
      $child_element = &$element[$key];
      if (isset($child_element['#type']) && $child_element['#type'] == 'checkbox') {
        $child_element['#checkboxes_child'] = TRUE;
      }
    }
    if (!empty($element['#inline'])) {
      $element['#attributes']['class'][] = 'ux-form-checkboxes-inline';
    }
  }

}
