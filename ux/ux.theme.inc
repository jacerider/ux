<?php

use Drupal\Core\Template\Attribute;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\RenderElement;

/**
 * Implements hook_preprocess_ux_select_as_checkbox().
 */
function template_preprocess_ux_select_as_checkbox(&$variables) {
  $element = $variables['element'];
  $variables['attributes']['type'] = 'checkbox';
  Element::setAttributes($element, array('id', 'name', 'size'));
  RenderElement::setAttributes($element, array('form-select'));

  $id = $element['#id'];

  $variables['attributes'] = $element['#attributes'];
  $variables['attributes']['class'] = 'js-hide';
  $variables['attributes']['id'] = $id . '-select';
  $variables['options'] = form_select_options($element);

  $variables['ux_attributes']['type'] = 'checkbox';
  $variables['ux_attributes']['id'] = $id . '-checkbox';
  $variables['ux_attributes']['class'][] = 'js-show';
  $variables['ux_attributes'] = new Attribute($variables['ux_attributes']);

  if ($element['#value'] === '1') {
    $variables['ux_attributes']['checked'] = 'checked';
  }

  $variables['#attached']['library'][] = 'ux/ux.select_as_checkbox';
  $variables['#attached']['drupalSettings']['ux']['theme']['select_as_checkbox'][$id] = $variables['options'];
}
