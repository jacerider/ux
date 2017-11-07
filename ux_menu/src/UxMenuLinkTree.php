<?php

namespace Drupal\ux_menu;

use Drupal\Core\Menu\MenuLinkTree;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Template\Attribute;

/**
 * Implements the loading, transforming and rendering of menu link trees.
 */
class UxMenuLinkTree extends MenuLinkTree {

  /**
   * Flag indicating if the parent menu item should be appended.
   *
   * @var bool
   */
  protected $prependParent = TRUE;

  /**
   * Set the parepend parent flag value.
   *
   * @param bool $value
   *   The value to set.
   */
  public function setPrependParent($value = TRUE) {
    $this->prependParent = $value;
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $tree) {
    $tree_access_cacheability = new CacheableMetadata();
    $tree_link_cacheability = new CacheableMetadata();

    $levels = [];
    foreach ($this->buildLevels($tree) as $key => $subtree) {
      $levels[$key] = $this->buildItems($subtree, $tree_access_cacheability, $tree_link_cacheability);
    }

    $build = [];

    // Apply the tree-wide gathered access cacheability metadata and link
    // cacheability metadata to the render array. This ensures that the
    // rendered menu is varied by the cache contexts that the access results
    // and (dynamic) links depended upon, and invalidated by the cache tags
    // that may change the values of the access results and links.
    $tree_cacheability = $tree_access_cacheability->merge($tree_link_cacheability);
    $tree_cacheability->applyTo($build);

    if ($levels) {
      // Make sure drupal_render() does not re-order the links.
      $build['#sorted'] = TRUE;
      $menu_names = [];
      foreach ($levels as $items) {
        foreach ($items as $item) {
          $link = $item['original_link'];
          $menu_name = $link->getMenuName();
          $menu_names[$menu_name] = $menu_name;
        }
      }

      $build['#theme'] = 'ux_menu_menu__' . strtr(implode('_', $menu_names), '-', '_');
      $build['#menu_name'] = $menu_names;
      foreach ($levels as $key => $items) {
        $build['#levels'][$key]['attributes'] = new Attribute([
          'data-menu' => 'submenu-' . $key,
        ]);
        $build['#levels'][$key]['items'] = $items;
      }
      foreach ($menu_names as $menu_name) {
        // Set cache tag.
        $build['#cache']['tags'][] = 'config:system.menu.' . $menu_name;
      }
    }

    return $build;
  }

  /**
   * Nests the tree into subtrees.
   *
   * @param \Drupal\Core\Menu\MenuLinkTreeElement[] $tree
   *   A data structure representing the tree, as returned from
   *   MenuLinkTreeInterface::load().
   * @param array $levels
   *   Storage for levels.
   * @param int $level
   *   Storage for current level id.
   * @param int $section
   *   Storage for current section id.
   *
   * @return array
   *   And array of tree items.
   */
  protected function buildLevels(array $tree, array $levels = [], $level = 1, $section = 0) {
    $key = '0';
    if ($section) {
      $key = $level . '-' . $section;
    }
    foreach ($tree as $i => $data) {
      $link = $data->link;
      $id = $link->getPluginId();
      $subtree = $data->subtree;

      $data->level = $key;
      $data->submenu = NULL;
      $data->subtree = [];
      $data->isSubmenuParent = FALSE;

      if ($subtree) {
        $section++;
        $sublevel = $level + 1;
        $data->submenu = $sublevel . '-' . $section;

        // Add parent to submenu if it is a URL.
        if ($this->prependParent && $data->link->getUrlObject()->toString()) {
          $parent_data = clone $data;
          $parent_data->isSubmenuParent = TRUE;
          $subtree = [$i => $parent_data] + $subtree;
        }

        $levels = $this->buildLevels($subtree, $levels, $sublevel, $section);
      }
      $levels[$key][$id] = $data;
    }
    ksort($levels);
    return $levels;

  }

  /**
   * Builds the #items property for a menu tree's renderable array.
   *
   * Helper function for ::build().
   *
   * @param \Drupal\Core\Menu\MenuLinkTreeElement[] $tree
   *   A data structure representing the tree, as returned from
   *   MenuLinkTreeInterface::load().
   * @param \Drupal\Core\Cache\CacheableMetadata &$tree_access_cacheability
   *   Internal use only. The aggregated cacheability metadata for the access
   *   results across the entire tree. Used when rendering the root level.
   * @param \Drupal\Core\Cache\CacheableMetadata &$tree_link_cacheability
   *   Internal use only. The aggregated cacheability metadata for the menu
   *   links across the entire tree. Used when rendering the root level.
   *
   * @return array
   *   The value to use for the #items property of a renderable menu.
   *
   * @throws \DomainException
   */
  protected function buildItems(array $tree, CacheableMetadata &$tree_access_cacheability, CacheableMetadata &$tree_link_cacheability) {
    $items = parent::buildItems($tree, $tree_access_cacheability, $tree_link_cacheability);

    foreach ($tree as $data) {
      /** @var \Drupal\Core\Menu\MenuLinkInterface $link */
      $link = $data->link;
      $id = $link->getPluginId();
      if (isset($items[$id])) {
        $element = &$items[$id];
        $options = $element['url']->getOptions();
        $options['attributes']['class'][] = 'uxMenu-link';
        if ($data->submenu) {
          $options['attributes']['class'][] = 'uxMenu-link--has-submenu';
          $options['attributes']['data-submenu'] = 'submenu-' . $data->submenu;
        }
        if ($data->isSubmenuParent) {
          $options['attributes']['class'][] = 'uxMenu-link--is-submenu-parent';
        }
        $element['url']->setOptions($options);
        $element['link_attributes'] = new Attribute($options['attributes']);
      }
    }

    return $items;
  }

}
