<?php

namespace Drupal\ux_filters\Plugin\UxFilter;

use Drupal\ux_filters\Plugin\UxFilterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'boolean' formatter.
 *
 * @UxFilter(
 *   id = "boolean_checkbox",
 *   label = @Translation("Checkbox"),
 *   fieldTypes = {
 *     "boolean",
 *   }
 * )
 */
class BooleanCheckbox extends UxFilterBase {

  /**
   * {@inheritdoc}
   */
  public function exposedElementAlter(&$element, FormStateInterface $form_state, $element_id) {
    $element['#theme'] = 'ux_select_as_checkbox';
  }

}
