<?php

namespace Drupal\ux_aside;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Template\Attribute;

/**
 * Class UxAside.
 *
 * @package Drupal\ux_aside
 */
class UxAside implements UxAsideInterface, RefinableCacheableDependencyInterface {
  use RefinableCacheableDependencyTrait;

  /**
   * The unique id of this aside.
   *
   * @var string
   */
  protected $id = '';

  /**
   * The full content formatted as a render array.
   *
   * @var array
   */
  protected $content = [];

  /**
   * The aside options.
   *
   * @var array
   */
  protected $options = [];

  /**
   * The content Attribute object.
   *
   * @var \Drupal\Core\Template\Attribute
   */
  protected $contentAttributes;

  /**
   * Constructs a \Drupal\ux_aside\UxAside object.
   *
   * @param string $id
   *   The unique id of this object.
   */
  public function __construct($id) {
    $this->id = $id;
    $this->options = self::uxAsideOptions()->getOptions();
    $this->contentAttributes = new Attribute();
    $this->addCacheTags(['config:ux_aside.settings']);
  }

  /**
   * Returns the aside id.
   *
   * @return string
   *   The aside id.
   */
  public function id() {
    return $this->id;
  }

  /**
   * Set the aside trigger text.
   *
   * @param string $text
   *   The text of the trigger.
   *
   * @return $this
   */
  public function setTriggerText($text) {
    $this->options['trigger']['text'] = $text;
    return $this;
  }

  /**
   * Set the aside trigger icon.
   *
   * @param string $icon
   *   The icon id.
   *
   * @return $this
   */
  public function setTriggerIcon($icon) {
    $this->options['trigger']['icon'] = $icon;
    return $this;
  }

  /**
   * Set the aside trigger icon.
   *
   * @param bool $icon_only
   *   True if icon only.
   *
   * @return $this
   */
  public function setTriggerIconOnly($icon_only = TRUE) {
    $this->options['trigger']['iconOnly'] = $icon_only;
    return $this;
  }

  /**
   * Get the aside trigger text.
   *
   * @return string
   *   The trigger text.
   */
  public function getTrigger() {
    return $this->options['trigger']['text'];
  }

  /**
   * Set the full content.
   *
   * @param array $content
   *   An array suitable for a render array.
   *
   * @return $this
   */
  public function setContent(array $content) {
    $this->content = $content;
    return $this;
  }

  /**
   * Returns the aside for a render array.
   *
   * @return array
   *   An associative array suitable for a render array.
   */
  public function getContent() {
    return $this->content;
  }

  /**
   * Returns the aside content attributes.
   *
   * @return \Drupal\Core\Template\Attribute
   *   An attributes object.
   */
  public function getContentAttributes() {
    return $this->contentAttributes;
  }

  /**
   * Adds classes or merges them on to array of existing CSS classes.
   *
   * @param string|array ...
   *   CSS classes to add to the class attribute array.
   *
   * @return $this
   */
  public function addContentClass($classes) {
    $this->contentAttributes->addClass($classes);
    return $this;
  }

  /**
   * Sets values for an attribute key.
   *
   * @param string $attribute
   *   Name of the attribute.
   * @param string|array $value
   *   Value(s) to set for the given attribute key.
   *
   * @return $this
   */
  public function setContentAttribute($attribute, $value) {
    $this->contentAttributes->setAttribute($attribute, $value);
    return $this;
  }

  /**
   * Set the aside options.
   *
   * @param array $options
   *   Options that will be sent when redering an aside.
   *
   * @return $this
   */
  public function setOptions(array $options) {
    $options = NestedArray::mergeDeep($this->options, $options);
    $this->options = self::uxAsideOptions()->optionsMerge($options);
    return $this;
  }

  /**
   * Get the aside options.
   *
   * @param string $type
   *   The type of options to return. Can be 'trigger' or 'content'.
   * @param bool $only_changed
   *   Return only values that are different than the default.
   *
   * @return array
   *   The options array.
   */
  public function getOptions($type = NULL, $only_changed = FALSE) {
    $options = $this->options;
    if ($only_changed) {
      $options = self::uxAsideOptions()->optionsDiff($options);
    }
    if ($type) {
      $options = isset($options[$type]) ? $options[$type] : [];
    }
    return $options;
  }

  /**
   * Returns the trigger for a render array.
   *
   * @return array
   *   An associative array suitable for a render array.
   */
  public function toRenderArray() {
    return [
      '#type' => 'ux_aside_trigger',
      '#aside' => $this,
    ];
  }

  /**
   * Prepare options for rendering.
   *
   * Should be called just before rendering.
   *
   * @see \Drupal\ux_aside\Element\UxAsideTrigger
   */
  public function prepareToRender() {
    if (!isset($this->prepared)) {
      $this->options = self::uxAsideOptions()->processOptions($this->options);
      $this->prepared = TRUE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function addCacheableDependency($dependency) {
    $cacheable_metadata = CacheableMetadata::createFromObject($dependency);
    $this->addCacheTags($cacheable_metadata->getCacheTags());
    $this->addCacheContexts($cacheable_metadata->getCacheContexts());
    $this->mergeCacheMaxAge($cacheable_metadata->getCacheMaxAge());
    return $this;
  }

  /**
   * Wraps the aside options service.
   *
   * @return \Drupal\ux_aside\UxAsideOptionsInterface
   *   The UX Aside options service.
   */
  protected static function uxAsideOptions() {
    return \Drupal::service('ux_aside.options');
  }

}
