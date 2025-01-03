<?php

namespace Drupal\powerbi_embed\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'NameFieldTypeDefaultWidget' widget.
 *
 * @FieldWidget(
 *   id = "powerbi_embed_widget",
 *   label = @Translation("PowerBI Embed report reference"),
 *   description = @Translation("Use to reference PowerBI Embed report"),
 *   field_types = {
 *     "powerbi_embed",
 *   }
 * )
 */
class PowerBIEmbedWidget extends WidgetBase {

      public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
      $element += [
        '#type' => 'fieldset',
      ];

      $element['report_id'] = [
        '#type' => 'textfield',
        '#title' => t('Report ID'),
        '#description' => t('PowerBI Report ID'),
        '#default_value' => isset($items[$delta]->report_id) ? $items[$delta]->report_id : NULL,
        '#size' => 255,
      ];

      $element['report_filter'] = [
            '#type' => 'textfield',
            '#title' => t('Report Filter'),
            '#description' => t('Enter the filter string in the format "filter=Table/Column eq \'Value\'". Single quotes around the value are required.'),
            '#default_value' => isset($items[$delta]->report_filter) ? $items[$delta]->report_filter : NULL,
            '#size' => 255,
        ];

      $element['report_width'] = [
        '#type' => 'textfield',
        '#title' => t('Report width'),
        '#description' => t('PowerBI Report width'),
        '#default_value' => isset($items[$delta]->report_width) ? $items[$delta]->report_width : 0,
        '#size' => 255,
      ];

      $element['report_height'] = [
        '#type' => 'textfield',
        '#title' => t('Report height'),
        '#description' => t('PowerBI Report height'),
        '#default_value' => isset($items[$delta]->report_height) ? $items[$delta]->report_height : 0,
        '#size' => 255,
      ];

      $element['report_title'] = [
        '#type' => 'textfield',
        '#title' => t('Report title'),
        '#description' => t('PowerBI Report title'),
        '#default_value' => isset($items[$delta]->report_title) ? $items[$delta]->report_title : NULL,
        '#size' => 255,
      ];

      $element['filter_pane_enabled'] = [
          '#type' => 'checkbox',
          '#title' => t('Enable Filter Pane'),
          '#default_value' => isset($items[$delta]->filter_pane_enabled) ? $items[$delta]->filter_pane_enabled : 1,
        ];

        $element['fullscreen_enabled'] = [
          '#type' => 'checkbox',
          '#title' => t('Enable Full Screen'),
          '#default_value' => isset($items[$delta]->fullscreen_enabled) ? $items[$delta]->fullscreen_enabled : 1,
        ];

      return $element;
    }

}
