<?php

namespace Drupal\powerbi_embed\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\powerbi_embed\Util\ConfigUtil;

/**
 * Class PowerBIEmbedFieldFormatter.
 *
 * Plugin implementation of the 'powerbi_embed_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "powerbi_embed_formatter",
 *   label = @Translation("PowerBI Embed report"),
 *   field_types = {
 *     "powerbi_embed"
 *   }
 * )
 */
class PowerBIEmbedFormatter extends FormatterBase {

    /**
    * {@inheritdoc}
    */
    public function viewElements(FieldItemListInterface $items, $langcode) {
        $element = [];

        $workspace_id = ConfigUtil::getWorkspaceID();
        $the_token = ConfigUtil::getPowerBIToken();

        foreach ($items as $delta => $item) {
        $the_embed_token = ConfigUtil::getEmbedToken($the_token, $workspace_id, $item->report_id);
        $the_url = ConfigUtil::getPowerBIURL($the_token, $workspace_id, $item->report_id);

        $element[$delta] = [
            '#type' => 'inline_template',
            '#template' => $this->getFormatTemplate(),
            '#context' => [
                'field_name' => $item->getParent()->getName(),
                'report_id' => $item->report_id,
                'report_filter' => $item->report_filter,
                'report_width' => $item->report_width,
                'report_height' => $item->report_height,
                'report_title' => $item->report_title,
                'filter_pane_enabled' => $item->filter_pane_enabled,
                'fullscreen_enabled' => $item->fullscreen_enabled,
                'workspace_id' => $workspace_id,
                'token' => $the_embed_token,
                'embed_url' => $the_url,
            ],
        ];
        }

        $element['#cache']['tags'][] = 'config:powerbi_embed.settings';

        return $element;
    }

    private function getFormatTemplate() {
        $format_template = '<div id="reportContainer"></div><script>var models = window["powerbi-client"].models;var report=powerbi.embed($("#reportContainer").get(0),{type:"report",id:"{{report_id}}",accessToken:"{{token}}",embedUrl:"{{embed_url}}"});</script>';

        // Load inline twig template from module or theme if available
        $slash = DIRECTORY_SEPARATOR;
        $template_filename = 'field--powerbi-embed.html.twig';
        $module_handler = \Drupal::service('module_handler');
        $module_path = $module_handler->getModule('powerbi_embed')->getPath();
        $module_template_path = $module_path . $slash . 'templates' . $slash . $template_filename;
        if (is_readable($module_template_path)) {
            $format_template = file_get_contents($module_template_path);
        }

        $theme_handler = \Drupal::service('theme.manager');
        $theme_path = $theme_handler->getActiveTheme()->getPath();
        $theme_template_path = $theme_path . $slash . 'templates' . $slash . $template_filename;
        if (is_readable($theme_template_path)) {
            $format_template = file_get_contents($theme_template_path);
        }

        return $format_template;
    }
}
