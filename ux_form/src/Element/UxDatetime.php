<?php

namespace Drupal\ux_form\Element;

use Drupal\Core\Datetime\Element\Datetime;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a datetime element.
 *
 * @FormElement("ux_datetime")
 */
class UxDatetime extends Datetime {

  /**
   * {@inheritdoc}
   */
  public static function processDatetime(&$element, FormStateInterface $form_state, &$complete_form) {
    $mode = isset($element['#ux_mode']) ? $element['#ux_mode'] : 'button';

    $element = parent::processDatetime($element, $form_state, $completeForm);
    $settings_date = [
      'mode' => $mode,
      'selectYears' => !empty($element['#ux_select_years']),
      'selectMonths' => !empty($element['#ux_select_months']),
    ];
    $settings_time = [
      'mode' => $mode,
      'format' => 'HH:i:00',
      'formatSubmit' => 'HH:i:00',
      'formatLabel' => 'h:i A'
    ];

    if (isset($element['date']['#date_date_format'])) {
      $date = $element['date']['#date_date_format'];
      $date = str_replace('Y', 'yyyy', $date);
      $date = str_replace('m', 'mm', $date);
      $date = str_replace('d', 'dd', $date);
      $settings_date['format'] = $date;
    }
    if (isset($element['date']['#attributes']['min'])) {
      $min = explode('-', $element['date']['#attributes']['min']);
      // Months in js start at 0.
      $min[1] = $min[1] - 1;
      $settings_date['min'] = $min;
    }
    if (isset($element['date']['#attributes']['max'])) {
      $max = explode('-', $element['date']['#attributes']['max']);
      // Months in js start at 0.
      $max[1] = $max[1] - 1;
      $settings_date['max'] = $max;
    }

    switch ($mode) {
      case 'button':
        if (isset($element['date'])) {
          $text = t('Select Date');
          if (\Drupal::service('module_handler')->moduleExists('micon')) {
            $icon = isset($element['#ux_icon_date']) ? $element['#ux_icon_date'] : 'fa-calendar';
            $text = micon($text)->setIcon('fa-calendar')->setIconOnly();
          }
          $element['date']['#field_suffix'] = [
            '#type' => 'html_tag',
            '#tag' => 'a',
            '#value' => $text,
            '#attributes' => ['class' => ['ux-form-date-button']],
          ];
          // In button mode field should be editable.
          $settings_date['editable'] = TRUE;
        }
        if (isset($element['time'])) {
          $text = t('Select Time');
          if (\Drupal::service('module_handler')->moduleExists('micon')) {
            $icon = isset($element['#ux_icon_time']) ? $element['#ux_icon_time'] : 'fa-clock';
            $text = micon($text)->setIcon($icon)->setIconOnly();
          }
          $element['time']['#field_suffix'] = [
            '#type' => 'html_tag',
            '#tag' => 'a',
            '#value' => $text,
            '#attributes' => ['class' => ['ux-form-time-button']],
          ];
          // In button mode field should be editable.
          $settings_date['editable'] = TRUE;
        }
        if (isset($element['time'])) {
          // In button mode field should be editable.
          $settings_time['editable'] = TRUE;
        }
        break;

      case 'full':
        if (isset($element['date']) && isset($settings_date['format'])) {
          $settings_date['formatSubmit'] = $settings_date['format'];
          $settings_date['format'] = 'mmmm d, yyyy';
        }
        if (isset($element['time'])) {
          $settings_time['format'] = $settings_time['formatLabel'];
        }
        break;
    }

    if (isset($element['date'])) {
      $element['#attached']['library'][] = 'ux_form/ux_form.date';
      $element['date']['#after_build'][] = [get_called_class(), 'afterBuildDate'];
      $element['#attached']['drupalSettings']['ux']['date']['items'][$element['#id']] = $settings_date;
    }

    if (isset($element['time'])) {
      $element['#attached']['library'][] = 'ux_form/ux_form.time';
      $element['#attached']['drupalSettings']['ux']['time']['items'][$element['#id']] = $settings_time;
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function afterBuildDate($element) {
    if (!empty($element['#attached']['library'])) {
      // Remove drupal core date js.
      $element['#attached']['library'] = array_filter($element['#attached']['library'], function ($library) {
        return $library !== 'core/drupal.date';
      });
    }
    return $element;
  }

}
