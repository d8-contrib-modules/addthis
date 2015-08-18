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
    ];
  }


}