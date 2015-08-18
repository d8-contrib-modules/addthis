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
    );
  }


}