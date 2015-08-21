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
      '#size' => $config['basic_toolbox']['buttons_size'],
      '#services' => $config['basic_toolbox']['share_services'],
      '#pre_render' => [
        [$class, 'preRender'],
      ],
    ];
  }

  public function preRender($element) {
    $element['addthis_basic_toolbox'] = array(
      '#type' => 'addthis_wrapper',
      '#tag' => 'div',
      '#attributes' => array(
        'class' => array(
          'addthis_toolbox',
          'addthis_default_style',
         // ($settings['buttons_size'] == AddThis::CSS_32x32 ? AddThis::CSS_32x32 : NULL),
          //$settings['extra_css'],
        ),
      ),
    );

    // Add Script.
    $element['#attached']['library'][] = 'addthis/addthis.widget';

    $script_manager = \Drupal::getContainer()->get('addthis.script_manager');

    $addThisConfig = $script_manager->getAddThisConfig();
    $addThisShareConfig = $script_manager->getAddThisShareConfig();

    $element['#attached']['drupalSettings']['addThisWidget'] = [
      'widgetScript' => 'http://example.dev/thing.js',
      'config' => $addThisConfig,
      'share' => $addThisShareConfig,
    ];

    $services = trim($element['#services']);
    $services = str_replace(' ', '', $services);
    $services = explode(',', $services);
    // All service elements
    $items = array();
    foreach ($services as $service) {
      $items[$service] = array(
        '#type' => 'addthis_element',
        '#tag' => 'a',
        'value' => '',
        '#attributes' => array(
          //'href' => $script_manager->getBaseBookmarkUrl(),
          'class' => array(
            'addthis_button_' . $service,
          ),
        ),
        '#addthis_service' => $service,
      );

      // Add individual counters.
      if (strpos($service, 'counter_') === 0) {
        $items[$service]['#attributes']['class'] = array("addthis_$service");
      }

      // Basic implementations of bubble counter orientation.
      // @todo Figure all the bubbles out and add them.
      //   Still missing: tweetme, hyves and stubleupon, google_plusone_badge.
      //
      $orientation = '';//($settings['counter_orientation'] == 'horizontal' ? TRUE : FALSE);
      switch ($service) {
        case 'linkedin_counter':
          $items[$service]['#attributes'] += array(
            'li:counter' => ($orientation ? '' : 'top'),
          );
          break;
        case 'facebook_like':
          $items[$service]['#attributes'] += array(
            'fb:like:layout' => ($orientation ? 'button_count' : 'box_count')
          );
          break;
        case 'facebook_share':
          $items[$service]['#attributes'] += array(
            'fb:share:layout' => ($orientation ? 'button_count' : 'box_count')
          );
          break;
        case 'google_plusone':
          $items[$service]['#attributes'] += array(
            'g:plusone:size' => ($orientation ? 'standard' : 'tall')
          );
          break;
        case 'tweet':
          $items[$service]['#attributes'] += array(
            'tw:count' => ($orientation ? 'horizontal' : 'vertical'),
            'tw:via' => $script_manager->getTwitterVia(),
          );
          break;
        case 'bubble_style':
          $items[$service]['#attributes']['class'] = array(
            'addthis_counter',
            'addthis_bubble_style'
          );
          break;
        case 'pill_style':
          $items[$service]['#attributes']['class'] = array(
            'addthis_counter',
            'addthis_pill_style'
          );
          break;
      }
    }

    $element['addthis_basic_toolbox'] += $items;

    return $element;
  }

}