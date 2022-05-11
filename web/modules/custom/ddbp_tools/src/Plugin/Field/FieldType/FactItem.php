<?php

namespace Drupal\ddbp_tools\Plugin\Field\FieldType;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * @FieldType(
 *   id = "fact_item",
 *   label = @Translation("Fact"),
 *   category = @Translation("General"),
 *   default_widget = "fact_item_default",
 *   default_formatter = "fact_item_default"
 * )
 */
class FactItem extends FieldItemBase {
  /**
   * Defines field item properties.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface[]
   *   An array of property definitions of contained properties, keyed by
   *   property name.
   *
   * @see \Drupal\Core\Field\BaseFieldDefinition
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['fact_label'] = DataDefinition::create('string')->setLabel(t('Fact label'));
    $properties['fact_value'] = DataDefinition::create('string')->setLabel(t('Fact value'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('fact_value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * Returns the schema for the field.
   *
   * @param \Drupal\Core\Field\FieldStorageDefinitionInterface $field_definition
   *   The field definition.
   *
   * @return array
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema['columns']['fact_label'] = [
      'description' => t('Fact label'),
      'type' => 'varchar',
      'length' => 255,
      'not null' => FALSE,
    ];
    $schema['columns']['fact_value'] = [
      'description' => t('Fact value'),
      'type' => 'text',
      'size' => 'big',
      'not null' => FALSE,
    ];

    return $schema;
  }

}
