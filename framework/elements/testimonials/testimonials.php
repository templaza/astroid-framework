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
use Astroid\Helper;

extract($displayData);
$testimonials   = $params->get('testimonials', '');
if (empty($testimonials)) {
    return false;
}
$testimonials   = json_decode($testimonials);
if (!count($testimonials)) {
    return false;
}

$enable_slider      =   $params->get('enable_slider', 0);
$slider_autoplay    =   $params->get('slider_autoplay', 0);
$slider_nav         =   $params->get('slider_nav', 1);
$nav_position       =   $params->get('nav_position', '');
$nav_position       =   $nav_position !== '' ? ' ' . $nav_position : $nav_position;
$slider_dotnav      =   $params->get('slider_dotnav', 0);
$dot_alignment      =   $params->get('dot_alignment', '');
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
    $slide_settings[]       =  'responsive: ['.implode(',', $slide_responsive).']';
}

$responsive_key     =   ['xxl', 'xl', 'lg', 'md', 'sm', 'xs'];
$gutter_cls         =   '';
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

$card_style         =   $params->get('card_style', '');
$card_style         =   $card_style ? ' text-bg-' . $card_style : '';

$card_size          =   $params->get('card_size', '');
$card_size          =   $card_size ? ' card-size-' . $card_size : '';

$card_rounded_size  =   $params->get('card_rounded_size', '3');
$border_radius      =   $params->get('card_border_radius', '');
$bd_radius          =   $border_radius != '' ? ' rounded-' . $border_radius : ' rounded-' . $card_rounded_size;

$avatar_width_cls    =   '';
$avatar_position    =   $params->get('avatar_position', 'top');
if ($avatar_position == 'right') {
    $avatar_width_cls.=  'order-2';
} else {
    $avatar_width_cls.=  'order-0';
}
$xxl_column_avatar   =   $params->get('xxl_column_avatar', '');
$avatar_width_cls    .=  $xxl_column_avatar ? ' col-xxl-' . $xxl_column_avatar : '';
$xl_column_avatar    =   $params->get('xl_column_avatar', '');
$avatar_width_cls    .=  $xl_column_avatar ? ' col-xl-' . $xl_column_avatar : '';
$lg_column_avatar    =   $params->get('lg_column_avatar', 4);
$avatar_width_cls    .=  $lg_column_avatar ? ' col-lg-' . $lg_column_avatar : '';
$md_column_avatar    =   $params->get('md_column_avatar', 12);
$avatar_width_cls    .=  $md_column_avatar ? ' col-md-' . $md_column_avatar : '';
$sm_column_avatar    =   $params->get('sm_column_avatar', 12);
$avatar_width_cls    .=  $sm_column_avatar ? ' col-sm-' . $sm_column_avatar : '';
$xs_column_avatar    =   $params->get('xs_column_avatar', 12);
$avatar_width_cls    .=  $xs_column_avatar ? ' col-' . $xs_column_avatar : '';

$enable_grid_match  =   $params->get('enable_grid_match', 0);

$box_shadow         =   $params->get('card_box_shadow', '');
$box_shadow         =   $box_shadow ? ' ' . $box_shadow : '';
$box_shadow_hover   =   $params->get('card_box_shadow_hover', '');
$box_shadow_hover   =   $box_shadow_hover ? ' ' . $box_shadow_hover : '';

$title_html_element =   $params->get('title_html_element', 'h3');
$title_font_style   =   $params->get('title_font_style');
if (!empty($title_font_style)) {
    Style::renderTypography('#'.$element->id.' .as-author-name', $title_font_style);
}
$title_heading_margin=  $params->get('title_heading_margin', '');

$designation_font_style    =   $params->get('designation_font_style');
if (!empty($designation_font_style)) {
    Style::renderTypography('#'.$element->id.' .as-author-designation', $designation_font_style);
}

$designation_heading_margin=   $params->get('designation_heading_margin', '');
$designation_position      =   $params->get('designation_position', 'after');

