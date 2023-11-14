<?php

namespace Drupal\ux_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ux\UxOptionsInterface;
use Drupal\Core\Menu\MenuActiveTrailInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ux_aside\UxAsideManagerInterface;


/**
 * Provides a 'UxMenuBlock' block.
 *
 * @Block(
 *   id = "ux_menu_tree_aside",
 *   admin_label = @Translation("UX | Menu Aside Tree"),
 *   category = @Translation("User Experience"),
 * )
 */
class UxMenuTreeAsideBlock extends UxMenuTreeBlock {

  /**
   * The aside manager.
   *
   * @var \Drupal\ux_aside\UxAsideManagerInterface
   */
  protected $uxAsideManager;

  /**
   * The aside options service.
   *
   * @var \Drupal\ux_aside\UxAsideOptionsInterface
   */
  protected $uxAsideOptions;

  /**
   * The aside.
   *
   * @var \Drupal\ux_aside\UxAsideInterface
   */
  protected $uxAside;

  /**
   * Constructs a new UxMenuBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\ux\UxOptionsInterface $ux_menu_options
   *   The UX options service.
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $menu_tree
   *   The menu tree service.
   * @param \Drupal\Core\Menu\MenuActiveTrailInterface $menu_active_trail
   *   The active menu trail service.
   * @param \Drupal\ux_aside\UxAsideManagerInterface $ux_aside_manager
   *   The aside manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, UxOptionsInterface $ux_menu_options, MenuLinkTreeInterface $menu_tree, MenuActiveTrailInterface $menu_active_trail, UxAsideManagerInterface $ux_aside_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $ux_menu_options, $menu_tree, $menu_active_trail);
    $this->uxAsideManager = $ux_aside_manager;
    $this->uxAsideOptions = $ux_aside_manager->getOptionsService();
    $this->uxAside = $this->buildAside();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('ux_menu.options'),
      $container->get('menu.link_tree'),
      $container->get('menu.active_trail'),
      $container->get('ux_aside.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'aside' => [
        'trigger' => [
          'text' => 'Menu',
          'icon' => 'fa-bars',
        ],
        'content' => [
          'restoreDefaultContent' => TRUE,
          'contentTransition' => FALSE,
        ],
      ],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $form['aside'] = $this->uxAsideOptions->form($this->configuration['aside']) + [
      '#weight' => 10,
    ];
    $form['aside']['#open'] = !empty($this->uxAsideOptions->optionsDiff($this->configuration['aside']));

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['aside'] = $this->uxAsideOptions->optionsDiff($form_state->getValue('aside'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    if ($this->uxAside) {
      $build = $this->uxAside
        ->setOptions($this->configuration['aside'])
        ->setContent($build)
        ->addContentClass('uxMenu-in-uxAside')
        ->toRenderArray();
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildAside() {
    $id = md5(json_encode($this->configuration['aside']));
    return $this->uxAsideManager->create($id);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $tags = parent::getCacheTags();
    if (isset($this->uxAside)) {
      $tags = Cache::mergeTags($tags, $this->uxAside->getCacheTags());
    }
    return $tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    if (isset($this->uxAside)) {
      $contexts = Cache::mergeContexts($contexts, $this->uxAside->getCacheContexts());
    }
    return $contexts;
  }

}
