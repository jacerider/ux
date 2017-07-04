<?php

namespace Drupal\ux_menu\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides block plugin definitions for custom menus.
 *
 * @see \Drupal\system\Plugin\Block\SystemMenuBlock
 */
class UxMenuAsideBlock extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The menu storage.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs new SystemMenuBlock.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    if ($this->moduleHandler->moduleExists('ux_aside')) {
      $this->derivatives['ux_aside'] = $base_plugin_definition;
      $this->derivatives['ux_aside']['admin_label'] = 'UX | Menu | Aside';
      $this->derivatives['ux_aside']['config_dependencies']['module'][] = 'ux_aside';
    }
    return $this->derivatives;
  }

}
