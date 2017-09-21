<?php

namespace Drupal\ux_form;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Delivery type plugin manager.
 */
class UxFormPluginManager extends DefaultPluginManager implements UxFormPluginManagerInterface {

  /**
   * An array of widget options for each field type.
   *
   * @var array
   */
  protected $elementPlugins;

  /**
   * Constructs a new UxForm object.
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
    parent::__construct('Plugin/UxForm', $namespaces, $module_handler, 'Drupal\ux_form\Plugin\UxForm\UxFormPluginInterface', 'Drupal\ux_form\Annotation\UxForm');
    $this->alterInfo('ux_form_info');
    $this->setCacheBackend($cache_backend, 'ux_form_plugins');
  }

  /**
   * Returns an array of element plugins for a field type.
   *
   * @param string|null $field_type
   *   (optional) The name of a field type, or NULL to retrieve all widget
   *   options. Defaults to NULL.
   *
   * @return array
   *   If no field type is provided, returns a nested array of all widget types,
   *   keyed by field type human name.
   */
  public function getPluginsByType($field_type = NULL) {
    if (!isset($this->elementPlugins)) {
      $options = [];
      $widget_types = $this->getDefinitions();
      uasort($widget_types, ['Drupal\Component\Utility\SortArray', 'sortByWeightElement']);
      foreach ($widget_types as $name => $widget_type) {
        foreach ($widget_type['element_types'] as $widget_field_type) {
          $options[$widget_field_type][$name] = $widget_type['label'];
        }
      }
      $this->elementPlugins = $options;
    }
    if (isset($field_type)) {
      return !empty($this->elementPlugins[$field_type]) ? $this->elementPlugins[$field_type] : [];
    }

    return $this->elementPlugins;
  }

}
