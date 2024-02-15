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

use Joomla\CMS\Factory;
use Astroid\Helper\Style;

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
$slider_autoplay    =   $params->get('slider_autoplay', 0);
$slider_nav         =   $params->get('slider_nav', 1);
$slider_dotnav      =   $params->get('slider_dotnav', 0);
$interval           =   $params->get('interval', 3);
$slide_settings     =   array();
$slide_responsive   =   array();

$row_column_cls     =   'row';

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

$row_gutter         =   $params->get('row_gutter', 4);
$gutter_cls         =  ' gx-' . $row_gutter;
$column_gutter      =   $params->get('column_gutter', 4);
$gutter_cls         .=  ' gy-' . $column_gutter;

$border_radius  = $params->get('border_radius', '');
$border_radius  = $border_radius !== '' ? ' ' . $border_radius : '';
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
        if ($image_params['use_link']) {
            echo '<a href="'.$image_params['link'].'" title="'.$image_params['title'].'">';
        }
        echo '<div class="position-relative overflow-hidden' . $border_radius . $box_shadow . $hover_effect . $transition . '">';
        echo '<img src="'. Astroid\Helper\Media::getPath() . '/' . $image_params['image'].'" alt="'.$image_params['title'].'" class="d-inline-block">';
        echo '</div>';
        if ($image_params['use_link']) {
            echo '</a>';
        }
    }
}
echo '</div>';
if ($enable_slider) {
    $mainframe = Factory::getApplication();
    $wa = $mainframe->getDocument()->getWebAssetManager();
    $wa->registerAndUseStyle('astroid.slick', 'astroid/slick.min.css');
    $wa->registerAndUseScript('astroid.slick', 'astroid/slick.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
    echo '<script type="text/javascript">jQuery(document).ready(function(){jQuery(\'#'.$element->id.' .astroid-slick\').slick({'.implode(',', $slide_settings).'})});</script>';
}