$content_margin     =   $params->get('content_margin', '');
$content_font_style =   $params->get('content_font_style');
if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id.' .as-author-message', $content_font_style);
}

$image_max_width        =   $params->get('image_max_width', '200');
$image_rounded_size     =   $params->get('image_rounded_size', '3');
$image_border_radius    =   $params->get('image_border_radius', '0');
$image_border_radius    =   $image_border_radius != 'rounded' ? ' rounded-' . $image_border_radius : ' rounded-' . $image_rounded_size;

$image_border           =   json_decode($params->get('image_border', ''), true);

$hover_effect   = $params->get('hover_effect', '');
$hover_effect   = $hover_effect !== '' ? ' as-effect-' . $hover_effect : '';
$transition     = $params->get('hover_transition', '');
$transition     = $transition !== '' ? ' as-transition-' . $transition : '';

$card_hover_transition     = $params->get('card_hover_transition', '');
$card_hover_transition     = $card_hover_transition !== '' ? ' as-transition-' . $card_hover_transition : '';

$overlay_text_color =   $params->get('overlay_text_color', '');
$overlay_text_color =   $overlay_text_color !== '' ? ' ' . $overlay_text_color : '';

$enable_rating      =   $params->get('enable_rating', 0);

// Alignment
$text_alignment             =   $params->get('text_alignment','');
$text_alignment_breakpoint  =   $params->get('text_alignment_breakpoint','');
$text_alignment_fallback    =   $params->get('text_alignment_fallback','');
if ($text_alignment) {
    $alignment              =   ' justify-content' . ($text_alignment_breakpoint ? '-' . $text_alignment_breakpoint : '') . '-' . $text_alignment . ($text_alignment_fallback ? ' justify-content-' . $text_alignment_fallback : '');
} else {
    $alignment              =   '';
}
echo '<div class="astroid-grid '.($enable_slider ? 'astroid-slick opacity-0' . $nav_position : $row_column_cls).$gutter_cls.$overlay_text_color.'">';
foreach ($testimonials as $key => $testimonial) {
    $testimonial_params    =   Helper::loadParams($testimonial->params);
    $avatar =   $testimonial_params->get('avatar', '');
    $rating =   $testimonial_params->get('rating', 5);
    $media  =   '';
    if ($avatar) {
        $media      =   '<div class="as-author-avatar d-inline-block position-relative overflow-hidden' . $image_border_radius . $box_shadow . $hover_effect . $transition . '">';
        $media      .=  '<img class="' . ($avatar_position == 'left' || $avatar_position == 'right' ? 'object-fit-cover w-100 h-100 ' : '') .'" src="'. Astroid\Helper\Media::getPath() . '/' . $avatar .'" alt="'.$testimonial_params->get('title', '').'">';
        $media      .=  '</div>';
    }

    echo '<div id="testimonial-'. $testimonial -> id .'"><div class="card' . $card_style . $box_shadow . $box_shadow_hover .$bd_radius . $card_hover_transition . ($enable_grid_match ? ' h-100' : '') . '">';
    if ($avatar_position == 'left' || $avatar_position == 'right') {
        echo '<div class="row g-0">';
        echo '<div class="'.$avatar_width_cls.'">';
    }
    if ($avatar_position == 'left' || $avatar_position == 'right') {
        echo $media;
    }
    if ($avatar_position == 'left' || $avatar_position == 'right') {
        echo '</div>';
        echo '<div class="col order-1">';
    }

    echo '<div class="order-1 card-body'.$card_size.'">'; // Start Card-Body

    if ($avatar_position == 'top') {
        echo $media;
    }
    if (!empty($testimonial_params->get('message', ''))) {
        echo '<div class="as-author-message">' . $testimonial_params->get('message', '') . '</div>';
    }
    if ($avatar_position == 'bottom') {
        echo $media;
    }
    if (!empty($enable_rating)) {
        echo '<div class="as-rating-block row row-cols-auto gx-2'.$alignment.'">';
        for ($i = 0; $i < 5 ; $i++) {
            if ($i < $rating) {
                if ($rating - $i >= 1) {
                    echo '<div class="as-star"><i class="fa-solid fa-star"></i></div>';
                } else {
                    echo '<div class="as-star"><i class="fa-solid fa-star-half-stroke"></i></div>';
                }
            } else {
                echo '<div class="as-star"><i class="fa-regular fa-star"></i></div>';
            }
        }
        echo '</div>';
    }
    if (!empty($testimonial_params->get('designation', '')) && $designation_position == 'before') {
        echo '<div class="as-author-designation">' . $testimonial_params->get('designation', '') . '</div>';
    }
    if (!empty($testimonial_params->get('title', ''))) {
        echo '<'.$title_html_element.' class="as-author-name">'. $testimonial_params->get('title', '') . '</'.$title_html_element.'>';
    }
    if (!empty($testimonial_params->get('designation', '')) && $designation_position == 'after') {
        echo '<div class="as-author-designation">' . $testimonial_params->get('designation', '') . '</div>';
    }
    if (!empty($testimonial_params->get('link', '')) && !empty($testimonial_params->get('link_title', ''))) {
        echo '<a class="as-author-url" href="'.$testimonial_params->get('link', '').'" target="_blank">' . $testimonial_params->get('link_title', '') . '</a>';
    }

    echo '</div>'; // End Card-Body

    if ($avatar_position == 'left' || $avatar_position == 'right') {
        echo '</div>';
        echo '</div>';
    }

    echo '</div></div>';
}
echo '</div>';
$mainframe = Factory::getApplication();
$wa = $mainframe->getDocument()->getWebAssetManager();

