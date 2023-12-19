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
$border_style   = $params->get('style', 'solid');
$color          = Style::getColor($params->get('color', ''));
$margin         = $params->get('margin', '');

echo '<div class="divider-content"></div>';
$style      = new Style('#'.$element->id.' .divider-content');
$style_dark = new Style('#'.$element->id.' .divider-content', 'dark');
$style->addCss('border-top', $border_width. 'px ' . $border_style . ' ' . $color['light']);
$style_dark->addCss('border-color', $color['dark']);

if (!empty($margin)) {
    $margin = \json_decode($margin, false);
    foreach ($margin as $device => $props) {
        $style->addStyle(Style::spacingValue($props, "margin"), $device);
    }
}

$style->render();
$style_dark->render();