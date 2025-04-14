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

use Astroid\Helper\Style;
use Astroid\Component\Article;
use Astroid\Framework;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\CMS\Uri\Uri;

extract($displayData);
$catids         = json_decode($params->get('catids', '[]'), true);

if (!count($catids)) {
    return false;
}
$categories = [];
foreach ($catids as $catid) {
    $categories[]   =   $catid['value'];
}
$document           =   Framework::getDocument();
$limit              =   $params->get('limit', 3);
$ordering           =   $params->get('ordering', 'latest');
$items = Article::getArticles($limit, $ordering, $categories);

$enable_slider      =   $params->get('enable_slider', 0);
$use_masonry        =   $params->get('use_masonry', 0);
$slider_autoplay    =   $params->get('slider_autoplay', 0);
$slider_nav         =   $params->get('slider_nav', 1);
$slider_dotnav      =   $params->get('slider_dotnav', 0);
$interval           =   $params->get('interval', 3);
$slide_settings     =   array();
$slide_responsive   =   array();

$style = $element->style;
$style_dark = $element->style_dark;
$row_column_cls     =   'row';

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
    $slide_settings[]       =  'responsive: ['.implode(',', $slide_responsive).']';
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

$card_style         =   $params->get('card_style', '');
$card_style         =   $card_style ? ' text-bg-' . $card_style : '';

$box_shadow         =   $params->get('card_box_shadow', '');
$box_shadow         =   $box_shadow ? ' ' . $box_shadow : '';
$box_shadow_hover   =   $params->get('card_box_shadow_hover', '');
$box_shadow         .=  $box_shadow_hover ? ' ' . $box_shadow_hover : '';

$card_size          =   $params->get('card_size', '');
$card_size          =   $card_size ? ' card-size-' . $card_size : '';

$text_color_mode    =   $params->get('text_color_mode', '');
$text_color_mode    =   $text_color_mode !== '' ? ' ' . $text_color_mode : '';

$card_rounded_size  =   $params->get('card_rounded_size', '3');
$border_radius      =   $params->get('card_border_radius', '');
$bd_radius          =   $border_radius != '' ? ' rounded-' . $border_radius : ' rounded-' . $card_rounded_size;

$media_width_cls    =   '';
$media_position     =   $params->get('media_position', 'top');
if ($media_position == 'right') {
    $media_width_cls.=  'order-2';
} else {
    $media_width_cls.=  'order-0';
}
$xxl_column_media   =   $params->get('xxl_column_media', '');
$media_width_cls    .=  $xxl_column_media ? ' col-xxl-' . $xxl_column_media : '';
$xl_column_media    =   $params->get('xl_column_media', '');
$media_width_cls    .=  $xl_column_media ? ' col-xl-' . $xl_column_media : '';
$lg_column_media    =   $params->get('lg_column_media', 4);
$media_width_cls    .=  $lg_column_media ? ' col-lg-' . $lg_column_media : '';
$md_column_media    =   $params->get('md_column_media', 12);
$media_width_cls    .=  $md_column_media ? ' col-md-' . $md_column_media : '';
$sm_column_media    =   $params->get('sm_column_media', 12);
$media_width_cls    .=  $sm_column_media ? ' col-sm-' . $sm_column_media : '';
$xs_column_media    =   $params->get('xs_column_media', 12);
$media_width_cls    .=  $xs_column_media ? ' col-' . $xs_column_media : '';

// Image Options
$layout             =   $params->get('layout', 'classic');
$thumbnail_only     =   $params->get('thumbnail_only', 0);
$linked_image       =   $params->get('linked_image', 0);
$enable_image_cover =   $params->get('enable_image_cover', 0);
$min_height         =   $params->get('min_height', 500);
$height             =   $params->get('height', '');
$overlay_type       =   $params->get('overlay_type', '');
$enable_grid_match  =   $params->get('enable_grid_match', 0);
$img_rounded_size   =   $params->get('img_rounded_size', '3');
$img_border_radius  =   $params->get('img_border_radius', '');
if ($img_border_radius == 'rounded') {
    $img_border_radius  = ' ' . $img_border_radius . '-' . $img_rounded_size;
} else {
    $img_border_radius  = $img_border_radius !== '' ? ' ' . $img_border_radius : '';
}

