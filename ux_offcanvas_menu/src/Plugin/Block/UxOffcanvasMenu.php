<?php

namespace Drupal\ux_offcanvas_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Menu\MenuLinkTree;
use Drupal\Core\Menu\MenuActiveTrail;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ux_offcanvas\UxOffcanvasManagerInterface;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'UxOffcanvasMenu' block.
 *
 * @Block(
 *  id = "ux_offcanvas_menu",
 *  admin_label = @Translation("Offcanvas Menu"),
 * )
 */
class UxOffcanvasMenu extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Menu\MenuLinkTree definition.
   *
   * @var \Drupal\Core\Menu\MenuLinkTree
   */
  protected $menuLinkTree;
  /**
   * Drupal\Core\Menu\MenuActiveTrail definition.
   *
   * @var \Drupal\Core\Menu\MenuActiveTrail
   */
  protected $menuActiveTrail;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The offcanvas manager.
   *
   * @var \Drupal\ux_offcanvas\UxOffcanvasManagerInterface
   */
  protected $uxOffcanvasManager;

  /**
   * The offcanvas object.
   *
   * @var \Drupal\ux_offcanvas\UxOffcanvas
   */
  protected $uxOffcanvas;

  /**
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MenuLinkTree $menu_link_tree,
    MenuActiveTrail $menu_active_trail,
    EntityTypeManagerInterface $entity_type_manager,
    UxOffcanvasManagerInterface $ux_offcanvas_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->menuLinkTree = $menu_link_tree;
    $this->menuActiveTrail = $menu_active_trail;
    $this->entityTypeManager = $entity_type_manager;
    $this->uxOffcanvasManager = $ux_offcanvas_manager;
    $this->uxOffcanvas = $this->uxOffcanvasManager->addOffcanvas('offcanvas_menu')
      ->addCacheTags($this->getCacheTags())
      ->addCacheContexts($this->getCacheContexts());
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('menu.link_tree'),
      $container->get('menu.active_trail'),
      $container->get('entity_type.manager'),
      $container->get('ux_offcanvas.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'menu' => 'main',
      'secondary_menu' => '',
      'depth' => 3,
      'text' => $this->t('Menu'),
      'icon' => '',
      'icon_only' => FALSE,
      'position' => 'left',
      'header' => '',
      'footer' => '',
    ] + parent::defaultConfiguration();

  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    // Get the theme.
    $theme = $form_state->get('block_theme');
    $theme_blocks = $this->entityTypeManager->getStorage('block')->loadByProperties(['theme' => $theme]);
    $block_options = [];
    if (!empty($theme_blocks)) {
      foreach ($theme_blocks as $block) {
        $block_options[$block->id()] = $block->label();
      }
    }

    $form['menus'] = [
      '#type' => 'details',
      '#title' => $this->t('Menus'),
      '#open' => TRUE,
    ];

    $form['menus']['menu'] = [
      '#type' => 'select',
      '#title' => $this->t('Primary menu'),
      '#options' => $this->getMenuOptions(),
      '#default_value' => $this->configuration['menu'],
      '#required' => TRUE,
    ];

    $form['menus']['secondary_menu'] = [
      '#type' => 'select',
      '#title' => $this->t('Secondary menu'),
      '#description' => $this->t('An additional menu that can be appended to the mobile menu.'),
      '#options' => ['- None -'] + $this->getMenuOptions(),
      '#default_value' => $this->configuration['secondary_menu'],
    ];

    $form['menus']['depth'] = [
      '#type' => 'select',
      '#title' => $this->t('A maximum menu depth'),
      '#default_value' => $this->configuration['depth'],
      '#options' => array_combine(
        [1, 2, 3, 4, 5, 6, 7, 8, 9],
        [1, 2, 3, 4, 5, 6, 7, 8, 9]
      ),
      '#required' => TRUE,
    ];

    if (!empty($block_options)) {

      $form['blocks'] = [
        '#type' => 'details',
        '#title' => $this->t('Blocks'),
        '#open' => TRUE,
      ];
      $form['blocks']['header'] = array(
        '#type' => 'select',
        '#title' => t('Header'),
        '#description' => t('A block placed in the header of the mobile menu element.'),
        '#options' => ['' => t('- None -')] + $block_options,
        '#default_value' => $this->configuration['header'],
      );
      $form['blocks']['footer'] = array(
        '#type' => 'select',
        '#title' => t('Footer'),
        '#description' => t('A block placed in the footer of the mobile menu element.'),
        '#options' => ['' => t('- None -')] + $block_options,
        '#default_value' => $this->configuration['footer'],
      );
    }

    $form['offcanvas'] = [
      '#type' => 'details',
      '#title' => $this->t('Offcanvas Settings'),
      '#open' => TRUE,
    ];

    $form['offcanvas']['text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Menu trigger link text'),
      '#description' => $this->t('The text to be placed within the link that will trigger the offcanvas element.'),
      '#default_value' => $this->configuration['text'],
      '#required' => TRUE,
    ];

    if (\Drupal::moduleHandler()->moduleExists('micon') && function_exists('micon')) {
      $form['offcanvas']['icon'] = [
        '#type' => 'micon',
        '#title' => $this->t('Icon'),
        '#default_value' => $this->configuration['icon'],

      ];
      $form['offcanvas']['icon_only'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Icon Only'),
        '#default_value' => $this->configuration['icon_only'],
      ];
    }

    $form['offcanvas']['position'] = [
      '#type' => 'select',
      '#title' => $this->t('Offcanvas element position'),
      '#default_value' => $this->configuration['position'],
      '#options' => [
        'left' => $this->t('Left'),
        'right' => $this->t('Right'),
        'top' => $this->t('Top'),
        'bottom' => $this->t('Bottom'),
      ],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    foreach (['menus', 'blocks', 'offcanvas'] as $key) {
      foreach ($values[$key] as $id => $value) {
        $this->configuration[$id] = $value;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $text = $this->configuration['text'];
    $icon = $this->configuration['icon'];
    $position = $this->configuration['position'];
    $header = $this->configuration['header'];
    $footer = $this->configuration['footer'];

    $content = [
      '#theme' => 'ux_offcanvas_menu_wrapper',
      '#menu' => $this->buildMenu(),
    ];

    if ($header || $footer) {
      $block_storage = $this->entityTypeManager->getStorage('block');
      if ($header && $block = $block_storage->load($header)) {
        $content['#header']['block'] = $this->entityTypeManager->getViewBuilder('block')->view($block);
      }
      if ($footer && $block = $block_storage->load($footer)) {
        $content['#footer']['block'] = $this->entityTypeManager->getViewBuilder('block')->view($block);
      }
    }

    if (!empty($icon)) {
      $text = micon($text)->setIcon($icon)->setIconOnly($this->configuration['icon_only']);
    }

    return $this->uxOffcanvas->setTriggerText($text)
      ->setContent($content)
      ->setPosition($position)
      ->toRenderableTrigger();
  }

  /**
   * Build the menu used within the offcanvas element.
   */
  protected function buildMenu() {
    $menu_name = $this->configuration['menu'];
    $tree = $this->buildMenuTree($menu_name);

    $active_trail = $this->menuActiveTrail->getActiveTrailIds($menu_name);

    // If secondary menu is added.
    if ($secondary_menu_name = $this->configuration['secondary_menu']) {
      $tree += $this->buildMenuTree($secondary_menu_name);
      if (empty(array_filter($active_trail))) {
        $active_trail = $this->menuActiveTrail->getActiveTrailIds($secondary_menu_name);
      }
    }

    $menu = $this->menuLinkTree->build($tree);
    $menu['#theme'] = 'ux_offcanvas_menu';
    $menu['#attributes']['data-depth'] = count(array_filter($active_trail)) - 1;

    return $menu;
  }

  /**
   * Build link tree.
   */
  protected function buildMenuTree($menu_name) {
    $depth = $this->configuration['depth'];
    $parameters = $this->menuLinkTree->getCurrentRouteMenuTreeParameters($menu_name);
    $parameters->setMaxDepth($depth);
    $parameters->expandedParents = [];
    $tree = $this->menuLinkTree->load($menu_name, $parameters);
    $manipulators = array(
      // Show links to nodes that are accessible for the current user.
      array('callable' => 'menu.default_tree_manipulators:checkNodeAccess'),
      // Only show links that are accessible for the current user.
      array('callable' => 'menu.default_tree_manipulators:checkAccess'),
      // Use the default sorting of menu links.
      array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
    );
    return $this->menuLinkTree->transform($tree, $manipulators);
  }

  /**
   * Gets a list of menu names for use as options.
   *
   * @param array $menu_names
   *   (optional) Array of menu names to limit the options, or NULL to load all.
   *
   * @return array
   *   Keys are menu names (ids) values are the menu labels.
   */
  protected function getMenuOptions(array $menu_names = NULL) {
    $menus = $this->entityTypeManager->getStorage('menu')->loadMultiple($menu_names);
    $options = array();
    /** @var \Drupal\system\MenuInterface[] $menus */
    foreach ($menus as $menu) {
      $options[$menu->id()] = $menu->label();
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // Even when the menu block renders to the empty string for a user, we want
    // the cache tag for this menu to be set: whenever the menu is changed, this
    // menu block must also be re-rendered for that user, because maybe a menu
    // link that is accessible for that user has been added.
    $cache_tags = parent::getCacheTags();
    $menu_name = $this->configuration['menu'];
    $cache_tags[] = 'config:system.menu.' . $menu_name;
    if ($secondary_menu_name = $this->configuration['secondary_menu']) {
      $cache_tags[] = 'config:system.menu.' . $secondary_menu_name;
    }
    return $cache_tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $cache_contexts = parent::getCacheContexts();
    $menu_name = $this->configuration['menu'];
    $cache_contexts[] = 'route.menu_active_trails:' . $menu_name;
    if ($secondary_menu_name = $this->configuration['secondary_menu']) {
      $cache_contexts[] = 'route.menu_active_trails:' . $secondary_menu_name;
    }
    return Cache::mergeContexts($cache_contexts);
  }

}
