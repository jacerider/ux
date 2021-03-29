<?php

namespace Drupal\ux_paragraphs\Plugin\Field\FieldWidget;

use Drupal\paragraphs\Plugin\Field\FieldWidget\ParagraphsWidget;

/**
 * Plugin implementation of the 'entity_reference_revisions paragraphs' widget.
 *
 * @FieldWidget(
 *   id = "ux_paragraphs",
 *   label = @Translation("UX | Paragraphs"),
 *   description = @Translation("A purdy paragraphs inline form widget."),
 *   field_types = {
 *     "entity_reference_revisions"
 *   }
 * )
 */
class UxParagraphsWidget extends ParagraphsWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'title' => t('Component'),
      'title_plural' => t('Components'),
      'edit_mode' => 'closed',
      'closed_mode' => 'summary',
      'autocollapse' => 'all',
      'add_mode' => 'modal',
      'form_display_mode' => 'default',
      'default_paragraph_type' => '',
    ] + parent::defaultSettings();
  }

  /**
   * Builds dropdown button for adding new paragraph.
   *
   * @return array
   *   The form element array.
   */
  protected function buildButtonsAddMode() {
    $moduleHandler = \Drupal::service('module_handler');
    $add_mode = $this->getSetting('add_mode');
    $add_more_elements = parent::buildButtonsAddMode();

    if ($moduleHandler->moduleExists('micon_paragraphs')) {
      $options = $this->getAccessibleOptions();
      foreach ($options as $machine_name => $label) {
        $button_key = 'add_more_button_' . $machine_name;
        $add_more_elements[$button_key]['#type'] = 'ux_button';
        $add_more_elements[$button_key]['#ajax']['event'] = 'click';
        $add_more_elements[$button_key]['#attributes']['data-uxaside-close'] = '';
        if ($add_mode != 'modal') {
          $add_more_elements[$button_key]['#wrapper_attributes']['class'][] = 'button';
        }
        if ($icon = micon($label)->addMatchPrefix('paragraphs')) {
          $add_more_elements[$button_key]['#label'] = $icon;
        }

      }
    }

    if ($add_mode == 'modal') {
      unset($add_more_elements['add_modal_form_area']);
      $add_more_elements['#type'] = 'ux_aside_container';
    }

    return $add_more_elements;
  }

  /**
   * Builds an add paragraph button for opening of modal form.
   *
   * @param array $element
   *   Render element.
   */
  protected function buildModalAddForm(array &$element) {
    $subtitle = $this->t('to %type', ['%type' => $this->fieldDefinition->getLabel()]);
    // $element['#type'] = 'ux_aside_container';
    $element['#attached']['library'][] = 'ux_paragraphs/ux_paragraphs';
    $element['#attributes']['class'][] = 'ux-paragraph-add-more-trigger';
    $element['#options']['content']['icon'] = 'fa-plus-circle';
    $element['#options']['content']['subtitle'] = $subtitle;
    $element['#options']['content']['attachTop'] = TRUE;
    $element['#options']['content']['transitionIn'] = 'fadeInDown';
    $element['#options']['content']['transitionOut'] = 'fadeOutUp';
    $element['#options']['content']['width'] = '100%';
    $element['#aside_attributes']['class'][] = 'ux-paragraph-add-more';
    $element['#trigger_attributes']['class'][] = 'button';
    $element['#title'] = $this->t('Add @title', ['@title' => $this->getSetting('title')]);
    $element['#field_suffix'] = $subtitle;
  }

}
