<?php

/**
 * @file
 * Contains \Drupal\addthis\Element\AddThisBasicToolbox
 */
namespace Drupal\addthis\Element;

use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\Template\Attribute;

/**
 * Class AddThisBasicToolbox
 *
 * @RenderElement("addthis_basic_toolbox")
 */
class AddThisBasicToolbox extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    $config = $this->configuration;
    return [
      '#theme' => 'addthis_basic_toolbox',
      '#size' => 'addthis_16x16_style',
      '#services' => 'facebook,twitter',
      '#extra_classes' => '',
      '#counter_orientation' => 'horizontal',
      '#pre_render' => [
        [$class, 'preRender'],
      ],
    ];
  }


  public function preRender($element) {
    // Add Script.
    $script_manager = \Drupal::getContainer()->get('addthis.script_manager');
    $script_manager->attachJsToElement($element);

    $services = trim($element['#services']);
    $services = str_replace(' ', '', $services);
    $services = explode(',', $services);

    // Orientation
    if ($element['#counter_orientation'] == 'vertical') {
      $isvertical = TRUE;
    }

    foreach ($services as $key => $service) {
      $element['services'][$key] = array();
      $element['services'][$key]['service'] = $service;
      switch ($service) {
        case 'linkedin_counter':
          $element['services'][$key]['attributes'] = new Attribute(array('li:counter' => ($isvertical ? 'top' : '')));
          break;
        case 'facebook_like':
          $element['services'][$key]['attributes'] = new Attribute(array('fb:like:layout' => ($isvertical ? 'box_count' : 'button_count')));
          break;
        case 'facebook_share':
          $element['services'][$key]['attributes'] = new Attribute(array('fb:share:layout' => ($isvertical ? 'box_count' : 'button_count')));
          break;
        case 'google_plusone':
          $element['services'][$key]['attributes'] = new Attribute(array('g:plusone:size' => ($isvertical ? 'tall' : 'standard')));
          break;
        case 'tweet':
          $element['services'][$key]['attributes'] = new Attribute(array(
            'tw:count' => ($isvertical ? 'vertical' : 'horizontal'),
            'tw:via' => '' // TODO: D7 used AddThis::getInstance()->getTwitterVia()
          ));
          break;
        /*
         * case 'bubble_style':
          $element['services'][$key]['attributes'] = new Attribute(array(
            'addthis_counter',
            'addthis_bubble_style'
          ));
          break;
        case 'pill_style':
          $element['services'][$key]['attributes'] = new Attribute(array(
            'addthis_counter',
            'addthis_pill_style'
          ));
          break;
        */
      }
    }


    return $element;
  }

}