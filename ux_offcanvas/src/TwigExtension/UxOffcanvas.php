<?php

namespace Drupal\ux_offcanvas\TwigExtension;

/**
 * A class providing UxOffcanvas Twig extensions.
 *
 * This provides a Twig extension that registers the {{ offcanvas() }} extension
 * to Twig.
 */
class UxOffcanvas extends \Twig_Extension {

  /**
   * Gets a unique identifier for this Twig extension.
   *
   * @return string
   *   A unique identifier for this Twig extension.
   */
  public function getName() {
    return 'twig.offcanvas';
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return array(
      new \Twig_SimpleFunction('offcanvas', array($this, 'renderTrigger')),
    );
  }

  /**
   * Render the icon.
   *
   * @param string $content
   *   The icon_id of the icon to render.
   *
   * @return mixed[]
   *   A render array.
   */
  public static function renderTrigger($id, $trigger_text, $content, $position = NULL) {
    $uxOffcanvasManager = \Drupal::service('ux_offcanvas.manager');
    $content['#markup'] = 'Hello World';
    $offcanvas = $uxOffcanvasManager->addOffcanvas($id, $trigger_text, $content);
    if (!empty($position)) {
      $offcanvas->setPosition($position);
    }
    $build = $offcanvas->toRenderableTrigger();
    return $build;
  }

}
