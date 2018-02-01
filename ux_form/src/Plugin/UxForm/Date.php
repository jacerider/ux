<?php

namespace Drupal\ux_form\Plugin\UxForm;

/**
 * Provides a plugin for element type(s).
 *
 * @UxForm(
 *   id = "date",
 *   label = @Translation("Date"),
 *   element_types = {
 *     "date",
 *   },
 * )
 */
class Date extends Input {

  /**
   * {@inheritdoc}
   */
  public function process(&$element) {
    // The property #checkboxes_child will be true if this date is part
    // of a datetime element.
    // @see Drupal\ux_form\Plugin\UxForm\DateTime
    if (!empty($element['#datetime_child'])) {
      // Do not wrap this element in ux_form_element_container because it is
      // part of a checkbox collection and the collection will be wrapped.
      $this->disableWrapper();
    }

    parent::process($element);

    // The last key of parents contains the type... either date or time.
    $type = array_values(array_slice($element['#parents'], -1))[0];
    switch ($type) {
      case 'date':
        $element['#wrapper_attributes']['class'][] = 'ux-form-date';
        break;

      case 'time':
        $element['#wrapper_attributes']['class'][] = 'ux-form-time';
        break;
    }
  }

}
