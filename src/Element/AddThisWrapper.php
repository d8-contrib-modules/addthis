<?php

/**
 * @file
 * Contains \Drupal\addthis\Element\AddThisWrapper
 */
namespace Drupal\addthis\Element;

use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Render\Element;

/**
 * Class AddThisElement
 *
 * @RenderElement("addthis_wrapper")
 */
class AddThisWrapper extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return array(
      '#theme' => 'addthis_wrapper',
      '#pre_render' => [
        [$class, 'preRender'],
      ],
    );
  }


  public function preRender($element){
    //$element['#attributes'] = new Attribute($element['#attributes']);
//    $children = Element::children($element);
//    $element['services'] = [];
//    if (count($children) > 0) {
//      foreach ($children as $child) {
//        //@TODO Figure out why we need to call render() here and it isn't handled
//        //by printing {{element}} in the twig template.
//        $element['services'][] = render($element[$child]);
//      }
//    }

    return $element;
  }

}