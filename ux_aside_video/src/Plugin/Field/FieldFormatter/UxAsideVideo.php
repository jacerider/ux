<?php

namespace Drupal\ux_aside_video\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\video_embed_field\Plugin\Field\FieldFormatter\Video;
use Drupal\video_embed_field\Plugin\Field\FieldFormatter\Thumbnail;
use Drupal\ux_aside\UxAsideManagerInterface;
use Drupal\core\Url;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Plugin implementation of the thumbnail field formatter.
 *
 * @FieldFormatter(
 *   id = "ux_aside_video",
 *   label = @Translation("Aside"),
 *   field_types = {
 *     "video_embed_field"
 *   }
 * )
 */
class UxAsideVideo extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The field formatter plugin instance for thumbnails.
   *
   * @var \Drupal\Core\Field\FormatterInterface
   */
  protected $thumbnailFormatter;

  /**
   * The field formatterp plguin instance for videos.
   *
   * @var \Drupal\Core\Field\FormatterInterface
   */
  protected $videoFormatter;

  /**
   * The ux aside manager.
   *
   * @var \Drupal\ux_aside\UxAsideManagerInterface
   */
  protected $uxAsideManager;

  /**
   * The aside options service.
   *
   * @var \Drupal\ux_aside\UxAsideOptionsInterface
   */
  protected $uxAsideOptions;

  /**
   * Constructs a new instance of the plugin.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Third party settings.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Drupal\Core\Field\FormatterInterface $thumbnail_formatter
   *   The field formatter for thumbnails.
   * @param \Drupal\Core\Field\FormatterInterface $video_formatter
   *   The field formatter for videos.
   * @param \Drupal\ux_aside\UxAsideManagerInterface $ux_aside_manager
   *   The ux aside manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, ModuleHandlerInterface $module_handler, RendererInterface $renderer, FormatterInterface $thumbnail_formatter, FormatterInterface $video_formatter, UxAsideManagerInterface $ux_aside_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->moduleHandler = $module_handler;
    $this->renderer = $renderer;
    $this->thumbnailFormatter = $thumbnail_formatter;
    $this->videoFormatter = $video_formatter;
    $this->uxAsideManager = $ux_aside_manager;
    $this->uxAsideOptions = $ux_aside_manager->getOptionsService();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $formatter_manager = $container->get('plugin.manager.field.formatter');
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('module_handler'),
      $container->get('renderer'),
      $formatter_manager->createInstance('video_embed_field_thumbnail', $configuration),
      $formatter_manager->createInstance('video_embed_field_video', $configuration),
      $container->get('ux_aside.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $entity = $items->getEntity();
    $type = $this->getSetting('type');
    $icon_support = $this->moduleHandler->moduleExists('micon');

    if ($type == 'image') {
      $thumbnails = $this->thumbnailFormatter->viewElements($items, $langcode);
    }

    foreach ($items as $delta => $item) {

      // Build trigger contents.
      switch ($type) {
        case 'image':
          $title = [];
          $title['image'] = $thumbnails[$delta];
          if (($icon = $this->getSetting('icon')) && $icon_support) {
            $title['icon'] = [
              '#theme' => 'micon',
              '#icon' => $icon,
            ];
          }
          break;

        default:
          $title = $this->getSetting('text');
          if (($icon = $this->getSetting('icon')) && $icon_support) {
            $title = micon($title)->setIcon($icon);
          }
          break;
      }

      // Build link.
      $element[$delta]['link'] = [
        '#type' => 'link',
        '#title' => $title,
        '#url' => Url::fromRoute('ux_aside_video.view', [
          'entity_type' => $entity->getEntityTypeId(),
          'entity_id' => $entity->id(),
          'langcode' => $langcode,
          'view_mode' => $this->viewMode,
          'field_name' => $this->fieldDefinition->getName(),
          'delta' => $delta,
        ], ['attributes' => ['class' => ['use-ajax']]]),
        '#attached' => ['library' => ['core/drupal.ajax']],
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return Thumbnail::defaultSettings() + Video::defaultSettings() + [
      'type' => 'image',
      'text' => 'View Video',
      'icon' => 'fa-play-circle-o',
      'aside' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $field_name = $this->fieldDefinition->getName();
    $icon_support = $this->moduleHandler->moduleExists('micon');

    $element['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Trigger Type'),
      '#options' => [
        'image' => $this->t('Image'),
        'text' => $this->t('Text'),
      ],
      '#default_value' => $this->getSetting('type'),
      '#required' => TRUE,
    ];
    $image_visibility = [
      'visible' => [
        ':input[name="fields[' . $field_name . '][settings_edit_form][settings][type]"]' => ['value' => 'image'],
      ],
    ];
    $text_visibility = [
      'visible' => [
        ':input[name="fields[' . $field_name . '][settings_edit_form][settings][type]"]' => ['value' => 'text'],
      ],
    ];

    $element += $this->thumbnailFormatter->settingsForm([], $form_state);
    $element['image_style']['#states'] = $image_visibility;
    $element['link_image_to']['#states'] = $image_visibility;

    $element['text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text'),
      '#default_value' => $this->getSetting('text'),
      '#states' => $text_visibility,
    ];

    if ($icon_support) {
      $element['type']['#options']['text'] .= ' and ' . $this->t('Icon');
      $element['icon'] = [
        '#type' => 'micon',
        '#title' => $this->t('Icon'),
        '#default_value' => $this->getSetting('icon'),
      ];
    }

    $element += $this->videoFormatter->settingsForm([], $form_state);

    $element['aside'] = $this->uxAsideOptions->form($this->settings['aside']) + [
      '#weight' => 10,
      '#element_validate' => [[$this, 'settingsFormAsideValidate']],
    ];
    $element['aside']['trigger']['#access'] = FALSE;

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function settingsFormAsideValidate($element, FormStateInterface $form_state) {
    $values = $form_state->getValue($element['#parents']);
    // Clean up submitted values.
    $values = \Drupal::service('ux_aside.options')->optionsDiff($values);
    $form_state->setValueForElement($element, $values);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    switch ($this->getSetting('type')) {
      case 'image':
        $summary[] = $this->t('Image that launches an aside.');
        $summary[] = implode(',', $this->videoFormatter->settingsSummary());
        break;

      case 'text':
        $summary[] = $this->t('Text that launches an aside.');
        if ($value = $this->getSetting('text')) {
          $summary[] = $this->t('Text: @value', ['@value' => $value]);
        }
        break;
    }
    if (($value = $this->getSetting('icon')) && $this->moduleHandler->moduleExists('micon')) {
      $summary[] = $this->t('Icon: @value', ['@value' => micon()->setIcon($value)]);
    }
    $summary[] = implode(',', $this->thumbnailFormatter->settingsSummary());
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return parent::calculateDependencies() + $this->videoFormatter->calculateDependencies();
  }

  /**
   * {@inheritdoc}
   */
  public function onDependencyRemoval(array $dependencies) {
    $parent = parent::onDependencyRemoval($dependencies);
    $video = $this->videoFormatter->onDependencyRemoval($dependencies);
    return $parent || $video;
  }

  /**
   * Return the video formatter.
   */
  public function getVideoFormatter() {
    return $this->videoFormatter;
  }

}
