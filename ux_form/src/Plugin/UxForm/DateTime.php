<?php

namespace Drupal\ux_form\Plugin\UxForm;

use Drupal\Core\Render\Element;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "datetime",
 *   label = @Translation("Date"),
 *   element_types = {
 *     "datetime",
 *   }
 * )
 */
class DateTime extends UxFormBase {

  /**
   * {@inheritdoc}
   */
  public function process(&$element) {
    parent::process($element);
    $element['#wrapper_attributes']['class'][] = 'ux-form-datetime';
    $element['#attributes']['class'][] = 'ux-form-inline';
    $element['#theme_wrappers'] = ['form_element'];
    $element['date']['#attributes']['placeholder'] = t('Date');
    $element['time']['#attributes']['placeholder'] = t('Time');
    foreach (Element::children($element) as $key) {
      $child_element = &$element[$key];
      if (isset($child_element['#type']) && $child_element['#type'] == 'date') {
        $child_element['#datetime_child'] = TRUE;
      }
    }
  }

}
