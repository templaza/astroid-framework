<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2026 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/media/templates/site/{YOUR_TEMPLATE_NAME}/astroid/elements/pie_progress/pie_progress.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;

use Astroid\Framework;
use Astroid\Helper\Style;
use Astroid\Helper\SubForm;

extract($displayData);
$pie_items = new SubForm($params->get('pie_items', ''));
if (!count($pie_items->getdata())) {
    return false;
}
$document = Framework::getDocument();
$document->loadPieProgress();
$style = $element->style;
$style_dark = $element->style_dark;
$row_column_cls     =   '';

$responsive_key     =   ['xxl', 'xl', 'lg', 'md', 'sm', 'xs'];
foreach ($responsive_key as $key) {
    $default        =   match ($key) {
        'xxl', 'xl' =>  '',
        'lg'        =>  3,
        default     =>  1
    };
    $column         =   $params->get($key . '_column', $default);

    if ($key !== 'xs') {
        $row_column_cls     .=  $column ? ' row-cols-'. $key .'-' . $column : '';
        $row_gutter         =   $params->get('row_gutter_'.$key, '');
        $column_gutter      =   $params->get('column_gutter_'. $key, '');
        if ($row_gutter) {
            $row_column_cls .=  ' gy-' . $key . '-' . $row_gutter;
        }
        if ($column_gutter) {
            $row_column_cls .=  ' gx-' . $key . '-' . $column_gutter;
        }
    } else {
        $row_column_cls     .=  $column ? ' row-cols-' . $column : '';
        $row_gutter         =   $params->get('row_gutter', 3);
        $column_gutter      =   $params->get('column_gutter', 3);
        $row_column_cls     .=  ' gy-' . $row_gutter;
        $row_column_cls     .=  ' gx-' . $column_gutter;
    }
}

$title_html_element =   $params->get('title_html_element', 'h3');
$title_font_style   =   $params->get('title_font_style');
if (!empty($title_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-pie-progress__title', $title_font_style, null, $element->isRoot);
}
$title_heading_margin=  $params->get('title_heading_margin', '');

$content_font_style =   $params->get('content_font_style');
if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-pie-progress__text', $content_font_style, null, $element->isRoot);
}

$chart_size = $params->get('chart_size', '{"xxl":"150","xl":"150","lg":"150","md":"150","sm":"150","xs":"150","postfix":"px"}');
$chart_size_data = \json_decode($chart_size, true);
if (json_last_error() === JSON_ERROR_NONE && is_array($chart_size_data)) {
    $element->style->child('.astroid-pie-progress')->addResponsiveCSS('--apb-size', $chart_size_data, $chart_size_data['postfix']);
}

$chart_size = $params->get('chart_size', '{"xxl":"150","xl":"150","lg":"150","md":"150","sm":"150","xs":"150","postfix":"px"}');
$chart_size_data = \json_decode($chart_size, true);
if (json_last_error() === JSON_ERROR_NONE && is_array($chart_size_data)) {
    $element->style->child('.astroid-pie-progress')->addResponsiveCSS('--apb-size', $chart_size_data, $chart_size_data['postfix']);
}

$chart_label_size = $params->get('chart_label_size', '{"xxl":"100","xl":"100","lg":"100","md":"100","sm":"100","xs":"100","postfix":"px"}');
$chart_label_size_data = \json_decode($chart_label_size, true);
if (json_last_error() === JSON_ERROR_NONE && is_array($chart_label_size_data)) {
    $element->style->child('.astroid-pie-progress')->addResponsiveCSS('--apb-label-size', $chart_label_size_data, $chart_label_size_data['postfix']);
}

$chart_bg_color = Style::getColor($params->get('chart_bg_color', ''));
$element->style->child('.astroid-pie-progress')->addCss('--apb-track', $chart_bg_color['light']);
$element->style_dark->child('.astroid-pie-progress')->addCss('--apb-track', $chart_bg_color['dark']);

$chart_fill_color = Style::getColor($params->get('chart_fill_color', ''));
$element->style->child('.astroid-pie-progress')->addCss('--apb-color', $chart_fill_color['light']);
$element->style_dark->child('.astroid-pie-progress')->addCss('--apb-color', $chart_fill_color['dark']);

$chart_done_color = Style::getColor($params->get('chart_done_color', ''));
$element->style->child('.astroid-pie-progress')->addCss('--apb-done', $chart_done_color['light']);
$element->style_dark->child('.astroid-pie-progress')->addCss('--apb-done', $chart_done_color['dark']);

$chart_label_bg_color = Style::getColor($params->get('chart_label_bg_color', ''));
$element->style->child('.astroid-pie-progress')->addCss('--apb-label-bg', $chart_label_bg_color['light']);
$element->style_dark->child('.astroid-pie-progress')->addCss('--apb-label-bg', $chart_label_bg_color['dark']);

$chart_label_text_color = Style::getColor($params->get('chart_label_text_color', ''));
$element->style->child('.astroid-pie-progress')->addCss('--apb-label-text', $chart_label_text_color['light']);
$element->style_dark->child('.astroid-pie-progress')->addCss('--apb-label-text', $chart_label_text_color['dark']);

echo '<div class="row'.$row_column_cls.'">';
foreach ($pie_items->getdata() as $key => $item) {
    $percent = $item->params->get('percent', 75);
    $duration = $item->params->get('duration', 1200);
    echo '<div id="grid-'. $item -> id .'" class="as-grid"><div class="astroid-pie-progress" data-value="'.$percent.'" data-duration="'.$duration.'">';
    echo '<div class="astroid-pie-progress__chart" role="img" aria-label="Progress 0%">';
    echo '<svg class="astroid-pie-progress__svg" viewBox="0 0 100 100" aria-hidden="true" focusable="false"><circle class="astroid-pie-progress__track" cx="50" cy="50" r="50"></circle><circle class="astroid-pie-progress__arc" cx="50" cy="50" r="25"></circle></svg>';
    echo '<span class="astroid-pie-progress__value">0%</span>';
    echo '</div>';

    $title = $item->params->get('title', '');
    if (!empty($title)) {
        echo '<'.$title_html_element.' class="astroid-pie-progress__title">'. $title . '</'.$title_html_element.'>';
    }
    if (!empty($item->params->get('description', ''))) {
        echo '<div class="astroid-pie-progress__text">' . $item->params->get('description', '') . '</div>';
    }

    echo '</div></div>';
}
echo '</div>';
if (!empty($title_heading_margin)) {
    Style::setSpacingStyle($element->style->child('.astroid-pie-progress__title'), $title_heading_margin, 'margin');
}