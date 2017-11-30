<?php

namespace Drupal\ux_form\Plugin\UxForm;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "language_select",
 *   label = @Translation("Language Select"),
 *   element_types = {
 *     "language_select",
 *   }
 * )
 */
class LanguageSelect extends Select {

  /**
   * {@inheritdoc}
   */
  public static function preRender($element) {
    // $element = parent::process($element);
    if (!isset($element['#options'])) {
      $element['#ux_wrapper_supported'] = FALSE;
    }
    return $element;
  }

}
