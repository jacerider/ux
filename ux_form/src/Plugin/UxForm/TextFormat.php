<?php

namespace Drupal\ux_form\Plugin\UxForm;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "text_format",
 *   label = @Translation("Text Format"),
 *   element_types = {
 *     "text_format",
 *   }
 * )
 */
class TextFormat extends UxFormBase {

  /**
   * {@inheritdoc}
   */
  public function process(&$element, FormStateInterface $form_state, &$complete_form) {
    parent::process($element, $form_state, $complete_form);
    parent::process($element['format'], $form_state, $complete_form);
    $element['format']['#attributes']['class'][] = 'ux-filter-wrapper';
  }

}
