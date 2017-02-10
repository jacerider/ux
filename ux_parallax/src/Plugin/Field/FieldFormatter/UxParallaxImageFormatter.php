<?php

namespace Drupal\ux_parallax\Plugin\Field\FieldFormatter;

use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;

/**
 * Plugin implementation of the parallax 'image' formatter.
 *
 * @FieldFormatter(
 *  id = "ux_parallax_image_formatter",
 *  label = @Translation("Parallax Image"),
 *  field_types = {"image"}
 * )
 */
class UxParallaxImageFormatter extends ImageFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return ux_parallax_defaults() + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['animation'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Animation'),
      '#description' => $this->t('Defines the default animation parameters.'),
      '#default_value' => $this->getSetting('animation'),
    ];

    $form['optimize'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Optimize'),
      '#description' => $this->t('Optimize animations to make sure there are no performance issues.'),
      '#default_value' => $this->getSetting('optimize'),
    ];

    $form['initialAnimationDuration'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Initial animation duration'),
      '#description' => $this->t('Animation duration for initial load, to set proper element state smoothly.'),
      '#default_value' => $this->getSetting('initialAnimationDuration'),
    ];

    $form['orientation'] = [
      '#type' => 'select',
      '#title' => $this->t('Orientation'),
      '#description' => $this->t('Scroll orientation.'),
      '#options' => [
        'vertical' => $this->t('Vertical'),
        'horizontal' => $this->t('Horizontal'),
      ],
      '#default_value' => $this->getSetting('orientation'),
    ];

    $form['factor'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Factor'),
      '#description' => $this->t('Scroll progress multiplier.'),
      '#default_value' => $this->getSetting('factor'),
    ];

    $form['perspective'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Perspective'),
      '#description' => $this->t('Specifies perspective for the parent parallax element.'),
      '#default_value' => $this->getSetting('perspective'),
    ];

    $form['perspectiveOrigin'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Perspective Origin'),
      '#description' => $this->t('Specifies perspective origin for the parent parallax element.'),
      '#default_value' => $this->getSetting('perspectiveOrigin'),
    ];

    $form['preload'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Preload'),
      '#description' => $this->t('Specifies whether to preload images and display a preloading spinner until it’s loaded.'),
      '#default_value' => $this->getSetting('preload'),
    ];

    $form['normalizeTop'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Normalize top'),
      '#description' => $this->t('Normalize progress when above the zero scroll screen center, to make sure they behave as if they were in the middle of the screen.'),
      '#default_value' => $this->getSetting('normalizeTop'),
    ];

    $form['overflow'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Overflow'),
      '#description' => $this->t('Allow overflowing and underflowing of background images during scroll.'),
      '#default_value' => $this->getSetting('overflow'),
    ];

    $form['size'] = [
      '#type' => 'select',
      '#title' => $this->t('Size'),
      '#description' => $this->t('Sets element size for various situations.'),
      '#options' => [
        'auto' => $this->t('Auto'),
        'fullscreen' => $this->t('Fullscreen'),
        'screenWidth' => $this->t('Screen width'),
        'screenHeight' => $this->t('Screen height'),
      ],
      '#default_value' => $this->getSetting('size'),
    ];

    $form['anchor'] = [
      '#type' => 'select',
      '#title' => $this->t('Anchor'),
      '#description' => $this->t('Sets the background anchor position. The center anchor will always keep the image centered.'),
      '#options' => [
        'center' => $this->t('Center'),
        'top' => $this->t('Top'),
        'bottom' => $this->t('Bottom'),
        'left' => $this->t('Left'),
        'right' => $this->t('Right'),
        'top-left' => $this->t('Top left'),
        'top-right' => $this->t('Top right'),
        'bottom-left' => $this->t('Bottom left'),
        'bottom-right' => $this->t('Bottom right'),
      ],
      '#default_value' => $this->getSetting('anchor'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $settings = $this->getSettings();

    $summary[] = t('Animation: @value', array('@value' => $settings['animation'] ? $settings['animation'] : '- None -'));
    $summary[] = t('Size: @value', array('@value' => $settings['size']));

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    if (!empty($elements)) {
      $entity = $items->getEntity();
      $entity_type = $entity->getEntityTypeId();
      $field_name = $this->fieldDefinition->getName();
      $class_keys = [
        $entity_type,
        $entity->bundle(),
        $this->viewMode,
        $field_name,
      ];
      $id = Html::cleanCssIdentifier(implode('-', $class_keys));
      $settings = array_diff(array_intersect_key($this->getSettings(), ux_parallax_defaults()), ux_parallax_defaults());

      foreach ($elements as &$element) {
        $element['#item_attributes']['class'][] = 'ux-parallax-background';
        $element['#prefix'] = '<div class="ux-parallax-' . $id . '">';
        $element['#suffix'] = '</div>';
      }
      // $elements['#attributes']['class'][] = 'ux-parallax-' . $id;.
      $elements['#attached']['library'][] = 'ux_parallax/ux_parallax';
      $elements['#attached']['drupalSettings']['ux']['parallax']['items'][$id] = $settings;
    }
    return $elements;
  }

}
