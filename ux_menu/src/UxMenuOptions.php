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
  public function processOptions(array $options) {
    $defaults = $this->getDefaults();
    $has_icon_support = $this->hasIconSupport();
    // Convert back icon to micon.
    if (!empty($options['backIcon']) && is_string($options['backIcon']) && $has_icon_support) {
      $options['backIcon'] = micon()->setIcon($options['backIcon'])->setIconOnly(TRUE)->render();
    }
    // Convert breadcrumb icon to micon.
    if (!empty($options['breadcrumbIcon']) && is_string($options['breadcrumbIcon']) && $has_icon_support) {
      $options['breadcrumbIcon'] = micon()->setIcon($options['breadcrumbIcon'])->setIconOnly(TRUE)->render();
    }
    // Convert item icon to micon.
    if (!empty($options['itemIcon']) && is_string($options['itemIcon']) && $has_icon_support) {
      $options['itemIcon'] = micon()->setIcon($options['itemIcon'])->setIconOnly(TRUE)->render();
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  protected function optionsForm(array $defaults = []) {
    $form = [];
    $has_icon_support = $this->hasIconSupport();

    $form = [
      '#type' => 'details',
      '#title' => $this->t('Menu Options'),
      '#open' => TRUE,
    ];

    $form['backNav'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use back button'),
      '#description' => $this->t('The back button provides a way to navigate backwards one level.'),
      '#default_value' => $defaults['backNav'],
      '#attributes' => [
        'class' => ['ux-menu-back-nav'],
      ],
      '#return_value' => TRUE,
    ];

    $form['backText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Back nav text'),
      '#description' => $this->t('The text shown that represents the home location.'),
      '#default_value' => $defaults['backText'],
      '#states' => [
        'visible' => [
          '.ux-menu-back-nav' => ['checked' => TRUE],
        ],
        'required' => [
          '.ux-menu-back-nav' => ['checked' => TRUE],
        ],
      ],
    ];

    if ($has_icon_support) {
      $form['backIcon'] = [
        '#type' => 'micon',
        '#title' => $this->t('Back button icon'),
        '#default_value' => $defaults['backIcon'],
        '#states' => [
          'visible' => [
            '.ux-menu-back-nav' => ['checked' => TRUE],
          ],
        ],
      ];
    }

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

    $form['breadcrumbText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Initial breadcrumb text'),
      '#description' => $this->t('The text shown that represents the home location.'),
      '#default_value' => $defaults['breadcrumbText'],
      '#states' => [
        'visible' => [
          '.ux-menu-breadcrumb-nav' => ['checked' => TRUE],
        ],
        'required' => [
          '.ux-menu-breadcrumb-nav' => ['checked' => TRUE],
        ],
      ],
    ];

    if ($has_icon_support) {
      $form['breadcrumbIcon'] = [
        '#type' => 'micon',
        '#title' => $this->t('Breadcrumb separator icon'),
        '#description' => $this->t('Icon used to separate breadcrumb items.'),
        '#default_value' => $defaults['breadcrumbIcon'],
        '#states' => [
          'visible' => [
            '.ux-menu-breadcrumb-nav' => ['checked' => TRUE],
          ],
        ],
      ];
    }

    if ($has_icon_support) {
      $form['itemIcon'] = [
        '#type' => 'micon',
        '#title' => $this->t('Item icon'),
        '#description' => $this->t('Icon used to signify menu items that will open a submenu.'),
        '#default_value' => $defaults['itemIcon'],
      ];
    }

    $form['theme'] = [
      '#type' => 'select',
      '#title' => $this->t('Theme'),
      '#options' => [
        'light' => $this->t('Light'),
        'dark' => $this->t('Dark'),
      ],
      '#empty_option' => $this->t('- None -'),
      '#default_value' => $defaults['theme'],
    ];

    return $form;
  }

}
