<?php

namespace Drupal\ux_form\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a ux form annotation object.
 *
 * Plugin namespace: Plugin\UxForm.
 *
 * @see plugin_api
 *
 * @Annotation
 */
class UxForm extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the ux form plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

  /**
   * An array of element types the plugin supports.
   *
   * @var array
   */
  public $element_types = [];

  /**
   * An integer to determine the weight of this widget relative to other widgets
   * when processing a given element.
   *
   * @var int optional
   */
  public $weight = NULL;

}
