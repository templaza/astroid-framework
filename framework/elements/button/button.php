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
use Astroid\Helper\SubForm;

extract($displayData);
$buttons     = new SubForm($params->get('buttons', ''));
if (!count($buttons->data)) {
    return false;
}
$button_group   =   intval($params->get('button_group', 0));
$gutter         =   $params->get('gutter', 'lg');
$border_radius  =   $params->get('btn_border_radius', '');
$bd_radius      =   $border_radius ? ' ' . $border_radius : '';
$button_size    =   $params->get('button_size', '');

$button_size    =   $button_size ? ' '. $button_size : '';
echo '<div class="'.($button_group ? 'btn-group' : 'as-gutter-' . $gutter).'" role="group">';
foreach ($buttons->data as $key => $button) {
    if ($button_group && $border_radius === 'rounded-pill') {
        if ($key === 0) {
            $bd_radius = ' rounded-start-pill';
        } elseif ($key === count($buttons) - 1) {
            $bd_radius = ' rounded-end-pill';
        } else {
            $bd_radius = '';
        }
    }
    $title = $button->params->get('title', '');
    if ($button->params->get('icon', '')) {
        $title      =   $button->params->get('icon_position', '') === 'first' ? '<i class="'.$button->params->get('icon', '').' me-2"></i>' . $title : $title . '<i class="'.$button->params->get('icon', '').' ms-2"></i>';
    }
    $btn_element_size = $button_size;
    if ($button->params->get('button_size', '')) {
        $btn_element_size = ' ' . $button->params->get('button_size', '');
        // Item Padding
        if (trim($button->params->get('button_size', '')) == 'custom') {
            $item_padding   =   $button->params->get('btn_padding', '');
            if (!empty($item_padding)) {
                Style::setSpacingStyle($element->style->child('#btn-'.$button->id), $item_padding);
            }
        }
    }

    // Button Custom Style
    $button_style   =   $button->params->get('button_style', '');
    if ($button_style === 'custom') {
        $color          =   Style::getColor($button->params->get('color', ''));
        $color_hover    =   Style::getColor($button->params->get('color_hover', ''));
        $bgcolor        =   Style::getColor($button->params->get('bgcolor', ''));
        $bgcolor_hover  =   Style::getColor($button->params->get('bgcolor_hover', ''));

        // Color style
        $element->style->child('#btn-'.$button->id)->addCss('color', $color['light']);
        $element->style_dark->child('#btn-'.$button->id)->addCss('color', $color['dark']);
        $element->style->child('#btn-'.$button->id)->hover()->addCss('color', $color_hover['light']);
        $element->style_dark->child('#btn-'.$button->id)->hover()->addCss('color', $color_hover['dark']);

        if (intval($button->params->get('button_outline', ''))) {
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

    $link_target    =   !empty($button->params->get('link_target', '')) ? ' target="'.$button->params->get('link_target', '').'"' : '';
    $button_class   =   $button_style !== 'text' ? 'btn btn-' . (intval($button->params->get('button_outline', '')) ? 'outline-' : '') . $button_style . $btn_element_size. $bd_radius : 'as-btn-text text-uppercase text-reset';
    $btn_title      =   $button_style == 'text' ? '<small>'. $title . '</small>' : $title;
    echo '<a id="btn-'.$button->id.'" href="' .$button->params->get('link', ''). '" class="' .$button_class . '"'.$link_target.'>'.$btn_title.'</a>';
}
echo '</div>';

// Item Padding
if (trim($button_size) == 'custom') {
    $item_padding   =   $params->get('btn_padding', '');
    if (!empty($item_padding)) {
        Style::setSpacingStyle($element->style->child('.btn'), $item_padding);
    }
    $button_font_style =   $params->get('button_font_style');
    if (!empty($button_font_style)) {
        Style::renderTypography('#'.$element->id.' .btn', $button_font_style, null, $element->isRoot);
    }
}