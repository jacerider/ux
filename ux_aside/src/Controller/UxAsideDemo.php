<?php

namespace Drupal\ux_aside\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ux_aside\UxAsideManager;

/**
 * Class UxAsideDemo.
 *
 * @package Drupal\ux_aside\Controller
 */
class UxAsideDemo extends ControllerBase {

  /**
   * Drupal\ux_aside\UxAsideManager definition.
   *
   * @var \Drupal\ux_aside\UxAsideManager
   */
  protected $uxAsideManager;

  /**
   * Constructs a new UxAsideDemo object.
   */
  public function __construct(UxAsideManager $ux_aside_manager) {
    $this->uxAsideManager = $ux_aside_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('ux_aside.manager')
    );
  }

  /**
   * Demo.
   *
   * @return string
   *   Return Hello string.
   */
  public function demo() {
    $build = [];

    $content = [
      '#markup' => 'WHOA!',
    ];

    $divider = [
      '#markup' => '<hr>',
    ];

    $build[] = $divider;
    $build[] = $this->uxAsideManager->create('demo-1')
      ->setTriggerText('Simple')
      ->setTriggerIcon('fa-user')
      ->setContent($content)
      ->setOptions([
        'content' => [
          'icon' => 'fa-user',
        ],
      ])
      ->toRenderArray();

    $build[] = $divider;
    $build[] = $this->uxAsideManager->create('demo-2')
      ->setOptions([
        'trigger' => [
          'text' => 'Demo with Fullscreen Toggle',
          'icon' => 'fa-rebel',
          'iconOnly' => FALSE,
        ],
        'content' => [
          'title' => 'Rebel Force',
          'subtitle' => 'More powerful than Josh',
          'icon' => 'fa-rebel',
          'padding' => 20,
          'transitionIn' => 'flipInX',
          'transitionOut' => 'flipOutX',
          'fullscreen' => TRUE,
          // 'group' => 'poop',
        ],
      ])
      ->setContent($content)
      ->toRenderArray();

    $build[] = $divider;
    $build[] = $this->uxAsideManager->create('demo-3')
      ->setOptions([
        'trigger' => [
          'text' => 'Attach to Top',
          'icon' => 'fa-rebel',
          'iconOnly' => FALSE,
        ],
        'content' => [
          'title' => 'Rebel Force',
          'subtitle' => 'More powerful than Josh',
          'icon' => 'fa-rebel',
          'headerColor' => '#5F8E2B',
          'padding' => 20,
          'attachTop' => TRUE,
          'width' => '100%',
          'transitionIn' => 'fadeInDown',
          'transitionOut' => 'fadeOutUp',
          // 'group' => 'poop',
        ],
      ])
      ->setContent($content)
      ->toRenderArray();

    $build[] = $divider;
    $build[] = $this->uxAsideManager->create('demo-4')
      ->setOptions([
        'trigger' => [
          'text' => 'Attach to Bottom',
          'icon' => 'fa-empire',
          'iconOnly' => FALSE,
        ],
        'content' => [
          'title' => 'Rebel Force',
          'subtitle' => 'More powerful than Josh',
          'icon' => 'fa-empire',
          'headerColor' => '#ffffff',
          'theme' => 'light',
          'padding' => 20,
          'attachBottom' => TRUE,
          'transitionIn' => 'bounceInUp',
          'transitionOut' => 'bounceOutDown',
          // 'group' => 'poop',
        ],
      ])
      ->setContent($content)
      ->toRenderArray();

    $build[] = $divider;
    $build[] = $this->uxAsideManager->create('demo-5')
      ->setOptions([
        'trigger' => [
          'text' => 'Attach to Left',
          'icon' => 'fa-empire',
          'iconOnly' => FALSE,
        ],
        'content' => [
          'title' => 'Rebel Force',
          'subtitle' => 'More powerful than Josh',
          'headerColor' => '#1F738B',
          'padding' => 20,
          'attachLeft' => TRUE,
          'transitionIn' => 'fadeInLeft',
          'transitionOut' => 'fadeOutLeft',
          // 'group' => 'poop',
        ],
      ])
      ->setContent($content)
      ->toRenderArray();

    $build[] = $divider;
    $build[] = $this->uxAsideManager->create('demo-6')
      ->setOptions([
        'trigger' => [
          'text' => 'Attach to Right',
          'icon' => 'fa-empire',
          'iconOnly' => FALSE,
        ],
        'content' => [
          'title' => 'Rebel Force',
          'subtitle' => 'More powerful than Josh',
          'headerColor' => '#1F738B',
          'padding' => 20,
          'attachRight' => TRUE,
          'transitionIn' => 'fadeInRight',
          'transitionOut' => 'fadeOutRight',
          // 'group' => 'poop',
        ],
      ])
      ->setContent($content)
      ->toRenderArray();

    $build[] = $divider;
    $build[] = $this->uxAsideManager->create('demo-7')
      ->setOptions([
        'trigger' => [
          'text' => 'Auto Open',
          'icon' => 'fa-empire',
          'iconOnly' => FALSE,
        ],
        'content' => [
          'title' => 'Rebel Force',
          'subtitle' => 'More powerful than Josh',
          'headerColor' => '#85078B',
          'padding' => 40,
          'width' => 900,
          // 'autoOpen' => TRUE,
          'timeout' => 5000,
          'timeoutProgressbar' => TRUE,
          // 'group' => 'poop',
        ],
      ])
      ->setContent($content)
      ->toRenderArray();

    $build[] = $divider;
    $build[] = $this->uxAsideManager->create('demo-8')
      ->setOptions([
        'trigger' => [
          'text' => 'Auto Fullscreen',
          'icon' => 'fa-empire',
          'iconOnly' => FALSE,
        ],
        'content' => [
          'title' => 'Rebel Force',
          'subtitle' => 'More powerful than Josh',
          'headerColor' => '#103F8B',
          'padding' => 40,
          'openFullscreen' => TRUE,
          // 'autoOpen' => TRUE,
          // 'timeout' => 5000,
          // 'timeoutProgressbar' => TRUE,
          // 'group' => 'poop',
          'transitionIn' => 'fadeInUp',
          'transitionOut' => 'fadeOutUp',
        ],
      ])
      ->setContent($content)
      ->toRenderArray();

    return $build;
  }

}
