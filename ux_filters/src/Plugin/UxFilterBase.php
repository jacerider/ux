<?php

namespace Drupal\ux_filters\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base class for UX | Filter plugins.
 */
abstract class UxFilterBase extends PluginBase implements UxFilterInterface {

  /**
   * {@inheritdoc}
   */
  public function exposedElementAlter(&$element, FormStateInterface $form_state, $element_id) {
  }

}
