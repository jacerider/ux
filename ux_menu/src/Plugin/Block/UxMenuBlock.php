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

/**
 * Provides a 'UxMenuBlock' block.
 *
 * @Block(
 *   id = "ux_menu",
 *   admin_label = @Translation("UX | Menu"),
 *   category = @Translation("User Experience"),
 * )
 */
class UxMenuBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The UX Menu options service.
   *
   * @var \Drupal\ux\UxOptionsInterface
   */
  protected $uxMenuOptions;

  /**
   * The menu link tree service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $menuTree;

  /**
   * The active menu trail service.
   *
   * @var \Drupal\Core\Menu\MenuActiveTrailInterface
   */
  protected $menuActiveTrail;

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
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, UxOptionsInterface $ux_menu_options, MenuLinkTreeInterface $menu_tree, MenuActiveTrailInterface $menu_active_trail) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->uxMenuOptions = $ux_menu_options;
    $this->menuTree = $menu_tree;
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
      $container->get('entity_type.manager'),
      $container->get('ux_menu.options'),
      $container->get('ux_menu.link_tree'),
      $container->get('menu.active_trail')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'menu' => [
        'primary' => '',
        'secondary' => '',
        'level' => 1,
        'depth' => 3,
        'expand' => 1,
      ],
      'block' => [
        'header' => '',
        'footer' => '',
      ],
      'options' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->configuration;

    $form['menu'] = [
      '#type' => 'details',
      '#title' => $this->t('Menus'),
      '#open' => TRUE,
    ];

    $form['menu']['primary'] = [
      '#type' => 'select',
      '#title' => $this->t('Primary'),
      '#options' => $this->getMenuOptions(),
      '#default_value' => $config['menu']['primary'],
      '#required' => TRUE,
    ];

    $form['menu']['secondary'] = [
      '#type' => 'select',
      '#title' => $this->t('Secondary'),
      '#options' => $this->getMenuOptions(),
      '#empty_option' => $this->t('- None -'),
      '#default_value' => $config['menu']['secondary'],
    ];

    $form['menu']['expand'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Expand all menu links'),
      '#default_value' => $config['menu']['expand'],
      '#description' => $this->t('All menu links that have children will "Show as expanded".'),
    ];

    $defaults = $this->defaultConfiguration();
    $form['menu']['menu_levels'] = [
      '#type' => 'details',
      '#title' => $this->t('Menu levels'),
      // Open if not set to defaults.
      '#open' => $defaults['menu']['level'] != $config['menu']['level'] || $defaults['menu']['depth'] != $config['menu']['depth'],
      '#process' => [[get_class(), 'processToParent']],
    ];

    $options = range(0, $this->menuTree->maxDepth());
    unset($options[0]);

    $form['menu']['menu_levels']['level'] = [
      '#type' => 'select',
      '#title' => $this->t('Initial visibility level'),
      '#default_value' => $config['menu']['level'],
      '#options' => $options,
      '#description' => $this->t('The menu is only visible if the menu item for the current page is at this level or below it. Use level 1 to always display this menu.'),
      '#required' => TRUE,
    ];

    $options[0] = $this->t('Unlimited');

    $form['menu']['menu_levels']['depth'] = [
      '#type' => 'select',
      '#title' => $this->t('Number of levels to display'),
      '#default_value' => $config['menu']['depth'],
      '#options' => $options,
      '#description' => $this->t('This maximum number includes the initial level.'),
      '#required' => TRUE,
    ];

    $theme = $form_state->get('block_theme');
    $options = $this->getBlockOptions($theme);
    if (!empty($options)) {
      $form['block'] = [
        '#type' => 'details',
        '#title' => $this->t('Blocks'),
        '#open' => !empty($this->configuration['block']['header']) || !empty($this->configuration['block']['footer']),
      ];
      $form['block']['header'] = [
        '#type' => 'select',
        '#title' => $this->t('Header'),
        '#description' => $this->t('A block placed in the header of the mobile menu element.'),
        '#options' => $options,
        '#empty_option' => $this->t('- None -'),
        '#default_value' => $this->configuration['block']['header'],
      ];
      $form['block']['footer'] = [
        '#type' => 'select',
        '#title' => $this->t('Footer'),
        '#description' => $this->t('A block placed in the footer of the mobile menu element.'),
        '#options' => $options,
        '#empty_option' => $this->t('- None -'),
        '#default_value' => $this->configuration['block']['footer'],
      ];
    }

    $form['options'] = $this->uxMenuOptions->form($this->configuration['options']) + [
      '#weight' => 10,
    ];

    return $form;
  }

  /**
   * Form API callback: Processes the menu_levels field element.
   *
   * Adjusts the #parents of menu_levels to save its children at the top level.
   */
  public static function processToParent(&$element, FormStateInterface $form_state, &$complete_form) {
    array_pop($element['#parents']);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['menu'] = $form_state->getValue('menu');
    $this->configuration['block'] = $form_state->getValue('block');
    $this->configuration['options'] = $this->uxMenuOptions->optionsDiff($form_state->getValue('options'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'ux_menu',
      '#menu' => $this->buildMenu(),
      '#options' => $this->configuration['options'],
    ];

    if ($block_id = $this->configuration['block']['header']) {
      $block = $this->entityTypeManager->getStorage('block')->load($block_id);
      if ($block) {
        $build['#header'] = $this->buildBlock($block);
        $this->addCacheableDependency($build, $block);
      }
    }

    if ($block_id = $this->configuration['block']['footer']) {
      $block = $this->entityTypeManager->getStorage('block')->load($block_id);
      if ($block) {
        $build['#footer'] = $this->buildBlock($block);
        $this->addCacheableDependency($build, $block);
      }
    }

    return $build;
  }

  /**
   * Load block entity.
   */
  protected function loadBlock($block_id) {
    return $this->entityTypeManager->getStorage('block')->load($block_id);
  }

  /**
   * Load configured block.
   */
  protected function buildBlock($block) {
    return $this->entityTypeManager->getViewBuilder('block')->view($block);
  }

  /**
   * Build the menu.
   */
  protected function buildMenu() {
    $menu_name = $this->configuration['menu']['primary'];
    $secondary_menu_name = $this->configuration['menu']['secondary'];
    $tree = $this->buildMenuTree($menu_name);

    if ($secondary_menu_name) {
      $tree += $this->buildMenuTree($secondary_menu_name);
    }

    return $this->menuTree->build($tree);
  }

  /**
   * Build link tree.
   */
  protected function buildMenuTree($menu_name) {
    $expand = $this->configuration['menu']['expand'];
    $level = $this->configuration['menu']['level'];
    $depth = $this->configuration['menu']['depth'];

    $parameters = $this->menuTree->getCurrentRouteMenuTreeParameters($menu_name);
    $parameters->setMinDepth($level);
    if ($depth > 0) {
      $parameters->setMaxDepth(min($level + $depth - 1, $this->menuTree->maxDepth()));
    }
    // If expandedParents is empty, the whole menu tree is built.
    if ($expand) {
      $parameters->expandedParents = [];
    }
    $tree = $this->menuTree->load($menu_name, $parameters);
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    return $this->menuTree->transform($tree, $manipulators);
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
  public function addCacheableDependency(array &$elements, $dependency) {
    $meta_a = CacheableMetadata::createFromRenderArray($elements);
    $meta_b = CacheableMetadata::createFromObject($dependency);
    $meta_a->merge($meta_b)->applyTo($elements);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $config = $this->configuration;
    $cache_tags = ['config:system.menu.' . $config['menu']['primary']];
    if ($config['menu']['secondary']) {
      $cache_tags[] = 'config:system.menu.' . $config['menu']['secondary'];
    }
    if ($config['block']['header']) {
      $cache_tags[] = 'config:block.block.' . $config['block']['header'];
    }
    if ($config['block']['footer']) {
      $cache_tags[] = 'config:block.block.' . $config['block']['footer'];
    }
    return Cache::mergeTags(parent::getCacheTags(), $cache_tags);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $config = $this->configuration;
    $cache_contexts = ['route.menu_active_trails:' . $config['menu']['primary']];
    if ($config['menu']['secondary']) {
      $cache_contexts[] = 'route.menu_active_trails:' . $config['menu']['secondary'];
    }
    return Cache::mergeContexts(parent::getCacheContexts(), $cache_contexts);
  }

}
