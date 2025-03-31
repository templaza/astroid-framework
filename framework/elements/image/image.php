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
extract($displayData);
$title          = $params->get('title', '');
$image          = $params->get('image', '');
$image_dark     = $params->get('image_dark', '');
$figure_caption = $params->get('figure_caption', '');
$use_link       = $params->get('use_link', 0);
$link           = $params->get('link', '');
$target         = $params->get('target', '');
$target         = $target !== '' ? ' target="'.$target.'"' : '';

$border_radius      =   $params->get('img_border_radius', '');
$rounded_size       =   $params->get('image_rounded_size', '3');
if ($border_radius == 'rounded') {
    $border_radius  =   ' ' . $border_radius . '-' . $rounded_size;
} else {
    $border_radius  =   $border_radius !== '' ? ' ' . $border_radius : '';
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
    echo '<div class="as-image-wrapper position-relative overflow-hidden'. $display . $border_radius . $box_shadow . $hover_effect . $transition . '">';
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