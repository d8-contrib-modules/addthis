<?php
/**
 * @file
 * Contains \Drupal\addthis\Plugin\Block\AddThisBlock.
 */

namespace Drupal\addthis\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\addthis\AddThis;
use Drupal\addthis\Services\AddThisScriptManager;

/**
 * Provides my custom block.
 *
 * @Block(
 *   id = "addthis_block",
 *   admin_label = @Translation("AddThis"),
 *   category = @Translation("Blocks")
 * )
 */
class AddThisBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'type' => 'addthis_disabled',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    //@TODO Implement block markup

    $widget_type = AddThis::getInstance()->getBlockDisplayType();
    $widget_settings = AddThis::getInstance()->getBlockDisplaySettings();

    $markup = AddThis::getInstance()->getDisplayMarkup('addthis_basic_button');
    $formatters = \Drupal::Service('plugin.manager.field.formatter')->getDefinitions();
    //$markup = 'Testing Markup';


    $button_img = 'http://s7.addthis.com/static/btn/sm-share-en.gif';
    $element = array(
      '#theme' => 'addthis_wrapper',
      '#tag' => 'a',
      '#attributes' => array(
        'class' => array(
          'addthis_button',
        ),
      ),
    );

    $test = drupal_render($element);

    // Add the widget script.
    $script_manager = AddThisScriptManager::getInstance();
    $script_manager->attachJsToElement($element);

    // Create img button.
    $image = array(
      '#theme' => 'addthis_element',
      '#tag' => 'img',
      '#attributes' => array(
        'src' => $button_img,
        'alt' => t('Share page with AddThis'),
      ),
    );

    $element[] = $image;

    $markup = render($element);
    return array(
      'content' => $markup,
    );
  }

  function blockForm($form, FormStateInterface $form_state) {
    //@TODO: Implement block form.
    $form['settings']['addthis_settings'] = array(
      '#type' => 'fieldset',
      '#title' => 'Display settings',
    );

    // Retrieve settings.
    $addthis_type = AddThis::getInstance()->getBlockDisplayType();
    $addthis_settings = AddThis::getInstance()->getBlockDisplaySettings();


    // The list of formatters.
    $formatter_options = AddThis::getInstance()->getDisplayTypes();

    $form['settings']['addthis_settings']['type'] = array(
      '#type' => 'select',
      '#title' => t('Formatter for @title', array('@title' => 'AddThis block')),
      '#title_display' => 'invisible',
      '#options' => $formatter_options,
      '#default_value' => $this->configuration['type'],
      '#attributes' => array('class' => array('addthis-display-type')),
    );
    return $form;
  }
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['type'] = $form_state->getValue('type');
  }

}

?>