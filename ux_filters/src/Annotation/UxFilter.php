<?php

namespace Drupal\ux_filters\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a UX | Filter item annotation object.
 *
 * @see \Drupal\ux_filters\Plugin\UxFilterManager
 * @see plugin_api
 *
 * @Annotation
 */
class UxFilter extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The name of the widget class.
   *
   * This is not provided manually, it will be added by the discovery mechanism.
   *
   * @var string
   */
  public $class;

  /**
   * An array of field types the formatter supports.
   *
   * @var array
   */
  public $fieldTypes = array();

}
