<?php

namespace Drupal\ux_form\Plugin\UxForm;

use Drupal\Core\Form\FormStateInterface;

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
  public function process(&$element, FormStateInterface $form_state, &$complete_form) {
    // The property #checkboxes_child will be true if this date is part
    // of a datetime element.
    // @see Drupal\ux_form\Plugin\UxForm\DateTime
    if (!empty($element['#datetime_child'])) {
      // Do not wrap this element in ux_form_element_container because it is
      // part of a checkbox collection and the collection will be wrapped.
      $this->disableWrapper();
    }

    parent::process($element, $form_state, $complete_form);

    // The last key of parents contains the type... either date or time.
    $type = array_values(array_slice($element['#parents'], -1))[0];
    switch ($type) {
      case 'date':
        $element['#wrapper_attributes']['class'][] = 'ux-form-date';
        if (!empty($element['#attached']['library'])) {
          $element['#attached']['library'] = array_filter($element['#attached']['library'], function ($library) {
            return $library !== 'core/drupal.date';
          });
        }
        $element['#attached']['library'][] = 'ux_form/ux_form.date';
        break;

      case 'time':
        $element['#wrapper_attributes']['class'][] = 'ux-form-time';
        $element['#attached']['library'][] = 'ux_form/ux_form.time';
        break;
    }
  }

}
