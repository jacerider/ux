<?php

namespace Drupal\ux_form\Plugin\UxForm;

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
  public function process(&$element) {
    parent::process($element);
    parent::process($element['format']);
    $element['format']['#attributes']['class'][] = 'ux-filter-wrapper';
  }

}
