<?php
/**
 * @file
 * Contains \Drupal\addthis_fields\Plugin\Field\FieldFormatter\AddThisBasicButtonFormatter.
 */

namespace Drupal\addthis_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\addthis\AddThis;
use Drupal\addthis\Services\AddThisScriptManager;


/**
 * Plugin implementation of the 'addthis_basic_button' formatter.
 *
 * @FieldFormatter(
 *   id = "addthis_basic_button",
 *   label = @Translation("AddThis Basic Button"),
 *   field_types = {
 *     "addthis"
 *   }
 * )
 */
class AddThisBasicButtonFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'button_size' => 'small',
      'extra_css' => '',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = array();

    $element['button_size'] = array(
      '#title' => t('Image'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('button_size'),
      '#options' => array(
        'small' => t('Small'),
        'big' => t('Big'),
      ),
    );
    $element['extra_css'] = array(
      '#title' => t('Extra CSS declaration'),
      '#type' => 'textfield',
      '#size' => 40,
      '#default_value' => $this->getSetting('extra_css'),
      '#description' => t('Specify extra CSS classes to apply to the button'),
    );

    return $element;
  }


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    //@TODO Implement viewElements()
    $addthis = AddThis::getInstance();
    //$settings = $options['#display']['settings'];

    $button_img = 'http://s7.addthis.com/static/btn/sm-share-en.gif';
    if (isset($settings['buttons_size']) && $settings['buttons_size'] == 'big') {
      $button_img = 'http://s7.addthis.com/static/btn/v2/lg-share-en.gif';
    }
    //$button_img = $addthis->transformToSecureUrl($button_img);

    //$extra_css = isset($settings['extra_css']) ? $settings['extra_css'] : '';
    $element = array(
      '#type' => 'addthis_wrapper',
      '#tag' => 'a',
      '#attributes' => array(
        'class' => array(
          'addthis_button',
        ),
      ),
    );
    //$element['#attributes'] += $addthis->getAddThisAttributesMarkup($options);

    // Add the widget script.
    $script_manager = AddThisScriptManager::getInstance();
    $script_manager->attachJsToElement($element);

    // Create img button.
    $image = array(
      '#type' => 'addthis_element',
      '#tag' => 'img',
      '#attributes' => array(
        'src' => $button_img,
        'alt' => t('Share page with AddThis'),
      ),
    );
    $element[] = $image;

    return $element;
  }




}