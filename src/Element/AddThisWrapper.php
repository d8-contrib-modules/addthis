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
      '#pre_render' => array(
        array($class, 'preRenderAddThisWrapper'),
      )
    );
  }

  /**
   * Implements preRenderAddThisWrapper()
   *   - Defines consistent markup for the addthis_wrapper render element.
   * @param $element
   * @return mixed
   */
  public static function preRenderAddThisWrapper($element){
    $output = '<' . $element['#tag'] . new Attribute($element['#attributes']) . '>';
    $children = Element::children($element);
     if (count($children) > 0) {
       foreach ($children as $child) {
         $output .= render($element[$child]);
       }
     }
    $output .= '</' . $element['#tag'] . ">  \n";
    $element['addthis_wrapper'] = [
      '#markup' => $output
      ];

    return $element;
  }

}