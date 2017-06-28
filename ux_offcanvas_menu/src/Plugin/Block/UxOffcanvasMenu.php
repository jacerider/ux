<?php

namespace Drupal\ux_offcanvas_menu\Plugin\Block;

use Drupal\ux_aside\Plugin\Block\UxAsideBlockBase;
use Drupal\ux_aside\UxAsideManagerInterface;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuActiveTrailInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ux_offcanvas\UxOffcanvasManagerInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Menu\MenuLinkTreeElement;
use Drupal\ux_offcanvas_menu\UxOffcanvasMenuLink;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Provides a 'UxOffcanvasMenu' block.
 *
 * @Block(
 *  id = "ux_offcanvas_menu",
 *  admin_label = @Translation("Offcanvas Menu"),
 * )
 */
class UxOffcanvasMenu extends UxAsideBlockBase {

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
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\ux_aside\UxAsideManagerInterface $ux_aside_manager
   *   The aside manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, UxAsideManagerInterface $ux_aside_manager, EntityTypeManagerInterface $entity_type_manager, MenuLinkTreeInterface $menu_link_tree, MenuActiveTrailInterface $menu_active_trail) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $ux_aside_manager, $entity_type_manager);
    $this->menuLinkTree = $menu_link_tree;
    $this->menuActiveTrail = $menu_active_trail;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ux_aside.manager'),
      $container->get('entity_type.manager'),
      $container->get('menu.link_tree'),
      $container->get('menu.active_trail')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'menu' => 'main',
      'secondary_menu' => '',
      'secondary_menu_title' => '',
      'depth' => 3,
      'trail' => 'breadcrumb',
      'animation' => 'slide',
      'header' => '',
      'footer' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

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
      '#options' => $this->getMenuOptions(),
      '#empty_option' => $this->t('- None -'),
      '#default_value' => $this->configuration['secondary_menu'],
    ];

    $form['menus']['secondary_menu_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Secondary menu item title'),
      '#description' => $this->t('The secondary menu can be contained within a root link.'),
      '#default_value' => $this->configuration['secondary_menu_title'],
      '#states' => [
        'visible' => [
          ':input[name="settings[menus][secondary_menu]"]' => ['!value' => ''],
        ],
      ],
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

    $form['menus']['trail'] = [
      '#type' => 'select',
      '#title' => $this->t('Trail type'),
      '#default_value' => $this->configuration['trail'],
      '#options' => [
        'breadcrumb' => $this->t('Breadcrumb'),
        'back' => $this->t('Simple Back Link'),
      ],
      '#required' => TRUE,
    ];

    $form['menus']['animation'] = [
      '#type' => 'select',
      '#title' => $this->t('Menu animation'),
      '#default_value' => $this->configuration['animation'],
      '#options' => [
        'slide' => $this->t('Slide'),
        'fade' => $this->t('Fade'),
      ],
      '#required' => TRUE,
    ];


    // Get the theme.
    $theme = $form_state->get('block_theme');
    $options = $this->getBlockOptions($theme);
    if (!empty($options)) {
      $form['blocks'] = [
        '#type' => 'details',
        '#title' => $this->t('Blocks'),
        '#open' => TRUE,
      ];
      $form['blocks']['header'] = [
        '#type' => 'select',
        '#title' => t('Header'),
        '#description' => t('A block placed in the header of the mobile menu element.'),
        '#options' => ['' => t('- None -')] + $options,
        '#default_value' => $this->configuration['header'],
      ];
      $form['blocks']['footer'] = [
        '#type' => 'select',
        '#title' => t('Footer'),
        '#description' => t('A block placed in the footer of the mobile menu element.'),
        '#options' => ['' => t('- None -')] + $options,
        '#default_value' => $this->configuration['footer'],
      ];
    }
    else {
      $form['block']['#markup'] = $this->t('No blocks are available for selection.');
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    foreach (['menus', 'blocks'] as $key) {
      foreach ($values[$key] as $id => $value) {
        $this->configuration[$id] = $value;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $content = [
      '#theme' => 'ux_offcanvas_menu_wrapper',
      '#menu' => $this->buildMenu(),
    ];

    $build = $this->uxAside->setContent($content)->toRenderArray();
    // if ($block = $this->loadBlock()) {
    //   $content = $this->entityTypeManager->getViewBuilder('block')->view($block);
    //   $build = $this->uxAside->setContent($content)->toRenderArray();
    // }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function _build() {
    $text = $this->configuration['text'];
    $icon = $this->configuration['icon'];
    $position = $this->configuration['position'];
    $size = $this->configuration['size'];
    $header = $this->configuration['header'];
    $footer = $this->configuration['footer'];
    $cacheable_metadata = new CacheableMetadata();

    $content = [
      '#theme' => 'ux_offcanvas_menu_wrapper',
      '#menu' => $this->buildMenu(),
    ];
    $cacheable_metadata = $cacheable_metadata->merge(CacheableMetadata::createFromRenderArray($content['#menu']));

    if ($header || $footer) {
      $block_storage = $this->entityTypeManager->getStorage('block');
      if ($header && $block = $block_storage->load($header)) {
        $content['#header']['block'] = $this->entityTypeManager->getViewBuilder('block')->view($block);
        $cacheable_metadata = $cacheable_metadata->merge(CacheableMetadata::createFromRenderArray($content['#header']['block']));
      }
      if ($footer && $block = $block_storage->load($footer)) {
        $content['#footer']['block'] = $this->entityTypeManager->getViewBuilder('block')->view($block);
        $cacheable_metadata = $cacheable_metadata->merge(CacheableMetadata::createFromRenderArray($content['#footer']['block']));
      }
    }

    if (!empty($icon)) {
      $text = micon($text)->setIcon($icon)->setIconOnly($this->configuration['icon_only']);
    }

    $cacheable_metadata->applyTo($content);

    return $this->uxOffcanvas->setTriggerText($text)
      ->setContent($content)
      ->setPosition($position)
      ->setSize($size)
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
      $secondary_tree = $this->buildMenuTree($secondary_menu_name);
      if ($secondary_menu_title = $this->configuration['secondary_menu_title']) {
        $link = new UxOffcanvasMenuLink($secondary_menu_title);
        $secondary_tree = [
          'ux-offcanvas-menu' => new MenuLinkTreeElement($link, (bool) count($secondary_tree), 0, FALSE, $secondary_tree),
        ];
      }
      $tree += $secondary_tree;
      if (empty(array_filter($active_trail))) {
        $active_trail = $this->menuActiveTrail->getActiveTrailIds($secondary_menu_name);
      }
    }

    $menu = $this->menuLinkTree->build($tree);
    $menu['#theme'] = 'ux_offcanvas_menu';
    $menu['#attributes']['data-depth'] = count(array_filter($active_trail)) - 1;
    $settings = [
      'trail' => $this->configuration['trail'],
      'animation' => $this->configuration['animation'],
    ];
    $menu['#attached']['drupalSettings']['ux']['offcanvasMenu']['items'][$this->uxAside->id()] = $settings;

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
    $manipulators = [
      // Show links to nodes that are accessible for the current user.
      ['callable' => 'menu.default_tree_manipulators:checkNodeAccess'],
      // Only show links that are accessible for the current user.
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      // Use the default sorting of menu links.
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
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
    $options = [];
    /** @var \Drupal\system\MenuInterface[] $menus */
    foreach ($menus as $menu) {
      $options[$menu->id()] = $menu->label();
    }
    return $options;
  }

  /**
   * Load configured block.
   */
  protected function loadBlock($id) {
    return $this->entityTypeManager->getStorage('block')->load($id);
  }

  /**
   * Get available blocks as options.
   */
  protected function getBlockOptions($theme) {
    $theme_blocks = $this->entityTypeManager->getStorage('block')->loadByProperties(['theme' => $theme]);
    $options = [];
    if (!empty($theme_blocks)) {
      foreach ($theme_blocks as $block) {
        $options[$block->id()] = $block->label();
      }
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
