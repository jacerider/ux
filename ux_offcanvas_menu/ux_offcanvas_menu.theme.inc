<?php

use Drupal\Core\Template\Attribute;

/**
 * Implements hook_preprocess_menu__mobile_menu().
 */
function ux_offcanvas_menu_preprocess_ux_offcanvas_menu(&$variables) {
  $variables['#attached']['library'][] = 'ux_offcanvas_menu/ux_offcanvas_menu';
  $variables['attributes']['class'][] = 'ux-offcanvas-menu';
  // kint($variables);
  ux_offcanvas_menu_mobile_append_parent($variables['items']);
  // kint($variables);
}

/**
 * Appends the parent menu item as a child so that it is accessible.
 */
function ux_offcanvas_menu_mobile_append_parent(&$items) {
  foreach ($items as $key => $item) {
    if (!empty($item['below'])) {
      if ($item['url']->toString()) {
        $parent = $item;
        $parent['below'] = [];
        $parent['is_expanded'] = FALSE;
        $parent['in_active_trail'] = FALSE;
        $parent['attributes'] = new Attribute();
        $items[$key]['below'] = [$key => $parent] + $items[$key]['below'];
      }
      ux_offcanvas_menu_mobile_append_parent($items[$key]['below']);
    }
  }
}
