<?php
/**
 * @file
 * Contains \Drupal\addthis\Plugin\Field\FieldFormatter\AddThisBasicToolboxFormatter.
 */

namespace Drupal\addthis\Plugin\Field\FieldFormatter;

use Drupal\addthis\AddThisBasicToolboxFormTrait;
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

  use AddThisBasicToolboxFormTrait;
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
    $element = $this->addThisBasicToolboxForm($settings);

    return $element;
  }


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {

    return array(
      '#type' => 'addthis_basic_toolbox',
      '#size' => $config['basic_toolbox']['buttons_size'],
      '#services' => $config['basic_toolbox']['share_services'],
      '#extra_classes' => $config['basic_toolbox']['extra_css'],
    );
  }

}