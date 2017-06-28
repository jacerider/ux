<?php

namespace Drupal\ux_aside\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ux_aside\UxAsideManagerInterface;
use Drupal\ux_aside\UxAsideOptionsInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'UxAsideBlockTest' block.
 *
 * @Block(
 *  id = "ux_aside_test",
 *  admin_label = @Translation("UX | Aside Cache Test"),
 * )
 */
class UxAsideBlockTest extends BlockBase implements ContainerFactoryPluginInterface {

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
   * @param \Drupal\ux_aside\UxAsideOptionsInterface $ux_aside_options
   *   The aside options.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, UxAsideManagerInterface $ux_aside_manager, UxAsideOptionsInterface $ux_aside_options, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->uxAsideManager = $ux_aside_manager;
    $this->uxAsideOptions = $ux_aside_options;
    $this->entityTypeManager = $entity_type_manager;

    $id = $plugin_id;
    // The aside element is instantiated in the constructor due to caching.
    $this->uxAside = $this->uxAsideManager->create($id)->addCacheContexts(['url.path']);
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
      $container->get('ux_aside.options'),
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
  public function blockForm($form, FormStateInterface $form_state) {
    $form['aside'] = $this->uxAsideOptions->form($this->configuration['aside']);
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
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#markup'] = time();
    $options = $this->configuration['aside'];
    $options['trigger']['text'] =  'Cache Trigger: ' . time();
    return $this->uxAside->setOptions($options)->setContent($build)->toRenderArray();
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
