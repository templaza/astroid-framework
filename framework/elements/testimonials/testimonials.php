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
use Joomla\CMS\Factory;
use Astroid\Helper\Style;
use Astroid\Helper;
use Astroid\Helper\SubForm;

extract($displayData);
$testimonials     = new SubForm($params->get('testimonials', ''));
if (!count($testimonials->getData())) {
    return false;
}
$enable_slider      =   $params->get('enable_slider', 0);
$slider_autoplay    =   $params->get('slider_autoplay', 0);
$slider_nav         =   $params->get('slider_nav', 1);
$slider_scrollbar   =   $params->get('slider_scrollbar', 0);
$nav_position       =   $params->get('nav_position', '');
$nav_position       =   $nav_position !== '' ? ' ' . $nav_position : $nav_position;
$slider_dotnav      =   $params->get('slider_dotnav', 0);
$dot_alignment      =   $params->get('dot_alignment', '');
$interval           =   $params->get('interval', 3);
$slider_type        =   $params->get('slider_type', '');
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
    $slide_settings[]       =   'pagination: {el: ".swiper-pagination",clickable: true,}';
}

if ($slider_nav) {
    $slide_settings[]       =   'navigation: {nextEl: ".swiper-button-next",prevEl: ".swiper-button-prev",}';
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
    Style::renderTypography('#'.$element->id.' .as-author-name', $title_font_style, null, $element->isRoot);
}
$title_heading_margin=  $params->get('title_heading_margin', '');

$designation_font_style    =   $params->get('designation_font_style');
if (!empty($designation_font_style)) {
    Style::renderTypography('#'.$element->id.' .as-author-designation', $designation_font_style, null, $element->isRoot);
}

$designation_heading_margin=   $params->get('designation_heading_margin', '');
$designation_position      =   $params->get('designation_position', 'after');

$content_margin     =   $params->get('content_margin', '');
$content_font_style =   $params->get('content_font_style');
if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id.' .as-author-message', $content_font_style, null, $element->isRoot);
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
$card_vertical_align    =   $params->get('card_vertical_align','');

$slider_nav_position    =   $params->get('slider_nav_position','');
$testimonial_icon    =   $params->get('testimonial_icon','');

if ($text_alignment) {
    $alignment              =   ' justify-content' . ($text_alignment_breakpoint ? '-' . $text_alignment_breakpoint : '') . '-' . $text_alignment . ($text_alignment_fallback ? ' justify-content-' . $text_alignment_fallback : '');
} else {
    $alignment              =   '';
}
$swiper_cl = '';
$use_masonry        =   $params->get('use_masonry', 0);
$item_cl = ' testimonial-item';
if ($enable_slider) {
    if($slider_type=='swiper'){
        echo '<div class="swiper"'.(!empty($dir) ? ' dir="'.$dir.'"' : '').'>';
        $item_cl = 'swiper-slide';
        $swiper_cl = ' swiper-wrapper ';
    }else{
        $swiper_cl= ' astroid-slick opacity-0 ';
    }
}

