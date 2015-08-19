<?php
/**
 * @file
 * Contains \Drupal\addthis_block\Plugin\Block\AddThisBlock.
 */

namespace Drupal\addthis_block\Plugin\Block;

use Drupal\addthis\AddThisBasicButtonFormTrait;
use Drupal\addthis\AddThisBasicToolboxFormTrait;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides my custom block.
 *
 * @Block(
 *   id = "addthis_block",
 *   admin_label = @Translation("AddThis"),
 *   category = @Translation("Blocks")
 * )
 */
class AddThisBlock extends BlockBase {

  use AddThisBasicButtonFormTrait;
  use AddThisBasicToolboxFormTrait;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
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

  /**
   * {@inheritdoc}
   */
  function blockForm($form, FormStateInterface $form_state) {

    // The list of formatters.
    $add_this_service = \Drupal::service('addthis.addthis');
    $formatter_options = $add_this_service->getDisplayTypes();
    $settings = $this->getConfiguration();

    $type = $settings['type'];
    $rebuild = $form_state->getValue([
      'settings',
      'settings',
      'addthis_settings',
      'type'
    ]);
    if (isset($rebuild)) {
      $type = $form_state->getValue([
        'settings',
        'settings',
        'addthis_settings',
        'type'
      ]);
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
    if ($type == 'addthis_basic_toolbox') {
      $basicToolbox = $this->addThisBasicToolboxForm($this, $settings['basic_toolbox']);
      $form['settings']['addthis_settings']['type_settings']['basic_toolbox'] = $basicToolbox;
    }
    else {
      if ($type == 'addthis_basic_button') {
        $basicButton =  $this->addThisBasicButtonForm($this, $settings['basic_button']);
        $form['settings']['addthis_settings']['type_settings']['basic_button'] = $basicButton;
      }
    }


    return $form;
  }

  /**
   * Callback for AddThisBlock blockForm() to control sub-settings based on display type.
   * @param array $form
   * @param FormStateInterface $form_state
   * @return mixed
   */
  public function addthisAjaxCallback(array $form, FormStateInterface $form_state) {
    return $form['settings']['settings']['addthis_settings']['type_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

    //@TODO Settings for unselected type get wiped because they dont exist in form.
    // Try not to overwrite them if possible.
    $this->configuration['type'] = $form_state->getValue([
      'settings',
      'addthis_settings',
      'type'
    ]);

    //Handle saving the partial elements provided by AddThisBasicToolboxFormTrait.
    $basicToolboxKeys = $this->addThisBasicToolboxGetDefaults();
    foreach($basicToolboxKeys as $key => $value) {
      $this->configuration['basic_toolbox'][$key] = $form_state->getValue([
        'settings',
        'addthis_settings',
        'type_settings',
        'basic_toolbox',
        $key
      ]);
    }

    //Handle saving the partial elements provided by AddThisBasicButtonFormTrait.
    $basicButtonKeys = $this->addThisBasicButtonGetDefaults();
    foreach($basicButtonKeys as $key => $value) {
      $this->configuration['basic_button'][$key] = $form_state->getValue([
        'settings',
        'addthis_settings',
        'type_settings',
        'basic_button',
        $key
      ]);
    }



  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->configuration;

    switch ($config['type']) {
      case 'addthis_basic_button':
        return [
          '#type' => 'addthis_basic_button',
          '#size' => $config['basic_button']['button_size'],
        ];
        break;
      case 'addthis_basic_toolbox':
        return [
          '#type' => 'addthis_basic_toolbox',
          '#size' => $config['basic_toolbox']['buttons_size'],
          '#services' => $config['basic_toolbox']['share_services'],
        ];
        break;
    }

    return [
      '#markup' => ''
    ];
  }
}
