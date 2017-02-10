```php
$uxOffcanvasManager = \Drupal::service('ux_offcanvas.manager');
$content['#markup'] = 'Hello World';
$nested = $uxOffcanvasManager->addOffcanvas('nested', 'Nested', 'Nested Markup')->setPosition('right');

$variables['content']['left'] = $uxOffcanvasManager
  ->addOffcanvas('left_of-cool', 'Left Trigger', $nested->toRenderableTrigger())
  ->setSize(500)
  ->toRenderableTrigger();

$variables['content']['right'] = $uxOffcanvasManager
  ->addOffcanvas('right_of-cool', 'Right Trigger', $content)
  ->setPosition('right')
  ->toRenderableTrigger();

$variables['content']['top'] = $uxOffcanvasManager
  ->addOffcanvas('top_of-cool', 'Top Trigger', $content)
  ->setPosition('top')
  ->setSize(1500)
  ->toRenderableTrigger();

$variables['content']['bottom'] = $uxOffcanvasManager
  ->addOffcanvas('bottom_of-cool', 'Bottom Trigger', $content)
  ->setPosition('bottom')
  ->toRenderableTrigger();
```
