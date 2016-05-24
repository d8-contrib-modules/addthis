<?php

/**
 * @file
 * Contains \Drupal\addthis\AddThisBasicToolboxFormTrait.
 *
 */

namespace Drupal\addthis;


/**
 * Class AddThisBasicToolboxFormTrait
 * @package Drupal\addthis
 */
trait AddThisBasicToolboxFormTrait {


  /**
   * Returns keys for accessing elements in the partial form.
   * @return array - list of Key:Value defaults defined by the AddThisBasicToolboxFormTrait
   */
  protected function addThisBasicToolboxGetDefaults(){
    return [
      'share_services' => 'facebook,twitter',
      'buttons_size' => 'addthis_16x16_style',
      'counter_orientation' => 'horizontal',
      'extra_css' => '',
    ];
  }

  /**
   * Returns partial configuration form for the AddThisBasicToolbox.
   *
   * @param $parent_class - The class that is requesting the form.
   * @param $options - The base configuration for the class. (Block/Field)
   *
   * @return array - Partial form to provide configuration.
   */
  protected function addThisBasicToolboxForm($options){
    $element = [];

    $element['share_services'] = [
      '#title' => t('Services'),
      '#type' => 'textfield',
      '#size' => 80,
      '#default_value' => $options['share_services'],
      '#required' => TRUE,
      //Validate function is defined in addthis.module.
      '#element_validate' => [
        get_class() . '::addThisDisplayElementServicesValidate'
      ],
      '#description' =>
        t('Specify the names of the sharing services and seperate them with a , (comma). <a href="http://www.addthis.com/services/list" target="_blank">The names on this list are valid.</a>') .
        t('Elements that are available but not ont the services list are (!services).',
          ['!services' => 'bubble_style, pill_style, tweet, facebook_send, twitter_follow_native, google_plusone, stumbleupon_badge, counter_* (several supported services), linkedin_counter']
        ),
    ];
    $element['buttons_size'] = [
      '#title' => t('Buttons size'),
      '#type' => 'select',
      '#default_value' => $options['buttons_size'],
      '#options' => [
        'addthis_16x16_style' => t('Small (16x16)'),
        'addthis_32x32_style' => t('Big (32x32)'),
      ],
    ];
    $element['counter_orientation'] = [
      '#title' => t('Counter orientation'),
      '#description' => t('Specify the way service counters are oriented.'),
      '#type' => 'select',
      '#default_value' => $options['counter_orientation'],
      '#options' => [
        'horizontal' => t('Horizontal'),
        'vertical' => t('Vertical'),
      ]
    ];
    $element['extra_css'] = [
      '#title' => t('Extra CSS declaration'),
      '#type' => 'textfield',
      '#size' => 40,
      '#default_value' => $options['extra_css'],
      '#description' => t('Specify extra CSS classes to apply to the toolbox'),
    ];

    return $element;
  }


  /**
   * @TODO Find out why this is never being called.
   *
   * Validation for services for BasicToolbox.
   * @param array $element
   * @param FormStateInterface $form_state
   */
  public static function addThisDisplayElementServicesValidate($element, $form_state) {
    $bad = FALSE;

    $services = trim($element['#value']);
    $services = str_replace(' ', '', $services);

    if (!preg_match('/^[a-z\_\,0-9]+$/', $services)) {
      $bad = TRUE;
    }
    // @todo Validate the service names against AddThis.com. Give a notice when there are bad names.

    // Return error.
    if ($bad) {
      $form_state->setErrorByName($element['#title'], t('The declared services are incorrect or nonexistent.'));
    }
  }

}