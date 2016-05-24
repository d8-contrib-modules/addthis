<?php
/**
 * @file
 * Contains \Drupal\addthis\Plugin\Block\AddThisBlock.
 */

namespace Drupal\addthis\Plugin\Block;

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
      'display_type' => 'addthis_basic_button',
      'basic_toolbox' => $this->addThisBasicToolboxGetDefaults(),
      'basic_button' => $this->addThisBasicButtonGetDefaults(),
    );
  }

  /**
   * {@inheritdoc}
   */
  function blockForm($form, FormStateInterface $form_state) {
    $settings = $this->getConfiguration();

    //get type from config
    $default_type = $settings['display_type'];

    $selected_type = $form_state->getValue([
      'settings',
      'addthis_settings',
      'display_type'
    ]);

    $selected_type = isset($selected_type) ? $selected_type : $default_type;


    $form['addthis_settings'] = array(
      '#type' => 'fieldset',
      '#title' => 'Display settings',

    );

    $form['addthis_settings']['display_type'] = array(
      '#type' => 'select',
      '#title' => t('Formatter for @title', array('@title' => 'AddThis block')),
      '#title_display' => 'invisible',
      '#options' => [
        'addthis_basic_button' => 'AddThis Basic Button',
        'addthis_basic_toolbox' => 'AddThis Basic Toolbox',
      ],
      '#default_value' => isset($default_type) ? $default_type : 'addthis_basic_button',
      '#attributes' => array('class' => array('addthis-display-type')),
      '#ajax' => array(
        'callback' => array($this, 'addthisAjaxCallback'),
        'wrapper' => 'addthis_type_settings',
      ),
    );
    $form['addthis_settings']['type_settings'] = array(
      '#prefix' => '<div id="addthis_type_settings"',
      '#suffix' => '</div>',
    );
    if (isset($selected_type) && $selected_type == 'addthis_basic_toolbox') {
      $basicToolbox = $this->addThisBasicToolboxForm($settings['basic_toolbox']);
      $form['addthis_settings']['type_settings']['basic_toolbox'] = $basicToolbox;
    }
    else {
      if (isset($selected_type) && $selected_type == 'addthis_basic_button') {
        $basicButton = $this->addThisBasicButtonForm($settings['basic_button']);
        $form['addthis_settings']['type_settings']['basic_button'] = $basicButton;
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
    return $form['settings']['addthis_settings']['type_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

    //@TODO Settings for unselected type get wiped because they dont exist in form.
    // Try not to overwrite them if possible.
    $this->configuration['display_type'] = $form_state->getValue([
      'addthis_settings',
      'display_type'
    ]);

    //Handle saving the partial elements provided by AddThisBasicToolboxFormTrait.
    $basicToolboxKeys = $this->addThisBasicToolboxGetDefaults();
    foreach ($basicToolboxKeys as $key => $value) {
      $this->configuration['basic_toolbox'][$key] = $form_state->getValue([
        'addthis_settings',
        'type_settings',
        'basic_toolbox',
        $key
      ]);
    }

    //Handle saving the partial elements provided by AddThisBasicButtonFormTrait.
    $basicButtonKeys = $this->addThisBasicButtonGetDefaults();
    foreach ($basicButtonKeys as $key => $value) {
      $this->configuration['basic_button'][$key] = $form_state->getValue([
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

    switch ($config['display_type']) {
      case 'addthis_basic_button':
        return [
          '#type' => 'addthis_basic_button',
          '#size' => $config['basic_button']['button_size'],
          '#extra_classes' => $config['basic_button']['extra_css'],
        ];
        break;
      case 'addthis_basic_toolbox':
        return [
          '#type' => 'addthis_basic_toolbox',
          '#size' => $config['basic_toolbox']['buttons_size'],
          '#services' => $config['basic_toolbox']['share_services'],
          '#extra_classes' => $config['basic_toolbox']['extra_css'],
          '#counter_orientation' => $config['basic_toolbox']['counter_orientation'],
        ];
        break;
    }

    return [
      '#markup' => ''
    ];
  }
}
