<?php

namespace Drupal\ux_aside\Element;

use Drupal\Core\Render\Element\Container;
use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

/**
 * Provides a render element that wraps child elements in an aside.
 *
 * Surrounds child elements with a <div> and adds attributes such as classes or
 * an HTML ID.
 *
 * Usage example:
 * @code
 * $form['needs_accommodation'] = array(
 *   '#type' => 'checkbox',
 *   '#title' => $this->t('Need Special Accommodations?'),
 * );
 *
 * $form['accommodation'] = array(
 *   '#type' => 'ux_aside_container',
 *   '#attributes' => array(
 *     'class' => 'accommodation',
 *   ),
 *   '#states' => array(
 *     'invisible' => array(
 *       'input[name="needs_accommodation"]' => array('checked' => FALSE),
 *     ),
 *   ),
 * );
 *
 * $form['accommodation']['diet'] = array(
 *   '#type' => 'textfield',
 *   '#title' => $this->t('Dietary Restrictions'),
 * );
 * @endcode
 *
 * @RenderElement("ux_aside_container")
 */
class UxAsideContainer extends Container {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#theme_wrappers' => ['ux_aside_container'],
    ] + parent::getInfo();
  }

}
