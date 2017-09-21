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

    // $form['mix'] = ['#open' => TRUE] + $this->buildMix($form_state);
    $form['date'] = ['#open' => TRUE] + $this->buildDate($form_state);
    $form['select'] = ['#open' => TRUE] + $this->buildSelect($form_state);
    $form['radio'] = ['#open' => TRUE] + $this->buildRadio($form_state);
    $form['checkbox'] = ['#open' => TRUE] + $this->buildCheckbox($form_state);
    // $form['other'] = ['#open' => TRUE] + $this->buildOther($form_state);
    $form['textfield'] = ['#open' => TRUE] + $this->buildTextfield($form_state);
    $form['textarea'] = ['#open' => TRUE] + $this->buildTextarea($form_state);

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Demo mix.
   */
  protected function buildMix(FormStateInterface $form_state) {
    $element = [
      '#type' => 'details',
      '#title' => $this->t('Mix'),
    ];

    $element['textfield'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield'),
    ];

    $element['select'] = [
      '#type' => 'select',
      '#title' => $this->t('Select'),
      '#options' => ['One', 'Two', 'Three','One', 'Two', 'Three','One', 'Two', 'Three','One', 'Two', 'Three','One', 'Two', 'Three','One', 'Two', 'Three','One', 'Two', 'Three', 'One', 'Two', 'Four', 'One', 'Two', 'Three'],
      '#empty_option' => '- None -',
    ];

    $element['select2'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Multiple with Defaults'),
      '#options' => ['One', 'Two', 'Three','One', 'Two', 'Three','One', 'Two', 'Three','One', 'Two', 'Three','One', 'Two', 'Three','One', 'Two', 'Three','One', 'Two', 'Three', 'One', 'Two', 'Four', 'One', 'Two', 'Three'],
      '#multiple' => TRUE,
      '#default_value' => [0, 1],
    ];

    $element['textfield2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield'),
    ];

    $element['radios'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios'),
      '#options' => ['One', 'Two', 'Three'],
    ];

    $element['textfield3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield'),
    ];

    $element['checkboxes'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Checkboxes'),
      '#options' => ['One', 'Two', 'Three'],
    ];

    $element['textfield4'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield'),
    ];

    return $element;
  }

  /**
   * Demo textfields.
   */
  protected function buildTextfield(FormStateInterface $form_state) {
    $element = [
      '#type' => 'details',
      '#title' => $this->t('Textfield'),
      '#description' => $this->t('Textfield examples showing all input types.'),
    ];

    $element['textfield'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield'),
    ];

    $element['textfield2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with Description'),
      '#description' => $this->t('Here is the description'),
    ];

    $element['textfield3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with Prefix & Suffix'),
      '#description' => $this->t('Here is the description'),
      '#field_prefix' => '$',
      '#field_suffix' => '.00',
    ];

    $element['textfield4'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield Required'),
      '#required' => TRUE,
    ];

    $element['textfield5'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield Disabled'),
      '#disabled' => TRUE,
    ];

    $element['textfield6'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with Empty Placeholder'),
      '#attributes' => [
        'placeholder' => '',
      ],
    ];

    $element['textfield7'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with Placeholder'),
      '#attributes' => [
        'placeholder' => $this->t('Placeholder'),
      ],
    ];

    $element['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
    ];

    $element['number'] = [
      '#type' => 'number',
      '#title' => $this->t('Number'),
    ];

    $element['tel'] = [
      '#type' => 'tel',
      '#title' => $this->t('Telephone'),
    ];

    $element['url'] = [
      '#type' => 'url',
      '#title' => $this->t('URL'),
    ];

    $element['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
    ];

    $element['fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Fieldset'),
      '#description' => $this->t('This is an example fieldset description.'),
    ];

    $element['fieldset']['textfield'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield'),
    ];

    $element['fieldset']['textfield2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with Prefix & Suffix'),
      '#description' => $this->t('Here is the description'),
      '#field_prefix' => '$',
      '#field_suffix' => '.00',
    ];

    $count = $form_state->get('count');
    if (empty($count)) {
      $count = 1;
      $form_state->set('count', $count);
    }

    $element['addmore'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Add More'),
      '#prefix' => '<div id="addmore-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    ];
    for ($i = 0; $i < $count; $i++) {
      $element['addmore'][$i] = [
        '#title' => t('Textfield dynamic %delta', ['%delta' => $i + 1]),
        '#type' => 'textfield',
      ];
    }

    $element['addmore_add'] = [
      '#type' => 'submit',
      '#value' => t('Add Another'),
      '#submit' => [[get_class($this), 'addOneSubmit']],
      '#limit_validation_errors' => array(),
      '#ajax' => [
        'callback' => [get_class($this), 'addOneAjax'],
        'wrapper' => 'addmore-wrapper',
        'effect' => 'fade',
      ],
    ];

    return $element;
  }

  /**
   * Demo select.
   */
  protected function buildSelect(FormStateInterface $form_state) {
    $element = [
      '#type' => 'details',
      '#title' => $this->t('Select'),
      '#open' => FALSE,
    ];

    $element['select'] = [
      '#type' => 'select',
      '#title' => $this->t('Select'),
      '#options' => ['One', 'Two', 'Three'],
      '#empty_option' => '- None -',
    ];

    $element['select2'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Multiple'),
      '#options' => ['One', 'Two', 'Three'],
      '#multiple' => TRUE,
    ];

    $element['select3'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Multiple with Defaults'),
      '#options' => ['One', 'Two', 'Three'],
      '#multiple' => TRUE,
      '#default_value' => [0, 1],
    ];

    $element['select4'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Group'),
      '#multiple' => TRUE,
      '#options' => [
        'One Label' => [11 => 'One 1', 21 => 'Two 1', 31 => 'Three 1'],
        'Two Label' => [12 => 'One 2', 22 => 'Two 2', 32 => 'Three 2'],
        'Three Label' => [13 => 'One 3', 23 => 'Two 3', 33 => 'Three 3'],
      ],
    ];

    $element['select5'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Disabled'),
      '#options' => ['One', 'Two', 'Three'],
      '#disabled' => TRUE,
    ];

    return $element;
  }

  /**
   * Demo checkbox.
   */
  protected function buildCheckbox(FormStateInterface $form_state) {
    $element = [
      '#type' => 'details',
      '#title' => $this->t('Checkbox'),
      '#open' => FALSE,
    ];

    $element['checkboxes'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Checkboxes'),
      '#options' => ['One', 'Two', 'Three'],
    ];

    $element['checkboxes2'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Checkboxes Inline'),
      '#options' => ['One', 'Two', 'Three'],
      '#inline' => TRUE,
    ];

    $element['checkboxes3'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Checkboxes Disabled'),
      '#options' => ['One', 'Two', 'Three'],
      '#disabled' => TRUE,
      '#inline' => TRUE,
    ];

    $element['checkbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Checkbox'),
    ];

    $element['checkbox2'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Checkbox Checked'),
      '#default_value' => TRUE,
    ];

    return $element;
  }

  /**
   * Demo checkbox.
   */
  protected function buildRadio(FormStateInterface $form_state) {
    $element = [
      '#type' => 'details',
      '#title' => $this->t('Radio'),
      '#open' => FALSE,
    ];

    $element['radios'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios'),
      '#options' => ['One', 'Two', 'Three'],
    ];

    $element['radios2'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios Inline'),
      '#options' => ['One', 'Two', 'Three'],
      '#default_value' => 1,
      '#inline' => TRUE,
    ];

    $element['radios3'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios Disabled'),
      '#options' => ['One', 'Two', 'Three'],
      '#disabled' => TRUE,
      '#inline' => TRUE,
    ];

    $element['radios4'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios with Default Value'),
      '#options' => ['One', 'Two', 'Three'],
      '#default_value' => 1,
    ];

    return $element;
  }

  /**
   * Demo checkbox.
   */
  protected function buildTextarea(FormStateInterface $form_state) {
    $element = [
      '#type' => 'details',
      '#title' => $this->t('Textarea'),
      '#open' => FALSE,
    ];

    $element['textarea'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Textarea'),
    ];

    $element['text_format'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Text Format'),
      '#format' => 'full_html',
    ];

    return $element;
  }

  /**
   * Demo date.
   */
  protected function buildDate(FormStateInterface $form_state) {
    $element = [
      '#type' => 'details',
      '#title' => $this->t('Date'),
      '#open' => FALSE,
    ];

    $element['date'] = [
      '#type' => 'date',
      '#title' => $this->t('Date'),
    ];

    $element['datetime'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Date & Time'),
      '#default_value' => NULL,
      '#date_increment' => 1,
      '#date_timezone' => drupal_get_user_timezone(),
    ];

    // $element['datetime2'] = [
    //   '#type' => 'datetime',
    //   '#title' => $this->t('Date & Time with Default'),
    //   '#default_value' => DrupalDateTime::createFromTimestamp(time()),
    //   '#date_increment' => 1,
    //   '#date_timezone' => drupal_get_user_timezone(),
    // ];

    return $element;
  }

  /**
   * Demo other.
   */
  protected function buildOther(FormStateInterface $form_state) {
    $element = [
      '#type' => 'details',
      '#title' => $this->t('Other'),
      '#open' => FALSE,
    ];

    $element['range'] = [
      '#type' => 'range',
      '#title' => $this->t('Range'),
    ];

    return $element;
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
