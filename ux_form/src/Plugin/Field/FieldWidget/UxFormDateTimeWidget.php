<?php

namespace Drupal\ux_form\Plugin\Field\FieldWidget;

use Drupal\datetime\Plugin\Field\FieldWidget\DateTimeDefaultWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'datetime_ux' widget.
 *
 * @FieldWidget(
 *   id = "datetime_ux",
 *   label = @Translation("UX Date and time"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class UxFormDateTimeWidget extends DateTimeDefaultWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'mode' => 'button',
      'select_years' => FALSE,
      'select_months' => FALSE,
      'icon_date' => 'fa-calendar',
      'icon_time' => 'fa-clock',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  protected function getModeOptions() {
    return ['button' => $this->t('Button'), 'full' => $this->t('Full')];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Mode'),
      '#default_value' => $this->getSetting('mode'),
      '#options' => $this->getModeOptions(),
      '#required' => TRUE,
    ];

    $elements['select_years'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow year selection'),
      '#default_value' => $this->getSetting('select_years'),
    ];

    $elements['select_months'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow month selection'),
      '#default_value' => $this->getSetting('select_months'),
    ];

    if (\Drupal::service('module_handler')->moduleExists('micon')) {
      $elements['icon_date'] = [
        '#type' => 'micon',
        '#title' => $this->t('Date select icon'),
        '#default_value' => $this->getSetting('icon_date'),
      ];
      $elements['icon_time'] = [
        '#type' => 'micon',
        '#title' => $this->t('Time select icon'),
        '#default_value' => $this->getSetting('icon_time'),
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $mode = $this->getModeOptions()[$this->getSetting('mode')];
    $summary[] = $this->t('Mode: @mode', ['@mode' => $mode]);
    if ($this->getSetting('select_years')) {
      $summary[] = $this->t('Allow year selection');
    }
    if ($this->getSetting('select_months')) {
      $summary[] = $this->t('Allow month selection');
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $element['value']['#type'] = 'ux_datetime';
    $element['value']['#ux_mode'] = $this->getSetting('mode');
    $element['value']['#ux_select_years'] = !empty($this->getSetting('select_years'));
    $element['value']['#ux_select_months'] = !empty($this->getSetting('select_months'));
    $element['value']['#ux_icon_date'] = !empty($this->getSetting('icon_date'));
    $element['value']['#ux_icon_time'] = !empty($this->getSetting('icon_time'));
    return $element;
  }

}
