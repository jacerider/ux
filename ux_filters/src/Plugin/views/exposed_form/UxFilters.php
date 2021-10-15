<?php

namespace Drupal\ux_filters\Plugin\views\exposed_form;

use Drupal\views\Plugin\views\exposed_form\ExposedFormPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ux_filters\Plugin\UxFilterManager;
use Drupal\Core\Form\FormStateInterface;

/**
 * Exposed form plugin that provides a basic exposed form.
 *
 * @ingroup views_exposed_form_plugins
 *
 * @ViewsExposedForm(
 *   id = "ux_filters",
 *   title = @Translation("UX | Filters"),
 *   help = @Translation("Provides additional options for exposed form elements.")
 * )
 */
class UxFilters extends ExposedFormPluginBase {

  /**
   * The UX filter plugin manager.
   *
   * @var \Drupal\ux_filters\Plugin\UxFilterManager
   */
  protected $uxFilterManager;

  /**
   * Constructs a PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, UxFilterManager $ux_filter_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->definition = $plugin_definition + $configuration;
    $this->uxFilterManager = $ux_filter_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.ux_filter')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['general'] = [
      'default' => [
        'autosubmit' => FALSE,
        'autosubmit_hide' => FALSE,
        'allow_secondary' => FALSE,
        'secondary_label' => $this->t('Advanced options'),
      ],
    ];
    foreach ($this->view->display_handler->getHandlers('filter') as $id => $filter) {
      $options[$id] = [
        'default' => [
          'format' => '',
          'more' => [
            'is_secondary' => 0,
          ],
        ],
      ];
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    /*
     * Add general options for exposed form items.
     */

    // Add the 'autosbumit' functionality from Views 7.x.
    $form['general']['autosubmit'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Autosubmit'),
      '#description' => $this->t('Automatically submit the form once an element is changed.'),
      '#default_value' => $this->options['general']['autosubmit'],
    ];

    $form['general']['autosubmit_hide'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide submit button'),
      '#description' => $this->t('Hide submit button if javascript is enabled.'),
      '#default_value' => $this->options['general']['autosubmit_hide'],
      '#states' => [
        'visible' => [
          ':input[name="exposed_form_options[general][autosubmit]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['general']['allow_secondary'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable secondary exposed form options'),
      '#default_value' => $this->options['general']['allow_secondary'],
      '#description' => t('Allows you to specify some exposed form elements as being secondary options and places those elements in a collapsible "details" element. Use this option to place some exposed filters in an "Advanced Search" area of the form, for example.'),
    ];
    $form['general']['secondary_label'] = [
      '#type' => 'textfield',
      '#default_value' => $this->options['general']['secondary_label'],
      '#title' => t('Secondary options label'),
      '#description' => t(
        'The name of the details element to hold secondary options. This cannot be left blank or there will be no way to show/hide these options.'
      ),
      '#states' => [
        'required' => [
          ':input[name="exposed_form_options[general][allow_secondary]"]' => ['checked' => TRUE],
        ],
        'visible' => [
          ':input[name="exposed_form_options[general][allow_secondary]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // Go through each filter and add UX options.
    foreach ($this->view->display_handler->getHandlers('filter') as $id => $filter) {
      if (!$filter->options['exposed']) {
        continue;
      }
      $type = $filter->getPluginId();
      $title = $filter->options['expose']['identifier'];
      $identifier = '"' . $title . '"';

      $form[$id] = [
        '#type' => 'fieldset',
        '#title' => $title,
      ];

      $options = ['' => '- Default -'] + $this->uxFilterManager->getOptions($type);
      $form[$id]['format'] = [
        '#type' => 'select',
        '#title' => t('Display @identifier exposed filter as', ['@identifier' => $identifier]),
        '#default_value' => $this->options[$id]['format'],
        '#options' => $options,
      ];
      // Details element to keep the UI from getting out of hand.
      $form[$id]['more'] = [
        '#type' => 'details',
        '#title' => t('More options for @identifier', ['@identifier' => $identifier]),
      ];

      // Allow any filter to be moved into the secondary options element.
      $form[$id]['more']['is_secondary'] = [
        '#type' => 'checkbox',
        '#title' => t('This is a secondary option'),
        '#default_value' => $this->options[$id]['more']['is_secondary'],
        '#states' => [
          'visible' => [
            ':input[name="exposed_form_options[general][allow_secondary]"]' => ['checked' => TRUE],
          ],
        ],
        '#description' => t('Places this element in the secondary options portion of the exposed form.'),
      ];
    }

  }

  /**
   * {@inheritdoc}
   */
  public function exposedFormAlter(&$form, FormStateInterface $form_state) {
    parent::exposedFormAlter($form, $form_state);
    $settings = $this->options;
    $allow_secondary = $settings['general']['allow_secondary'];

    // Some elements may be placed in a secondary details element (eg: "Advanced
    // search options"). Place this after the exposed filters and before the
    // rest of the items in the exposed form.
    if ($allow_secondary) {
      $secondary = [
        '#type' => 'details',
        '#title' => $this->options['general']['secondary_label'],
        '#weight' => 1000,
      ];
      $form['actions']['#weight'] = 1001;
    }

    // Apply autosubmit values.
    if (!empty($settings['general']['autosubmit'])) {
      $form = array_merge_recursive($form, ['#attributes' => ['data-ux-auto-submit-full-form' => '']]);
      $form['actions']['submit']['#attributes']['data-ux-auto-submit-click'] = '';
      $form['#attached']['library'] = ['ux/ux.auto_submit'];

      if (!empty($settings['general']['autosubmit_hide'])) {
        $form['actions']['submit']['#attributes']['class'][] = 'js-hide';
      }
    }

    // Go through each filter and alter if necessary.
    foreach ($this->view->display_handler->getHandlers('filter') as $id => $filter) {
      if (!isset($form['#info']["filter-$id"]['value'])) {
        continue;
      }
      $identifier = $form['#info']["filter-$id"]['value'];
      $format = $this->options[$id]['format'];
      if ($format) {
        $plugin = $this->uxFilterManager->createInstance($format);
        $context = [
          'id' => $identifier,
          'plugin' => $this,
        ];
        $plugin->exposedElementAlter($form[$identifier], $form_state, $context);
      }
      if ($allow_secondary && $this->options[$id]['more']['is_secondary']) {
        if (!empty($form[$identifier])) {
          // Move exposed operators with exposed filters.
          if (!empty($this->display->display_options['filters'][$identifier]['expose']['use_operator'])) {
            $op_id = $this->display->display_options['filters'][$identifier]['expose']['operator_id'];
            $secondary[$op_id] = $form[$op_id];
            unset($form[$op_id]);
          }
          $secondary[$identifier] = $form[$identifier];
          unset($form[$identifier]);
          $secondary[$identifier]['#title'] = $form['#info']["filter-$id"]['label'];
          unset($form['#info']["filter-$id"]);
        }
      }
    }

    // Check for secondary elements.
    if ($allow_secondary && !empty($secondary)) {
      // Add secondary elements after regular exposed filter elements.
      $remaining = array_splice($form, count($form['#info']) + 1);
      $form['secondary'] = $secondary;
      $form = array_merge($form, $remaining);
      $form['#info']['filter-secondary']['value'] = 'secondary';
    }
  }

}
