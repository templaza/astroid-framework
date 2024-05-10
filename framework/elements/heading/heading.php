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
use Astroid\Helper\Style;
// No direct access.
defined('_JEXEC') or die;
extract($displayData);
$style = new Style('#'. $element->id);
$style_dark = new Style('#'. $element->id, 'dark');
$title          = $params->get('title', '');
$html_element   = $params->get('html_element', 'h2');
$font_style     = $params->get('font_style', null);
$use_link       = $params->get('use_link', 0);
$link           = $params->get('link', '');
$add_icon       = $params->get('add_icon', 0);
$icon           = $params->get('icon', '');
$icon_color     = Style::getColor($params->get('icon_color', ''));
$style->child('.astroid-icon')->addCss('color', $icon_color['light']);
$style_dark->child('.astroid-icon')->addCss('color', $icon_color['dark']);
if (!empty($title)) {
    if ($use_link) {
        echo '<a href="'.$link.'" title="'.$title.'">';
    }
    echo '<'.$html_element.' class="heading">'. ($add_icon && $icon ? '<i class="'.$icon.' astroid-icon me-2"></i>' : '') . $title . '</'.$html_element.'>';
    if ($use_link) {
        echo '</a>';
    }
}
if (!empty($font_style)) {
    Astroid\Helper\Style::renderTypography('#'.$element->id.' .heading', $font_style);
}
$style->render();
$style_dark->render();