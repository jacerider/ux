<?php

namespace Drupal\ux_form\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ThemeHandler;

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
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, ThemeHandler $theme_handler) {
    parent::__construct($config_factory);
    $this->themeHandler = $theme_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('theme_handler')
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
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ux_form.settings');

    $themes = $themes = $this->themeHandler->listInfo();
    uasort($themes, 'system_sort_modules_by_info_name');

    $theme_options = [];
    foreach ($themes as &$theme) {
      if (!empty($theme->info['hidden'])) {
        continue;
      }
      if (!empty($theme->status)) {
        $theme_options[$theme->getName()] = $theme->info['name'];
      }
    }

    $form['themes'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enabled Themes'),
      '#options' => $theme_options,
      '#default_value' => $config->get('themes'),
    ];

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
      ->set('themes', array_filter($form_state->getValue('themes')))
      ->save();
  }

}
