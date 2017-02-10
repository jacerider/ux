<?php

namespace Drupal\ux_filters\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the UX | Filter plugin manager.
 */
class UxFilterManager extends DefaultPluginManager {

  /**
   * Constructor for UxFilterManager objects.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/UxFilter', $namespaces, $module_handler, 'Drupal\ux_filters\Plugin\UxFilterInterface', 'Drupal\ux_filters\Annotation\UxFilter');

    $this->alterInfo('ux_filters_ux_filter_info');
    $this->setCacheBackend($cache_backend, 'ux_filters_ux_filter_plugins');
  }

  /**
   * Returns an array of filter options for a field type.
   *
   * @param string|null $field_type
   *   (optional) The name of a field type, or NULL to retrieve all filters.
   *
   * @return array
   *   If no field type is provided, returns a nested array of all filters,
   *   keyed by field type.
   */
  public function getOptions($field_type = NULL) {
    $filter_options = array();
    $filter_types = $this->getDefinitions();
    foreach ($filter_types as $name => $filter_type) {
      foreach ($filter_type['fieldTypes'] as $filter_field_type) {
        $filter_options[$filter_field_type][$name] = $filter_type['label'];
      }
    }
    if ($field_type) {
      return !empty($filter_options[$field_type]) ? $filter_options[$field_type] : array();
    }
    return $filter_options;
  }

}
