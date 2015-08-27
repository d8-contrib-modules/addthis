<?php

/**
 * @file
 * Contains \Drupal\addthis\Element\AddThisBasicButton
 */
namespace Drupal\addthis\Element;

use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\Template\Attribute;

/**
 * Class AddThisBasicButton
 *
 * @RenderElement("addthis_basic_button")
 */
class AddThisBasicButton extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#theme' => 'addthis_basic_button',
      '#size' => 'addthis_16x16_style',
      '#extra_classes' => '',
      '#pre_render' => [
        [$class, 'preRender'],
      ],
    ];
  }

  public static function preRender($element) {
    // Add button.
    $button_img = 'http://s7.addthis.com/static/btn/sm-share-en.gif';
    if ($element['#size'] === 'big') {
      $button_img = 'http://s7.addthis.com/static/btn/v2/lg-share-en.gif';
    }

    // Create img button.
    $element['image'] = [
      '#theme' => 'image',
      '#uri' => $button_img,
      '#alt' => t('Share page with AddThis'),
    ];


    $script_manager = \Drupal::getContainer()->get('addthis.script_manager');
    $script_manager->attachJsToElement($element);

    return $element;
  }

}