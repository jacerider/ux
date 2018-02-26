<?php

namespace Drupal\ux;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Component\Serialization\Yaml;
use Drupal\Component\Utility\NestedArray;

/**
 * Defines a base options implementation that other ux modules can use.
 *
 * @ingroup ux_api
 */
abstract class UxOptionsBase implements UxOptionsInterface {
  use StringTranslationTrait;

  /**
   * The module id.
   *
   * @var string
   *   The module id.
   */
  protected $moduleId;

  /**
   * The options config id.
   *
   * @var string
   *   The option config id.
   */
  protected $configId = 'settings';

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The options config name.
   *
   * @var string
   *   The options config name.
   */
  protected $configName;

  /**
   * The option defaults.
   *
   * @var array
   */
  protected $defaults;

  /**
   * The processed option defaults.
   *
   * @var array
   */
  protected $defaultsProcessed;

  /**
   * The options.
   *
   * @var Drupal\Core\Config\ImmutableConfig
   */
  protected $options;

  /**
   * The processed options.
   *
   * @var Drupal\Core\Config\ImmutableConfig
   */
  protected $optionsProcessed;

  /**
   * Constructs a new UxAsideOptions object.
   */
  public function __construct(ConfigFactoryInterface $options_factory) {
    $this->moduleId = $this->getModuleId();
    $this->configName = $this->moduleId . '.' . $this->configId;
    $this->optionsFactory = $options_factory;
    $this->options = $options_factory->get($this->configName);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaults($process = FALSE) {
    if (!isset($this->defaults)) {
      $file = drupal_get_path('module', $this->moduleId) . '/config/install/' . $this->configName . '.yml';
      $options = Yaml::decode(file_get_contents($file));
      $this->defaults = isset($options['options']) ? $options['options'] : [];
    }
    if ($process) {
      if (!isset($this->defaultsProcessed)) {
        $this->defaultsProcessed = $this->processOptions($this->defaults);
      }
      return $this->defaultsProcessed;
    }
    return $this->defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions($process = FALSE) {
    $options = $this->options->get('options') ?: [];
    $options = NestedArray::mergeDeep($this->getDefaults(), $options);
    if ($process) {
      if (!isset($this->optionsProcessed)) {
        $this->optionsProcessed = $this->processOptions($options);
      }
      $options = $this->optionsProcessed;
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function saveOptions(array $options) {
    $config = $this->optionsFactory->getEditable($this->configName);
    $config->set('options', $this->optionsDefaultDiff($options))->save();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function processOptions(array $options) {
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function form($defaults = []) {
    $defaults = $this->optionsMerge($defaults);
    return $this->optionsForm($defaults);
  }

  /**
   * Generate an options optionsuration form.
   *
   * @param array $defaults
   *   The default form options.
   *
   * @return array
   *   The form definition array.
   */
  protected function optionsForm(array $defaults = []) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function hasIconSupport() {
    return \Drupal::moduleHandler()->moduleExists('micon');
  }

  /**
   * {@inheritdoc}
   */
  public function optionsMerge(array $options, $is_processed = FALSE) {
    return NestedArray::mergeDeep($this->getOptions($is_processed), $options);
  }

  /**
   * {@inheritdoc}
   */
  public function optionsDiff(array $options, $process = FALSE) {
    if ($process) {
      $options = $this->processOptions($options);
    }
    return $this->optionsDeepDiff($options, $this->getOptions());
  }

  /**
   * {@inheritdoc}
   */
  public function optionsDefaultDiff(array $options, $process = FALSE) {
    if ($process) {
      $options = $this->processOptions($options);
    }
    return $this->optionsDeepDiff($options, $this->getDefaults());
  }

  /**
   * Compare a options array to another and return that which differs.
   *
   * @param array $options1
   *   First options array.
   * @param array $options2
   *   Second options array.
   *
   * @return array
   *   The options containing only the results that differ.
   */
  protected function optionsDeepDiff(array $options1, array $options2) {
    $return = [];
    foreach ($options1 as $key => $value) {
      if (array_key_exists($key, $options2)) {
        if (is_array($value) && is_array($options2[$key])) {
          $aRecursiveDiff = $this->optionsDeepDiff($value, $options2[$key]);
          if (count($aRecursiveDiff)) {
            $return[$key] = $aRecursiveDiff;
          }
        }
        else {
          if ($value != $options2[$key]) {
            $return[$key] = $value;
          }
        }
      }
      else {
        $return[$key] = $value;
      }
    }
    return $return;
  }

}
