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
use Astroid\Helper;
use Astroid\Helper\Media;
use Joomla\CMS\Uri\Uri;
use Astroid\Helper\SubForm;

extract($displayData);
$grids     = new SubForm($params->get('grids', ''));
if (!count($grids->data)) {
    return false;
}
$document = Framework::getDocument();
$style = $element->style;
$style_dark = $element->style_dark;
$row_column_cls     =   '';
$document->loadUIKit();
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
$card_custom_radius      =   $params->get('card_custom_radius', '');

$media_width_cls    =   '';
$media_position     =   $params->get('media_position', 'top');
$content_position     =   $params->get('content_position', 'uk-position-bottom');
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
$cover_toggle = '';
if($enable_image_cover){
    $cover_toggle = ' uk-transition-toggle ';
}
$content_hover_transition     = $params->get('media_hover_transition', '');

$title_html_element =   $params->get('title_html_element', 'h3');
$title_font_style   =   $params->get('title_font_style');
if (!empty($title_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-heading', $title_font_style, null, $element->isRoot);
}
$title_heading_margin=  $params->get('title_heading_margin', '');

$meta_font_style    =   $params->get('meta_font_style');
if (!empty($meta_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-meta', $meta_font_style, null, $element->isRoot);
}

$meta_heading_margin=   $params->get('meta_heading_margin', '');
$meta_heading_padding=   $params->get('meta_heading_padding', '');
$meta_heading_radius=   $params->get('meta_heading_radius', '');
$meta_position      =   $params->get('meta_position', 'before');

$content_font_style =   $params->get('content_font_style');
if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-text', $content_font_style, null, $element->isRoot);
}

$button_style       =   $params->get('button_style', 'primary');
$button_outline     =   $params->get('button_outline', 0);

$button_size        =   $params->get('button_size', '');
$button_size        =   $button_size ? ' '. $button_size : '';

$button_radius      =   $params->get('btn_border_radius', '');
$button_bd_radius   =   $button_radius ? ' ' . $button_radius : '';

$image_rounded_size     =   $params->get('image_rounded_size', '3');
$image_border_radius    =   $params->get('image_border_radius', '0');
$image_border_radius    =   $image_border_radius != 'rounded' ? ' rounded-' . $image_border_radius : ' rounded-' . $image_rounded_size;
$image_radius      =   $params->get('image_radius', '');

$hover_effect   = $params->get('hover_effect', '');
if (str_contains($hover_effect, 'uk-transition')==false) {
    $hover_effect2   = $hover_effect !== '' ? ' as-effect-' . $hover_effect : '';
}

$transition     = $params->get('hover_transition', '');
$transition     = $transition !== '' ? ' as-transition-' . $transition : '';

$card_hover_transition     = $params->get('card_hover_transition', '');
$card_hover_transition     = $card_hover_transition !== '' ? ' as-transition-' . $card_hover_transition : '';

$button_margin_top  =   $params->get('button_margin_top', '');

$use_masonry        =   $params->get('use_masonry', 0);
$hover_tog_class = $img_tog_class =$img_eff='';
if (str_contains($hover_effect, 'uk-transition')) {
    $hover_tog_class = ' uk-transition-toggle ';
    $img_tog_class = ' uk-transition-opaque ';
}else{
    $img_eff = ' '.$hover_effect.' ';
}

$autoplay       = $params->get('autoplay', 0);
$navigation     = $params->get('navigation', 0);
$dot            = $params->get('dot', 1);
$dot_margin     =  $params->get('dot_margin', '');

$attrs_slider[] = '';
$attrs_slider[] = (  $autoplay  ) ? 'autoplay: 1' : '';
$attrs_slider   = ' data-uk-slider="' . implode( '; ', array_filter( $attrs_slider ) ) . '"';

$enable_slider        =   $params->get('enable_slider', 0);
$slider_wrap='';
if($enable_slider){
    $slider_wrap = ' uk-slider-items flex-nowrap ';
    echo '<div class=" p-0 uk-position-relative uk-visible-toggle" tabindex="-1" '.$attrs_slider.'>';
}
echo '<div class="row'.($use_masonry ? ' as-masonry as-loading' : '').$row_column_cls.$slider_wrap.'">';
foreach ($grids->data as $key => $grid) {
    $link_target    =   !empty($grid->params->get('link_target', '')) ? ' target="'.$grid->params->get('link_target', '').'"' : '';

    $media          =   '';
    if ($grid->params->get('type', '') == 'image' && $grid->params->get('image', '')) {
        $media      =   '<div class="as-image-cover grid-media position-relative overflow-hidden' . $image_border_radius . $hover_tog_class.$hover_effect2.$img_eff . $transition . ($media_position == 'bottom' ? ' order-2 ' : '') . '">';
        $media      .=  $layout == 'overlay' ? '<div class="as-image-cover astroid-image-overlay-cover">' : '';
        $media      .=  '<img class="tz-img-grid ' .$hover_effect.$img_tog_class  . ($image_fullwidth ? 'w-100' : '') . ($enable_image_cover || $media_position == 'left' || $media_position == 'right' ? ' object-fit-cover h-100' : '') . ($params->get('card_style', '') == 'none' ? '' : ' card-img-'. $media_position) .'" src="'. Astroid\Helper\Media::getMediaPath($grid->params->get('image', '')) .'" alt="'.$grid->params->get('title', '').'">';
        $media      .=  $layout == 'overlay' ? '</div>' : '';
        if ($enable_image_cover) {
            if($content_hover_transition){
                $media .= '<div class="card-img-overlay uk-transition-fade"></div>';
            }else{
                $media .= '<div class="card-img-overlay"></div>';
            }

        }
        $media      .=  '</div>';
        if ( !empty($grid->params->get('link', '')) ) {
            $media      =   '<a href="'. $grid->params->get('link', '') . '"'.$link_target.' class="'.($media_position == 'bottom' ? 'order-2 ' : '').'">'. $media .'</a>';
        }

    } elseif ($grid->params->get('type', '') == 'icon') {
        $media = '<div class="moon-icon-wrapper">';
        switch ($grid->params->get('icon_type', '')) {
            case 'fontawesome':
                $media  .=   '<i class="astroid-icon '. ($media_position == 'bottom' ? 'order-2 ' : '') .$grid->params->get('fa_icon', '').'"></i>';
                break;
            case 'astroid':
                $media  .=   '<i class="astroid-icon '. ($media_position == 'bottom' ? 'order-2 ' : '') .$grid->params->get('as_icon', '').'"></i>';
                $document->loadASIcon();
                break;
            default:
                $media .=   '<i class="astroid-icon '. ($media_position == 'bottom' ? 'order-2 ' : '') .$grid->params->get('custom_icon', '').'"></i>';
                break;
        }
        $media .= '</div>';
        if ( !empty($grid->params->get('link', '')) && !empty($params->get('enable_icon_link', 0)) ) {
            $media      =   '<a href="'. $grid->params->get('link', '') . '"'.$link_target.' class="'.($media_position == 'bottom' ? 'order-2 ' : '').'">'. $media .'</a>';
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

    if($media_position == 'cover' && $content_position){
        echo '<div class="'.$content_position.' '.$content_hover_transition.'">';
    }

    echo '<div class="' . ($layout == 'overlay' && $enable_image_cover ? ' as-light' : 'order-1 card-body' ) . $card_size . '">'; // Start Card-Body

    if ($media_position == 'inside') {
        echo $media;
    }
    if (!empty($grid->params->get('meta', '')) && $meta_position !='after') {
        echo '<div class="astroid-meta '.$meta_position.'">' . $grid->params->get('meta', '') . '</div>';
    }
    if (!empty($grid->params->get('title', ''))) {
        echo '<'.$title_html_element.' class="astroid-heading">'. $grid->params->get('title', '') . '</'.$title_html_element.'>';
    }
    if (!empty($grid->params->get('meta', '')) && $meta_position == 'after') {
        echo '<div class="astroid-meta '.$meta_position.'">' . $grid->params->get('meta', '') . '</div>';
    }
    if (!empty($grid->params->get('description', ''))) {
        echo '<div class="astroid-text">' . $grid->params->get('description', '') . '</div>';
    }
    if (!empty($grid->params->get('link', '')) && !empty($grid->params->get('link_title', ''))) {
        $button_class   =   $button_style !== 'text' ? 'btn btn-' . (intval($button_outline) ? 'outline-' : '') . $button_style . $button_size. $button_bd_radius : 'as-btn-text text-uppercase text-reset';
        $btn_title      =   $button_style == 'text' ? '<small>'. $grid->params->get('link_title', '') . '</small>' : $grid->params->get('link_title', '');
        echo '<a class="'. $button_class . (!empty($button_margin_top) ? ' mt-' . $button_margin_top : '') .'" href="'.$grid->params->get('link', '').'" role="button"'.$link_target.'>' . $btn_title . '</a>';
    }

    echo '</div>'; // End Card-Body
    if($media_position == 'cover' && $content_position){
        echo '</div>';
    }

    if ($media_position == 'left' || $media_position == 'right') {
        echo '</div>';
        echo '</div>';
    }

    echo '</div></div>';

    if ($grid->params->get('enable_background_image', 0)) {
        $image = $grid->params->get('background_image', '');
        if (!empty($image)) {
            $element->style->child('#grid-' . $grid->id)->child('.card')->addCss('background-image', 'url(' . Uri::base(true) . '/' . Media::getPath() . '/' . $image . ')');
            $element->style->child('#grid-' . $grid->id)->child('.card')->addCss('background-repeat', $grid->params->get('background_repeat', ''));
            $element->style->child('#grid-' . $grid->id)->child('.card')->addCss('background-size', $grid->params->get('background_size', ''));
            $element->style->child('#grid-' . $grid->id)->child('.card')->addCss('background-attachment', $grid->params->get('background_attachment', ''));
            $element->style->child('#grid-' . $grid->id)->child('.card')->addCss('background-position', $grid->params->get('background_position', ''));
        }
    }
}
echo '</div>';
if($navigation){
    echo '<a class="uk-position-center-left uk_slider_preview uk-position-small uk-hidden-hover" href data-uk-slidenav-previous data-uk-slider-item="previous"></a>
        <a class="uk-position-center-right uk_slider_next  uk-position-small uk-hidden-hover" href data-uk-slidenav-next data-uk-slider-item="next"></a>';
}
if($dot){
    echo '<ul class="uk-slider-nav uk-dotnav uk-flex-center"></ul>';
}
if($enable_slider){
    echo '</div>';
}

if ($use_masonry) {
    $document->loadMasonry('#'. $element->id .' .as-masonry');
}
$style->child('.astroid-icon')->addCss('font-size', $icon_size.'px');
if ($params->get('card_size', '') == 'custom') {
    $card_padding   =   $params->get('card_padding', '');
    if (!empty($card_padding)) {
        Style::setSpacingStyle($element->style->child('.card-size-custom'), $card_padding);
    }
}
if (!empty($title_heading_margin)) {
    Style::setSpacingStyle($element->style->child('.astroid-heading'), $title_heading_margin, 'margin');
}
if (!empty($meta_heading_margin)) {
    Style::setSpacingStyle($element->style->child('.astroid-meta'), $meta_heading_margin, 'margin');
}
if (!empty($meta_heading_padding)) {
    Style::setSpacingStyle($element->style->child('.moon-meta'), $meta_heading_padding);
}
if (!empty($meta_heading_radius)) {
    Style::setSpacingStyle($element->style->child('.moon-meta'), $meta_heading_radius,'radius');
}

$meta_bg       =   Style::getColor($params->get('meta_heading_bg', ''));
$style->child('.moon-meta')->addCss('background-color', $meta_bg['light']);
$style_dark->child('.moon-meta')->addCss('background-color', $meta_bg['dark']);

if ($enable_image_cover) {
    $style->child('.as-image-cover')->addCss('height', $min_height . 'px');
}
if ($params->get('card_style', '') == 'custom') {
    $text_color     =   Style::getColor($params->get('text_color', ''));
    $style->child('.as-grid > .card')->addCss('color', $text_color['light']);
    $style_dark->child('.as-grid > .card')->addCss('color', $text_color['dark']);
    $bg_color['light'] = 'transparent';
    $bg_color       =   Style::getColor($params->get('bg_color', ''));

    if($bg_color['light']==''){
        $bg_color['light'] = 'transparent';
    }
    if($bg_color['dark']==''){
        $bg_color['dark'] = 'transparent';
    }
    $style->child('.as-grid > .card')->addCss('background-color', $bg_color['light']);
    $style_dark->child('.as-grid > .card')->addCss('background-color', $bg_color['dark']);

    $card_border    =   json_decode($params->get('card_border', ''), true);
    if (!empty($card_border)) {
        Style::addBorderStyle('#'. $element->id . ' .as-grid > .card', $card_border, 'global', $element->isRoot);
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
$media_margin =   $params->get('media_margin', '');

if (!empty($media_margin)) {
    Style::setSpacingStyle($element->style->child('.grid-media'), $media_margin,'margin');
}
if (!empty($image_radius) && $image_border_radius==' rounded-custom') {
    Style::setSpacingStyle($element->style->child('.grid-media'), $image_radius,'radius');
}
if (!empty($card_custom_radius) && $border_radius=='custom') {
    Style::setSpacingStyle($element->style->child('.card'), $card_custom_radius,'radius');
}

$button_font_style   =   $params->get('button_font_style');
if (!empty($button_font_style)) {
    Style::renderTypography('#'.$element->id.' .btn', $button_font_style, null, $element->isRoot);
}

$button_color     = Style::getColor($params->get('button_color', ''));
$button_color_hover     = Style::getColor($params->get('button_color_hover', ''));
$button_bg_color     = Style::getColor($params->get('button_bg_color', ''));
$button_bg_color_hover     = Style::getColor($params->get('button_bg_color_hover', ''));
$style->child('.btn')->addCss('color', $button_color['light']);
$style_dark->child('.btn')->addCss('color', $button_color['dark']);
$style->child('.btn:hover')->addCss('color', $button_color_hover['light']);
$style_dark->child('.btn:hover')->addCss('color', $button_color_hover['dark']);
$style->child('.btn')->addCss('background-color', $button_bg_color['light']);
$style_dark->child('.btn')->addCss('background-color', $button_bg_color['dark']);
$style->child('.btn:hover')->addCss('background-color', $button_bg_color_hover['light']);
$style_dark->child('.btn:hover')->addCss('background-color', $button_bg_color_hover['dark']);

$button_padding =   $params->get('button_padding', '');

if (!empty($button_padding)) {
    Style::setSpacingStyle($element->style->child('.btn'), $button_padding);
}
$button_custom_radius      =   $params->get('button_custom_radius', '');
if (!empty($button_custom_radius) && $button_radius=='custom') {
    Style::setSpacingStyle($element->style->child('.btn'), $button_custom_radius,'radius');
}
$button_custom_margin =   $params->get('button_custom_margin', '');

if (!empty($button_custom_margin)) {
    Style::setSpacingStyle($element->style->child('.btn'), $button_custom_margin,'margin');
}
$image_height      =   $params->get('image_height', '');
$image_width      =   $params->get('image_width', '');

$image_height_data = json_decode($image_height, true);
$image_width_data = json_decode($image_width, true);
if (json_last_error() === JSON_ERROR_NONE && is_array($image_width_data)) {
    $style->child('.grid-media')->addResponsiveCSS('width', $image_width_data, $image_width_data['postfix']);
}
if (json_last_error() === JSON_ERROR_NONE && is_array($image_height_data)) {
    $style->child('.grid-media')->addResponsiveCSS('height', $image_height_data, $image_height_data['postfix']);
}
if (!empty($dot_margin)) {
    Style::setSpacingStyle($element->style->child('.uk-dotnav'), $dot_margin, 'margin');
}