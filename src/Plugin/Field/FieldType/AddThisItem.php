<?php

/**
 * @file
 * Contains \Drupal\addthis\Plugin\Field\FieldType\AddThisItem.
 */

namespace Drupal\addthis\Plugin\Field\FieldType;


use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslationWrapper;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'addthis' entity field type.
 *
 * @FieldType(
 *   id = "addthis",
 *   label = @Translation("AddThis"),
 *   description = @Translation("This field stores addthis settings in the database."),
 *   category = @Translation("Addthis"),
 *   default_widget = "addthis_button_widget",
 *   default_formatter = "addthis_disabled"
 * )
 */
class AddThisItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'varchar',
          'length' => 255,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return FALSE;
  }


  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // This is called very early by the user entity roles field. Prevent
    // early t() calls by using the TranslationWrapper.
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslationWrapper('Text value'));
    return $properties;
  }

}
