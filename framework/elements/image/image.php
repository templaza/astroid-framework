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
extract($displayData);
$title          = $params->get('title', '');
$image          = $params->get('image', '');
$use_link       = $params->get('use_link', 0);
$link           = $params->get('link', '');

$border_radius  = $params->get('border_radius', '');
$border_radius  = $border_radius !== '' ? ' ' . $border_radius : '';
$box_shadow     = $params->get('box_shadow', '');
$box_shadow     = $box_shadow !== '' ? ' ' . $box_shadow : '';
$hover_effect   = $params->get('hover_effect', '');
$hover_effect   = $hover_effect !== '' ? ' as-effect-' . $hover_effect : '';
$transition     = $params->get('hover_transition', '');
$transition     = $transition !== '' ? ' as-transition-' . $transition : '';
$max_width      = $params->get('max_width', '');
$max_width      = $max_width !== '' ? ' style="max-width:'.$max_width.'"' : '';
if (!empty($image)) {
    if ($use_link) {
        echo '<a href="'.$link.'" title="'.$title.'">';
    }
    echo '<div class="d-inline-block position-relative overflow-hidden' . $border_radius . $box_shadow . $hover_effect . $transition . '"'.$max_width.'>';
    echo '<img src="'. Astroid\Helper\Media::getPath() . '/' . $image.'" alt="'.$title.'">';
    echo '</div>';
    if ($use_link) {
        echo '</a>';
    }
}