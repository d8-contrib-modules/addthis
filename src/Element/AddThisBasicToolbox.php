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


  public static function preRender($element) {
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
      $attributes = [
        'class' => ['addthis_button_' . $service]
      ];
      switch ($service) {
        case 'linkedin_counter':
          $attributes['li:counter'] = $isvertical ? 'top' : '';
          break;
        case 'facebook_like':
          $attributes['fb:like:layout'] = $isvertical ? 'box_count' : 'button_count';
          break;
        case 'facebook_share':
          $attributes['fb:share:layout'] = $isvertical ? 'box_count' : 'button_count';
          break;
        case 'google_plusone':
          $attributes['g:plusone:size'] = $isvertical ? 'tall' : 'standard';
          break;
        case 'tweet':
          $attributes['tw:count'] = $isvertical ? 'vertical' : 'horizontal';
          // $attributes['tw:via'] = $isvertical ? 'vertical' : 'horizontal'; // TODO: D7 used AddThis::getInstance()->getTwitterVia()
          break;
        case 'bubble_style':
          $attributes['class'][] = 'addthis_counter';
          $attributes['class'][] = 'addthis_bubble_style';
          break;
        case 'pill_style':
          $attributes['class'][] = 'addthis_counter';
          $attributes['class'][] = 'addthis_pill_style';
          break;
      }

      $element['services'][$key]['attributes'] = new Attribute($attributes);

    }


    return $element;
  }

}