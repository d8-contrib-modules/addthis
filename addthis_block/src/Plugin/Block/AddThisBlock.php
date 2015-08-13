<?php
/**
 * @file
 * Contains \Drupal\addthis_block\Plugin\Block\AddThisBlock.
 */

namespace Drupal\addthis_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\addthis\AddThis;

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
  public function defaultConfiguration()
  {
    return array(
      'type' => 'addthis_disabled',
      'basic_toolbox' => array(
        'share_services' => 'facebook,twitter',
        'buttons_size' => 'addthis_16x16_style',
        'counter_orientation' => 'horizontal',
        'extra_css' => '',
      ),
      'basic_button' => array(
        'buttons_size' => 'addthis_16x16_style',
        'extra_css' => '',
      ),
    );
  }


  function blockForm($form, FormStateInterface $form_state)
  {

    // The list of formatters.
    $formatter_options = AddThis::getInstance()->getDisplayTypes();
    $settings = $this->getConfiguration();

    $type = $settings['type'];
    $rebuild = $form_state->getValue(['settings', 'settings', 'addthis_settings', 'type']);
    if( isset($rebuild) ){
      $type = $form_state->getValue(['settings', 'settings', 'addthis_settings', 'type']);
    }

    $form['settings']['addthis_settings'] = array(
      '#type' => 'fieldset',
      '#title' => 'Display settings',

    );

    $form['settings']['addthis_settings']['type'] = array(
      '#type' => 'select',
      '#title' => t('Formatter for @title', array('@title' => 'AddThis block')),
      '#title_display' => 'invisible',
      '#options' => $formatter_options,
      '#default_value' => $settings['type'],
      '#attributes' => array('class' => array('addthis-display-type')),
      '#ajax' => array(
        'callback' => array($this, 'addthisAjaxCallback'),
        'wrapper' => 'addthis_type_settings',
      ),
    );
    $form['settings']['addthis_settings']['type_settings'] = array(
      '#prefix' => '<div id="addthis_type_settings"',
      '#suffix' => '</div>',
    );
    if($type == 'addthis_basic_toolbox'){
      $basicToolbox = AddThis::getInstance()->getBasicToolboxForm($this, $settings['basic_toolbox']);
      $form['settings']['addthis_settings']['type_settings']['basic_toolbox'] = $basicToolbox;
    }
    else if ($type == 'addthis_basic_button'){
      $basicButton = AddThis::getInstance()->getBasicButtonForm($this, $settings['basic_button']);
      $form['settings']['addthis_settings']['type_settings']['basic_button'] = $basicButton;
    }


    return $form;
  }

  public function addthisAjaxCallback(array $form, FormStateInterface $form_state){
    return $form['settings']['settings']['addthis_settings']['type_settings'];
  }

  public function blockSubmit($form, FormStateInterface $form_state)
  {
    $this->configuration['type'] = $form_state->getValue(['settings', 'addthis_settings', 'type']);
    $this->configuration['basic_toolbox']['share_services'] = $form_state->getValue(['settings', 'addthis_settings', 'type_settings', 'basic_toolbox', 'share_services']);
    $this->configuration['basic_toolbox']['buttons_size'] = $form_state->getValue(['settings', 'addthis_settings', 'type_settings', 'basic_toolbox', 'buttons_size']);
    $this->configuration['basic_toolbox']['counter_orientation'] = $form_state->getValue(['settings', 'addthis_settings', 'type_settings', 'basic_toolbox', 'counter_orientation']);
    $this->configuration['basic_toolbox']['extra_css'] = $form_state->getValue(['settings', 'addthis_settings', 'type_settings', 'basic_toolbox', 'extra_css']);
    $this->configuration['basic_button']['button_size'] = $form_state->getValue(['settings', 'addthis_settings', 'type_settings', 'basic_button', 'button_size']);
    $this->configuration['basic_button']['extra_css'] = $form_state->getValue(['settings', 'addthis_settings', 'type_settings', 'basic_button', 'extra_css']);

  }

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $config = $this->configuration;
    switch($config['type']){
      case 'addthis_basic_button':
        $markup = AddThis::getInstance()->getBasicButtonMarkup($config['basic_button']);
        break;
      case 'addthis_basic_toolbox':
        $markup = AddThis::getInstance()->getBasicToolboxMarkup($config['basic_toolbox']);
        break;
      default:
        $markup = '';
        break;
    }

    return array(
      '#markup' => $markup
    );

  }



}

?>