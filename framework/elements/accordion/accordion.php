<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/templates/YOUR_ASTROID_TEMPLATE/astroid/elements/module_position/module_position.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;

use Astroid\Helper;
use Joomla\CMS\Factory;
use Astroid\Helper\Style;

extract($displayData);
$accordions     = $params->get('accordions', '');
if (empty($accordions)) {
    return false;
}
$accordions     = json_decode($accordions);
if (!count($accordions)) {
    return false;
}
$style          = $params->get('style', '');
$style          = $params->get('style', '');
$style          = $style !== '' ? ' '. $style : '';

$collapse       = $params->get('collapse', '');
$always_open    = $params->get('always_open', 0);
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('bootstrap.collapse');
echo '<div class="accordion'.$style.'" id="accordion-'.$element->id.'">';
foreach ($accordions as $key => $accordions) {
    $item_params = Helper::loadParams($accordions->params);
    echo '<div class="accordion-item">';

    echo '<h2 class="accordion-header">';
    echo '<button class="accordion-button'.($key != 0 || $collapse === 'close-all' ? ' collapsed' : '').'" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$element->id.$key.'" aria-expanded="true" aria-controls="collapse'.$element->id.$key.'">'.$item_params->get('title', '').'</button>';
    echo '</h2>';

    echo '<div id="collapse'.$element->id.$key.'" class="accordion-collapse collapse'.($key == 0 && $collapse === '' ? ' show' : '').'"'.(!$always_open ? ' data-bs-parent="#accordion-'.$element->id.'"' : '').'>';
    echo '<div class="accordion-body">'. $item_params->get('content', '') . '</div>';
    echo '</div>';

    echo '</div>';
}
echo '</div>';

$title_font_style   =   $params->get('title_font_style');
if (!empty($title_font_style)) {
    Style::renderTypography('#'.$element->id.' .accordion-button', $title_font_style);
}

$content_font_style =   $params->get('content_font_style');
if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id.' .accordion-body', $content_font_style);
}

$color          = Style::getColor($params->get('color', ''));
$color_hover    = Style::getColor($params->get('color_hover', ''));
$color_active   = Style::getColor($params->get('color_active', ''));
$bgcolor        = Style::getColor($params->get('bgcolor', ''));
$bgcolor_hover  = Style::getColor($params->get('bgcolor_hover', ''));
$bgcolor_active = Style::getColor($params->get('bgcolor_active', ''));

// Color style
$element->style->child('.accordion-button')->addCss('color', $color['light']);
$element->style_dark->child('.accordion-button')->addCss('color', $color['dark']);
$element->style->child('.accordion-button')->hover()->addCss('color', $color_hover['light']);
$element->style_dark->child('.accordion-button')->hover()->addCss('color', $color_hover['dark']);
$element->style->child('.accordion-button:not(.collapsed)')->addCss('color', $color_active['light']);
$element->style_dark->child('.accordion-button:not(.collapsed)')->addCss('color', $color_active['dark']);

// Background color style
$element->style->child('.accordion-button')->addCss('background-color', $bgcolor['light']);
$element->style_dark->child('.accordion-button')->addCss('background-color', $bgcolor['dark']);
$element->style->child('.accordion-button')->hover()->addCss('background-color', $bgcolor_hover['light']);
$element->style_dark->child('.accordion-button')->hover()->addCss('background-color', $bgcolor_hover['dark']);
$element->style->child('.accordion-button:not(.collapsed)')->addCss('background-color', $bgcolor_active['light']);
$element->style_dark->child('.accordion-button:not(.collapsed)')->addCss('background-color', $bgcolor_active['dark']);