<?php

namespace Drupal\ux_offcanvas;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
// use Drupal\Core\Cache\CacheableMetadata;

/**
 * Class UxOffcanvas.
 *
 * @package Drupal\ux_offcanvas
 */
class UxOffcanvas implements UxOffcanvasInterface, RefinableCacheableDependencyInterface {
  use RefinableCacheableDependencyTrait;

  /**
   * The unique id of this UxOffcanvas object.
   *
   * @var string
   */
  protected $id = '';

  /**
   * The text of the trigger link.
   *
   * @var string
   */
  protected $triggerText = '';

  /**
   * A render array to place within the offcanvas element.
   *
   * @var string
   */
  protected $content = '';

  /**
   * The offcanvas options.
   *
   * @var array
   *   An associative array of optional options, with the following elements:
   *   - 'position' (defaults to 'left'): The position of the offcanvas element.
   *   - 'size' (defaults to '320px'): The size of the offcanvas element.
   */
  protected $defaults = [
    'position' => 'left',
    'size' => 320,
  ];

  /**
   * Constructs a \Drupal\ux_offcanvas\UxOffcanvas object.
   *
   * @param string $id
   *   The unique id of this UxOffcanvas object.
   * @param string $trigger_text
   *   The text of the trigger link.
   * @param mixed[] $content
   *   A render array to place within the offcanvas element.
   */
  public function __construct($id, $trigger_text = NULL, $content = NULL) {
    $this->id = $id;
    $this->options = $this->defaults;
    $this->options['id'] = $id;
    $this->setTriggerText($trigger_text);
    $this->setContent($content);
  }

  /**
   * Get the offcanvas id.
   *
   * @return string
   *   The offcanvas id.
   */
  public function id() {
    return $this->id;
  }

  /**
   * Set the offcanvas content.
   *
   * @param mixed[] $content
   *   A render array to place within the offcanvas element.
   *
   * @return $this
   */
  public function setContent($content) {
    $this->content = $content;
    // kint($content);
    if (is_array($content) && !empty($content['#cache'])) {
      // dsm(CacheableMetadata::createFromRenderArray($content));
      // CacheableMetadata::createFromRenderArray($build)
      //   ->merge(CacheableMetadata::createFromRenderArray($content))
      //   ->applyTo($build);
    }
    return $this;
  }

  /**
   * Get the offcanvas content.
   *
   * @return mixed[]
   *   A render array.
   */
  public function getContent() {
    return $this->content;
  }

  /**
   * Set the offcanvas trigger link.
   *
   * @param string $trigger_text
   *   The text of the trigger link.
   *
   * @return $this
   */
  public function setTriggerText($trigger_text) {
    $this->triggerText = $trigger_text;
    return $this;
  }

  /**
   * Get the offcanvas trigger link.
   *
   * @return string
   *   The trigger text.
   */
  public function getTriggerText() {
    return $this->triggerText;
  }

  /**
   * Get the offcanvas options.
   *
   * @return array
   *   An array of options.
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * Get the offcanvas options which has been changed from the defaults.
   *
   * @return array
   *   An array of options.
   */
  public function getChangedOptions() {
    return array_diff($this->options, $this->defaults);
  }

  /**
   * Set the position on-screen of the offcanvas content.
   *
   * @param string $position
   *   Can be either 'top','left','bottom','right'.
   *
   * @return $this
   */
  public function setPosition($position) {
    $allowed = [
      'top',
      'left',
      'bottom',
      'right',
    ];
    if (is_string($position) && in_array($position, $allowed)) {
      $this->options['position'] = $position;
    }
    return $this;
  }

  /**
   * Get the position on-screen of the offcanvas content.
   *
   * @return string
   *   The position
   */
  public function getPosition() {
    return $this->options['position'];
  }

  /**
   * Set the size of the offcanvas element.
   *
   * @param int $size
   *   The size withput 'px'. Example: 400.
   *
   * @return $this
   */
  public function setSize($size) {
    $this->options['size'] = $size;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function toRenderableTrigger() {
    $build = [
      '#theme' => 'ux_offcanvas_trigger',
      '#offcanvas' => $this,
      '#attributes' => ['href' => ''],
      '#cache' => [
        'tags' => $this->getCacheTags(),
        'contexts' => $this->getCacheContexts(),
        'max-age' => $this->getCacheMaxAge(),
      ],
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function toRenderableContent() {
    $build = [
      '#theme' => 'ux_offcanvas_item',
      '#offcanvas' => $this,
      '#cache' => [
        'tags' => $this->getCacheTags(),
        'contexts' => $this->getCacheContexts(),
        'max-age' => $this->getCacheMaxAge(),
      ],
    ];
    $build['#attached']['drupalSettings']['ux']['offcanvas']['items'][$this->id()] = $this->getChangedOptions();
    return $build;
  }

}
