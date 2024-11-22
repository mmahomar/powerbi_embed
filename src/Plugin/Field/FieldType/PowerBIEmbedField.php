<?php

namespace Drupal\powerbi_embed\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'powerbi_embed' field type.
 *
 * @FieldType(
 *   id = "powerbi_embed",
 *   label = @Translation("PowerBI Embed"),
 *   description = @Translation("A field to store PowerBI Embed report reference."),
 *   default_widget = "powerbi_embed_widget",
 *   default_formatter = "powerbi_embed_formatter"
 * )
 */
class PowerBIEmbedField extends FieldItemBase {

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties = [];

    $properties['report_id'] = DataDefinition::create('string')
      ->setLabel(t('Report ID'))
      ->setDescription(t('PowerBI Report ID'));

    $properties['report_filter'] = DataDefinition::create('string')
        ->setLabel(t('Report Filter'))
        ->setDescription(t('PowerBI Report Filter'))
        ->setSetting('max_length', 2048);

    $properties['report_width'] = DataDefinition::create('float')
      ->setLabel(t('Report width'))
      ->setDescription(t('PowerBI Report width'));

    $properties['report_height'] = DataDefinition::create('float')
      ->setLabel(t('Report height'))
      ->setDescription(t('PowerBI Report height'));

    $properties['report_title'] = DataDefinition::create('string')
      ->setLabel(t('Report title'))
      ->setDescription(t('PowerBI Report title'));

    $properties['filter_pane_enabled'] = DataDefinition::create('boolean')
      ->setLabel(t('Filter Pane Enabled'))
      ->setDescription(t('Enable or disable the Filter pane'));

    $properties['fullscreen_enabled'] = DataDefinition::create('boolean')
      ->setLabel(t('Full Screen Enabled'))
      ->setDescription(t('Enable or disable the Full-Screen option'));

    return $properties;
  }
    
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $columns = [
      'report_id' => [
        'type' => 'varchar',
        'length' => 1024,
      ],
      'report_filter' => [
        'type' => 'varchar',
        'length' => 2048,
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
      'filter_pane_enabled' => [
            'type' => 'int',
            'size' => 'tiny',
            'default' => 1,
        ],
        'fullscreen_enabled' => [
            'type' => 'int',
            'size' => 'tiny',
            'default' => 1,
        ],
    ];

    $schema = [
      'columns' => $columns,
      'indexes' => [],
    ];

    return $schema;
  }

  public function isEmpty() {
    $value = $this->get('report_id')->getValue();

    return $value === NULL;
  }

}
