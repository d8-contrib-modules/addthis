<?php

/**
 * @file
 * Contains \Drupal\addthis\Plugin\Field\FieldWidget\AddThisWidget.
 */

namespace Drupal\addthis\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'AddThis' widget.
 *
 * @FieldWidget(
 *   id = "addthis_button_widget",
 *   label = @Translation("AddThis button"),
 *   field_types = {
 *     "addthis"
 *   },
 *   multiple_values = FALSE
 * )
 */
class AddThisWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array() + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    return $element;
  }

}
