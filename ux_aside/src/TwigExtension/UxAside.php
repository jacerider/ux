<?php

namespace Drupal\ux_aside\TwigExtension;

use Drupal\ux_aside\UxAsideManagerInterface;

/**
 * A class providing UxAside Twig extensions.
 *
 * This provides a Twig extension that registers the
 * {{ aside(id, string, string) }} extension to Twig.
 */
class UxAside extends \Twig_Extension {

  /**
   * Constructs \Drupal\Core\Template\TwigExtension.
   *
   * @param \Drupal\ux_aside\UxAsideManagerInterface $ux_aside_manager
   *   The aside manager.
   */
  public function __construct(UxAsideManagerInterface $ux_aside_manager) {
    $this->uxAsideManager = $ux_aside_manager;
  }

  /**
   * Gets a unique identifier for this Twig extension.
   *
   * @return string
   *   A unique identifier for this Twig extension.
   */
  public function getName() {
    return 'twig.aside';
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('aside', [$this, 'renderTrigger']),
    ];
  }

  /**
   * Render the icon.
   *
   * @param string $id
   *   The unique id of the asside element.
   * @param string $trigger_text
   *   The trigger text that will be returned as a link.
   * @param array|string $content
   *   The content prepared as a render array or string.
   *
   * @return mixed[]
   *   A render array.
   */
  public static function renderTrigger($id, $trigger_text, $content) {
    $uxAsideManager = \Drupal::service('ux_aside.manager');

    if (is_string($content)) {
      $content = [
        '#markup' => $content,
      ];
    }

    return $uxAsideManager->create($id)->setTriggerText($trigger_text)->setContent($content)->toRenderArray();
  }

}
