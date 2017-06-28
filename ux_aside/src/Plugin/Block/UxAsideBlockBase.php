<?php

namespace Drupal\ux_aside\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ux_aside\UxAsideInterface;
use Drupal\ux_aside\UxAsideManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a base block implementation that most blocks plugins will extend.
 *
 * This abstract class provides the generic aside block configuration form,
 * default block settings, and handling for general user-defined block
 * visibility settings.
 *
 * @ingroup ux_aside_api
 */
abstract class UxAsideBlockBase extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The aside manager.
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
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The aside.
   *
   * @var \Drupal\ux_aside\UxAsideInterface
   */
  protected $uxAside;

  /**
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\ux_aside\UxAsideManagerInterface $ux_aside_manager
   *   The aside manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, UxAsideManagerInterface $ux_aside_manager, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->uxAsideManager = $ux_aside_manager;
    $this->uxAsideOptions = $ux_aside_manager->getOptionsService();
    $this->entityTypeManager = $entity_type_manager;
    // The aside element is instantiated in the constructor due to caching.
    $uxAside = $this->buildAside();
    if ($uxAside instanceof UxAsideInterface) {
      $this->uxAside = $uxAside;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ux_aside.manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'aside' => [],
    ] + parent::defaultConfiguration();

  }

  /**
   * {@inheritdoc}
   */
  protected function buildAside() {
    return $this->uxAsideManager->create($this->getPluginId())
      ->setOptions($this->configuration['aside']);
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['aside'] = $this->uxAsideOptions->form($this->configuration['aside']) + [
      '#weight' => 10,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['aside'] = $this->uxAsideOptions->optionsDiff($values['aside']);
  }

  /**
   * Check if micon is installed.
   */
  protected function hasIconSupport() {
    return \Drupal::moduleHandler()->moduleExists('micon');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $tags = parent::getCacheTags();
    if (isset($this->uxAside)) {
      $tags = Cache::mergeTags($tags, $this->uxAside->getCacheTags());
    }
    return $tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    if (isset($this->uxAside)) {
      $contexts = Cache::mergeContexts($contexts, $this->uxAside->getCacheContexts());
    }
    return $contexts;
  }

}