// Image Effect
$enable_image_effect=   $params->get('enable_image_effect', 0);
$brightness         =   $params->get('brightness', '100');
$contrast           =   $params->get('contrast', '100');
$saturate           =   $params->get('saturate', '100');
$blur               =   $params->get('blur', '0');
$hue_rotate         =   $params->get('hue_rotate', '0');

$brightness_hover   =   $params->get('brightness_hover', '100');
$contrast_hover     =   $params->get('contrast_hover', '100');
$saturate_hover     =   $params->get('saturate_hover', '100');
$blur_hover         =   $params->get('blur_hover', '0');
$hue_rotate_hover   =   $params->get('hue_rotate_hover', '0');

$title_html_element =   $params->get('title_html_element', 'h3');
$title_font_style   =   $params->get('title_font_style');
if (!empty($title_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-article-heading', $title_font_style, null, $element->isRoot);
}
$title_heading_margin=  $params->get('title_heading_margin', '');

$info_before_title  =   json_decode($params->get('info_before_title', '[]'), true);
$info_after_title   =   json_decode($params->get('info_after_title', '[]'), true);
$info_after_intro   =   json_decode($params->get('info_after_intro', '[]'), true);

$info_font_style    =   $params->get('info_font_style');
if (!empty($info_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-article-info', $info_font_style, null, $element->isRoot);
}

$info_margin                =   $params->get('info_margin', '');
$info_margin_before_title   =   $params->get('info_margin_before_title', '');
$info_margin_after_intro    =   $params->get('info_margin_after_intro', '');

$enable_intro_text  =   $params->get('enable_intro_text', 1);
$content_font_style =   $params->get('content_font_style');
$intro_limit        =   $params->get('intro_limit', 0);
if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-article-introtext', $content_font_style, null, $element->isRoot);
}

$readmore           =   $params->get('enable_readmore', 0);
$button_style       =   $params->get('button_style', 'primary');
$button_size        =   $params->get('button_size', '');
$button_size        =   $button_size ? ' '. $button_size : '';
$button_outline     =   $params->get('button_outline', 0);
$button_radius      =   $params->get('button_border_radius', '');
$button_radius      =   $button_radius ? ' ' . $button_radius : '';

// Button Custom Style

if ($button_style === 'custom') {
$color          = Style::getColor($params->get('btn_color', ''));
$color_hover    = Style::getColor($params->get('btn_color_hover', ''));
$color_active   = Style::getColor($params->get('btn_color_active', ''));
$bgcolor        = Style::getColor($params->get('btn_bgcolor', ''));
$bgcolor_hover  = Style::getColor($params->get('btn_bgcolor_hover', ''));
$bgcolor_active = Style::getColor($params->get('btn_bgcolor_active', ''));

// Color style
$element->style->child('.btn')->addCss('color', $color['light']);
$element->style_dark->child('.btn')->addCss('color', $color['dark']);
$element->style->child('.btn')->hover()->addCss('color', $color_hover['light']);
$element->style_dark->child('.btn')->hover()->addCss('color', $color_hover['dark']);
$element->style->child('.btn:not(.collapsed)')->addCss('color', $color_active['light']);
$element->style_dark->child('.btn:not(.collapsed)')->addCss('color', $color_active['dark']);

// Background color style
$element->style->child('.btn')->addCss('background-color', $bgcolor['light']);
$element->style_dark->child('.btn')->addCss('background-color', $bgcolor['dark']);
$element->style->child('.btn')->hover()->addCss('background-color', $bgcolor_hover['light']);
$element->style_dark->child('.btn')->hover()->addCss('background-color', $bgcolor_hover['dark']);
$element->style->child('.btn:not(.collapsed)')->addCss('background-color', $bgcolor_active['light']);
$element->style_dark->child('.btn:not(.collapsed)')->addCss('background-color', $bgcolor_active['dark']);
    }

$has_gallery        =   false;
echo '<div class="'.($enable_slider ? 'astroid-slick opacity-0' : $row_column_cls).$gutter_cls.$text_color_mode.'">';
foreach ($items as $key => $item) {
    $link           =   RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language);
    $video_type     =   $item->params->get('astroid_article_video_type', '');
    $media          =   '';
    if ($thumbnail_only && !empty($item->image_thumbnail)) {
        $media      =   $media      =   '<img class="'. ($media_position == 'bottom' ? 'order-2 ' : '') . ($media_position == 'left' || $media_position == 'right' ? 'object-fit-cover w-100 h-100 ' : '') . ($params->get('card_style', '') == 'none' || $border_radius !== '' ? '' : 'card-img-'. $media_position) .'" src="'. $item->image_thumbnail .'" alt="'.$item->title.'">';
            if ($linked_image) { $media = '<a href="' . Route::_($link) . '">' . $media . '</a>'; }
    } else {
        switch ($item->post_format) {
            case 'gallery':
                $gallery    =   (array) $item->params->get('astroid_article_gallery_items', array());
                if (count($gallery)) {
                    $has_gallery    =   true;
                    $media  =   '<div id="astroid-articles-'.$item->id.'" class="as-slideshow-media carousel slide carousel-fade overflow-hidden'.$img_border_radius.'" data-bs-ride="carousel">';
                    $media  .=  '<div class="carousel-inner">';
                    $active =   true;
                    foreach ($gallery as $gallery_item) {
                        $media  .=  '<div class="carousel-item'.($active ? ' active' : '').'" data-bs-interval="3000">';
                        $media  .=  '<a href="'.Route::_($link).'" title="'. $item->title . '">';
                        if ($enable_image_cover) {
                            $media  .=  '<div class="position-absolute top-0 start-0 end-0 bottom-0 astroid-image-overlay-cover">';
                        }
                        $media  .=  '<img src="'.$gallery_item->image.'" class="d-block w-100'.($enable_image_cover ? ' object-fit-cover w-100 h-100' : '').'" alt="'.$gallery_item->title.'">';
                        if ($enable_image_cover) {
                            $media  .=  '</div>';
                        }
                        $media  .=  '</a>';
                        $media  .=  '</div>';
                        $active =   false;
                    }
                    $media  .=  '</div>';
                    $media  .=  '</div>';
                }
                break;
            case 'video':
                $video_url  =   $item->params->get('astroid_article_video_url', '');
                $video_local_url  =   $item->params->get('astroid_article_video_local', '');
                $video_src  =   Article::getVideoSrc($video_url);
                if ($video_type !== 'local') {
                    if ($video_src) {
                        if ($video_type == 'vimeo') {
                            $video_src  .=  '?autoplay=1&loop=1&muted=1&autopause=0&title=0&byline=0&portrait=0&controls=0';
                        }
                        $media =    '<div class="entry-video ratio ratio-16x9 overflow-hidden'.$img_border_radius.'">';
                        $media .=   '<iframe src="' . $video_src . '" title="'.$item->title.'" allowfullscreen></iframe>';
                        $media .=   '</div>';
                        if ($video_type == 'youtube' && !empty($item->image_thumbnail)) {
                            $media      =   '<img class="'. ($media_position == 'bottom' ? 'order-2 ' : '') . ($media_position == 'left' || $media_position == 'right' ? 'object-fit-cover w-100 h-100 ' : '') . ($params->get('card_style', '') == 'none' || $border_radius !== '' ? '' : 'card-img-'. $media_position) .'" src="'. $item->image_thumbnail .'" alt="'.$item->title.'">';
                        }
                    }
                } elseif (!empty($video_local_url)) {
                    $document->loadVideoBG();
                    $media = '<a href="'.Route::_($link).'" title="'. $item->title . '"><div class="as-article-video-local as-image-cover astroid-image-overlay-cover'.(!$enable_image_cover ? ' ratio ratio-16x9' : '').'" data-as-video-bg="'.Uri::base('true').'/images/'.$video_local_url.'"'.(!empty($item->image_thumbnail) ? ' data-as-video-poster="'.$item->image_thumbnail.'"' : '').'></div></a>';
                }
                break;
            case 'audio':
                $renderer   =   new FileLayout('blog.audio', JPATH_LIBRARIES . '/astroid/framework/frontend');
                $media      =   $renderer->render(['article' => $item]);
                break;
            default:
                if (!empty($item->image_thumbnail)) {
                    $media      =   '<img class="'. ($media_position == 'bottom' ? 'order-2 ' : '') . ($media_position == 'left' || $media_position == 'right' ? 'object-fit-cover w-100 h-100 ' : '') . ($params->get('card_style', '') == 'none' || $border_radius !== '' ? '' : 'card-img-'. $media_position) .'" src="'. $item->image_thumbnail .'" alt="'.$item->title.'">';
                        if ($linked_image) { $media = '<a href="' . Route::_($link) . '">' . $media . '</a>'; }
                }
                break;
        }
    }
    $item_image_cover = !empty($item->image_thumbnail) && ($enable_image_cover || $layout == 'overlay');
    if ($item_image_cover && ($item->post_format !== 'video' || $video_type !== 'local')) {
        $media  =   '<a href="'.Route::_($link).'" title="'. $item->title . '"><div class="as-image-cover d-block overflow-hidden'.($layout == 'overlay' ? ' astroid-image-overlay-cover' : '').$img_border_radius.'"><img class="object-fit-cover w-100 h-100" src="'. $item->image_thumbnail .'" alt="'.$item->title.'"></div></a>';
    }
    if ($enable_image_effect) {
        $media  =   '<div class="as-image-effect">' . $media . '</div>';
    }
    echo '<div class="astroid-article-item astroid-grid '.$item->post_format.'"><div class="card overflow-hidden' . $card_style . $box_shadow . $bd_radius . ($enable_grid_match ? ' h-100' : '') . '">';
    if (($media_position == 'left' || $media_position == 'right') && !$item_image_cover && $layout == 'classic') {
        echo '<div class="row g-0">';
        echo '<div class="'.$media_width_cls.'">';
    }
    if ($media_position != 'inside') {
        echo $media;
    }
    if (($media_position == 'left' || $media_position == 'right') && !$item_image_cover && $layout == 'classic') {
        echo '</div>';
        echo '<div class="col order-1">';
    }

    echo '<div class="'.($layout == 'overlay' ? 'card-img-overlay as-light z-1' : 'order-1 card-body' ) . $card_size.'">'; // Start Card-Body

    if ($media_position == 'inside') {
        echo $media;
    }

    if (count($info_before_title)) {
        echo '<dl class="astroid-article-info before-title as-gutter-x-lg">';
        echo '<dt class="article-info-term">'.Text::_('COM_CONTENT_ARTICLE_INFO').'</dt>';
        foreach ($info_before_title as $info_item) {
            echo LayoutHelper::render('joomla.content.info_block.' . $info_item['value'], array('item' => $item, 'params' => $item->params));
        }
        echo '</dl>';
    }
    if (!empty($item->title)) {
        echo '<'.$title_html_element.' class="astroid-article-heading"><a href="'.Route::_($link).'" title="'. $item->title . '">'. $item->title . '</a></'.$title_html_element.'>';
    }
    if (count($info_after_title)) {
        echo '<dl class="astroid-article-info after-title as-gutter-x-lg">';
        echo '<dt class="article-info-term">'.Text::_('COM_CONTENT_ARTICLE_INFO').'</dt>';
        foreach ($info_after_title as $info_item) {
            echo LayoutHelper::render('joomla.content.info_block.' . $info_item['value'], array('item' => $item, 'params' => $item->params));
        }
        echo '</dl>';
    }
    if (!empty($item->introtext) && $enable_intro_text) {
        echo '<div class="astroid-article-introtext">' . (!empty($intro_limit) ? mb_substr(strip_tags($item->introtext), 0, $intro_limit, 'UTF-8') : $item->introtext) . '</div>';
    }

    if (count($info_after_intro)) {
        echo '<dl class="astroid-article-info after-intro as-gutter-x-lg">';
        echo '<dt class="article-info-term">'.Text::_('COM_CONTENT_ARTICLE_INFO').'</dt>';
        foreach ($info_after_intro as $info_item) {
            echo LayoutHelper::render('joomla.content.info_block.' . $info_item['value'], array('item' => $item, 'params' => $item->params));
        }
        echo '</dl>';
    }
    if ($readmore) {
        $button_class   =   $button_style !== 'text' ? 'btn btn-' . (intval($button_outline) ? 'outline-' : '') . $button_style . $button_size . $button_radius : 'as-btn-text text-uppercase text-reset';
        $btn_title      =   $button_style == 'text' ? '<small>'. Text::_('JGLOBAL_READ_MORE') . '</small>' : Text::_('JGLOBAL_READ_MORE');
        echo '<a id="btn-'.$item->id.'" href="'.Route::_($link).'" class="mt-3 ' . $button_class . '">' . $btn_title . '</a>';
    }

    echo '</div>'; // End Card-Body

    if (($media_position == 'left' || $media_position == 'right') && !$item_image_cover && $layout == 'classic') {
        echo '</div>';
        echo '</div>';
    }

    echo '</div></div>';
}
echo '</div>';
$mainframe = Factory::getApplication();
$wa = $mainframe->getDocument()->getWebAssetManager();
if ($has_gallery) {
    $wa->useScript('bootstrap.carousel');
}

