<?php

namespace Drupal\ux_offcanvas;

use Drupal\Component\Utility\Html;
use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Class UxOffcanvas.
 *
 * @package Drupal\ux_offcanvas
 */
class UxOffcanvasManager implements UxOffcanvasManagerInterface, RefinableCacheableDependencyInterface, TrustedCallbackInterface {
  use RefinableCacheableDependencyTrait;

  protected static $offcanvas = [];

  /**
   * Add an offcanvas object.
   *
   * @param string $id
   *   The unique id of the UxOffcanvas object.
   * @param string $trigger_text
   *   The text of the trigger link.
   * @param mixed[] $content
   *   A render array.
   *
   * @return \Drupal\ux_offcanvas\UxOffcanvas
   *   An offcanvas object.
   */
  public function addOffcanvas($id, $trigger_text = NULL, $content = NULL) {
    $id = Html::getId($id);
    self::$offcanvas[$id] = new UxOffcanvas($id, $trigger_text, $content);
    return self::$offcanvas[$id];
  }

  /**
   * Return an offcanvas object.
   *
   * @param string $id
   *   The unique id of the UxOffcanvas object.
   *
   * @return \Drupal\ux_offcanvas\UxOffcanvas
   *   An offcanvas object.
   */
  public function getOffcanvas($id) {
    return self::$offcanvas[$id];
  }

  /**
   * A #lazy_builder callback; builds offcanvas wrapper.
   */
  public static function lazyBuilder() {
    $build = [
      '#theme' => 'ux_offcanvas',
      '#attached' => [
        'library' => ['ux_offcanvas/ux_offcanvas'],
      ],
      '#cache' => [
        'keys' => ['ux-offcanvas'],
        'tags' => [],
        'contexts' => ['user.permissions'],
        'max-age' => Cache::PERMANENT,
      ],
    ];
    foreach (self::$offcanvas as $id => $offcanvas) {
      $build['#children'][$id] = [
        '#lazy_builder' => [static::class . '::lazyItemBuilder', [$id]],
        '#cache' => [
          'keys' => ['ux-offcanvas', $id],
          'tags' => $offcanvas->getCacheTags(),
          'contexts' => $offcanvas->getCacheContexts(),
          'max-age' => $offcanvas->getCacheMaxAge(),
        ],
      ];
      // Merge all #cache information into parent wrapper.
      $build['#cache']['keys'][] = $id;
      $build['#cache']['tags'] = Cache::mergeTags($build['#cache']['tags'], $offcanvas->getCacheTags());
      $build['#cache']['contexts'] = Cache::mergeContexts($build['#cache']['contexts'], $offcanvas->getCacheContexts());
      $build['#cache']['max-age'] = Cache::mergeMaxAges($build['#cache']['max-age'], $offcanvas->getCacheMaxAge());
    }
    return $build;
  }

  /**
   * A #lazy_builder callback: builds individual offcanvas items.
   */
  public static function lazyItemBuilder($id) {
    return \Drupal::service('ux_offcanvas.manager')->getOffcanvas($id)->toRenderableContent();
  }

  /**
   * To lazy loadable render array.
   */
  public function toLazyRenderable() {
    // We always attempt to render as offcanvas elements can be added late
    // in the render process.
    return [
      '#lazy_builder' => [static::class . '::lazyBuilder', []],
    ];
  }

  /**
   * {@inheritDoc}
   */
  public static function trustedCallbacks() {
    return [
      'lazyBuilder',
      'lazyItemBuilder',
    ];
  }

}
