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
use Astroid\Helper\Style;
// No direct access.
defined('_JEXEC') or die;
extract($displayData);
$style = $element->style;
$style_dark = $element->style_dark;
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

$title_heading_margin=  $params->get('title_heading_margin', '');

$title_clone       = $params->get('title_clone', 0);
$title_clone_txt           = $params->get('title_clone_txt', '');
// Meta
$meta = $params->get('meta_text', '');
$meta_font_style     = $params->get('meta_font_style', null);
$meta_heading_margin=  $params->get('meta_heading_margin', '');
$meta_position=  $params->get('meta_position', 'before');
$meta_border    =   json_decode($params->get('meta_border', ''), true);
if (!empty($meta_border)) {
    Style::addBorderStyle('#'. $element->id . ' .heading-meta', $meta_border, 'global', $element->isRoot);
}
$meta_radius=  $params->get('meta_radius', '');
if (!empty($meta_radius)) {
    Style::setSpacingStyle($element->style->child(' .heading-meta'), $meta_radius, 'radius');
}

if (!empty($title)) {
    if ($meta !='' && $meta_position == 'before') {
        echo '<div class="heading-meta d-inline-block">'.$meta.'</div>';
    }
    if ($use_link) {
        echo '<a href="'.$link.'" title="'.$title.'">';
    }
    echo '<'.$html_element.' class="heading">'. ($add_icon && $icon ? '<i class="'.$icon.' astroid-icon me-2"></i>' : '') . $title . '</'.$html_element.'>';
    if ($use_link) {
        echo '</a>';
    }
    if($title_clone){
        echo '<div class="heading-clone position-absolute">'.$title_clone_txt.'</div>';
    }
    if ($meta !=''  && $meta_position == 'after') {
        echo '<div class="heading-meta">'.$meta.'</div>';
    }
}
if (!empty($font_style)) {
    Astroid\Helper\Style::renderTypography('#'.$element->id.' .heading', $font_style, null, $element->isRoot);
}
if (!empty($title_heading_margin)) {
    Style::setSpacingStyle($element->style->child('.heading'), $title_heading_margin, 'margin');
}
if (!empty($meta_font_style)) {
    Style::renderTypography('#'.$element->id.' .heading-meta', $meta_font_style, null, $element->isRoot);
}
if (!empty($meta_heading_margin)) {
    Style::setSpacingStyle($element->style->child('.heading-meta'), $meta_heading_margin, 'margin');
}

$title_clone_margin=  $params->get('title_clone_margin', '');
if (!empty($title_clone_margin)) {
    Style::setSpacingStyle($element->style->child('.heading-clone'), $title_clone_margin, 'margin');
}

$clone_font_style     = $params->get('title_clone_font_style', null);
if (!empty($clone_font_style)) {
    Style::renderTypography('#'.$element->id.' .heading-clone', $clone_font_style, null, $element->isRoot);
}