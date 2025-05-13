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

use Astroid\Framework;
use Astroid\Helper\Style;
use Joomla\CMS\Uri\Uri;
use Astroid\Helper\SubForm;

extract($displayData);
$images     = new SubForm($params->get('images', ''));
if (!count($images->getData())) {
    return false;
}

$enable_slider      =   $params->get('enable_slider', 0);
$use_masonry        =   $params->get('use_masonry', 0);
$use_lightbox       =   $params->get('use_lightbox', 0);
$enable_image_cover =   $params->get('enable_image_cover', 0);
$min_height         =   $params->get('min_height', 500);
$height             =   $params->get('height', '');
$slider_autoplay    =   $params->get('slider_autoplay', 0);
$slider_nav         =   $params->get('slider_nav', 1);
$slider_dotnav      =   $params->get('slider_dotnav', 0);
$interval           =   $params->get('interval', 3);
$slide_settings     =   array();
$slide_responsive   =   array();

$enable_column_alignment    =   $params->get('enable_column_alignment', 0);
$column_alignment           =   $params->get('column_alignment', '');
$text_alignment             =   $params->get('text_alignment', '');

$row_column_cls     =   'row';

if ($enable_column_alignment) {
    $row_column_cls .=  ' ' . $column_alignment;
}

if ($text_alignment) {
    $row_column_cls .=  ' justify-content-' . $text_alignment;
}

if ($use_masonry && !$enable_slider) {
    $row_column_cls .=  ' as-masonry as-loading';
}

$xxl_column         =   $params->get('xxl_column', '');
if ($xxl_column) {
    $slide_settings[]=  'slidesToShow: ' . $xxl_column;
    $row_column_cls .=  ' row-cols-xxl-' . $xxl_column;
}

$xl_column          =   $params->get('xl_column', '');
if ($xl_column) {
    $row_column_cls .=  ' row-cols-xl-' . $xl_column;
    if (!count($slide_settings)) {
        $slide_settings[]       =  'slidesToShow: ' . $xl_column;
    } else {
        $slide_responsive[]     =   '{breakpoint: 1400,settings: {slidesToShow: ' . $xl_column.'}}';
    }
}

$lg_column          =   $params->get('lg_column', 3);
if ($lg_column) {
    $row_column_cls .=  ' row-cols-lg-' . $lg_column;
    if (!count($slide_settings)) {
        $slide_settings[]       =  'slidesToShow: ' . $lg_column;
    } else {
        $slide_responsive[]     =   '{breakpoint: 1200,settings: {slidesToShow: ' . $lg_column.'}}';
    }
}

$md_column          =   $params->get('md_column', 1);
if ($md_column) {
    $row_column_cls .=  ' row-cols-md-' . $md_column;
    if (!count($slide_settings)) {
        $slide_settings[]       =  'slidesToShow: ' . $md_column;
    } else {
        $slide_responsive[]     =   '{breakpoint: 992,settings: {slidesToShow: ' . $md_column.'}}';
    }
}

$sm_column          =   $params->get('sm_column', 1);
if ($sm_column) {
    $row_column_cls .=  ' row-cols-sm-' . $sm_column;
    if (!count($slide_settings)) {
        $slide_settings[]       =  'slidesToShow: ' . $sm_column;
    } else {
        $slide_responsive[]     =   '{breakpoint: 768,settings: {slidesToShow: ' . $sm_column.'}}';
    }
}

$xs_column          =   $params->get('xs_column', 1);
if ($xs_column) {
    $row_column_cls .=  ' row-cols-' . $xs_column;
    if (!count($slide_settings)) {
        $slide_settings[]       =  'slidesToShow: ' . $xs_column;
    } else {
        $slide_responsive[]     =   '{breakpoint: 576,settings: {slidesToShow: ' . $xs_column.'}}';
    }
}

if ($slider_autoplay) {
    $slide_settings[]       =   'autoplay: true';
    $slide_settings[]       =   'autoplaySpeed: '. ($interval * 1000);
}

if ($slider_dotnav) {
    $slide_settings[]       =   'dots: true';
}

if (!$slider_nav) {
    $slide_settings[]       =   'arrows: false';
}

if (count($slide_responsive)) {
    $slide_settings[]       =   'responsive: ['.implode(',', $slide_responsive).']';
}
$gutter_cls         =   '';
$responsive_key     =   ['xxl', 'xl', 'lg', 'md', 'sm', 'xs'];
foreach ($responsive_key as $key) {
    if ($key !== 'xs') {
        $row_gutter         =   $params->get('row_gutter_'.$key, '');
        $column_gutter      =   $params->get('column_gutter_'. $key, '');
        if ($row_gutter) {
            $gutter_cls     .=  ' gy-' . $key . '-' . $row_gutter;
        }
        if ($column_gutter) {
            $gutter_cls     .=  ' gx-' . $key . '-' . $column_gutter;
        }
    } else {
        $row_gutter         =   $params->get('row_gutter', 3);
        $column_gutter      =   $params->get('column_gutter', 3);
        $gutter_cls         .=  ' gy-' . $row_gutter;
        $gutter_cls         .=  ' gx-' . $column_gutter;
    }
}

