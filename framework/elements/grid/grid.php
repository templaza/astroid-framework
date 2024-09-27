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
use Astroid\Helper;
use Astroid\Helper\Media;
use Joomla\CMS\Uri\Uri;

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

$responsive_key     =   ['xxl', 'xl', 'lg', 'md', 'sm', 'xs'];
foreach ($responsive_key as $key) {
    $default        =   match ($key) {
        'xxl', 'xl' =>  '',
        'lg'        =>  3,
        default     =>  1
    };
    $column         =   $params->get($key . '_column', $default);

    if ($key !== 'xs') {
        $row_column_cls     .=  $column ? ' row-cols-'. $key .'-' . $column : '';
        $row_gutter         =   $params->get('row_gutter_'.$key, '');
        $column_gutter      =   $params->get('column_gutter_'. $key, '');
        if ($row_gutter) {
            $row_column_cls .=  ' gy-' . $key . '-' . $row_gutter;
        }
        if ($column_gutter) {
            $row_column_cls .=  ' gx-' . $key . '-' . $column_gutter;
        }
    } else {
        $row_column_cls     .=  $column ? ' row-cols-' . $column : '';
        $row_gutter         =   $params->get('row_gutter', 3);
        $column_gutter      =   $params->get('column_gutter', 3);
        $row_column_cls     .=  ' gy-' . $row_gutter;
        $row_column_cls     .=  ' gx-' . $column_gutter;
    }
}

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

foreach ($responsive_key as $device) {
    $default        =   match ($device) {
        'xxl', 'xl' =>  '',
        'lg'        =>  4,
        default     =>  12
    };
    $column_media   =   $params->get($device . '_column_media', $default);
    if ($device === 'xs') {
        $media_width_cls.=  $column_media ? ' col-' . $column_media . ($media_position == 'right' && $column_media < 12 ? ' text-end' : '') : '';
    } else {
        $media_width_cls.=  $column_media ? ' col-' . $device . '-' . $column_media . ($media_position == 'right' && $column_media < 12 ? ' text-'.$device.'-end' : '') : '';
    }
}

$icon_size          =   $params->get('icon_size', 60);

$icon_color         =   Style::getColor($params->get('icon_color', ''));
$style->child('.astroid-icon')->addCss('color', $icon_color['light']);
$style_dark->child('.astroid-icon')->addCss('color', $icon_color['dark']);

$icon_color_hover   =   Style::getColor($params->get('icon_color_hover', ''));
$style->child('.astroid-icon')->hover()->addCss('color', $icon_color_hover['light']);
$style_dark->child('.astroid-icon')->hover()->addCss('color', $icon_color_hover['dark']);

$layout             =   $params->get('layout', 'classic');
$enable_image_cover =   $params->get('enable_image_cover', 0);
$image_fullwidth    =   $enable_image_cover ? 1 : $params->get('image_fullwidth', 1);
$min_height         =   $params->get('min_height', 0);
$overlay_type       =   $params->get('overlay_type', '');
$enable_grid_match  =   $params->get('enable_grid_match', 0);
$vertical_middle    =   $params->get('vertical_middle', 0);

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

$button_style       =   $params->get('button_style', 'primary');
$button_outline     =   $params->get('button_outline', 0);

$button_size        =   $params->get('button_size', '');
$button_size        =   $button_size ? ' '. $button_size : '';

$button_radius      =   $params->get('border_radius', '');
$button_bd_radius   =   $button_radius ? ' ' . $button_radius : '';

$image_rounded_size     =   $params->get('image_rounded_size', '3');
$image_border_radius    =   $params->get('image_border_radius', '0');
$image_border_radius    =   $image_border_radius != 'rounded' ? ' rounded-' . $image_border_radius : ' rounded-' . $image_rounded_size;

$hover_effect   = $params->get('hover_effect', '');
$hover_effect   = $hover_effect !== '' ? ' as-effect-' . $hover_effect : '';
$transition     = $params->get('hover_transition', '');
$transition     = $transition !== '' ? ' as-transition-' . $transition : '';

$card_hover_transition     = $params->get('card_hover_transition', '');
$card_hover_transition     = $card_hover_transition !== '' ? ' as-transition-' . $card_hover_transition : '';

