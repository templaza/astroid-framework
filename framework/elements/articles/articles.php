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
use Astroid\Component\Article;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

extract($displayData);
$catids         = json_decode($params->get('catids', '[]'), true);

if (!count($catids)) {
    return false;
}
$categories = [];
foreach ($catids as $catid) {
    $categories[]   =   $catid['value'];
}
$limit              =   $params->get('limit', 3);
$ordering           =   $params->get('ordering', 'latest');
$items = Article::getArticles($limit, $ordering, $categories);

$style = new Style('#'. $element->id);
$style_dark = new Style('#'. $element->id, 'dark');
$row_column_cls     =   '';
$xxl_column         =   $params->get('xxl_column', '');
$row_column_cls     .=  $xxl_column ? ' row-cols-xxl-' . $xxl_column : '';
$xl_column          =   $params->get('xl_column', '');
$row_column_cls     .=  $xl_column ? ' row-cols-xl-' . $xl_column : '';
$lg_column          =   $params->get('lg_column', 3);
$row_column_cls     .=  $lg_column ? ' row-cols-lg-' . $lg_column : '';
$md_column          =   $params->get('md_column', 1);
$row_column_cls     .=  $md_column ? ' row-cols-md-' . $md_column : '';
$sm_column          =   $params->get('sm_column', 1);
$row_column_cls     .=  $sm_column ? ' row-cols-sm-' . $sm_column : '';
$xs_column          =   $params->get('xs_column', 1);
$row_column_cls     .=  $xs_column ? ' row-cols-' . $xs_column : '';
$row_gutter         =   $params->get('row_gutter', 4);
$row_column_cls     .=  ' gx-' . $row_gutter;
$column_gutter      =   $params->get('column_gutter', 4);
$row_column_cls     .=  ' gy-' . $column_gutter;

$card_style         =   $params->get('card_style', '');
$card_style         =   $card_style ? ' text-bg-' . $card_style : '';

$card_size          =   $params->get('card_size', '');
$card_size          =   $card_size ? ' card-size-' . $card_size : '';

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
$enable_image_cover =   $params->get('enable_image_cover', 0);
$min_height         =   $params->get('min_height', 0);
$overlay_type       =   $params->get('overlay_type', '');
$enable_grid_match  =   $params->get('enable_grid_match', 0);

