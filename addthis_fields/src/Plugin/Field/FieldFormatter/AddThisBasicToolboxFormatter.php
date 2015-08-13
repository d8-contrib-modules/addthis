<?php
/**
 * @file
 * Contains \Drupal\addthis_fields\Plugin\Field\FieldFormatter\AddThisBasicToolboxFormatter.
 */

namespace Drupal\addthis_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\addthis\AddThis;

/**
 * Plugin implementation of the 'addthis_basic_toolbox' formatter.
 *
 * @FieldFormatter(
 *   id = "addthis_basic_toolbox",
 *   label = @Translation("AddThis Basic Toolbox"),
 *   field_types = {
 *     "addthis"
 *   }
 * )
 */
class AddThisBasicToolboxFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'share_services' => 'facebook,twitter',
      'buttons_size' => 'addthis_16x16_style',
      'counter_orientation' => 'horizontal',
      'extra_css' => '',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $settings = $this->getSettings();
    $element = array();

    AddThis::getInstance()->getBasicToolboxForm($this, $settings);

    return $element;
  }


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $settings = $this->getSettings();

    $markup = AddThis::getInstance()->getBasicToolboxMarkup($settings);

    return array(
      '#markup' => $markup
    );
  }

}