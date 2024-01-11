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
use Astroid\Framework;
use Astroid\Helper\Style;
$title                  = $params->get('title', '');
$url                    = $params->get('url', '');
$button_size            = $params->get('button_size', 24);
$width                  = $params->get('width', '');
$height                 = $params->get('height', '');
$use_border             = $params->get('use_border', '');
$border_width           = $params->get('border_width', '');
$ripple_color           = Style::getColor($params->get('ripple_color', ''));
$color                  = Style::getColor($params->get('color', ''));
$color_hover            = Style::getColor($params->get('color_hover', ''));
$background_color       = Style::getColor($params->get('background_color', ''));
$background_color_hover = Style::getColor($params->get('background_color_hover', ''));
$border_color           = Style::getColor($params->get('border_color', ''));

if (!empty($url)) {
    echo '<a class="video-button button-ripple d-inline-flex align-items-center justify-content-center rounded-pill" href="'.$url.'" title="'.$title.'" data-fancybox="astroid-'.$element->id.'"><span class="d-inline-flex justify-content-center align-items-center"><i class="fas fa-play"></i></span></a>';
    $document = Framework::getDocument();
    $document->loadFancyBox();
    $document->addScriptDeclaration('Fancybox.bind(\'[data-fancybox="astroid-'.$element->id.'"]\');', 'body');
    $style = new Style('#'. $element->id);
    $style_dark = new Style('#'. $element->id, 'dark');

    $style->child('.video-button')->addCss('font-size', $button_size . 'px');
    $style->child('.video-button i')->addCss('width', $button_size . 'px');
    $style->child('.video-button i')->addCss('height', $button_size . 'px');

    if ($ripple_color) {
        $style->child('.button-ripple:before')->addCss('box-shadow', '0 0 0 0 '.$ripple_color['light']);
        $style->child('.button-ripple:after')->addCss('box-shadow', '0 0 0 0 '.$ripple_color['light']);
        $style_dark->child('.button-ripple:before')->addCss('box-shadow', '0 0 0 0 '.$ripple_color['dark']);
        $style_dark->child('.button-ripple:after')->addCss('box-shadow', '0 0 0 0 '.$ripple_color['dark']);
    }

    $style->child('.video-button')->addCss('color', $color['light']);
    $style_dark->child('.video-button')->addCss('color', $color['dark']);
    $style->child('.video-button')->addCss('background-color', $background_color['light']);
    $style_dark->child('.video-button')->addCss('background-color', $background_color['dark']);

    $style->child('.video-button')->hover()->addCss('color', $color_hover['light']);
    $style_dark->child('.video-button')->hover()->addCss('color', $color_hover['dark']);
    $style->child('.video-button')->hover()->addCss('background-color', $background_color_hover['light']);
    $style_dark->child('.video-button')->hover()->addCss('background-color', $background_color_hover['dark']);

    $style->child('.video-button')->addCss('width', $width. 'px');
    $style->child('.video-button:before')->addCss('width', $width. 'px');
    $style->child('.video-button:after')->addCss('width', $width. 'px');

    $style->child('.video-button')->addCss('height', $height. 'px');
    $style->child('.video-button:before')->addCss('height', $height. 'px');
    $style->child('.video-button:after')->addCss('height', $height. 'px');

    if ($use_border) {
        $style->child('.video-button')->addCss('border-style', 'solid');
        $style->child('.video-button')->addCss('border-color', $border_color['light']);
        $style_dark->child('.video-button')->addCss('border-color', $border_color['dark']);
        $style->child('.video-button')->addCss('border-width', $border_width . 'px');
    }

    $style->render();
    $style_dark->render();
}