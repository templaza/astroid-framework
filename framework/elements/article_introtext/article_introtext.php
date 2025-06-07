<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/media/templates/site/YOUR_ASTROID_TEMPLATE/astroid/elements/module_position/module_position.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;
use Astroid\Helper\Style;
use Astroid\Helper\GSAP;
extract($displayData);
$item = $options['article'];
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
if (!empty($text_column_cls)) {
    echo '<div class="astroid-content-text'.$text_column_cls.'">'. $item->article->introtext . '</div>';
} else {
    echo $item->article->introtext;
}
if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id, $content_font_style, null, $element->isRoot);
}
$content_effect_type = $params->get('content_effect_type', '');
if (!empty($content_effect_type)) {
    $gsap = new GSAP('#'.$element->id.' p');
    $gsap->textEffect($content_effect_type);
}