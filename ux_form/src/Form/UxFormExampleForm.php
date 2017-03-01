<?php

namespace Drupal\ux_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Class UxFormExampleForm.
 *
 * @package Drupal\ux_form\Form
 */
class UxFormExampleForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ux_form_example_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['select'] = [
      '#type' => 'select',
      '#title' => $this->t('Select'),
      '#options' => ['One', 'Two', 'Three'],
      '#empty_option' => '- None -',
    ];

    $form['select2'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Multiple'),
      '#options' => ['One', 'Two', 'Three'],
      '#multiple' => TRUE,
    ];

    $form['select3'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Multiple with Defaults'),
      '#options' => ['One', 'Two', 'Three'],
      '#multiple' => TRUE,
      '#default_value' => [0, 1],
    ];

    $form['select4'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Group'),
      '#options' => [
        'One Label' => ['One 1', 'Two 1', 'Three 1'],
        'Two Label' => ['One 2', 'Two 2', 'Three 2'],
        'Three Label' => ['One 3', 'Two 3', 'Three 3'],
      ],
    ];

    $form['select5'] = [
      '#type' => 'select',
      '#title' => $this->t('Select'),
      '#options' => ['One', 'Two', 'Three'],
      '#disabled' => TRUE,
    ];

    $form['textfield'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield'),
      // '#maxlength' => 5,
      '#attributes' => ['length' => 5],
    ];

    $form['textfield2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with Description'),
      '#description' => $this->t('Here is the description'),
    ];

    $form['textfield3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with Prefix & Suffix'),
      '#description' => $this->t('Here is the description'),
      '#field_prefix' => '$',
      '#field_suffix' => '.00',
    ];

    $form['textfield4'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield Required'),
      '#required' => TRUE,
    ];

    $form['textfield5'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield Disabled'),
      '#disabled' => TRUE,
    ];

    $form['textfield6'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with Empty Placeholder'),
      '#attributes' => [
        'placeholder' => '',
      ],
    ];

    $form['textfield7'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with Placeholder'),
      '#attributes' => [
        'placeholder' => $this->t('Placeholder'),
      ],
    ];

    $form['checkboxes'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Checkboxes'),
      '#options' => ['One', 'Two', 'Three'],
    ];

    $form['checkboxes2'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Checkboxes Inline'),
      '#options' => ['One', 'Two', 'Three'],
      '#attributes' => ['class' => ['inline']],
    ];

    $form['checkboxes3'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Checkboxes Disabled'),
      '#options' => ['One', 'Two', 'Three'],
      '#attributes' => ['class' => ['inline']],
      '#disabled' => TRUE,
    ];

    $form['checkbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Checkbox'),
    ];

    $form['checkbox2'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Checkbox Checked'),
      '#default_value' => TRUE,
    ];

    $form['radios'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios'),
      '#options' => ['One', 'Two', 'Three'],
    ];

    $form['radios2'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios Inline'),
      '#options' => ['One', 'Two', 'Three'],
      '#attributes' => ['class' => ['inline']],
      '#default_value' => 1,
    ];

    $form['radios3'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios Disabled'),
      '#options' => ['One', 'Two', 'Three'],
      '#attributes' => ['class' => ['inline']],
      '#disabled' => TRUE,
    ];

    $count = $form_state->get('count');
    if (empty($count)) {
      $count = 1;
      $form_state->set('count', $count);
    }

    $form['addmore'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Add More'),
      '#prefix' => '<div id="addmore-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    ];
    for ($i = 0; $i < $count; $i++) {
      $form['addmore'][$i] = [
        '#title' => t('Textfield dynamic %delta', ['%delta' => $i + 1]),
        '#type' => 'textfield',
        // '#type' => 'datetime',
        // '#default_value' => DrupalDateTime::createFromTimestamp(time()),
        // '#date_increment' => 1,
        // '#date_timezone' => drupal_get_user_timezone(),
      ];
    }

    $form['addmore_add'] = [
      '#type' => 'submit',
      '#value' => t('Add Image Style'),
      '#submit' => [[get_class($this), 'addOneSubmit']],
      '#limit_validation_errors' => array(),
      '#ajax' => [
        'callback' => [get_class($this), 'addOneAjax'],
        'wrapper' => 'addmore-wrapper',
        'effect' => 'fade',
      ],
    ];

    $form['text_format'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Text Format'),
      '#format' => 'full_html',
    ];

    $form['date'] = [
      '#type' => 'date',
      '#title' => $this->t('Date'),
    ];

    $form['datetime'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Date & Time'),
      '#default_value' => NULL,
      '#date_increment' => 1,
      '#date_timezone' => drupal_get_user_timezone(),
    ];

    $form['textarea'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Textarea'),
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
    ];

    $form['number'] = [
      '#type' => 'number',
      '#title' => $this->t('Number'),
    ];

    $form['tel'] = [
      '#type' => 'tel',
      '#title' => $this->t('Telephone'),
    ];

    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('URL'),
    ];

    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
    ];

    $form['range'] = [
      '#type' => 'range',
      '#title' => $this->t('Range'),
    ];

    $form['fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Fieldset'),
    ];

    $form['fieldset']['textfield'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield'),
    ];

    $form['fieldset']['textfield2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with Prefix & Suffix'),
      '#description' => $this->t('Here is the description'),
      '#field_prefix' => '$',
      '#field_suffix' => '.00',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Callback for both ajax-enabled buttons.
   */
  public static function addOneAjax(array $form, FormStateInterface $form_state) {
    $button = $form_state->getTriggeringElement();

    // Go one level up in the form, to the widgets container.
    $element = NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -1));
    return $element['addmore'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public static function addOneSubmit(array $form, FormStateInterface $form_state) {
    $count = $form_state->get('count');
    $count = $count + 1;
    $form_state->set('count', $count);
    $form_state->setRebuild();
  }

  /**
    * {@inheritdoc}
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      if (is_array($value)) {
        foreach ($value as $i => $v) {
          drupal_set_message($key . ':' . $i . ': ' . $v);
        }
      }
      else {
        drupal_set_message($key . ': ' . $value);
      }
    }

  }

}
