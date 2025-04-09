<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/media/templates/site/{YOUR_TEMPLATE_NAME}/astroid/elements/module_position/module_position.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;

use Astroid\Helper\Style;
use Astroid\Helper\SubForm;

extract($displayData);
$menu_items     = new SubForm($params->get('menu_items', ''));
if (!count($menu_items->getData())) {
    return false;
}
$font_style     = $params->get('font_style', null);
$item_margin    = $params->get('item_margin', '');
$item_padding   = $params->get('item_padding', '');
$list_style     = $params->get('list_style', '');
$list_style     = $list_style !== '' ? ' ' . $list_style : '';
$text_alignment             =   $params->get('text_alignment','');
$text_alignment_breakpoint  =   $params->get('text_alignment_breakpoint','');
$text_alignment_fallback    =   $params->get('text_alignment_fallback','');

$alignment                  =   '';
if ($text_alignment) {
    $alignment              =   ' justify-content' . ($text_alignment_breakpoint ? '-' . $text_alignment_breakpoint : '') . '-' . $text_alignment . ($text_alignment_fallback ? ' justify-content-' . $text_alignment_fallback : '');
}
$color          = Style::getColor($params->get('color', ''));
$color_hover    = Style::getColor($params->get('color_hover', ''));
$color_active   = Style::getColor($params->get('color_active', ''));
$bgcolor        = Style::getColor($params->get('bgcolor', ''));
$bgcolor_hover  = Style::getColor($params->get('bgcolor_hover', ''));
$bgcolor_active = Style::getColor($params->get('bgcolor_active', ''));

$nav_column_cls        =   'as-nav-'. (empty($list_style) ? 'bar' : 'list');
$xxl_column             =   $params->get('nav_column_xxl', '');
$nav_column_cls        .=  $xxl_column ? ' as-column-xxl-' . $xxl_column : '';
$xl_column              =   $params->get('nav_column_xl', '');
$nav_column_cls        .=  $xl_column ? ' as-column-xl-' . $xl_column : '';
$lg_column              =   $params->get('nav_column_lg', '');
$nav_column_cls        .=  $lg_column ? ' as-column-lg-' . $lg_column : '';
$md_column              =   $params->get('nav_column_md', '');
$nav_column_cls        .=  $md_column ? ' as-column-md-' . $md_column : '';
$sm_column              =   $params->get('nav_column_sm', '');
$nav_column_cls        .=  $sm_column ? ' as-column-sm-' . $sm_column : '';
$xs_column              =   $params->get('nav_column_xs', '');
$nav_column_cls        .=  $xs_column ? ' as-column-' . $xs_column : '';
echo '<nav id="'.$element->id.'-nav" class="'.$nav_column_cls.'" aria-label="'.$params->get('title', '').'">';
echo '<ul class="nav' . $alignment . $list_style . '">';
foreach ($menu_items->getData() as $item) {
    $target         =   $item->params->get('target') ? ' target="'.$item->params->get('target').'"' : '';
    $show_title     =   $item->params->get('show_title', 0) ? ' title="'.$item->params->get('title', '').'"' : '';
    $active         =   $item->params->get('active', 0) ? ' active' : '';
    $rel            =   $item->params->get('rel', '') ? ' rel="'.$item->params->get('rel', '').'"' : '';
    $icon           =   $item->params->get('icon', '') ? '<i class="me-2 '.$item->params->get('icon', '').'"></i>' : '';
    echo '<li class="nav-item"><a id="item-'.$item->id.'"  class="nav-link'.$active.'" href="' .$item->params->get('link', ''). '"'. $show_title . $target . $rel . '>'.$icon.$item->params->get('title', '').'</a></li>';
}
echo '</ul>';
echo '</nav>';

// Set styles for widget
$style = $element->style;
$style_dark = $element->style_dark;

// Font style
if (!empty($font_style)) {
    Style::renderTypography('#'.$element->id.' .nav-link', $font_style, null, $element->isRoot);
}
// Color style
$style->child('.nav-link')->addCss('color', $color['light']);
$style_dark->child('.nav-link')->addCss('color', $color['dark']);
$style->child('.nav-link')->hover()->addCss('color', $color_hover['light']);
$style_dark->child('.nav-link')->hover()->addCss('color', $color_hover['dark']);
$style->child('.nav-link')->active('.active')->addCss('color', $color_active['light']);
$style_dark->child('.nav-link')->active('.active')->addCss('color', $color_active['dark']);

// Background color style
$style->child('.nav-link')->addCss('background-color', $bgcolor['light']);
$style_dark->child('.nav-link')->addCss('background-color', $bgcolor['dark']);
$style->child('.nav-link')->hover()->addCss('background-color', $bgcolor_hover['light']);
$style_dark->child('.nav-link')->hover()->addCss('background-color', $bgcolor_hover['dark']);
$style->child('.nav-link')->active('.active')->addCss('background-color', $bgcolor_active['light']);
$style_dark->child('.nav-link')->active('.active')->addCss('background-color', $bgcolor_active['dark']);

// Item Margin
if (!empty($item_margin)) {
    Style::setSpacingStyle($element->style->child('.nav-link'), $item_margin, 'margin');
}
// Item Padding
if (!empty($item_padding)) {
    Style::setSpacingStyle($element->style->child('.nav-link'), $item_padding);
}