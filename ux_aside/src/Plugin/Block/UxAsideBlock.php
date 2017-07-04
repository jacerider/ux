<?php

namespace Drupal\ux_aside\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'UxAsideBlock' block.
 *
 * @Block(
 *   id = "ux_aside",
 *   admin_label = @Translation("UX | Aside"),
 *   category = @Translation("User Experience"),
 * )
 */
class UxAsideBlock extends UxAsideBlockBase {

  /**
   * The block object.
   *
   * @var \Drupal\block\BlockInterface
   */
  protected $block;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'block' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  protected function buildAside() {
    if ($block = $this->loadBlock()) {
      $unique_id = $this->getPluginId() . md5(json_encode($this->configuration['block']) . json_encode($this->configuration['aside']));
      return $this->uxAsideManager->create($unique_id)
        ->setOptions($this->configuration['aside'])
        ->addCacheableDependency($block);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    // Get the theme.
    $theme = $form_state->get('block_theme');

    $options = $this->getBlockOptions($theme);
    if (!empty($options)) {
      $form = parent::blockForm($form, $form_state);
      $form['block'] = [
        '#type' => 'select',
        '#title' => t('Block'),
        '#description' => t('The block to place within the aside element.'),
        '#options' => ['' => t('- None -')] + $options,
        '#default_value' => $this->configuration['block'],
        '#required' => TRUE,
      ];
    }
    else {
      $form['block']['#markup'] = $this->t('No blocks are available for selection.');
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['block'] = $values['block'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    if ($block = $this->loadBlock() && $this->uxAside) {
      $content = $this->entityTypeManager->getViewBuilder('block')->view($block);
      $build = $this->uxAside->setContent($content)->toRenderArray();
    }
    return $build;
  }

  /**
   * Load configured block.
   */
  protected function loadBlock() {
    if (!isset($this->block) && !empty($this->configuration['block'])) {
      $this->block = $this->entityTypeManager->getStorage('block')->load($this->configuration['block']);
    }
    return $this->block;
  }

  /**
   * Get available blocks as options.
   */
  protected function getBlockOptions($theme) {
    $theme_blocks = $this->entityTypeManager->getStorage('block')->loadByProperties(['theme' => $theme]);
    $options = [];
    if (!empty($theme_blocks)) {
      foreach ($theme_blocks as $block) {
        $options[$block->id()] = $block->label();
      }
    }
    return $options;
  }

}
