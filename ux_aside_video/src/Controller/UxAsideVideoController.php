<?php

namespace Drupal\ux_aside_video\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\ux_aside\Ajax\UxAsideOpenCommand;
use Drupal\ux_aside_video\Plugin\Field\FieldFormatter\UxAsideVideo;

/**
 * Class UxAsideVideoController.
 */
class UxAsideVideoController extends ControllerBase {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs a new UxAsideVideoController object.
   */
  public function __construct(EntityTypeManager $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * View.
   *
   * @return string
   *   Return Hello string.
   */
  public function view($entity_type, $entity_id, $langcode, $view_mode, $field_name, $delta) {
    $build = [];

    $entity = $this->entityTypeManager->getStorage($entity_type)->load($entity_id);
    if ($entity && $entity->hasField($field_name) && ($value = $entity->{$field_name}->get($delta))) {
      // Load display.
      $display = $this->entityTypeManager
        ->getStorage('entity_view_display')
        ->load($entity_type . '.' . $entity->bundle() . '.' . $view_mode);
      $renderer = $display->getRenderer($field_name);

      // Make sure we are making this call for a field that supports this
      // callback.
      if ($renderer instanceof UxAsideVideo) {
        // Get render array for all videos.
        $videos = $renderer->getVideoFormatter()->viewElements($entity->{$field_name}, $langcode);
        if (isset($videos[$delta])) {
          $build = $videos[$delta];

          // Field settings and set aside options.
          $settings = $renderer->getSettings();
          $options = isset($settings['aside']['content']) ? $settings['aside']['content'] : [];
        }
      }
    }

    $response = new AjaxResponse();
    $response->addCommand(new UxAsideOpenCommand($build, $options));
    return $response;
  }

}
