<?php

namespace Drupal\ux_form\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextareaWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'string_textarea' widget.
 *
 * @FieldWidget(
 *   id = "ux_textarea",
 *   label = @Translation("UX Text area"),
 *   field_types = {
 *     "string_long"
 *   }
 * )
 */
class UxTextareaWidget extends StringTextareaWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'autogrow' => FALSE,
      'maxheight' => 400,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $states_prefix = 'fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings]';

    $element['autogrow'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Autogrow'),
      '#description' => $this->t('Automatically resize textarea to fit within content.'),
      '#default_value' => $this->getSetting('autogrow'),
    ];
    $element['maxheight'] = [
      '#type' => 'number',
      '#title' => t('Max height'),
      '#description' => $this->t('Limit the textarea max-height when autogrow is enabled. If value is 0 there will be no max-height.'),
      '#field_suffix' => 'px',
      '#default_value' => $this->getSetting('maxheight'),
      '#min' => 1,
      '#states' => [
        'visible' => [
          ':input[name="' . $states_prefix . '[autogrow]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    // $summary = [];

    if ($value = $this->getSetting('autogrow')) {
      $summary[] = $this->t('Autogrow');
      if ($value = $this->getSetting('maxheight')) {
        $summary[] = $this->t('Max-height: @value', ['@value' => $value ? $value . 'px' : 'none']);
      }
    }

    // $summary[] = t('Number of rows: @rows', ['@rows' => $this->getSetting('rows')]);
    // $placeholder = $this->getSetting('placeholder');
    // if (!empty($placeholder)) {
    //   $summary[] = t('Placeholder: @placeholder', ['@placeholder' => $placeholder]);
    // }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    if ($value = $this->getSetting('autogrow')) {
      $element['value']['#attributes']['data-autogrow'] = 'true';
      $element['#attached']['library'][] = 'ux_form/ux_form.autogrow';
      if ($value = $this->getSetting('maxheight')) {
        $element['value']['#attributes']['data-autogrow-max'] = $value;
      }
    }
    // $element['value'] = $element + [
    //   '#type' => 'textarea',
    //   '#default_value' => $items[$delta]->value,
    //   '#rows' => $this->getSetting('rows'),
    //   '#placeholder' => $this->getSetting('placeholder'),
    //   '#attributes' => ['class' => ['js-text-full', 'text-full']],
    // ];

    return $element;
  }

}