if ($enable_slider) {
    $document->loadSlick('#'.$element->id.' .astroid-slick', implode(',', $slide_settings));
    if ($slider_nav) {
        $nav_color  =   Style::getColor($params->get('nav_color', ''));
        $style->child('.astroid-slick .slick-prev')->addCss('color', $nav_color['light']);
        $style->child('.astroid-slick .slick-next')->addCss('color', $nav_color['light']);
        $style_dark->child('.astroid-slick .slick-prev')->addCss('color', $nav_color['dark']);
        $style_dark->child('.astroid-slick .slick-next')->addCss('color', $nav_color['dark']);
    }

} elseif ($use_masonry) {
    $document->loadMasonry('#'. $element->id .' .as-masonry');
}
if ($params->get('card_size', '') == 'custom') {
    $card_padding   =   $params->get('card_padding', '');
    if (!empty($card_padding)) {
        Style::setSpacingStyle($element->style->child('.card-size-custom'), $card_padding);
    }
}
if (!empty($title_heading_margin)) {
    Style::setSpacingStyle($element->style->child('.astroid-article-heading'), $title_heading_margin, 'margin');
}
if (!empty($info_margin)) {
    Style::setSpacingStyle($element->style->child('.astroid-article-info.after-title'), $info_margin, 'margin');
}
if (!empty($info_margin_before_title)) {
    Style::setSpacingStyle($element->style->child('.astroid-article-info.before-title'), $info_margin_before_title, 'margin');
}
if (!empty($info_margin_after_intro)) {
    Style::setSpacingStyle($element->style->child('.astroid-article-info.after-intro'), $info_margin_after_intro, 'margin');
}
if ($enable_image_cover) {
    if (!empty($height)) {
        $style->child('.as-image-cover')->addCss('min-height', $min_height . 'px');
        $style->child('.as-slideshow-media .carousel-item')->addCss('min-height', $min_height . 'px');
        $style->child('.as-image-cover')->addCss('height', $height);
        $style->child('.as-slideshow-media .carousel-item')->addCss('height', $height);
    } else {
        $style->child('.as-image-cover')->addCss('height', $min_height . 'px');
        $style->child('.as-slideshow-media .carousel-item')->addCss('height', $min_height . 'px');
    }
}
if ($enable_image_effect) {
    $style->child('.as-image-effect')->addCss('filter', 'brightness('.$brightness.'%) contrast('.$contrast.'%) saturate('.$saturate.'%) blur('.$blur.'px) hue-rotate('.$hue_rotate.'deg)');
    $style->child('.card')->hover()->child('.as-image-effect')->addCss('filter', 'brightness('.$brightness_hover.'%) contrast('.$contrast_hover.'%) saturate('.$saturate_hover.'%) blur('.$blur_hover.'px) hue-rotate('.$hue_rotate_hover.'deg)');
}
switch ($overlay_type) {
    case 'color':
        $overlay_color      =   Style::getColor($params->get('overlay_color', ''));
        $style->child('.astroid-image-overlay-cover:after')->addCss('background-color', $overlay_color['light']);
        $style_dark->child('.astroid-image-overlay-cover:after')->addCss('background-color', $overlay_color['dark']);

        $style->child('.astroid-element-overlay:before')->addCss('background-color', $overlay_color['light']);
        $style_dark->child('.astroid-element-overlay:before')->addCss('background-color', $overlay_color['dark']);
        break;
    case 'background-color':
        $overlay_gradient   =   $params->get('overlay_gradient', '');
        if (!empty($overlay_gradient)) {
            $style->child('.astroid-image-overlay-cover:after')->addCss('background-image', Style::getGradientValue($overlay_gradient));
            $style->child('.astroid-element-overlay:before')->addCss('background-image', Style::getGradientValue($overlay_gradient));
        }
        break;
}