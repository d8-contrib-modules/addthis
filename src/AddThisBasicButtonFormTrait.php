<?php

/**
 * @file
 * Contains \Drupal\addthis\AddThisBasicButtonFormTrait.
 *
 */

namespace Drupal\addthis;

/**
 * Class AddThisBasicButtonFormTrait
 * @package Drupal\addthis
 */
trait AddThisBasicButtonFormTrait {


  /**
   * Returns keys for accessing elements in the partial form.
   * @return array - list of key:value defaults defined by the AddThisBasicButtonFormTrait
   */
  protected function addThisBasicButtonGetDefaults(){
    return [
      'button_size' => 'small',
      'extra_css' => '',
    ];
  }

  /**
   * Returns partial configuration form for the AddThisBasicButton.
   *
   * @param $parent_class - The class that is requesting the form.
   * @param $options - The base configuration for the class. (Block/Field)
   *
   * @return array - Partial form to provide configuration.
   */
  protected function addThisBasicButtonForm($options){
    $element = [];

    $element['button_size'] = [
      '#title' => t('Image'),
      '#type' => 'select',
      '#default_value' => $options['button_size'],
      '#options' => [
        'small' => t('Small'),
        'big' => t('Big'),
      ],
    ];
    $element['extra_css'] = [
      '#title' => t('Extra CSS declaration'),
      '#type' => 'textfield',
      '#size' => 40,
      '#default_value' => $options['extra_css'],
      '#description' => t('Specify extra CSS classes to apply to the button'),
    ];

    return $element;
  }



}