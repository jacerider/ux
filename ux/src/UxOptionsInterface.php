<?php

namespace Drupal\ux;

/**
 * Interface UxOptionsInterface.
 *
 * @package Drupal\ux
 */
interface UxOptionsInterface {

  /**
   * The module id implementing these options.
   *
   * @return string
   *   The module id implementing these options.
   */
  public function getModuleId();

  /**
   * Get default options as defined in the base settings yml file.
   *
   * @param bool $process
   *   Run options through preprocess. This should be used before using options
   *   for output.
   *
   * @return array
   *   The options array.
   */
  public function getDefaults($process = FALSE);

  /**
   * Get options as defined by site configuration.
   *
   * These defaults are set via the module configuration form and merged with
   * the module defined defaults.
   *
   * @param bool $process
   *   Run options through preprocess. This should be used before using options
   *   for output.
   *
   * @return array
   *   The options array.
   */
  public function getOptions($process = FALSE);

  /**
   * Save options to the site base configuration.
   *
   * These defaults are set via the module configuration form and merged with
   * the module defined defaults.
   *
   * @return $this
   */
  public function saveOptions(array $options);

  /**
   * Preprocess options as needed.
   *
   * @param array $options
   *   Options that will be sent when redering an module.
   *
   * @return array
   *   The options array.
   */
  public function processOptions(array $options);

  /**
   * Generate an options configuration form.
   *
   * @param array|null $defaults
   *   The default form options.
   *
   * @return array
   *   The form definition array.
   */
  public function form($defaults = []);

  /**
   * Check if micon is installed.
   *
   * @return bool
   *   Returns true if icons are supported.
   */
  public function hasIconSupport();

  /**
   * Merge options with defaults.
   *
   * @param array $options
   *   The options to merge into the default options.
   */
  public function optionsMerge(array $options);

  /**
   * Multidimentional options diff.
   *
   * Will compare an options array against the global configured options and
   * return only those that are different.
   *
   * @param array $options
   *   The options to merge into the default options.
   * @param bool $process
   *   Run options through preprocess. This should be used before using options
   *   for output.
   *
   * @return array
   *   The options containing only the results that differ.
   */
  public function optionsDiff(array $options, $process = FALSE);

  /**
   * Multidimentional default options diff.
   *
   * Will compare an options array against the system default options
   * and return only those that are different.
   *
   * @param array $options
   *   The options to merge into the default options.
   * @param bool $process
   *   Run options through preprocess. This should be used before using options
   *   for output.
   *
   * @return array
   *   The options containing only the results that differ.
   */
  public function optionsDefaultDiff(array $options, $process = FALSE);

}