$rounded_size       =   $params->get('rounded_size', '3');
$border_radius      =   $params->get('img_border_radius', '');
if ($border_radius == 'rounded') {
    $border_radius  = ' ' . $border_radius . '-' . $rounded_size;
} else {
    $border_radius  = $border_radius !== '' ? ' ' . $border_radius : '';
}
$box_shadow     = $params->get('box_shadow', '');
$box_shadow     = $box_shadow !== '' ? ' ' . $box_shadow : '';
$hover_effect   = $params->get('hover_effect', '');
$hover_effect_cls   = $hover_effect !== '' ? ' as-effect-' . $hover_effect : ' astroid-image-overlay-cover';
$transition     = $params->get('hover_transition', '');
$transition     = $transition !== '' ? ' as-transition-' . $transition : '';

$text_color_mode    =   $params->get('text_color_mode', '');
$text_color_mode    =   $text_color_mode !== '' ? ' ' . $text_color_mode : '';

$display_title      =   $params->get('display_title', 0);
$title_html_element =   $params->get('title_html_element', 'h3');
$title_font_style   =   $params->get('title_font_style');
if (!empty($display_title) && !empty($title_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-heading', $title_font_style, null, $element->isRoot);
}

$overlay_type       =   $params->get('overlay_type', '');
echo '<div class="'.($enable_slider ? 'astroid-slick overflow-hidden opacity-0' : $row_column_cls).$gutter_cls.$text_color_mode.'">';
foreach ($images->getData() as $image) {
    if (!empty($image->params->get('image'))) {
        echo '<div>';
        if ($image->params->get('use_link')) {
            echo '<a href="'.$image->params->get('link').'" title="'.$image->params->get('title').'">';
        } elseif ($use_lightbox) {
            echo '<a href="'. Astroid\Helper\Media::getMediaPath($image->params->get('image')).'" data-fancybox="astroid-'.$element->id.'">';
        }
        echo '<div class="as-image-group-item position-relative overflow-hidden' . ($enable_image_cover ? ' as-image-cover' : '') . $border_radius . $box_shadow . $hover_effect_cls . $transition . '">';
        echo '<img src="'. Astroid\Helper\Media::getMediaPath($image->params->get('image')).'" alt="'.$image->params->get('title').'" class="d-inline-block'.($enable_image_cover ? ' object-fit-cover w-100 h-100' : '').'">';
        echo !empty($display_title) && !empty($image->params->get('title')) ? '<'.$title_html_element.' class="astroid-heading position-absolute bottom-0 w-100 p-5 m-0 pe-none">'. $image->params->get('title') .'</'.$title_html_element.'>' : '';
        echo '</div>';
        if ($image->params->get('use_link') || $use_lightbox) {
            echo '</a>';
        }
        echo '</div>';
    }
}
echo '</div>';
$document = Framework::getDocument();

if ($use_lightbox) {
    $document->loadFancyBox();
    $document->addScriptDeclaration('document.addEventListener(\'DOMContentLoaded\', function() {Fancybox.bind(\'[data-fancybox="astroid-'.$element->id.'"]\');});', 'body');
}
if ($enable_slider) {
    $document->loadSlick('#'.$element->id.' .astroid-slick', implode(',', $slide_settings));
} elseif ($use_masonry) {
    $document->loadMasonry('#'. $element->id .' .as-masonry');
}

if ($enable_image_cover) {
    if (!empty($height)) {
        $element->style->child('.as-image-cover')->addCss('min-height', $min_height . 'px');
        $element->style->child('.as-image-cover')->addCss('height', $height);
    } else {
        $element->style->child('.as-image-cover')->addCss('height', $min_height . 'px');
    }
}

if ($hover_effect == '') {
    switch ($overlay_type) {
        case 'color':
            $overlay_color      =   Style::getColor($params->get('overlay_color', ''));
            $element->style->child('.astroid-image-overlay-cover:after')->addCss('background-color', $overlay_color['light']);
            $element->style_dark->child('.astroid-image-overlay-cover:after')->addCss('background-color', $overlay_color['dark']);
            break;
        case 'gradient':
            $overlay_gradient   =   $params->get('overlay_gradient', '');
            if (!empty($overlay_gradient)) {
                $element->style->child('.astroid-image-overlay-cover:after')->addCss('background-image', Style::getGradientValue($overlay_gradient));
            }
            break;
    }
}