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
      '#pre_render' => [
        [$class, 'preRenderAddThisElement'],
      ]
    ];
  }


  public static function preRenderAddThisElement($element){
    if (!isset($element['#value'])) {
      $element['addthis_element'] = [
        '#markup' => '<' . $element['#tag'] . new Attribute($element['#attributes']) . " />\n"
        ];
      return $element;
    }

    $output = '<' . $element['#tag'] . new Attribute($element['#attributes']) . '>';
    if (isset($element['#value_prefix'])) {
      $output .= $element['#value_prefix'];
    }
    $output .= $element['#value'];
    if (isset($element['#value_suffix'])) {
      $output .= $element['#value_suffix'];
    }
    $output .= '</' . $element['#tag'] . ">\n";
    $element['addthis_element'] = [
      '#markup' => $output,
      ];

    return $element;
  }

}