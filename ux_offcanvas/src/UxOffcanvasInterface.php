<?php

namespace Drupal\ux_offcanvas;

/**
 * Interface UxOffcanvasInterface.
 *
 * @package Drupal\ux_offcanvas
 */
interface UxOffcanvasInterface {

  /**
   * Returns a render array representation of the object.
   *
   * @return mixed[]
   *   A render array.
   */
  public function toRenderableTrigger();

}
