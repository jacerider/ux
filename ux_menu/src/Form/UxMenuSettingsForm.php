<?php

namespace Drupal\ux_menu\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ux_menu\UxMenuOptions;

/**
 * Class UxMenuSettingsForm.
 *
 * @package Drupal\ux_menu\Form
 */
class UxMenuSettingsForm extends ConfigFormBase {

  /**
   * Drupal\ux_menu\UxMenuOptions definition.
   *
   * @var \Drupal\ux_menu\UxMenuOptions
   */
  protected $uxMenuOptions;

  /**
   * Constructs a new UxMenuSettingsForm object.
   */
  public function __construct(ConfigFactoryInterface $config_factory, UxMenuOptions $ux_menu_options) {
    parent::__construct($config_factory);
    $this->uxMenuOptions = $ux_menu_options;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('ux_menu.options')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ux_menu.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ux_menu_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ux_menu.settings');

    $form['options'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Default options'),
      '#tree' => TRUE,
    ] + $this->uxMenuOptions->form();

    $form = parent::buildForm($form, $form_state);

    $form['actions']['reset'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reset configuration'),
      '#submit' => [
        [$this, 'resetForm'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->uxMenuOptions->saveOptions($form_state->getValue('options'));
  }

  /**
   * {@inheritdoc}
   */
  public function resetForm(array &$form, FormStateInterface $form_state) {
    $this->uxMenuOptions->saveOptions($this->uxMenuOptions->getDefaults());
  }

}
