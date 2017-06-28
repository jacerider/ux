<?php

namespace Drupal\ux_aside\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ux_aside\UxAsideOptions;

/**
 * Class UxAsideSettingsForm.
 *
 * @package Drupal\ux_aside\Form
 */
class UxAsideSettingsForm extends ConfigFormBase {

  /**
   * Drupal\ux_aside\UxAsideOptions definition.
   *
   * @var \Drupal\ux_aside\UxAsideOptions
   */
  protected $uxAsideOptions;

  /**
   * Constructs a new UxAsideSettingsForm object.
   */
  public function __construct(ConfigFactoryInterface $config_factory, UxAsideOptions $ux_aside_options) {
    parent::__construct($config_factory);
    $this->uxAsideOptions = $ux_aside_options;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('ux_aside.options')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ux_aside.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ux_aside_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ux_aside.settings');

    $form['options'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Default options'),
      '#tree' => TRUE,
    ] + $this->uxAsideOptions->form();

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
    $config = $this->config('ux_aside.settings')
      ->set('options', array_filter($form_state->getValue('options')))
      ->save();
  }

}
