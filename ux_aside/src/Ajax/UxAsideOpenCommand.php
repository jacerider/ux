<?php

namespace Drupal\ux_aside\Ajax;

use Drupal\Core\Ajax\CommandInterface;
use Drupal\Core\Ajax\CommandWithAttachedAssetsInterface;
use Drupal\Core\Ajax\CommandWithAttachedAssetsTrait;
use Drupal\Component\Render\PlainTextOutput;

/**
 * Defines an AJAX command to open certain content in a aside.
 *
 * @ingroup ajax
 */
class UxAsideOpenCommand implements CommandInterface, CommandWithAttachedAssetsInterface {

  use CommandWithAttachedAssetsTrait;

  /**
   * The title of the aside.
   *
   * @var string
   */
  protected $title;

  /**
   * The content for the aside.
   *
   * Either a render array or an HTML string.
   *
   * @var string|array
   */
  protected $content;

  /**
   * Stores aside-specific options passed directly to asides. Any
   * jQuery UI option can be used. See http://api.jqueryui.com/aside.
   *
   * @var array
   */
  protected $options;

  /**
   * Custom settings that will be passed to the Drupal behaviors on the content
   * of the aside.
   *
   * @var array
   */
  protected $settings;

  /**
   * Constructs an OpenDialogCommand object.
   *
   * @param string $title
   *   The title of the aside.
   * @param string|array $content
   *   The content that will be placed in the aside, either a render array
   *   or an HTML string.
   * @param array $options
   *   (optional) Options to be passed to the aside implementation. Any
   *   jQuery UI option can be used. See http://api.jqueryui.com/aside.
   * @param array|null $settings
   *   (optional) Custom settings that will be passed to the Drupal behaviors
   *   on the content of the aside. If left empty, the settings will be
   *   populated automatically from the current request.
   */
  public function __construct($content, array $options = [], $settings = NULL) {
    // $title = PlainTextOutput::renderFromHtml($title);
    $this->content = $content;
    $this->options = $options;
    $this->settings = $settings;
  }

  /**
   * Returns the aside options.
   *
   * @return array
   */
  public function getOptions() {
    return $this->options + [
      'autoOpen' => TRUE,
      'removeOnClose' => TRUE,
    ];
  }

  /**
   * Sets the aside options array.
   *
   * @param array $options
   *   Options to be passed to the aside implementation. Any jQuery UI option
   *   can be used. See http://api.jqueryui.com/aside.
   */
  public function setOptions($options) {
    $this->options = $options;
  }

  /**
   * Sets a single aside option value.
   *
   * @param string $key
   *   Key of the aside option. Any jQuery UI option can be used.
   *   See http://api.jqueryui.com/aside.
   * @param mixed $value
   *   Option to be passed to the aside implementation.
   */
  public function setOption($key, $value) {
    $this->options[$key] = $value;
  }

  /**
   * Sets the aside title (an alias of setOptions).
   *
   * @param string $title
   *   The new title of the aside.
   */
  public function setTitle($title) {
    $this->setOptions('title', $title);
  }

  /**
   * Implements \Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render() {

    // Prepare aside.
    $uxAsideManager = \Drupal::service('ux_aside.manager');
    $aside = $uxAsideManager->create(time())->setOptions([
      'content' => $this->getOptions(),
    ])->setContent($this->content);
    $this->content = [
      '#type' => 'ux_aside',
      '#aside' => $aside,
    ];

    return [
      'command' => 'uxAsideOpen',
      'settings' => $this->settings,
      'data' => $this->getRenderedContent(),
    ];
  }

}
