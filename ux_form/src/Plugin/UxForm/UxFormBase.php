<?php

namespace Drupal\ux_form\Plugin\UxForm;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Base class for Delivery type plugins.
 */
abstract class UxFormBase extends PluginBase implements UxFormPluginInterface, TrustedCallbackInterface {
  use StringTranslationTrait;

  /**
   * Plugin configuration.
   *
   * @var array
   */
  protected $configuration = [];

  /**
   * Disable the wrapping element for this input field.
   *
   * @var bool
   *   If TRUE the wrapping element will be added to this field.
   */
  protected $wrapperSupported = TRUE;

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = NestedArray::mergeDeep(
      $this->baseConfigurationDefaults(),
      $this->defaultConfiguration(),
      $configuration
    );
  }

  /**
   * Returns generic default configuration for ux form plugins.
   *
   * @return array
   *   An associative array with the default configuration.
   */
  protected function baseConfigurationDefaults() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form += $this->configurationForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function configurationForm($form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configurationValidate($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function configurationValidate($form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Process the delivery type's submission handling if no errors occurred
    // only.
    if (!$form_state->getErrors()) {
      $this->configurationSubmit($form, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function configurationSubmit($form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function applies($element) {
    // Allow individual form elements to pass #ux_form_default to bypass UX form
    // modifications.
    if (!empty($element['#ux_form_default'])) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function process(&$element) {
    $element['#pre_render'][] = [get_class($this), 'preRender'];
    $element['#ux_wrapper_supported'] = isset($element['#ux_wrapper_supported']) ? $element['#ux_wrapper_supported'] : $this->wrapperSupported;
  }

  /**
   * {@inheritdoc}
   */
  public static function preRender($element) {
    if ($element['#ux_wrapper_supported']) {
      $element['#theme_wrappers'][] = 'ux_form_element_container';
    }
    if (isset($element['#attributes'])) {
      ux_form_inline_convert($element['#attributes']);
    }
    return $element;
  }

  /**
   * Enable wrapping element support for this field.
   */
  protected function enableWrapper() {
    $this->wrapperSupported = TRUE;
  }

  /**
   * Disable wrapping element support for this field.
   */
  protected function disableWrapper() {
    $this->wrapperSupported = FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public static function trustedCallbacks() {
    return ['preRender'];
  }

}
