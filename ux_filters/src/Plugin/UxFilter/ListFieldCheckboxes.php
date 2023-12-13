<?php

namespace Drupal\ux_filters\Plugin\UxFilter;

use Drupal\ux_filters\Plugin\UxFilterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'boolean' formatter.
 *
 * @UxFilter(
 *   id = "listfield_checkboxes",
 *   label = @Translation("Checkboxes"),
 *   fieldTypes = {
 *     "list_field",
 *     "taxonomy_index_tid",
 *     "eref_node_titles",
 *   }
 * )
 */
class ListFieldCheckboxes extends UxFilterBase {

  /**
   * {@inheritdoc}
   */
  public function exposedElementAlter(&$element, FormStateInterface $form_state, $context) {
    $element_id = $context['id'];
    if (empty($element['#multiple'])) {
      $element['#type'] = 'radios';
    }
    else {
      $element['#type'] = 'checkboxes';
    }
    unset($element['#options']['All']);
    $user_input = $form_state->getUserInput();
    if ($user_input[$element_id] == 'All') {
      $user_input[$element_id] = array();
      $form_state->setUserInput($user_input);
    }
  }

}
