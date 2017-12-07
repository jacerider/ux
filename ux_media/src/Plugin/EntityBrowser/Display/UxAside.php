<?php

namespace Drupal\ux_media\Plugin\EntityBrowser\Display;

use Drupal\entity_browser\Plugin\EntityBrowser\Display\Modal;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\ux_aside\Ajax\UxAsideOpenCommand;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Presents entity browser in an Modal.
 *
 * @EntityBrowserDisplay(
 *   id = "ux_aside",
 *   label = @Translation("UX Aside"),
 *   description = @Translation("Displays the entity browser in a UX aside."),
 *   uses_route = TRUE
 * )
 */
class UxAside extends Modal {

  /**
   * {@inheritdoc}
   */
  public function displayEntityBrowser(array $element, FormStateInterface $form_state, array &$complete_form, array $persistent_data = []) {
    $display = parent::displayEntityBrowser($element, $form_state, $complete_form, $persistent_data);
    $display['open_modal']['#attached']['library'] = ['ux_media/ux_aside'];
    $display['open_modal']['#attached']['drupalSettings']['entity_browser']['ux_aside'] = $display['open_modal']['#attached']['drupalSettings']['entity_browser']['modal'];
    unset($display['open_modal']['#attached']['drupalSettings']['entity_browser']['modal']);
    return $display;
  }

  /**
   * Generates the content and opens the modal.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   An ajax response.
   */
  public function openModal(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $parents = $triggering_element['#parents'];
    array_pop($parents);
    $parents = array_merge($parents, ['path']);
    $input = $form_state->getUserInput();
    $src = NestedArray::getValue($input, $parents);

    $field_name = $triggering_element['#parents'][0];
    $element_name = $this->configuration['entity_browser_id'];
    $name = 'entity_browser_iframe_' . $element_name;

    $options = [
      'title' => $this->configuration['link_text'],
      'subtitle' => 'subtitle',
      'icon' => 'fa-camera',
      'width' => '100%',
      'attachTop' => TRUE,
      'transitionIn' => 'fadeInDown',
      'transitionOut' => 'fadeOutUp',
      'padding' => 20,
      'iframe' => TRUE,
      'iframeURL' => $src,
    ];
    if (!empty($this->configuration['height']) && is_numeric($this->configuration['height']) && $this->configuration['height'] > 90) {
      $content['#attributes']['height'] = $this->configuration['height'] - 90;
    }

    $response = new AjaxResponse();
    $response->addCommand(new UxAsideOpenCommand('', $options));
    return $response;
  }

  /**
   * KernelEvents::RESPONSE listener.
   *
   * Intercepts default response and injects response that will trigger JS to
   * propagate selected entities upstream.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   Response event.
   */
  public function propagateSelection(FilterResponseEvent $event) {
    $render = [
      'labels' => [
        '#markup' => 'Labels: ' . implode(', ', array_map(function (EntityInterface $item) {
          return $item->label();
        }, $this->entities)),
        '#attached' => [
          'library' => ['ux_media/ux_aside_selection'],
          'drupalSettings' => [
            'entity_browser' => [
              $this->pluginDefinition['id'] => [
                'entities' => array_map(function (EntityInterface $item) {
                  return [$item->id(), $item->uuid(), $item->getEntityTypeId()];
                }, $this->entities),
                'uuid' => $this->request->query->get('uuid'),
              ],
            ],
          ],
        ],
      ],
    ];

    $event->setResponse(new Response(\Drupal::service('bare_html_page_renderer')->renderBarePage($render, 'Entity browser', 'page')));
  }

}
