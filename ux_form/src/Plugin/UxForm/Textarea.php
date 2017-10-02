<?php

namespace Drupal\ux_form\Plugin\UxForm;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "textarea",
 *   label = @Translation("Textarea"),
 *   element_types = {
 *     "textarea",
 *   }
 * )
 */
class Textarea extends Input {

  /**
   * {@inheritdoc}
   */
  public function process(&$element) {
    if (!empty($element['#allowed_formats']) || !empty($element['#format'])) {
      // This textarea has a format selector and does not support label
      // floating.
      $this->disableFloat();
    }
    parent::process($element);
  }

}
