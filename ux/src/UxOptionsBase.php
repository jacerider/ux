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
  protected static $defaults;

  /**
   * The options.
   *
   * @var Drupal\Core\Config\ImmutableConfig
   */
  protected $options;

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
  public function getDefaults() {
    if (!isset(self::$defaults)) {
      $file = drupal_get_path('module', $this->moduleId) . '/config/install/' . $this->configName . '.yml';
      $options = Yaml::decode(file_get_contents($file));
      self::$defaults = isset($options['options']) ? $options['options'] : [];
    }
    return self::$defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $options = $this->options->get('options') ?: [];
    return NestedArray::mergeDeep($this->getDefaults(), $options);
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
  public function prepareOptions(array $options) {
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
  public function optionsMerge(array $options) {
    return NestedArray::mergeDeep($this->getOptions(), $options);
  }

  /**
   * {@inheritdoc}
   */
  public function optionsDiff(array $options) {
    return $this->optionsDeepDiff($options, $this->getOptions());
  }

  /**
   * {@inheritdoc}
   */
  public function optionsDefaultDiff(array $options) {
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
        if (is_array($value)) {
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