echo '<div class="astroid-grid '.$swiper_cl.' '.($enable_slider ? '' . $nav_position : $row_column_cls . ($use_masonry ? ' as-masonry as-loading' : '')).$gutter_cls.$overlay_text_color.'">';
foreach ($testimonials->getData() as $key => $testimonial) {
    $avatar =   $testimonial->params->get('avatar', '');
    $rating =   $testimonial->params->get('rating', 5);
    $media  =   '';
    if ($avatar) {
        $media      =   '<div class="as-author-avatar d-inline-block position-relative overflow-hidden' . $image_border_radius . $box_shadow . $hover_effect . $transition . '">';
        $media      .=  '<img class="' . ($avatar_position == 'left' || $avatar_position == 'right' ? 'object-fit-cover w-100 h-100 ' : '') .'" src="'. Astroid\Helper\Media::getMediaPath($avatar) .'" alt="'.$testimonial->params->get('title', '').'">';
        $media      .=  '</div>';
    }

    echo '<div id="testimonial-'. $testimonial -> id .'" class="'.$item_cl.'"><div class="card' . $card_style . $box_shadow . $box_shadow_hover .$bd_radius . $card_hover_transition . ($enable_grid_match ? ' h-100' : '') . '">';
    if ($avatar_position == 'left' || $avatar_position == 'right') {
        echo '<div class="row g-0 '.$card_vertical_align.'">';
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
    if ($avatar_position == 'left' || $avatar_position == 'right' || $avatar_position == 'bottom') {
        if($testimonial_icon){
            echo '<div class="testimonial_icon"><i class="'.$testimonial_icon.'"></i></div>';
        }
    }
    if ($avatar_position == 'top') {
        echo $media;
    }
    if (!empty($testimonial->params->get('message', ''))) {
        echo '<div class="as-author-message">' . $testimonial->params->get('message', '') . '</div>';
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
    if (!empty($testimonial->params->get('designation', '')) && $designation_position == 'before') {
        echo '<div class="as-author-designation">' . $testimonial->params->get('designation', '') . '</div>';
    }
    if (!empty($testimonial->params->get('title', ''))) {
        echo '<'.$title_html_element.' class="as-author-name">'. $testimonial->params->get('title', '') . '</'.$title_html_element.'>';
    }
    if (!empty($testimonial->params->get('designation', '')) && $designation_position == 'after') {
        echo '<div class="as-author-designation">' . $testimonial->params->get('designation', '') . '</div>';
    }
    if (!empty($testimonial->params->get('link', '')) && !empty($testimonial->params->get('link_title', ''))) {
        echo '<a class="as-author-url" href="'.$testimonial->params->get('link', '').'" target="_blank">' . $testimonial->params->get('link_title', '') . '</a>';
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
$document = Astroid\Framework::getDocument();

$document->loadUIKit();
if ($enable_slider) {
    if($slider_type=='swiper') {
        if ($slider_dotnav) {
            echo '<div class="swiper-pagination"></div>';
        }
        if ($slider_nav) {
            if ($slider_nav_position) {
                echo '<div class="swiper_nav uk-flex ' . $slider_nav_position . '"><div class="swiper-button-prev swiper-nav-button uk-position-relative"></div><div class="swiper-button-next swiper-nav-button uk-position-relative"></div></div> ';
            } else {
                echo '<div class="swiper_nav uk-flex ' . $slider_nav_position . '"><div class="swiper-button-prev swiper-nav-button"></div><div class="swiper-button-next swiper-nav-button"></div></div> ';
            }

        }
        if ($slider_scrollbar) {
            echo '<div class="swiper-scrollbar"></div>';
        }
    }

    if($slider_type=='swiper'){
        echo '</div>';
        $document->loadSwiper('#'.$element->id.' .swiper', implode(',', $slide_settings));
    }else{
        $wa->registerAndUseStyle('slick.css', 'astroid/slick.min.css');
        $wa->registerAndUseScript('slick.js', 'astroid/slick.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
        echo '<script type="text/javascript">jQuery(document).ready(function(){jQuery(\'#'.$element->id.' .astroid-slick\').slick({'.implode(',', $slide_settings).'})});</script>';
    }
} elseif ($use_masonry) {
    $document->loadMasonry('#'. $element->id .' .as-masonry');
}

if ($params->get('card_size', '') == 'custom') {
    $card_padding   =   $params->get('card_padding', '');
    if (!empty($card_padding)) {
        Style::setSpacingStyle($element->style->child('.card-size-custom'), $card_padding);
    }
    $card_margin   =   $params->get('card_margin', '');
    if (!empty($card_margin)) {
        Style::setSpacingStyle($element->style->child('.card-size-custom'), $card_margin, 'margin');
    }
}
if ($params->get('card_style', '') == 'custom') {
    $text_color     =   Style::getColor($params->get('text_color', ''));
    $element->style->child('.card')->addCss('color', $text_color['light']);
    $element->style_dark->child('.card')->addCss('color', $text_color['dark']);

    $bg_color       =   Style::getColor($params->get('bg_color', ''));
    if ($avatar_position == 'left' || $avatar_position == 'right') {
        $element->style->child('.card-body')->addCss('background-color', $bg_color['light']);
        $element->style_dark->child('.card-body')->addCss('background-color', $bg_color['dark']);
    }else{
        $element->style->child('.card')->addCss('background-color', $bg_color['light']);
        $element->style_dark->child('.card')->addCss('background-color', $bg_color['dark']);
    }

    $card_border    =   json_decode($params->get('card_border', ''), true);
    if (!empty($card_border)) {
        Style::addBorderStyle('#'. $element->id . ' .card', $card_border, 'global', $element->isRoot);
    }

}
$slider_padding   =   $params->get('slider_padding', '');
if (!empty($slider_padding)) {
    Style::setSpacingStyle($element->style->child('.swiper'), $slider_padding);
}


$next_margin   =   $params->get('next_margin', '');
if (!empty($next_margin)) {
    Style::setSpacingStyle($element->style->child('.swiper-button-next'), $next_margin, 'margin');
}
$preview_margin   =   $params->get('preview_margin', '');
if (!empty($preview_margin)) {
    Style::setSpacingStyle($element->style->child('.swiper-button-prev'), $preview_margin, 'margin');
}
$slider_nav_height      =   $params->get('slider_nav_height', '');
$nav_height = json_decode($slider_nav_height, true);
if (json_last_error() === JSON_ERROR_NONE && is_array($nav_height)) {
    $element->style->child('.swiper-nav-button')->addResponsiveCSS('height', $nav_height, $nav_height['postfix']);
}
$slider_nav_width      =   $params->get('slider_nav_width', '');
$nav_width = json_decode($slider_nav_width, true);
if (json_last_error() === JSON_ERROR_NONE && is_array($nav_width)) {
    $element->style->child('.swiper-nav-button')->addResponsiveCSS('width', $nav_width, $nav_width['postfix']);
}
$nav_border    =   json_decode($params->get('slider_nav_border', ''), true);
if (!empty($nav_border)) {
    Style::addBorderStyle('#'. $element->id . ' .swiper-nav-button', $nav_border, 'global', $element->isRoot);
}
$nav_radius  =   $params->get('slider_nav_radius', '');
if (!empty($nav_radius)) {
    Style::setSpacingStyle($element->style->child('.swiper-nav-button'), $nav_radius,'radius');
}

if (!empty($image_max_width)) {
    $element->style->child('.as-author-avatar > img')->addCss('max-width', $image_max_width . 'px');
}
if (!empty($title_heading_margin)) {
    Style::setSpacingStyle($element->style->child('.as-author-name'), $title_heading_margin, 'margin');
}
if (!empty($designation_heading_margin)) {
    Style::setSpacingStyle($element->style->child('.as-author-designation'), $designation_heading_margin, 'margin');
}
if (!empty($content_margin)) {
    Style::setSpacingStyle($element->style->child('.as-author-message'), $content_margin, 'margin');
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

$nav_color     = Style::getColor($params->get('nav_color', ''));
$element->style->child('.swiper-nav-button')->addCss('color', $nav_color['light']);
$element->style_dark->child('.swiper-nav-button')->addCss('color', $nav_color['dark']);

$nav_bg_color     = Style::getColor($params->get('nav_bg_color', ''));
$element->style->child('.swiper-nav-button')->addCss('background-color', $nav_bg_color['light']);
$element->style_dark->child('.swiper-nav-button')->addCss('background-color', $nav_bg_color['dark']);

$nav_bg_color_hover     = Style::getColor($params->get('nav_bg_color_hover', ''));
$element->style->child('.swiper-nav-button:hover')->addCss('background-color', $nav_bg_color_hover['light']);
$element->style_dark->child('.swiper-nav-button:hover')->addCss('background-color', $nav_bg_color_hover['dark']);

$nav_color_hover     = Style::getColor($params->get('nav_color_hover', ''));
$element->style->child('.swiper-nav-button:hover')->addCss('color', $nav_color_hover['light']);
$element->style_dark->child('.swiper-nav-button:hover')->addCss('color', $nav_color_hover['dark']);

$nav_border_hover    =   json_decode($params->get('slider_nav_border_hover', ''), true);
if (!empty($nav_border_hover)) {
    Style::addBorderStyle('#'. $element->id . ' .swiper-nav-button:hover', $nav_border_hover, 'global', $element->isRoot);
}
$testimonial_icon_color     = Style::getColor($params->get('testimonial_icon_color', ''));
$element->style->child('.testimonial_icon')->addCss('color', $testimonial_icon_color['light']);
$element->style_dark->child('.testimonial_icon')->addCss('color', $testimonial_icon_color['dark']);
$testimonial_icon_size        =   $params->get('testimonial_icon_size', '30');
$icon_size = json_decode($testimonial_icon_size, true);
if (json_last_error() === JSON_ERROR_NONE && is_array($icon_size)) {
    $element->style->child('.testimonial_icon')->addResponsiveCSS('font-size', $icon_size, $icon_size['postfix']);
}

if($border_radius  == 'custom'){
    $card_radius_custom  =   $params->get('card_radius_custom', '');
    if ($avatar_position == 'left' || $avatar_position == 'right') {
        if (!empty($card_radius_custom)) {
            Style::setSpacingStyle($element->style->child('.card-body'), $card_radius_custom,'radius');
        }
    }else{
        if (!empty($card_radius_custom)) {
            Style::setSpacingStyle($element->style->child('.card'), $card_radius_custom,'radius');
        }
    }

}