<?php

namespace Drupal\ux_menu;

use Drupal\ux\UxOptionsBase;

/**
 * Class UxMenuOptions.
 *
 * @package Drupal\ux_menu
 */
class UxMenuOptions extends UxOptionsBase {

  /**
   * {@inheritdoc}
   */
  public function getModuleId() {
    return 'ux_menu';
  }

  /**
   * {@inheritdoc}
   */
  protected function optionsForm(array $defaults = []) {
    $form = [];

    $form = [
      '#type' => 'fieldset',
      '#title' => $this->t('Options'),
    ];

    $form['backNav'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use back button'),
      '#description' => $this->t('The back button provides a way to navigate backwards one level.'),
      '#default_value' => $defaults['backNav'],
      '#return_value' => TRUE,
    ];

    $form['breadcrumbNav'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use breadcrumb'),
      '#description' => $this->t('The breadcrumb provides a navigation trail to return to previous levels.'),
      '#default_value' => $defaults['breadcrumbNav'],
      '#attributes' => [
        'class' => ['ux-menu-breadcrumb-nav'],
      ],
      '#return_value' => TRUE,
    ];

    $form['breadcrumbInitialText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Use breadcrumb'),
      '#description' => $this->t('The breadcrumb provides a navigation trail to return to previous levels.'),
      '#default_value' => $defaults['breadcrumbInitialText'],
      '#states' => [
        'visible' => [
          '.ux-menu-breadcrumb-nav' => ['checked' => TRUE],
        ],
        'required' => [
          '.ux-menu-breadcrumb-nav' => ['checked' => TRUE],
        ],
      ],
    ];

    return $form;
  }

}
