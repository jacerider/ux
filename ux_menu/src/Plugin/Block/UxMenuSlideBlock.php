<?php

namespace Drupal\ux_menu\Plugin\Block;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'UxMenuSlideBlock' block.
 *
 * @Block(
 *   id = "ux_menu_slide",
 *   admin_label = @Translation("UX | Menu Slide"),
 *   category = @Translation("User Experience"),
 * )
 */
class UxMenuSlideBlock extends UxMenuBase {

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
    $default = parent::defaultConfiguration() + [
      'block' => [
        'header' => '',
        'footer' => '',
      ],
      'options' => [],
    ];
    $default['menu']['prepend_parent'] = 1;
    return $default;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->configuration;

    $form['menu']['prepend_parent'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Prepand parent item as first nested item'),
      '#default_value' => $config['menu']['prepend_parent'],
      '#description' => $this->t('For nested menu items, this will add the parent link as the first child link so that it is accessible.'),
    ];

    $theme = $form_state->get('block_theme');
    $options = $this->getBlockOptions($theme);
    if (!empty($options)) {
      $form['block'] = [
        '#type' => 'details',
        '#title' => $this->t('Blocks'),
        '#open' => !empty($config['block']['header']) || !empty($config['block']['footer']),
      ];
      $form['block']['header'] = [
        '#type' => 'select',
        '#title' => $this->t('Header'),
        '#description' => $this->t('A block placed in the header of the mobile menu element.'),
        '#options' => $options,
        '#empty_option' => $this->t('- None -'),
        '#default_value' => $config['block']['header'],
      ];
      $form['block']['footer'] = [
        '#type' => 'select',
        '#title' => $this->t('Footer'),
        '#description' => $this->t('A block placed in the footer of the mobile menu element.'),
        '#options' => $options,
        '#empty_option' => $this->t('- None -'),
        '#default_value' => $config['block']['footer'],
      ];
    }

    $form['options'] = $this->uxMenuOptions->form($config['options']) + [
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
    parent::blockSubmit($form, $form_state);
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

    if (!empty($this->configuration['block']['header']) && ($block_id == $this->configuration['block']['header'])) {
      $block = $this->entityTypeManager->getStorage('block')->load($block_id);
      if ($block) {
        $build['#header'] = $this->buildBlock($block);
        $this->addCacheableDependency($build, $block);
      }
    }

    if (!empty($this->configuration['block']['footer']) && ($block_id == $this->configuration['block']['footer'])) {
      $block = $this->entityTypeManager->getStorage('block')->load($block_id);
      if ($block) {
        $build['#footer'] = $this->buildBlock($block);
        $this->addCacheableDependency($build, $block);
      }
    }

    if (empty($build['#menu']['#levels'])) {
      // If we have no menu items simply return the cache.
      $build = [
        '#cache' => [
          'contexts' => $this->getCacheContexts(),
          'tags' => $this->getCacheTags(),
        ],
      ];
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
   * {@inheritdoc}
   */
  public function buildMenu() {
    $this->menuTree->setPrependParent($this->configuration['menu']['prepend_parent']);
    return parent::buildMenu();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $config = $this->configuration;
    $cache_tags = parent::getCacheTags();
    if (!empty($config['block']['header']) && ($config['block']['header'])) {
      $cache_tags[] = 'config:block.block.' . $config['block']['header'];
    }
    if (!empty($config['block']['footer']) && ($config['block']['footer'])) {
      $cache_tags[] = 'config:block.block.' . $config['block']['footer'];
    }
    return Cache::mergeTags(parent::getCacheTags(), $cache_tags);
  }

}
