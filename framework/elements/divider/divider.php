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
$border_width   = $params->get('border_width', 1);
$type           = $params->get('type', '');
$border_style   = $params->get('style', 'solid');
$color          = Style::getColor($params->get('color', ''));
$margin         = $params->get('margin', '');
$height         = $params->get('height_divider', '60');

echo '<div class="divider-content'. ($type == 'vertical' ? ' d-flex justify-content-center' : '') .'"></div>';
if ($type == 'vertical') {
    $element->style->child('.divider-content:after')->addCss('content', '""');
    $element->style->child('.divider-content:after')->addCss('height', $height . 'px');
    $element->style->child('.divider-content:after')->addCss('border-left', $border_width. 'px ' . $border_style . ' ' . $color['light']);
} else {
    $element->style->child('.divider-content')->addCss('border-top', $border_width. 'px ' . $border_style . ' ' . $color['light']);
    $element->style_dark->child('.divider-content')->addCss('border-color', $color['dark']);
}

if (!empty($margin)) {
    $margin = \json_decode($margin, false);
    foreach ($margin as $device => $props) {
        $element->style->child('.divider-content')->addStyle(Style::spacingValue($props, "margin"), $device);
    }
}