$title_html_element =   $params->get('title_html_element', 'h3');
$title_font_style   =   $params->get('title_font_style');
if (!empty($title_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-article-heading', $title_font_style);
}
$title_heading_margin=  $params->get('title_heading_margin', '');

$info_before_title  =   json_decode($params->get('info_before_title', '[]'), true);
$info_after_title   =   json_decode($params->get('info_after_title', '[]'), true);
$info_after_intro   =   json_decode($params->get('info_after_intro', '[]'), true);

$info_font_style    =   $params->get('info_font_style');
if (!empty($info_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-article-info', $info_font_style);
}

$info_margin        =   $params->get('info_margin', '');

$enable_intro_text =   $params->get('enable_intro_text', 1);
$content_font_style =   $params->get('content_font_style');
if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-article-introtext', $content_font_style);
}

$readmore           =   $params->get('enable_readmore', 0);
$button_style       =   $params->get('button_style', 'primary');
$button_size        =   $params->get('button_size', '');
$button_size        =   $button_size ? ' '. $button_size : '';
$button_outline     =   $params->get('button_outline', 0);
$button_radius      =   $params->get('button_border_radius', '');
$button_radius      =   $button_radius ? ' ' . $button_radius : '';

$has_gallery        =   false;
echo '<div class="row'.$row_column_cls.'">';
foreach ($items as $key => $item) {
    $link           =   RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language);
    $media          =   '';
    switch ($item->post_format) {
        case 'regular':
        case 'review':
            if (!empty($item->image_thumbnail)) {
                $media      =   '<img class="'. ($media_position == 'bottom' ? 'order-2 ' : '') . ($media_position == 'left' || $media_position == 'right' ? 'object-fit-cover w-100 h-100 ' : '') . ($params->get('card_style', '') == 'none' || $border_radius !== '' ? '' : 'card-img-'. $media_position) .'" src="'. $item->image_thumbnail .'" alt="'.$item->title.'">';
            }
            break;
        case 'gallery':
            $gallery    =   (array) $item->params->get('astroid_article_gallery_items', array());
            if (count($gallery)) {
                $has_gallery    =   true;
                $media  =   '<div id="astroid-articles-'.$item->id.'" class="carousel slide carousel-fade" data-bs-ride="carousel">';
                $media  .=  '<div class="carousel-inner">';
                $active =   true;
                foreach ($gallery as $gallery_item) {
                    $media  .=  '<div class="carousel-item'.($active ? ' active' : '').'"><img src="'.$gallery_item->image.'" class="d-block w-100" alt="'.$gallery_item->title.'"></div>';
                    $active =   false;
                }
                $media  .=  '</div>';
                $media  .=  '<button class="carousel-control-prev" type="button" data-bs-target="#astroid-articles-'.$item->id.'" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span></button>';
                $media  .=  '<button class="carousel-control-next" type="button" data-bs-target="#astroid-articles-'.$item->id.'" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span></button>';
                $media  .=  '</div>';
            }
            break;
        case 'video':
            $video_url  =   $item->params->get('astroid_article_video_url', '');
            $video_type =   $item->params->get('astroid_article_video_type', '');
            $video_src  =   Article::getVideoSrc($video_url);
            if ($video_src) {
                if ($video_type == 'vimeo') {
                    $video_src  .=  '?autoplay=1&loop=1&muted=1&autopause=0&title=0&byline=0&portrait=0&controls=0';
                }
                $media =    '<div class="entry-video ratio ratio-16x9">';
                $media .=   '<iframe src="' . $video_src . '" title="'.$item->title.'" allowfullscreen></iframe>';
                $media .=   '</div>';
            }
            break;
        case 'audio':
            $renderer   =   new FileLayout('blog.audio', JPATH_LIBRARIES . '/astroid/framework/frontend');
            $media      =   $renderer->render(['article' => $item]);
            break;
    }
    $item_image_cover = !empty($item->image_thumbnail) ? $enable_image_cover : 0;
    if ($item_image_cover) {
        $media  =   '<div class="d-block'.($layout == 'overlay' ? ' astroid-image-overlay-cover' : '').'"><a href="'.Route::_($link).'" title="'. $item->title . '"><img class="object-fit-cover w-100 h-100" src="'. $item->image_thumbnail .'" alt="'.$item->title.'"></a></div>';
    }
    echo '<div id="article-'. $item -> id .'" class="astroid-article-item astroid-grid '.$item->post_format.'"><div class="card overflow-hidden' . $card_style . $bd_radius . ($enable_grid_match ? ' h-100' : '') . '">';
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

    echo '<div class="'.($layout == 'overlay' && $item_image_cover ? 'card-img-overlay' : 'order-1 card-body' ) . $card_size.'">'; // Start Card-Body

    if ($media_position == 'inside') {
        echo $media;
    }

    if (count($info_before_title)) {
        echo '<div class="astroid-article-info as-gutter-lg">';
        foreach ($info_before_title as $info_item) {
            echo '<div class="d-inline-block">' . LayoutHelper::render('joomla.content.info_block.' . $info_item['value'], array('item' => $item, 'params' => $item->params)) .'</div>';
        }
        echo '</div>';
    }
    if (!empty($item->title)) {
        echo '<'.$title_html_element.' class="astroid-article-heading"><a href="'.Route::_($link).'" title="'. $item->title . '">'. $item->title . '</a></'.$title_html_element.'>';
    }
    if (count($info_after_title)) {
        echo '<div class="astroid-article-info after-title as-gutter-lg">';
        foreach ($info_after_title as $info_item) {
            echo '<div class="d-inline-block">' . LayoutHelper::render('joomla.content.info_block.' . $info_item['value'], array('item' => $item, 'params' => $item->params)) .'</div>';
        }
        echo '</div>';
    }
    if (!empty($item->introtext) && $enable_intro_text) {
        echo '<div class="astroid-article-introtext">' . $item->introtext . '</div>';
    }

    if (count($info_after_intro)) {
        echo '<dl class="astroid-article-info as-gutter-lg">';
        echo '<dt class="article-info-term">'.Text::_('COM_CONTENT_ARTICLE_INFO').'</dt>';
        foreach ($info_after_intro as $info_item) {
            echo LayoutHelper::render('joomla.content.info_block.' . $info_item['value'], array('item' => $item, 'params' => $item->params));
        }
        echo '</dl>';
    }
    if ($readmore) {
        echo '<a id="btn-'.$item->id.'" href="'.Route::_($link).'" class="mt-3 btn btn-' .(intval($button_outline) ? 'outline-' : ''). $button_style . $button_size . $button_radius . '">'.Text::_('JGLOBAL_READ_MORE').'</a>';
    }

    echo '</div>'; // End Card-Body

    if (($media_position == 'left' || $media_position == 'right') && !$item_image_cover && $layout == 'classic') {
        echo '</div>';
        echo '</div>';
    }

    echo '</div></div>';
}
echo '</div>';

if ($has_gallery) {
    $mainframe = Factory::getApplication();
    $wa = $mainframe->getDocument()->getWebAssetManager();
    $wa->useScript('bootstrap.carousel');
}
if ($params->get('card_size', '') == 'custom') {
    $card_padding   =   $params->get('card_padding', '');
    if (!empty($card_padding)) {
        $padding = \json_decode($card_padding, false);
        foreach ($padding as $device => $props) {
            $style->child('.card-size-custom')->addStyle(Style::spacingValue($props, "padding"), $device);
        }
    }
}
if (!empty($title_heading_margin)) {
    $margin = \json_decode($title_heading_margin, false);
    foreach ($margin as $device => $props) {
        $style->child('.astroid-article-heading')->addStyle(Style::spacingValue($props, "margin"), $device);
    }
}
if (!empty($info_margin)) {
    $margin = \json_decode($info_margin, false);
    foreach ($margin as $device => $props) {
        $style->child('.astroid-article-info.after-title')->addStyle(Style::spacingValue($props, "margin"), $device);
    }
}
if ($enable_image_cover) {
    $style->child('.astroid-image-overlay-cover')->addCss('height', $min_height . 'px');
}
switch ($overlay_type) {
    case 'color':
        $overlay_color      =   Style::getColor($params->get('overlay_color', ''));
        $style->child('.astroid-image-overlay-cover:after')->addCss('background-color', $overlay_color['light']);
        $style_dark->child('.astroid-image-overlay-cover:after')->addCss('background-color', $overlay_color['dark']);
        break;
    case 'background-color':
        $overlay_gradient   =   $params->get('overlay_gradient', '');
        if (!empty($overlay_gradient)) {
            $style->child('.astroid-image-overlay-cover:after')->addCss('background-image', Style::getGradientValue($overlay_gradient));
        }
        break;
}
$style->render();
$style_dark->render();