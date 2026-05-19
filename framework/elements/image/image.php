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
use Joomla\CMS\Uri\Uri;
extract($displayData);
$title          = $params->get('title', '');
$image          = $params->get('image', '');
$image_dark     = $params->get('image_dark', '');
$figure_caption = $params->get('figure_caption', '');
$use_link       = $params->get('use_link', 0);
$link           = $params->get('link', '');
$target         = $params->get('target', '');
$target         = $target !== '' ? ' target="'.$target.'"' : '';

$shape          = $params->get('img_mask', '');
$border_radius      =   $params->get('img_border_radius', '');
$rounded_size       =   $params->get('image_rounded_size', '3');
if ($border_radius == 'rounded') {
    $border_radius  =   ' ' . $border_radius . '-' . $rounded_size;
}elseif($border_radius =='custom') {
    $image_radius=  $params->get('image_radius', '');
    if (!empty($image_radius)) {
        Style::setSpacingStyle($element->style->child('.astroid-image-element img'), $image_radius, 'radius');
    }
} else {
    $border_radius  =   $border_radius !== '' ? ' ' . $border_radius : '';
}
$image_height      =   $params->get('image_height', '');
$image_width      =   $params->get('image_width', '');

$image_height_data = json_decode($image_height, true);
$image_width_data = json_decode($image_width, true);
$style = $element->style;
if (json_last_error() === JSON_ERROR_NONE && is_array($image_width_data)) {
    $style->child('.astroid-image-element')->addResponsiveCSS('width', $image_width_data, $image_width_data['postfix']);
}
if (json_last_error() === JSON_ERROR_NONE && is_array($image_height_data)) {
    $style->child('.astroid-image-element')->addResponsiveCSS('height', $image_height_data, $image_height_data['postfix']);
}
$cus_cl = '';
if($image_height_data["global"] && $image_width_data["global"]){
    $cus_cl = ' custom-size ';
}
$image_border    =   json_decode($params->get('image_border', ''), true);
if (!empty($image_border)) {
    Style::addBorderStyle('#'. $element->id . ' .as-image', $image_border, 'global', $element->isRoot);
}
$box_shadow     = $params->get('box_shadow', '');
$box_shadow     = $box_shadow !== '' ? ' ' . $box_shadow : '';
$hover_effect   = $params->get('hover_effect', '');
$hover_effect   = $hover_effect !== '' ? ' as-effect-' . $hover_effect : '';
$transition     = $params->get('hover_transition', '');
$transition     = $transition !== '' ? ' as-transition-' . $transition : '';
$display        = $params->get('display', '');
$display        = $display !== '' ? ' ' . $display : '';
if (!empty($image)) {
    if ($use_link) {
        echo '<a href="'.$link.'" title="'.$title.'"'.$target.'>';
    }

    if (!empty($figure_caption)) {
        echo '<figure class="m-0">';
    }
    echo '<div class="as-image-wrapper position-relative astroid-image-element overflow-hidden'. $display .$cus_cl. $border_radius . $box_shadow . $hover_effect . $transition . '">';
    echo '<img class="as-image" src="'. Astroid\Helper\Media::getMediaPath($image) .'" alt="'.$title.'">';
    if (!empty($image_dark)) {
        echo '<img class="as-image-dark d-none" src="'. Astroid\Helper\Media::getMediaPath($image_dark).'" alt="'.$title.'">';
        $element->style_dark->child('.as-image')->addCss('display', 'none !important');
        $element->style_dark->child('.as-image-dark')->addCss('display', 'inline-block !important');
    }
    echo '</div>';
    if (!empty($figure_caption)) {
        echo '<figcaption class="figure-caption">'.$figure_caption.'</figcaption>';
        echo '</figure>';
    }
    if ($use_link) {
        echo '</a>';
    }
}
$mask_scale         = $params->get('mask_scale', '');
$mask_repeat         = $params->get('mask_repeat', '');
$mask_position         = $params->get('mask_position', '');
if($shape=='style1'){
    $shape_style = ''. Uri::root() . 'media/astroid/assets/images/style1.svg';
    $style->child('.as-image-wrapper img')->addCss('-webkit-mask-image', 'url('.$shape_style.')');
    $style->child('.as-image-wrapper img')->addCss('-webkit-mask-repeat', $mask_repeat);
    $style->child('.as-image-wrapper img')->addCss('-webkit-mask-position', $mask_position);
    $style->child('.as-image-wrapper img')->addCss('-webkit-mask-size', $mask_scale.'%');
}