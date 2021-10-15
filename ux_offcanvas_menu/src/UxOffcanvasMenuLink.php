<?php

namespace Drupal\ux_offcanvas_menu;

use Drupal\Core\Menu\MenuLinkBase;
use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Url;

/**
 * A menu link plugin for wrapping another menu link, in sensitive situations.
 *
 * @see \Drupal\Core\Menu\DefaultMenuLinkTreeManipulators::checkAccess()
 */
class UxOffcanvasMenuLink extends MenuLinkBase {

  /**
   * The link title.
   *
   * @var string
   */
  protected $title;

  /**
   * The link description.
   *
   * @var string
   */
  protected $description;

  /**
   * The link options.
   *
   * @var array
   */
  protected $options;

  /**
   * Constructs a new InaccessibleMenuLink.
   *
   * @param \Drupal\Core\Menu\MenuLinkInterface $wrapped_link
   *   The menu link to wrap.
   */
  public function __construct($title, $description = '', $options = []) {
    $this->title = $title;
    $this->description = $description;
    $this->options = $options;
    parent::__construct([], NULL, [
      'menu_name' => 'main',
      'options' => [],
      'enabled' => TRUE,
      'url' => NULL,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->t($this->title);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t($this->description);
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrlObject($title_attribute = TRUE) {
    $options = $this->getOptions();
    if ($title_attribute && $description = $this->getDescription()) {
      $options['attributes']['title'] = $description;
    }
    return Url::fromUserInput('#', $options);
  }

  /**
   * {@inheritdoc}
   */
  public function updateLink(array $new_definition_values, $persist) {
    throw new PluginException('Inaccessible menu link plugins do not support updating');
  }

}
