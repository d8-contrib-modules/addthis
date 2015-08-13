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
    return array(
      'max_length' => 255,
      'is_ascii' => FALSE,
    ) + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => $field_definition->getSetting('is_ascii') === TRUE ? 'varchar_ascii' : 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'binary' => $field_definition->getSetting('case_sensitive'),
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
  public function getConstraints() {
    $constraints = parent::getConstraints();

    if ($max_length = $this->getSetting('max_length')) {
      $constraint_manager = \Drupal::typedDataManager()
        ->getValidationConstraintManager();
      $constraints[] = $constraint_manager->create('ComplexData', array(
        'value' => array(
          'Length' => array(
            'max' => $max_length,
            'maxMessage' => t('%name: may not be longer than @max characters.', array(
              '%name' => $this->getFieldDefinition()
                ->getLabel(),
              '@max' => $max_length
            )),
          ),
        ),
      ));
    }

    return $constraints;
  }


  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // This is called very early by the user entity roles field. Prevent
    // early t() calls by using the TranslationWrapper.
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslationWrapper('Text value'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(TRUE);

    return $properties;
  }

}
