<?php

namespace Drupal\ux_offcanvas\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ux_offcanvas\UxOffcanvasManagerInterface;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'UxOffcanvas' block.
 *
 * @Block(
 *  id = "ux_offcanvas",
 *  admin_label = @Translation("Offcanvas"),
 * )
 */
class UxOffcanvas extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Menu\MenuLinkTree definition.
   *
   * @var \Drupal\Core\Menu\MenuLinkTree
   */
  protected $menuLinkTree;
  /**
   * Drupal\Core\Menu\MenuActiveTrail definition.
   *
   * @var \Drupal\Core\Menu\MenuActiveTrail
   */
  protected $menuActiveTrail;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The offcanvas manager.
   *
   * @var \Drupal\ux_offcanvas\UxOffcanvasManagerInterface
   */
  protected $uxOffcanvasManager;

  /**
   * The offcanvas object.
   *
   * @var \Drupal\ux_offcanvas\UxOffcanvas
   */
  protected $uxOffcanvas;

  /**
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    UxOffcanvasManagerInterface $ux_offcanvas_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->uxOffcanvasManager = $ux_offcanvas_manager;
    $this->uxOffcanvas = $this->uxOffcanvasManager->addOffcanvas($plugin_id)
      ->addCacheTags($this->getCacheTags())
      ->addCacheContexts($this->getCacheContexts());
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('ux_offcanvas.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'block' => '',
      'offcanvas_text' => $this->t('Trigger'),
      'offcanvas_icon' => '',
      'offcanvas_icon_only' => FALSE,
      'offcanvas_position' => 'left',
    ] + parent::defaultConfiguration();

  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    // Get the theme.
    $theme = $form_state->get('block_theme');
    $theme_blocks = $this->entityTypeManager->getStorage('block')->loadByProperties(['theme' => $theme]);
    $block_options = [];
    if (!empty($theme_blocks)) {
      foreach ($theme_blocks as $block) {
        $block_options[$block->id()] = $block->label();
      }
    }

    if (!empty($block_options)) {
      $form['block'] = array(
        '#type' => 'select',
        '#title' => t('Block'),
        '#description' => t('The block to place within the offcanvas element.'),
        '#options' => ['' => t('- None -')] + $block_options,
        '#default_value' => $this->configuration['block'],
        '#required' => TRUE,
      );

      $form['offcanvas'] = [
        '#type' => 'details',
        '#title' => $this->t('Offcanvas Settings'),
        '#open' => TRUE,
      ];

      $form['offcanvas']['offcanvas_text'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Menu trigger link text'),
        '#description' => $this->t('The text to be placed within the link that will trigger the offcanvas element.'),
        '#default_value' => $this->configuration['offcanvas_text'],
        '#required' => TRUE,
      ];

      if (\Drupal::moduleHandler()->moduleExists('micon') && function_exists('micon')) {
        $form['offcanvas']['offcanvas_icon'] = [
          '#type' => 'micon',
          '#title' => $this->t('Icon'),
          '#default_value' => $this->configuration['offcanvas_icon'],

        ];
        $form['offcanvas']['offcanvas_icon_only'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Icon Only'),
          '#default_value' => $this->configuration['offcanvas_icon_only'],
        ];
      }

      $form['offcanvas']['offcanvas_position'] = [
        '#type' => 'select',
        '#title' => $this->t('Offcanvas element position'),
        '#default_value' => $this->configuration['offcanvas_position'],
        '#options' => [
          'left' => $this->t('Left'),
          'right' => $this->t('Right'),
          'top' => $this->t('Top'),
          'bottom' => $this->t('Bottom'),
        ],
        '#required' => TRUE,
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['block'] = $values['block'];
    foreach (['offcanvas'] as $key) {
      foreach ($values[$key] as $id => $value) {
        $this->configuration[$id] = $value;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $text = $this->configuration['offcanvas_text'];
    $icon = $this->configuration['offcanvas_icon'];
    $position = $this->configuration['offcanvas_position'];

    $block_storage = $this->entityTypeManager->getStorage('block');
    if ($block = $block_storage->load($this->configuration['block'])) {
      $content = $this->entityTypeManager->getViewBuilder('block')->view($block);

      if (!empty($icon)) {
        $text = micon($text)->setIcon($icon)->setIconOnly($this->configuration['offcanvas_icon_only']);
      }

      return $this->uxOffcanvas->setTriggerText($text)
        ->setContent($content)
        ->setPosition($position)
        ->toRenderableTrigger();
    }

    return [];
  }

}
