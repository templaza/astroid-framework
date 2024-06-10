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
$menu_items     = $params->get('menu_items', '');
if (empty($menu_items)) {
    return false;
}
$menu_items     = json_decode($menu_items);
if (!count($menu_items)) {
    return false;
}

$font_style     = $params->get('font_style', null);
$item_margin    = $params->get('item_margin', '');
$item_padding   = $params->get('item_padding', '');
$list_style     = $params->get('list_style', '');
$list_style     = $list_style !== '' ? ' ' . $list_style : '';
$alignment      = $params->get('alignment', '');
$alignment      = $alignment !== '' ? ' '. $alignment : '';
$color          = Style::getColor($params->get('color', ''));
$color_hover    = Style::getColor($params->get('color_hover', ''));
$color_active   = Style::getColor($params->get('color_active', ''));
$bgcolor        = Style::getColor($params->get('bgcolor', ''));
$bgcolor_hover  = Style::getColor($params->get('bgcolor_hover', ''));
$bgcolor_active = Style::getColor($params->get('bgcolor_active', ''));
echo '<nav class="nav' . $alignment . $list_style . '">';
foreach ($menu_items as $item) {
    $menu_params    =   Style::getSubFormParams($item->params);
    $target         =   isset($menu_params['target']) && $menu_params['target'] ? ' target="'.$menu_params['target'].'"' : '';
    $show_title     =   isset($menu_params['show_title']) && intval($menu_params['show_title']) ? ' title="'.$menu_params['title'].'"' : '';
    $active         =   isset($menu_params['active']) && intval($menu_params['active']) ? ' active' : '';
    $rel            =   isset($menu_params['rel']) && $menu_params['rel'] ? ' rel="'.$menu_params['rel'].'"' : '';
    $icon           =   $menu_params['icon'] !== '' ? '<i class="me-2 '.$menu_params['icon'].'"></i>' : '';
    echo '<a id="item-'.$item->id.'"  class="nav-link'.$active.'" href="' .$menu_params['link']. '"'. $show_title . $target . $rel . '>'.$icon.$menu_params['title'].'</a>';
}
echo '</nav>';

// Set styles for widget
$style = new Style('#'. $element->id);
$style_dark = new Style('#'. $element->id, 'dark');

// Font style
if (!empty($font_style)) {
    Style::renderTypography('#'.$element->id.' .nav-link', $font_style);
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
    $margin = \json_decode($item_margin, false);
    foreach ($margin as $device => $props) {
        $style->child('.nav-link')->addStyle(Style::spacingValue($props, "margin"), $device);
    }
}
// Item Padding
if (!empty($item_padding)) {
    $padding = \json_decode($item_padding, false);
    foreach ($padding as $device => $props) {
        $style->child('.nav-link')->addStyle(Style::spacingValue($props, "padding"), $device);
    }
}

$style->render();
$style_dark->render();