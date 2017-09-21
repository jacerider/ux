<?php

namespace Drupal\ux_form\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ThemeHandler;
use Drupal\Core\Render\ElementInfoManager;

/**
 * Class UxFormSettingsForm.
 *
 * @package Drupal\ux_form\Form
 */
class UxFormSettingsForm extends ConfigFormBase {

  /**
   * Drupal\Core\Extension\ThemeHandler definition.
   *
   * @var \Drupal\Core\Extension\ThemeHandler
   */
  protected $themeHandler;

  /**
   * Drupal\Core\Render\ElementInfoManager definition.
   *
   * @var Drupal\Core\Render\ElementInfoManager
   */
  protected $elementInfoManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, ThemeHandler $theme_handler, ElementInfoManager $element_info_manager) {
    parent::__construct($config_factory);
    $this->themeHandler = $theme_handler;
    $this->elementInfoManager = $theme_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('theme_handler'),
      $container->get('plugin.manager.element_info')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ux_form.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ux_form_settings_form';
  }

  /**
   * Theme setting defaults.
   */
  protected function getThemeSettingDefaults() {
    return [
      'status' => FALSE,
      'float' => FALSE,
      'theme' => 'blue',
    ];
  }

  /**
   * Get theme setting.
   */
  protected function getSetting($theme_id, $key) {
    $config = $this->config('ux_form.settings')->get('themes');
    $theme_config = isset($config[$theme_id]) ? $config[$theme_id] : [];
    $theme_config += $this->getThemeSettingDefaults();
    return isset($theme_config[$key]) ? $theme_config[$key] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ux_form.settings');

    $themes = $themes = $this->themeHandler->listInfo();
    uasort($themes, 'system_sort_modules_by_info_name');

    $form['themes'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];

    $theme_options = [];
    foreach ($themes as &$theme) {
      if (!empty($theme->info['hidden'])) {
        continue;
      }
      $theme_id = $theme->getName();
      $states = [
        'visible' => [
          ':input[name="themes[' . $theme_id . '][status]"]' => ['checked' => TRUE],
        ],
      ];

      $element = [
        '#type' => 'details',
        '#title' => $theme->info['name'],
        '#open' => TRUE,
      ];

      $element['status'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Enabled'),
        '#default_value' => $this->getSetting($theme_id, 'status'),
      ];

      $element['float'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Float input labels over empty fields'),
        '#default_value' => $this->getSetting($theme_id, 'float'),
        '#states' => $states,
      ];

      $element['theme'] = [
        '#type' => 'select',
        '#title' => $this->t('Theme'),
        '#options' => [
          'light' => $this->t('Light'),
          'dark' => $this->t('Dark'),
          'blue' => $this->t('Blue'),
          'blue.dark' => $this->t('Blue Dark'),
        ],
        '#default_value' => $this->getSetting($theme_id, 'theme'),
        '#states' => $states,
      ];

      $form['themes'][$theme_id] = $element;
    }

    // $form['themes'] = [
    //   '#type' => 'checkboxes',
    //   '#title' => $this->t('Enabled Themes'),
    //   '#options' => $theme_options,
    //   '#default_value' => $config->get('themes'),
    // ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->config('ux_form.settings')
      ->set('themes', array_filter($form_state->getValue('themes'), function ($item) {
        return !empty($item['status']);
      }))
      ->save();
  }

}
