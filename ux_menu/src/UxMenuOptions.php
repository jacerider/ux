<?php

namespace Drupal\ux_menu;

use Drupal\ux\UxOptionsBase;
use Drupal\Core\Form\FormStateInterface;

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
      $options['breadcrumbIcon'] = micon()->setIcon($options['breadcrumbIcon'])->setIconOnly(empty($options['breadcrumbText']))->render();
    }
    // Convert breadcrumb icon to micon.
    if (!empty($options['breadcrumbSeparatorIcon']) && is_string($options['breadcrumbSeparatorIcon']) && $has_icon_support) {
      $options['breadcrumbSeparatorIcon'] = micon()->setIcon($options['breadcrumbSeparatorIcon'])->setIconOnly(TRUE)->render();
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

    // Back settings.
    $form['back'] = [
      '#type' => 'details',
      '#title' => $this->t('Back Nav'),
      '#process' => [[get_class(), 'processParents']],
    ];

    $form['back']['backNav'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use back button'),
      '#description' => $this->t('The back button provides a way to navigate backwards one level.'),
      '#default_value' => $defaults['backNav'],
      '#attributes' => [
        'class' => ['ux-menu-back-nav'],
      ],
      '#return_value' => TRUE,
    ];

    $form['back']['backText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Back nav text'),
      '#description' => $this->t('The text shown that represents the home location.'),
      '#default_value' => $defaults['backText'],
      '#attributes' => [
        'class' => ['ux-menu-back-text'],
      ],
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
      unset($form['back']['backText']['#states']['required']);
      $form['back']['backIcon'] = [
        '#type' => 'micon',
        '#title' => $this->t('Back button icon'),
        '#default_value' => $defaults['backIcon'],
        '#states' => [
          'visible' => [
            '.ux-menu-back-nav' => ['checked' => TRUE],
          ],
          'required' => [
            '.ux-menu-back-text' => ['value' => ''],
          ],
        ],
      ];
    }

    // Breadcrumb settings.
    $form['breadcrumb'] = [
      '#type' => 'details',
      '#title' => $this->t('Breadcrumb Nav'),
      '#process' => [[get_class(), 'processParents']],
    ];

    $form['breadcrumb']['breadcrumbNav'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use breadcrumb'),
      '#description' => $this->t('The breadcrumb provides a navigation trail to return to previous levels.'),
      '#default_value' => $defaults['breadcrumbNav'],
      '#attributes' => [
        'class' => ['ux-menu-breadcrumb-nav'],
      ],
      '#return_value' => TRUE,
    ];

    $form['breadcrumb']['breadcrumbText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Initial breadcrumb text'),
      '#description' => $this->t('The text shown that represents the home location.'),
      '#default_value' => $defaults['breadcrumbText'],
      '#attributes' => [
        'class' => ['ux-menu-breadcrumb-text'],
      ],
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
      unset($form['breadcrumb']['breadcrumbText']['#states']['required']);
      $form['breadcrumb']['breadcrumbIcon'] = [
        '#type' => 'micon',
        '#title' => $this->t('Breadcrumb icon'),
        '#description' => $this->t('Icon used to represent the home location.'),
        '#default_value' => $defaults['breadcrumbIcon'],
        '#states' => [
          'visible' => [
            '.ux-menu-breadcrumb-nav' => ['checked' => TRUE],
          ],
          'required' => [
            '.ux-menu-breadcrumb-text' => ['value' => ''],
          ],
        ],
      ];
      $form['breadcrumb']['breadcrumbSeparatorIcon'] = [
        '#type' => 'micon',
        '#title' => $this->t('Breadcrumb separator icon'),
        '#description' => $this->t('Icon used to separate breadcrumb items.'),
        '#default_value' => $defaults['breadcrumbSeparatorIcon'],
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

  /**
   * {@inheritdoc}
   */
  public static function processParents(&$element, FormStateInterface $form_state, &$complete_form) {
    array_pop($element['#parents']);
    return $element;
  }

}
