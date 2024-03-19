<?php

namespace Drupal\ux_dialog\Render\MainContent;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Render\MainContent\DialogRenderer;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\ux_dialog\Ajax\OpenUxDialogCommand;

/**
 * Default main content renderer for offcanvas dialog requests.
 */
class UxDialogRenderer extends DialogRenderer {

  /**
   * {@inheritdoc}
   */
  public function renderResponse(array $main_content, Request $request, RouteMatchInterface $route_match) {
    $response = new AjaxResponse();

    // First render the main content, because it might provide a title.
    $content = $this->renderer->renderRoot($main_content);

    // Attach the library necessary for using the OpenUxDialogCommand and
    // set the attachments for this Ajax response.
    $main_content['#attached']['library'][] = 'ux_dialog/ux_dialog';
    $response->setAttachments($main_content['#attached']);

    // If the main content doesn't provide a title, use the title resolver.
    $title = isset($main_content['#title']) ? $main_content['#title'] : $this->titleResolver->getTitle($request, $route_match->getRouteObject());

    // Determine the title: use the title provided by the main content if any,
    // otherwise get it from the routing information.
    $options = $this->getDialogOptions($request);

    $response->addCommand(new OpenUxDialogCommand($title, $content, $options));
    return $response;
  }

}
