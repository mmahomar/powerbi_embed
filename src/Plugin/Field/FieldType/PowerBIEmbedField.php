<?php

namespace Drupal\powerbi_embed\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Class PowerBIEmbedField.
 *
 * @FieldType(
 *   id = "powerbi_embed",
 *   module = "powerbi_embed",
 *   label = @Translation("PowerBI Embed report"),
 *   category = @Translation("Reference"),
 *   description = @Translation("This field type stores PowerBI Embed report reference information."),
 *   default_widget = "powerbi_embed_widget",
 *   default_formatter = "powerbi_embed_formatter",
 *   column_groups = {
 *     "report_id" = {
 *       "label" = @Translation("Report ID"),
 *       "translatable" = TRUE
 *     },
 *     "report_width" = {
 *       "label" = @Translation("Report width"),
 *       "translatable" = TRUE
 *     },
 *     "report_height" = {
 *       "label" = @Translation("Report height"),
 *       "translatable" = TRUE
 *     },
 *     "report_title" = {
 *       "label" = @Translation("Report title"),
 *       "translatable" = TRUE
 *     },
 *   },
 * )
 */
class PowerBIEmbedField extends FieldItemBase {

  /**
   * {@inheritDoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties = [];

    $properties['report_id'] = DataDefinition::create('string')
      ->setLabel(t('Report ID'))
      ->setDescription(t('PowerBI Report ID'));

    $properties['report_width'] = DataDefinition::create('float')
      ->setLabel(t('Report width'))
      ->setDescription(t('PowerBI Report width'));

    $properties['report_height'] = DataDefinition::create('float')
      ->setLabel(t('Report height'))
      ->setDescription(t('PowerBI Report height'));

    $properties['report_title'] = DataDefinition::create('string')
      ->setLabel(t('Report title'))
      ->setDescription(t('PowerBI Report title'));

    return $properties;
  }

  /**
   * {@inheritDoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $columns = [
      'report_id' => [
        'type' => 'varchar',
        'length' => 1024,
      ],
      'report_height' => [
        'type' => 'float',
      ],
      'report_width' => [
        'type' => 'float',
      ],
      'report_title' => [
        'type' => 'varchar',
        'length' => 1024,
      ],
    ];

    $schema = [
      'columns' => $columns,
      'indexes' => [],
    ];

    return $schema;
  }

  /**
   * {@inheritDoc}
   */
  public function isEmpty() {
    $value = $this->get('report_id')->getValue();

    return $value === NULL;
  }

}
