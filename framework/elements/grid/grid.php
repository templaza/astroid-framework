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
$grids          = $params->get('grids', '');
if (empty($grids)) {
    return false;
}
$grids          = json_decode($grids);
if (!count($grids)) {
    return false;
}

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

$icon_size          =   $params->get('icon_size', 60);

$icon_color         =   Style::getColor($params->get('icon_color', ''));
$style->child('.astroid-icon')->addCss('color', $icon_color['light']);
$style_dark->child('.astroid-icon')->addCss('color', $icon_color['dark']);

$icon_color_hover   =   Style::getColor($params->get('icon_color_hover', ''));
$style->child('.astroid-icon')->hover()->addCss('color', $icon_color_hover['light']);
$style_dark->child('.astroid-icon')->hover()->addCss('color', $icon_color_hover['dark']);

$enable_image_cover =   $params->get('enable_image_cover', 0);
$min_height         =   $params->get('min_height', 0);
$overlay_color      =   $params->get('overlay_color', '');
$enable_grid_match  =   $params->get('enable_grid_match', 0);

$box_shadow         =   $params->get('card_box_shadow', '');
$box_shadow         =   $box_shadow ? ' ' . $box_shadow : '';
$box_shadow_hover   =   $params->get('card_box_shadow_hover', '');
$box_shadow_hover   =   $box_shadow_hover ? ' ' . $box_shadow_hover : '';


$title_html_element =   $params->get('title_html_element', 'h3');
$title_font_style   =   $params->get('title_font_style');
if (!empty($title_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-heading', $title_font_style);
}
$title_heading_margin=  $params->get('title_heading_margin', '');

$meta_font_style    =   $params->get('meta_font_style');
if (!empty($meta_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-meta', $meta_font_style);
}

$meta_heading_margin=   $params->get('meta_heading_margin', '');
$meta_position      =   $params->get('meta_position', 'before');

$content_font_style =   $params->get('content_font_style');
if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-text', $content_font_style);
}

$button_size        =   $params->get('button_size', '');
$button_size        =   $button_size ? ' '. $button_size : '';

echo '<div class="row'.$row_column_cls.'">';
foreach ($grids as $key => $grid) {
    $grid_params    =   Style::getSubFormParams($grid->params);
    $media          =   '';
    if ($grid_params['type'] == 'image' && $grid_params['image']) {
        $media      =   '<img class="'. ($media_position == 'bottom' ? 'order-2 ' : '') . ($media_position == 'left' || $media_position == 'right' ? 'object-fit-cover w-100 h-100 ' : '') . ($params->get('card_style', '') == 'none' ? '' : 'card-img-'. $media_position) .'" src="'. Astroid\Helper\Media::getPath() . '/' . $grid_params['image'].'" alt="'.$grid_params['title'].'">';
    } elseif ($grid_params['type'] == 'icon') {
        if ($grid_params['icon_type'] == 'fontawesome') {
            $media  =   '<i class="astroid-icon '. ($media_position == 'bottom' ? 'order-2 ' : '') .$grid_params['fa_icon'].'"></i>';
        } else {
            $media  =   '<i class="astroid-icon '. ($media_position == 'bottom' ? 'order-2 ' : '') .$grid_params['custom_icon'].'"></i>';
        }
    }
    echo '<div id="grid-'. $grid -> id .'"><div class="card' . $card_style . $box_shadow . $box_shadow_hover .$bd_radius . ($enable_grid_match ? ' h-100' : '') . '">';
    if ($media_position == 'left' || $media_position == 'right') {
        echo '<div class="row g-0">';
        echo '<div class="'.$media_width_cls.'">';
    }
    if ($media_position != 'inside') {
        echo $media;
    }
    if ($media_position == 'left' || $media_position == 'right') {
        echo '</div>';
        echo '<div class="col order-1">';
    }

    echo '<div class="order-1 card-body'.$card_size.'">'; // Start Card-Body

    if ($media_position == 'inside') {
        echo $media;
    }
    if (!empty($grid_params['meta']) && $meta_position == 'before') {
        echo '<div class="astroid-meta">' . $grid_params['meta'] . '</div>';
    }
    if (!empty($grid_params['title'])) {
        echo '<'.$title_html_element.' class="astroid-heading">'. $grid_params['title'] . '</'.$title_html_element.'>';
    }
    if (!empty($grid_params['meta']) && $meta_position == 'after') {
        echo '<div class="astroid-meta">' . $grid_params['meta'] . '</div>';
    }
    if (!empty($grid_params['description'])) {
        echo '<div class="astroid-text">' . $grid_params['description'] . '</div>';
    }

    echo '</div>'; // End Card-Body

    if ($media_position == 'left' || $media_position == 'right') {
        echo '</div>';
        echo '</div>';
    }

    echo '</div></div>';
}
echo '</div>';

$style->child('.astroid-icon')->addCss('font-size', $icon_size.'px');
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
        $style->child('.astroid-heading')->addStyle(Style::spacingValue($props, "margin"), $device);
    }
}
if (!empty($meta_heading_margin)) {
    $margin = \json_decode($meta_heading_margin, false);
    foreach ($margin as $device => $props) {
        $style->child('.astroid-meta')->addStyle(Style::spacingValue($props, "margin"), $device);
    }
}
$style->render();
$style_dark->render();