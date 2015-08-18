<?php

/**
 * @file
 * Contains \Drupal\addthis\Element\AddThisElement
 */
namespace Drupal\addthis\Element;

use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\Template\Attribute;

/**
 * Class AddThisElement
 *
 * @RenderElement("addthis_element")
 */
class AddThisElement extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#theme' => 'addthis_element',
      '#attributes' => array(),
      '#pre_render' => [
        [$class, 'preRender'],
      ],
    ];
  }

  /**
   * PreRender function for addthis_element
   *
   * @TODO: Figure out why twig doesn't handle #attributes OOTB.
   *
   * @param $element
   * @return mixed
   */
  public function preRender($element){
    $element['#attributes'] = new Attribute($element['#attributes']);
    return $element;
  }

}