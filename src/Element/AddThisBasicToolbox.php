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
      '#extra_classes' => $config['basic_toolbox']['extra_css'],
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
    $element['services'] = $services;

    return $element;
  }

}