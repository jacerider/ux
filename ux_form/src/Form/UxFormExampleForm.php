<?php

namespace Drupal\ux_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

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

    $form['textfield'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield'),
      '#required' => TRUE,
    ];

    $form['textfield2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with Description'),
      '#description' => $this->t('Here is the description'),
      '#required' => TRUE,
    ];

    $form['textfield3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with Prefix & Suffix'),
      '#description' => $this->t('Here is the description'),
      '#field_prefix' => '$',
      '#field_suffix' => '.00',
      '#required' => TRUE,
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
    ];

    $form['radios3'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios Disabled'),
      '#options' => ['One', 'Two', 'Three'],
      '#attributes' => ['class' => ['inline']],
      '#disabled' => TRUE,
    ];

    // $form['text_format'] = [
    //   '#type' => 'text_format',
    //   '#title' => $this->t('Text Format'),
    //   '#format' => 'full_html',
    // ];

    // $form['date'] = [
    //   '#type' => 'date',
    //   '#title' => $this->t('Date'),
    // ];

    // $form['datetime'] = [
    //   '#type' => 'datetime',
    //   '#title' => $this->t('Date & Time'),
    //   '#default_value' => NULL,
    //   '#date_increment' => 1,
    //   '#date_timezone' => drupal_get_user_timezone(),
    // ];

    // $form['textarea'] = [
    //   '#type' => 'textarea',
    //   '#title' => $this->t('Textarea'),
    // ];

    // $form['checkboxes'] = [
    //   '#type' => 'checkboxes',
    //   '#title' => $this->t('Checkboxes'),
    //   '#options' => ['One', 'Two', 'Three'],
    // ];

    // $form['checkboxes2'] = [
    //   '#type' => 'checkboxes',
    //   '#title' => $this->t('Checkboxes Inline'),
    //   '#options' => ['One', 'Two', 'Three'],
    //   '#attributes' => ['class' => ['inline']],
    // ];

    // $form['checkboxes3'] = [
    //   '#type' => 'checkboxes',
    //   '#title' => $this->t('Checkboxes Disabled'),
    //   '#options' => ['One', 'Two', 'Three'],
    //   '#attributes' => ['class' => ['inline']],
    //   '#disabled' => TRUE,
    // ];

    // $form['checkbox'] = [
    //   '#type' => 'checkbox',
    //   '#title' => $this->t('Checkbox'),
    // ];

    // $form['checkbox2'] = [
    //   '#type' => 'checkbox',
    //   '#title' => $this->t('Checkbox Checked'),
    //   '#default_value' => TRUE,
    // ];

    // $form['select'] = [
    //   '#type' => 'select',
    //   '#title' => $this->t('Select'),
    //   '#options' => ['One', 'Two', 'Three'],
    // ];

    // $form['email'] = [
    //   '#type' => 'email',
    //   '#title' => $this->t('Email'),
    // ];

    // $form['number'] = [
    //   '#type' => 'number',
    //   '#title' => $this->t('Number'),
    // ];

    // $form['tel'] = [
    //   '#type' => 'tel',
    //   '#title' => $this->t('Telephone'),
    // ];

    // $form['url'] = [
    //   '#type' => 'url',
    //   '#title' => $this->t('URL'),
    // ];

    // $form['password'] = [
    //   '#type' => 'password',
    //   '#title' => $this->t('Password'),
    // ];

    // $form['range'] = [
    //   '#type' => 'range',
    //   '#title' => $this->t('Range'),
    // ];

    // $form['fieldset'] = [
    //   '#type' => 'fieldset',
    //   '#title' => $this->t('Fieldset'),
    // ];

    // $form['fieldset']['textfield'] = [
    //   '#type' => 'textfield',
    //   '#title' => $this->t('Textfield'),
    // ];

    // $form['fieldset']['textfield2'] = [
    //   '#type' => 'textfield',
    //   '#title' => $this->t('Textfield with Prefix & Suffix'),
    //   '#description' => $this->t('Here is the description'),
    //   '#field_prefix' => '$',
    //   '#field_suffix' => '.00',
    // ];

    // $form['submit'] = [
    //   '#type' => 'submit',
    //   '#value' => $this->t('Submit'),
    // ];

    return $form;
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
        drupal_set_message($key . ': ' . $value);
    }

  }

}
