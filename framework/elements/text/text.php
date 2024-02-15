<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/templates/YOUR_ASTROID_TEMPLATE/astroid/elements/module_position/module_position.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;
use Astroid\Helper\Style;
extract($displayData);
$title          = $params->get('heading', '');
$html_element   = $params->get('html_element', 'h2');
$font_style     = $params->get('font_style');
$heading_margin = $params->get('heading_margin', '');
$content        = $params->get('content', '');
$content_font_style= $params->get('content_font_style');

$text_column_cls        =   '';
$xxl_column             =   $params->get('text_column_xxl', '');
$text_column_cls        .=  $xxl_column ? ' as-column-xxl-' . $xxl_column : '';
$xl_column              =   $params->get('text_column_xl', '');
$text_column_cls        .=  $xl_column ? ' as-column-xl-' . $xl_column : '';
$lg_column              =   $params->get('text_column_lg', '');
$text_column_cls        .=  $lg_column ? ' as-column-lg-' . $lg_column : '';
$md_column              =   $params->get('text_column_md', '');
$text_column_cls        .=  $md_column ? ' as-column-md-' . $md_column : '';
$sm_column              =   $params->get('text_column_sm', '');
$text_column_cls        .=  $sm_column ? ' as-column-sm-' . $sm_column : '';
$xs_column              =   $params->get('text_column_xs', '');
$text_column_cls        .=  $xs_column ? ' as-column-' . $xs_column : '';

if (!empty($title)) {
    echo '<'.$html_element.' class="astroid-content-heading">'. $title . '</'.$html_element.'>';
}
if (!empty($content)) {
    echo '<div class="astroid-content-text'.$text_column_cls.'">'. $content . '</div>';
}

if (!empty($font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-content-heading', $font_style);
}
if (!empty($heading_margin)) {
    $heading_style = new Style('#'.$element->id.' .astroid-content-heading');
    $margin = \json_decode($heading_margin, false);
    foreach ($margin as $device => $props) {
        $heading_style->addStyle(Style::spacingValue($props, "margin"), $device);
    }
    $heading_style->render();
}

if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-content-text', $content_font_style);
}