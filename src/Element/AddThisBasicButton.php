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
      '#size' => 'small',
      '#pre_render' => [
        [$class, 'preRender'],
      ],
    ];
  }

  public function preRender($element) {
    // Add button.
    $button_img = 'http://s7.addthis.com/static/btn/sm-share-en.gif';
    if ($element['size'] === 'big') {
      $button_img = 'http://s7.addthis.com/static/btn/v2/lg-share-en.gif';
    }

    // Create img button.
    $element['image'] = [
      '#theme' => 'image',
      '#uri' => $button_img,
      '#alt' => t('Share page with AddThis'),
    ];

    // Add Script.
    // TODO: Figure out what JS should be attached here.
    $config_factory = \Drupal::getContainer()->get('config.factory');
    $config = $config_factory->get('addthis.settings');
    $adv_config = $config_factory->get('addthis.settings.advanced');

    $element['#attached']['library'][] = 'addthis/addthis.widget';

    /**
     * Every setting value passed here overrides previously set values but
     * leaves the values that are already set somewhere else and that are not
     * passed here.
     */
    $element['#attached']['drupalSettings']['addthis'] = [];

    return $element;
  }

}