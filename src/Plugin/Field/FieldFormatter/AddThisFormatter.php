<?php
/**
 * @file
 * Contains \Drupal\addthis\Plugin\Field\FieldFormatter\AddThisFormatter.
 */

namespace Drupal\addthis\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'addthis_disabled' formatter.
 *
 * @FieldFormatter(
 *   id = "addthis_disabled",
 *   label = @Translation("AddThis Disabled"),
 *   field_types = {
 *     "addthis"
 *   }
 * )
 */
class AddThisFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    //@TODO Implement viewElements()
    $element = array();
    $display_type = 'addthis_disabled';


    $markup = array(
      '#display' => array(),
    );
  }

}