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
$icons        = $params->get('icons', '');
if (empty($icons)) {
    return false;
}
$icons        = json_decode($icons);
if (!count($icons)) {
    return false;
}
$icon_size      = $params->get('icon_size', '18');
$icon_gutter    = $params->get('icon_gutter', '3');
$color          = Style::getColor($params->get('color', ''));
$color_hover    = Style::getColor($params->get('color_hover', ''));
echo '<div class="row row-cols-auto g-'.$icon_gutter.'">';
foreach ($icons as $icon) {
    $icon_params    =   Style::getSubFormParams($icon->params);
    $target         =   isset($icon_params['target']) && $icon_params['target'] ? ' target="'.$icon_params['target'].'"' : '';
    echo '<div class="astroid-icon-item">';
    echo '<a id="btn-'.$icon->id.'" href="' .$icon_params['link']. '" title="'.$icon_params['title'].'"' . $target . '><i class="'.$icon_params['icon'].'"></i></a>';
    echo '</div>';
}
echo '</div>';

// Set styles for widget
$style = new Style('#'. $element->id);
$style_dark = new Style('#'. $element->id, 'dark');
$style->child('.astroid-icon-item')->addCss('font-size', $icon_size.'px');
$style->child('.astroid-icon-item > a')->addCss('color', $color['light']);
$style_dark->child('.astroid-icon-item > a')->addCss('color', $color['dark']);
$style->child('.astroid-icon-item > a')->hover()->addCss('color', $color_hover['light']);
$style_dark->child('.astroid-icon-item > a')->hover()->addCss('color', $color_hover['dark']);
$style->render();
$style_dark->render();