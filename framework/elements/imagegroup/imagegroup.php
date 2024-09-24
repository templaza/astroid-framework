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

use Astroid\Framework;
use Astroid\Helper\Style;
use Joomla\CMS\Uri\Uri;

extract($displayData);
$images       = $params->get('images', '');
if (empty($images)) {
    return false;
}
$images       = json_decode($images);
if (!count($images)) {
    return false;
}

$enable_slider      =   $params->get('enable_slider', 0);
$use_masonry        =   $params->get('use_masonry', 0);
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
    $row_column_cls .=  ' as-masonry';
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
$border_radius      =   $params->get('border_radius', '');
if ($border_radius == 'rounded') {
    $border_radius  = ' ' . $border_radius . '-' . $rounded_size;
} else {
    $border_radius  = $border_radius !== '' ? ' ' . $border_radius : '';
}
$box_shadow     = $params->get('box_shadow', '');
$box_shadow     = $box_shadow !== '' ? ' ' . $box_shadow : '';
$hover_effect   = $params->get('hover_effect', '');
$hover_effect   = $hover_effect !== '' ? ' as-effect-' . $hover_effect : '';
$transition     = $params->get('hover_transition', '');
$transition     = $transition !== '' ? ' as-transition-' . $transition : '';

$text_color_mode    =   $params->get('text_color_mode', '');
$text_color_mode    =   $text_color_mode !== '' ? ' ' . $text_color_mode : '';
echo '<div class="'.($enable_slider ? 'astroid-slick overflow-hidden opacity-0' : $row_column_cls).$gutter_cls.$text_color_mode.'">';
foreach ($images as $image) {
    $image_params   =   Style::getSubFormParams($image->params);
    if (!empty($image_params['image'])) {
        echo '<div>';
        if ($image_params['use_link']) {
            echo '<a href="'.$image_params['link'].'" title="'.$image_params['title'].'">';
        }
        echo '<div class="position-relative overflow-hidden' . $border_radius . $box_shadow . $hover_effect . $transition . '">';
        echo '<img src="'. Astroid\Helper\Media::getMediaPath($image_params['image']).'" alt="'.$image_params['title'].'" class="d-inline-block">';
        echo '</div>';
        if ($image_params['use_link']) {
            echo '</a>';
        }
        echo '</div>';
    }
}
echo '</div>';
$document = Framework::getDocument();

if ($enable_slider) {
    $document->loadSlick('#'.$element->id.' .astroid-slick', implode(',', $slide_settings));
} elseif ($use_masonry) {
    $document->loadMasonry();
}