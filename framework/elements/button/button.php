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
use Astroid\Helper;
use Astroid\Helper\Style;

extract($displayData);
$buttons        = $params->get('buttons', '');
if (empty($buttons)) {
    return false;
}
$buttons        = json_decode($buttons);
if (!count($buttons)) {
    return false;
}
$button_group   =   intval($params->get('button_group', 0));
$gutter         =   $params->get('gutter', 'lg');
$border_radius  =   $params->get('border_radius', '');
$bd_radius      =   $border_radius ? ' ' . $border_radius : '';
$button_size    =   $params->get('button_size', '');

$button_size    =   $button_size ? ' '. $button_size : '';
echo '<div class="'.($button_group ? 'btn-group' : 'as-gutter-' . $gutter).'" role="group">';
foreach ($buttons as $key => $button) {
    $btn_params = Helper::loadParams($button->params);
    if ($button_group && $border_radius === 'rounded-pill') {
        if ($key === 0) {
            $bd_radius = ' rounded-start-pill';
        } elseif ($key === count($buttons) - 1) {
            $bd_radius = ' rounded-end-pill';
        } else {
            $bd_radius = '';
        }
    }
    $title = $btn_params->get('title', '');
    if ($btn_params->get('icon', '')) {
        $title      =   $btn_params->get('icon_position', '') === 'first' ? '<i class="'.$btn_params->get('icon', '').' me-2"></i>' . $title : $title . '<i class="'.$btn_params->get('icon', '').' ms-2"></i>';
    }
    $btn_element_size = $button_size;
    if ($btn_params->get('button_size', '')) {
        $btn_element_size = ' ' . $btn_params->get('button_size', '');
        // Item Padding
        if (trim($btn_params->get('button_size', '')) == 'custom') {
            $item_padding   =   $btn_params->get('btn_padding', '');
            if (!empty($item_padding)) {
                $padding = \json_decode($item_padding, false);
                foreach ($padding as $device => $props) {
                    $element->style->child('#btn-'.$button->id)->addStyle(Style::spacingValue($props, "padding"), $device);
                }
            }
        }
    }

    // Button Custom Style
    $button_style   =   $btn_params->get('button_style', '');
    if ($button_style === 'custom') {
        $color          =   Style::getColor($btn_params->get('color', ''));
        $color_hover    =   Style::getColor($btn_params->get('color_hover', ''));
        $bgcolor        =   Style::getColor($btn_params->get('bgcolor', ''));
        $bgcolor_hover  =   Style::getColor($btn_params->get('bgcolor_hover', ''));

        // Color style
        $element->style->child('#btn-'.$button->id)->addCss('color', $color['light']);
        $element->style_dark->child('#btn-'.$button->id)->addCss('color', $color['dark']);
        $element->style->child('#btn-'.$button->id)->hover()->addCss('color', $color_hover['light']);
        $element->style_dark->child('#btn-'.$button->id)->hover()->addCss('color', $color_hover['dark']);

        if (intval($btn_params->get('button_outline', ''))) {
            // Background color style
            $element->style->child('#btn-'.$button->id)->addCss('border-color', $bgcolor['light']);
            $element->style_dark->child('#btn-'.$button->id)->addCss('border-color', $bgcolor['dark']);
            $element->style->child('#btn-'.$button->id)->hover()->addCss('border-color', $bgcolor_hover['light']);
            $element->style_dark->child('#btn-'.$button->id)->hover()->addCss('border-color', $bgcolor_hover['dark']);
        } else {
            // Background color style
            $element->style->child('#btn-'.$button->id)->addCss('background-color', $bgcolor['light']);
            $element->style_dark->child('#btn-'.$button->id)->addCss('background-color', $bgcolor['dark']);
            $element->style->child('#btn-'.$button->id)->hover()->addCss('background-color', $bgcolor_hover['light']);
            $element->style_dark->child('#btn-'.$button->id)->hover()->addCss('background-color', $bgcolor_hover['dark']);
        }
    }

    $link_target    =   !empty($btn_params->get('link_target', '')) ? ' target="'.$btn_params->get('link_target', '').'"' : '';
    $button_class   =   $button_style !== 'text' ? 'btn btn-' . (intval($btn_params->get('button_outline', '')) ? 'outline-' : '') . $button_style . $btn_element_size. $bd_radius : 'as-btn-text text-uppercase text-reset';
    $btn_title      =   $button_style == 'text' ? '<small>'. $title . '</small>' : $title;
    echo '<a id="btn-'.$button->id.'" href="' .$btn_params->get('link', ''). '" class="' .$button_class . '"'.$link_target.'>'.$btn_title.'</a>';
}
echo '</div>';

// Item Padding
if (trim($button_size) == 'custom') {
    $item_padding   =   $params->get('btn_padding', '');
    if (!empty($item_padding)) {
        $padding = \json_decode($item_padding, false);
        foreach ($padding as $device => $props) {
            $element->style->child('.btn')->addStyle(Style::spacingValue($props, "padding"), $device);
        }
    }
    $button_font_style =   $params->get('button_font_style');
    if (!empty($button_font_style)) {
        Style::renderTypography('#'.$element->id.' .btn', $button_font_style);
    }
}