$button_margin_top  =   $params->get('button_margin_top', '');
echo '<div class="row'.$row_column_cls.'">';
foreach ($grids as $key => $grid) {
    $grid_params    =   Helper::loadParams($grid->params);
    $link_target    =   !empty($grid_params->get('link_target', '')) ? ' target="'.$grid_params->get('link_target', '').'"' : '';

    $media          =   '';
    if ($grid_params->get('type', '') == 'image' && $grid_params->get('image', '')) {
        $media      =   '<div class="as-image-cover position-relative overflow-hidden' . $image_border_radius . $hover_effect . $transition . ($media_position == 'bottom' ? ' order-2 ' : '') . '">';
        $media      .=  $layout == 'overlay' ? '<div class="as-image-cover astroid-image-overlay-cover">' : '';
        $media      .=  '<img class="' . ($image_fullwidth ? 'w-100' : '') . ($enable_image_cover || $media_position == 'left' || $media_position == 'right' ? ' object-fit-cover h-100' : '') . ($params->get('card_style', '') == 'none' ? '' : ' card-img-'. $media_position) .'" src="'. Astroid\Helper\Media::getPath() . '/' . $grid_params->get('image', '').'" alt="'.$grid_params->get('title', '').'">';
        $media      .=  $layout == 'overlay' ? '</div>' : '';
        $media      .=  '</div>';
        if ( !empty($grid_params->get('link', '')) ) {
            $media      =   '<a href="'. $grid_params->get('link', '') . '"'.$link_target.' class="'.($media_position == 'bottom' ? 'order-2 ' : '').'">'. $media .'</a>';
        }
    } elseif ($grid_params->get('type', '') == 'icon') {
        if ($grid_params->get('icon_type', '') == 'fontawesome') {
            $media  =   '<i class="astroid-icon '. ($media_position == 'bottom' ? 'order-2 ' : '') .$grid_params->get('fa_icon', '').'"></i>';
        } else {
            $media  =   '<i class="astroid-icon '. ($media_position == 'bottom' ? 'order-2 ' : '') .$grid_params->get('custom_icon', '').'"></i>';
        }
        if ( !empty($grid_params->get('link', '')) && !empty($params->get('enable_icon_link', 0)) ) {
            $media      =   '<a href="'. $grid_params->get('link', '') . '"'.$link_target.' class="'.($media_position == 'bottom' ? 'order-2 ' : '').'">'. $media .'</a>';
        }
    }

    echo '<div id="grid-'. $grid -> id .'" class="as-grid"><div class="card' . $card_style . $box_shadow . $box_shadow_hover .$bd_radius . $card_hover_transition . ($enable_grid_match ? ' h-100' : '') . '">';
    if ($media_position == 'left' || $media_position == 'right') {
        echo '<div class="row g-0'.($vertical_middle ? ' align-items-center' : '').'">';
        echo '<div class="'.$media_width_cls.'">';
    }
    if ($media_position != 'inside') {
        echo $media;
    }
    if ($media_position == 'left' || $media_position == 'right') {
        echo '</div>';
        echo '<div class="col order-1">';
    }

    echo '<div class="' . ($layout == 'overlay' && $enable_image_cover ? 'card-img-overlay as-light' : 'order-1 card-body' ) . $card_size . '">'; // Start Card-Body

    if ($media_position == 'inside') {
        echo $media;
    }
    if (!empty($grid_params->get('meta', '')) && $meta_position == 'before') {
        echo '<div class="astroid-meta">' . $grid_params->get('meta', '') . '</div>';
    }
    if (!empty($grid_params->get('title', ''))) {
        echo '<'.$title_html_element.' class="astroid-heading">'. $grid_params->get('title', '') . '</'.$title_html_element.'>';
    }
    if (!empty($grid_params->get('meta', '')) && $meta_position == 'after') {
        echo '<div class="astroid-meta">' . $grid_params->get('meta', '') . '</div>';
    }
    if (!empty($grid_params->get('description', ''))) {
        echo '<div class="astroid-text">' . $grid_params->get('description', '') . '</div>';
    }
    if (!empty($grid_params->get('link', '')) && !empty($grid_params->get('link_title', ''))) {
        $button_class   =   $button_style !== 'text' ? 'btn btn-' . (intval($button_outline) ? 'outline-' : '') . $button_style . $button_size. $button_bd_radius : 'as-btn-text text-uppercase text-reset';
        $btn_title      =   $button_style == 'text' ? '<small>'. $grid_params->get('link_title', '') . '</small>' : $grid_params->get('link_title', '');
        echo '<a class="'. $button_class . (!empty($button_margin_top) ? ' mt-' . $button_margin_top : '') .'" href="'.$grid_params->get('link', '').'" role="button"'.$link_target.'>' . $btn_title . '</a>';
    }

    echo '</div>'; // End Card-Body

    if ($media_position == 'left' || $media_position == 'right') {
        echo '</div>';
        echo '</div>';
    }

    echo '</div></div>';

    if ($grid_params->get('enable_background_image', 0)) {
        $image = $grid_params->get('background_image', '');
        if (!empty($image)) {
            $element->style->child('#grid-' . $grid->id)->child('.card')->addCss('background-image', 'url(' . Uri::base(true) . '/' . Media::getPath() . '/' . $image . ')');
            $element->style->child('#grid-' . $grid->id)->child('.card')->addCss('background-repeat', $grid_params->get('background_repeat', ''));
            $element->style->child('#grid-' . $grid->id)->child('.card')->addCss('background-size', $grid_params->get('background_size', ''));
            $element->style->child('#grid-' . $grid->id)->child('.card')->addCss('background-attachment', $grid_params->get('background_attchment', ''));
            $element->style->child('#grid-' . $grid->id)->child('.card')->addCss('background-position', $grid_params->get('background_position', ''));
        }
    }
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
if ($enable_image_cover) {
    $style->child('.as-image-cover')->addCss('height', $min_height . 'px');
}
if ($params->get('card_style', '') == 'custom') {
    $text_color     =   Style::getColor($params->get('text_color', ''));
    $style->child('.as-grid > .card')->addCss('color', $text_color['light']);
    $style_dark->child('.as-grid > .card')->addCss('color', $text_color['dark']);

    $bg_color       =   Style::getColor($params->get('bg_color', ''));
    $style->child('.as-grid > .card')->addCss('background-color', $bg_color['light']);
    $style_dark->child('.as-grid > .card')->addCss('background-color', $bg_color['dark']);

    $card_border    =   json_decode($params->get('card_border', ''), true);
    if (!empty($card_border)) {
        Style::addBorderStyle('#'. $element->id . ' .as-grid > .card', $card_border);
    }
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