if ($enable_slider) {
    $wa->registerAndUseStyle('slick.css', 'astroid/slick.min.css');
    $wa->registerAndUseScript('slick.js', 'astroid/slick.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
    echo '<script type="text/javascript">jQuery(document).ready(function(){jQuery(\'#'.$element->id.' .astroid-slick\').slick({'.implode(',', $slide_settings).'})});</script>';
}

if ($params->get('card_size', '') == 'custom') {
    $card_padding   =   $params->get('card_padding', '');
    if (!empty($card_padding)) {
        $padding = \json_decode($card_padding, false);
        foreach ($padding as $device => $props) {
            $element->style->child('.card-size-custom')->addStyle(Style::spacingValue($props, "padding"), $device);
        }
    }
}
if (!empty($image_max_width)) {
    $element->style->child('.as-author-avatar > img')->addCss('max-width', $image_max_width . 'px');
}
if (!empty($title_heading_margin)) {
    $margin = \json_decode($title_heading_margin, false);
    foreach ($margin as $device => $props) {
        $element->style->child('.as-author-name')->addStyle(Style::spacingValue($props, "margin"), $device);
    }
}
if (!empty($designation_heading_margin)) {
    $margin = \json_decode($designation_heading_margin, false);
    foreach ($margin as $device => $props) {
        $element->style->child('.as-author-designation')->addStyle(Style::spacingValue($props, "margin"), $device);
    }
}
if (!empty($content_margin)) {
    $margin = \json_decode($content_margin, false);
    foreach ($margin as $device => $props) {
        $element->style->child('.as-author-message')->addStyle(Style::spacingValue($props, "margin"), $device);
    }
}
if (!empty($image_border)) {
    Style::addBorderStyle('#'. $element->id . ' .as-author-avatar', $image_border);
}
if (!empty($dot_alignment)) {
    $element->style->child('.astroid-slick .slick-dots')->addCss('text-align', $dot_alignment);
}
if (!empty($enable_rating)) {
    $rating_color   =   Style::getColor($params->get('rating_color', ''));
    $element->style->child('.as-rating-block')->addCss('color', $rating_color['light']);
    $element->style_dark->child('.as-rating-block')->addCss('color', $rating_color['dark']);
}