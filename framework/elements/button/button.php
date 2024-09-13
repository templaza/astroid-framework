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
    $btn_params = Style::getSubFormParams($button->params);
    if ($button_group && $border_radius === 'rounded-pill') {
        if ($key === 0) {
            $bd_radius = ' rounded-start-pill';
        } elseif ($key === count($buttons) - 1) {
            $bd_radius = ' rounded-end-pill';
        } else {
            $bd_radius = '';
        }
    }
    $title = $btn_params['title'];
    if (isset($btn_params['icon']) && $btn_params['icon']) {
        $title      =   $btn_params['icon_position'] === 'first' ? '<i class="'.$btn_params['icon'].' me-2"></i>' . $title : $title . '<i class="'.$btn_params['icon'].' ms-2"></i>';
    }
    $btn_element_size = $button_size;
    if (isset($btn_params['button_size']) && $btn_params['button_size']) {
        $btn_element_size = ' ' . $btn_params['button_size'];
        // Item Padding
        if (trim($btn_params['button_size']) == 'custom') {
            $item_padding   =   $btn_params['btn_padding'];
            if (!empty($item_padding)) {
                $padding = \json_decode($item_padding, false);
                foreach ($padding as $device => $props) {
                    $element->style->child('#btn-'.$button->id)->addStyle(Style::spacingValue($props, "padding"), $device);
                }
            }
        }
    }
    $link_target    =   !empty($btn_params['link_target']) ? ' target="'.$btn_params['link_target'].'"' : '';
    echo '<a id="btn-'.$button->id.'" href="' .$btn_params['link']. '" class="btn btn-' .(intval($btn_params['button_outline']) ? 'outline-' : ''). $btn_params['button_style'] . $btn_element_size . $bd_radius . '"'.$link_target.'>'.$title.'</a>';
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
}