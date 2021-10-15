<?php

namespace Drupal\ux_filters\Plugin\UxFilter;

use Drupal\ux_filters\Plugin\UxFilterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'select to link' formatter.
 *
 * @UxFilter(
 *   id = "select_link",
 *   label = @Translation("Links"),
 *   fieldTypes = {
 *     "taxonomy_index_tid",
 *     "taxonomy_index_machine_name",
 *   }
 * )
 */
class SelectLink extends UxFilterBase {

  /**
   * {@inheritdoc}
   */
  public function exposedElementAlter(&$element, FormStateInterface $form_state, $context) {
    $element['#theme'] = 'ux_select_as_links';
    if (isset($element['#options']['All'])) {
      $element['#options']['All'] = t('All');
    }
  }

}
