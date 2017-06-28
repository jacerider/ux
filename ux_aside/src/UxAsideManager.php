<?php

namespace Drupal\ux_aside;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Component\Utility\Html;

/**
 * Class UxAsideManager.
 *
 * @package Drupal\ux_aside
 */
class UxAsideManager implements UxAsideManagerInterface, RefinableCacheableDependencyInterface {
  use RefinableCacheableDependencyTrait;

  /**
   * The aside options service.
   *
   * @var \Drupal\ux_aside\UxAsideOptionsInterface
   */
  protected $uxAsideOptions;

  /**
   * An array of asides.
   *
   * @var array
   */
  protected static $asides = [];

  /**
   * Constructs a new UxAsideManager object.
   */
  public function __construct(UxAsideOptionsInterface $ux_aside_options) {
    $this->uxAsideOptions = $ux_aside_options;
    $this->addCacheTags(['config:ux_aside.settings']);
  }

  /**
   * Create a new aside.
   *
   * @param string $id
   *   The unique id of this object.
   *
   * @return \Drupal\ux_aside\UxAsideInterface
   *   A UxAsideInterface object.
   */
  public function create($id) {
    $id = Html::getId($id);
    if (!isset(self::$asides[$id])) {
      self::$asides[$id] = new UxAside($id);
    }
    return self::$asides[$id];
  }

  /**
   * Get UxAsideOptions service.
   *
   * @return \Drupal\ux_aside\UxAsideOptionsInterface
   *   The UxAsideOptions service.
   */
  public function getOptionsService() {
    return $this->uxAsideOptions;
  }

  /**
   * Get specific aside.
   *
   * @param string $id
   *   The unique id of this object.
   *
   * @return \Drupal\ux_aside\UxAsideInterface|null
   *   A UxAsideInterface object.
   */
  public function get($id) {
    return isset(self::$asides[$id]) ? self::$asides[$id] : NULL;
  }

  /**
   * Get asides.
   */
  public function getAll() {
    return self::$asides;
  }

  /**
   * Get asides.
   */
  public function getOptions($type = NULL, $only_changed = FALSE) {
    $options = $this->uxAsideOptions->getOptions();
    if ($only_changed) {
      $options = $this->uxAsideOptions->optionsDefaultDiff($options);
    }
    if ($type) {
      $options = isset($options[$type]) ? $options[$type] : [];
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    foreach ($this->getAll() as $aside) {
      $this->addCacheTags($aside->getCacheTags());
    }
    return $this->cacheTags;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    foreach ($this->getAll() as $aside) {
      $this->addCacheContexts($aside->getCacheContexts());
    }
    return $this->cacheContexts;
  }

}
