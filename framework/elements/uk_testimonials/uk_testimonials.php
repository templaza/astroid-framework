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
use Astroid\Helper\SubForm;

extract($displayData);
$testimonials     = new SubForm($params->get('testimonials', ''));
if (!count($testimonials->getData())) {
    return false;
}
$document           = Framework::getDocument();
$document->loadUIKit();
$enable_slider      =   $params->get('enable_slider', 0);
$slider_autoplay    =   $params->get('slider_autoplay', 0);
$slider_nav         =   $params->get('slider_nav', 1);
$slider_dotnav      =   $params->get('slider_dotnav', 0);
$dot_alignment      =   $params->get('dot_alignment', '');
$interval           =   $params->get('interval', 3);
$responsive_key     =   ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'];
$grid_columns       =   [];
$current_column     =   1;
foreach ($responsive_key as $key) {
    $column = (int) $params->get($key . '_column', 0);
    if ($column >= 1 && $column <= 6) {
        $current_column = $column;
    }
    $grid_columns[$key] = $current_column;
}
$grid_widths = [
    'mobile' => 100 / $grid_columns['xs'],
    'landscape_mobile' => 100 / $grid_columns['sm'],
    'tablet' => 100 / $grid_columns['md'],
    'desktop' => 100 / $grid_columns['lg'],
    'large_desktop' => 100 / $grid_columns['xl'],
    'larger_desktop' => 100 / $grid_columns['xxl'],
    'global' => 100 / $grid_columns['xxl'],
];

$slider_options = [];
if ($slider_autoplay) {
    $slider_options[] = 'autoplay: true';
    $slider_options[] = 'autoplay-interval: ' . max(0, (int) $interval * 1000);
}
$slider_options[] = 'finite: ' . (empty($params->get('loop', 0)) ? 'true' : 'false');
$dir = $params->get('direction', '');
$slider_attrs = ' data-uk-slider="' . implode('; ', $slider_options) . '"';
$dir_attr = in_array($dir, ['ltr', 'rtl'], true) ? ' dir="' . $dir . '"' : '';

$card_style_value   =   $params->get('card_style', '');
$card_style         =   match ($card_style_value) {
    'primary' => ' uk-card-primary',
    'secondary', 'dark' => ' uk-card-secondary',
    'none' => '',
    default => ' uk-card-default',
};

$card_size          =   $params->get('card_size', '');
$card_size          =   match ($card_size) {
    'small' => ' uk-card-small',
    'large' => ' uk-card-large',
    'custom' => ' uk-card-body-custom',
    'none' => ' uk-padding-remove',
    default => '',
};

$border_radius      =   $params->get('card_border_radius', '');
$bd_radius          =   match ($border_radius) {
    'circle' => ' uk-border-circle',
    'pill' => ' uk-border-pill',
    '0' => '',
    'custom' => '',
    default => ' uk-border-rounded',
};

$avatar_width_cls    =   '';
$avatar_position    =   $params->get('avatar_position', 'top');
$avatar_widths = [
    'xxl' => $params->get('xxl_column_avatar', ''),
    'xl' => $params->get('xl_column_avatar', ''),
    'lg' => $params->get('lg_column_avatar', 4),
    'md' => $params->get('md_column_avatar', 12),
    'sm' => $params->get('sm_column_avatar', 12),
    'xs' => $params->get('xs_column_avatar', 12),
];
$avatar_width_cls .= $avatar_position === 'right' ? ' uk-flex-last' : '';

$enable_grid_match  =   $params->get('enable_grid_match', 0);

$box_shadow         =   $params->get('card_box_shadow', '');
$box_shadow         =   match ($box_shadow) {
    'shadow-sm', 'uk-box-shadow-small' => ' uk-box-shadow-small',
    'shadow', 'uk-box-shadow-medium' => ' uk-box-shadow-medium',
    'shadow-lg', 'uk-box-shadow-large' => ' uk-box-shadow-large',
    default => '',
};
$box_shadow_hover   =   $params->get('card_box_shadow_hover', '');
$box_shadow_hover   =   match ($box_shadow_hover) {
    'shadow-hover-sm', 'uk-box-shadow-hover-small' => ' uk-box-shadow-hover-small',
    'shadow-hover', 'uk-box-shadow-hover-medium' => ' uk-box-shadow-hover-medium',
    'shadow-hover-lg', 'uk-box-shadow-hover-large' => ' uk-box-shadow-hover-large',
    default => '',
};

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
$image_border_radius    =   $params->get('image_border_radius', '0');
$image_border_radius    =   match ($image_border_radius) {
    'circle' => ' uk-border-circle',
    'pill' => ' uk-border-pill',
    'rounded' => ' uk-border-rounded',
    default => '',
};

$image_border           =   json_decode($params->get('image_border', ''), true);

$hover_effect   = $params->get('hover_effect', '');
$hover_effect   = $hover_effect !== '' ? ' as-effect-' . $hover_effect : '';
$transition     = $params->get('hover_transition', '');
$transition     = $transition !== '' ? ' as-transition-' . $transition : '';

$card_hover_transition     = $params->get('card_hover_transition', '');
$card_hover_transition     = $card_hover_transition !== '' ? ' as-transition-' . $card_hover_transition : '';

$enable_rating      =   $params->get('enable_rating', 0);

$text_alignment             =   $params->get('text_alignment','');
$card_vertical_align    =   $params->get('card_vertical_align','');

$slider_nav_position    =   $params->get('slider_nav_position','');
$testimonial_icon    =   $params->get('fa_icon','');

if ($text_alignment) {
    $alignment              =   ' uk-flex-' . match ($text_alignment) {
        'start', 'left' => 'left',
        'end', 'right' => 'right',
        default => 'center',
    };
} else {
    $alignment              =   '';
}
$use_masonry        =   $params->get('use_masonry', 0);
$element->style->child('.uk-testimonial-item')->addResponsiveCSS('width', $grid_widths, '%');
if ($avatar_position === 'left' || $avatar_position === 'right') {
    $avatar_column_widths = [];
    $current_avatar_column = 12;
    foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $key) {
        $width = $avatar_widths[$key] ?? '';
        if ($width === 'auto') {
            $current_avatar_column = 'auto';
        } elseif (is_numeric($width) && (int) $width >= 1 && (int) $width <= 12) {
            $current_avatar_column = (int) $width;
        }
        $avatar_column_widths[$key] = $current_avatar_column === 'auto'
            ? 'auto'
            : (100 * $current_avatar_column / 12) . '%';
    }
    $avatar_column_widths = [
        'mobile' => $avatar_column_widths['xs'],
        'landscape_mobile' => $avatar_column_widths['sm'],
        'tablet' => $avatar_column_widths['md'],
        'desktop' => $avatar_column_widths['lg'],
        'large_desktop' => $avatar_column_widths['xl'],
        'larger_desktop' => $avatar_column_widths['xxl'],
        'global' => $avatar_column_widths['xxl'],
    ];
    $element->style->child('.uk-testimonial-avatar-column')->addResponsiveCSS('width', $avatar_column_widths);
}
$gutter_sizes = [0 => 0, 1 => 4, 2 => 8, 3 => 16, 4 => 24, 5 => 48];
$row_gaps = $column_gaps = [];
$gutter_breakpoints = [
    'mobile' => 'xs',
    'landscape_mobile' => 'sm',
    'tablet' => 'md',
    'desktop' => 'lg',
    'large_desktop' => 'xl',
    'larger_desktop' => 'xxl',
];
$static_gutters = [];
$current_row_gutter = $current_column_gutter = 3;
foreach ($responsive_key as $key) {
    $suffix = $key === 'xs' ? '' : '_' . $key;
    $row = $params->get('row_gutter' . $suffix, '');
    $column = $params->get('column_gutter' . $suffix, '');
    if ($row !== '') {
        $current_row_gutter = (int) $row;
    }
    if ($column !== '') {
        $current_column_gutter = (int) $column;
    }
    $static_gutters[$key] = [
        $gutter_sizes[max(0, min(5, $current_row_gutter))],
        $gutter_sizes[max(0, min(5, $current_column_gutter))],
    ];
}
foreach ($gutter_breakpoints as $device => $key) {
    if ($enable_slider) {
        $value = max(0, min(100, (int) $params->get('gutter_' . $key, 10)));
        $gap = $value === 0 ? '0.0' : $value;
        $row_gaps[$device] = $column_gaps[$device] = $gap;
    } else {
        [$row_gap, $column_gap] = $static_gutters[$key];
        $row_gaps[$device] = $row_gap === 0 ? '0.0' : $row_gap;
        $column_gaps[$device] = $column_gap === 0 ? '0.0' : $column_gap;
    }
}
$row_gaps['global'] = $row_gaps['larger_desktop'] ?? 16;
$column_gaps['global'] = $column_gaps['larger_desktop'] ?? 16;
$negative_column_gaps = array_map(static fn ($gap) => (string) (0 - (float) $gap), $column_gaps);
$element->style->child('.uk-testimonial-grid')->addResponsiveCSS('margin-left', $negative_column_gaps, 'px');
$element->style->child('.uk-testimonial-grid')->child('> *')->addResponsiveCSS('padding-left', $column_gaps, 'px');
$element->style->child('.uk-testimonial-grid')->child('> .uk-grid-margin')->addResponsiveCSS('margin-top', $row_gaps, 'px');
$element->style->child('.as-rating-block')->addCss('gap', '0.25rem');
if ($enable_grid_match) {
    $element->style->child('.uk-testimonial-item')->addCss('display', 'flex');
    $element->style->child('.uk-testimonial-item > .uk-card')->addCss('width', '100%');
}
if ($enable_slider) {
    if ($params->get('slider_sets', 0)) {
        $slider_options[] = 'sets: true';
        $slider_attrs = ' data-uk-slider="' . implode('; ', $slider_options) . '"';
    }
    echo '<div class="uk-position-relative uk-testimonials-slider"'.$slider_attrs.$dir_attr.'>';
    echo '<div class="uk-slider-container">';
}

$grid_options = [];
if ($use_masonry && !$enable_slider) {
    $grid_options[] = 'masonry: true';
}
$grid_attributes = ' data-uk-grid' . (count($grid_options) ? '="' . implode('; ', $grid_options) . '"' : '');
echo '<div class="uk-grid uk-testimonial-grid'.($enable_slider ? ' uk-slider-items' : '').'"'.$grid_attributes.'>';
foreach ($testimonials->getData() as $key => $testimonial) {
    $avatar =   $testimonial->params->get('avatar', '');
    $rating =   $testimonial->params->get('rating', 5);
    $media  =   '';
    if ($avatar) {
        $media      =   '<div class="as-author-avatar uk-inline uk-overflow-hidden' . $image_border_radius . $box_shadow . $hover_effect . $transition . '">';
        $media      .=  '<img class="' . ($avatar_position == 'left' || $avatar_position == 'right' ? 'uk-width-1-1 uk-height-1-1 ' : '') .'" src="'. Astroid\Helper\Media::getMediaPath($avatar) .'" alt="'.$testimonial->params->get('title', '').'">';
        $media      .=  '</div>';
    }

    echo '<div id="testimonial-'. $testimonial -> id .'" class="uk-testimonial-item"><div class="uk-card' . $card_style . $box_shadow . $box_shadow_hover .$bd_radius . $card_hover_transition . '">';
    if ($avatar_position == 'left' || $avatar_position == 'right') {
        echo '<div class="uk-grid uk-grid-collapse '.$card_vertical_align.'" data-uk-grid>';
        echo '<div class="uk-testimonial-avatar-column '.$avatar_width_cls.'">';
    }
    if ($avatar_position == 'left' || $avatar_position == 'right') {
        echo $media;
    }
    if ($avatar_position == 'left' || $avatar_position == 'right') {
        echo '</div>';
        echo '<div class="uk-width-expand">';
    }

    echo '<div class="uk-card-body'.$card_size.'">'; // Start Card-Body
    if ($avatar_position == 'left' || $avatar_position == 'right' || $avatar_position == 'bottom') {
        if($testimonial_icon){
            echo '<div class="testimonial_icon"><i class="'.$testimonial_icon.'"></i></div>';
        }
    }
    if ($avatar_position == 'top') {
        if($testimonial_icon){
            echo '<div class="uk-flex uk-flex-between uk-flex-middle">';
            echo $media;
            echo '<div class="testimonial_icon"><i class="'.$testimonial_icon.'"></i></div>';
            echo '</div>';
        }else{
            echo $media;
        }
    }
    if (!empty($testimonial->params->get('message', ''))) {
        echo '<div class="as-author-message">' . $testimonial->params->get('message', '') . '</div>';
    }
    if ($avatar_position == 'bottom') {
        echo $media;
    }
    if (!empty($enable_rating)) {
        echo '<div class="as-rating-block uk-flex uk-flex-wrap'.$alignment.'">';
        for ($i = 0; $i < 5 ; $i++) {
            if ($i < $rating) {
                if ($rating - $i >= 1) {
                    echo '<span class="as-star" aria-hidden="true">&#9733;</span>';
                } else {
                    echo '<span class="as-star as-star-half" aria-hidden="true">&#9733;</span>';
                }
            } else {
                echo '<span class="as-star" aria-hidden="true">&#9734;</span>';
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
if ($enable_slider) {
    if ($slider_dotnav) {
        echo '<ul class="uk-slider-nav uk-dotnav uk-flex-center"></ul>';
    }
    if ($slider_nav) {
        echo '<div class="uk-testimonials-nav-wrap uk-flex ' . $slider_nav_position . '">';
        echo '<a href="#" class="uk-slidenav uk-slidenav-previous uk-testimonials-nav" data-uk-slidenav-previous data-uk-slider-item="previous" aria-label="Previous"></a>';
        echo '<a href="#" class="uk-slidenav uk-slidenav-next uk-testimonials-nav" data-uk-slidenav-next data-uk-slider-item="next" aria-label="Next"></a>';
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';
}

if ($params->get('card_size', '') == 'custom') {
    $card_padding   =   $params->get('card_padding', '');
    if (!empty($card_padding)) {
        Style::setSpacingStyle($element->style->child('.uk-card-body-custom'), $card_padding);
    }
    $card_margin   =   $params->get('card_margin', '');
    if (!empty($card_margin)) {
        Style::setSpacingStyle($element->style->child('.uk-card-body-custom'), $card_margin, 'margin');
    }
}
if ($params->get('card_style', '') == 'custom') {
    $text_color     =   Style::getColor($params->get('text_color', ''));
    $element->style->child('.uk-card')->addCss('color', $text_color['light']);
    $element->style_dark->child('.uk-card')->addCss('color', $text_color['dark']);

    $bg_color       =   Style::getColor($params->get('bg_color', ''));
    if($bg_color['light']==''){
        $bg_color['light'] = 'transparent';
    }
    if($bg_color['dark']==''){
        $bg_color['dark'] = 'transparent';
    }

    if ($avatar_position == 'left' || $avatar_position == 'right') {
        $element->style->child('.uk-card-body')->addCss('background-color', $bg_color['light']);
        $element->style_dark->child('.uk-card-body')->addCss('background-color', $bg_color['dark']);
    }else{
        $element->style->child('.uk-card')->addCss('background-color', $bg_color['light']);
        $element->style_dark->child('.uk-card')->addCss('background-color', $bg_color['dark']);
    }

    $card_border    =   json_decode($params->get('card_border', ''), true);
    if (!empty($card_border)) {
        Style::addBorderStyle('#'. $element->id . ' .uk-card', $card_border, 'global', $element->isRoot);
    }

}
$slider_padding   =   $params->get('slider_padding', '');
if (!empty($slider_padding)) {
    Style::setSpacingStyle($element->style->child('.uk-slider-container'), $slider_padding);
}


$next_margin   =   $params->get('next_margin', '');
if (!empty($next_margin)) {
    Style::setSpacingStyle($element->style->child('.uk-slidenav-next'), $next_margin, 'margin');
}
$preview_margin   =   $params->get('preview_margin', '');
if (!empty($preview_margin)) {
    Style::setSpacingStyle($element->style->child('.uk-slidenav-previous'), $preview_margin, 'margin');
}
$slider_nav_height      =   $params->get('slider_nav_height', '');
$nav_height = json_decode($slider_nav_height, true);
if (json_last_error() === JSON_ERROR_NONE && is_array($nav_height)) {
    $element->style->child('.uk-testimonials-nav')->addResponsiveCSS('height', $nav_height, $nav_height['postfix']);
}
$slider_nav_width      =   $params->get('slider_nav_width', '');
$nav_width = json_decode($slider_nav_width, true);
if (json_last_error() === JSON_ERROR_NONE && is_array($nav_width)) {
    $element->style->child('.uk-testimonials-nav')->addResponsiveCSS('width', $nav_width, $nav_width['postfix']);
}
$nav_border    =   json_decode($params->get('slider_nav_border', ''), true);
if (!empty($nav_border)) {
    Style::addBorderStyle('#'. $element->id . ' .uk-testimonials-nav', $nav_border, 'global', $element->isRoot);
}
$nav_radius  =   $params->get('slider_nav_radius', '');
if (!empty($nav_radius)) {
    Style::setSpacingStyle($element->style->child('.uk-testimonials-nav'), $nav_radius,'radius');
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
    $justify = match ($dot_alignment) {
        'left' => 'flex-start',
        'right' => 'flex-end',
        default => 'center',
    };
    $element->style->child('.uk-slider-nav')->addCss('justify-content', $justify);
}
if (!empty($enable_rating)) {
    $rating_color   =   Style::getColor($params->get('rating_color', ''));
    $element->style->child('.as-rating-block')->addCss('color', $rating_color['light']);
    $element->style_dark->child('.as-rating-block')->addCss('color', $rating_color['dark']);
    $element->style->child('.as-rating-block')->addCss('--rating-color', $rating_color['light'] ?: 'currentColor');
    $element->style_dark->child('.as-rating-block')->addCss('--rating-color', $rating_color['dark'] ?: 'currentColor');
    $element->style->child('.as-star-half')->addCss('background', 'linear-gradient(90deg, var(--rating-color) 50%, transparent 50%)');
    $element->style->child('.as-star-half')->addCss('-webkit-background-clip', 'text');
    $element->style->child('.as-star-half')->addCss('background-clip', 'text');
    $element->style->child('.as-star-half')->addCss('color', 'transparent');
    $element->style->child('.as-star-half')->addCss('-webkit-text-fill-color', 'transparent');
}

$nav_color     = Style::getColor($params->get('nav_color', ''));
$element->style->child('.uk-testimonials-nav')->addCss('color', $nav_color['light']);
$element->style_dark->child('.uk-testimonials-nav')->addCss('color', $nav_color['dark']);

$nav_bg_color     = Style::getColor($params->get('nav_bg_color', ''));
$element->style->child('.uk-testimonials-nav')->addCss('background-color', $nav_bg_color['light']);
$element->style_dark->child('.uk-testimonials-nav')->addCss('background-color', $nav_bg_color['dark']);

$nav_bg_color_hover     = Style::getColor($params->get('nav_bg_color_hover', ''));
$element->style->child('.uk-testimonials-nav:hover')->addCss('background-color', $nav_bg_color_hover['light']);
$element->style_dark->child('.uk-testimonials-nav:hover')->addCss('background-color', $nav_bg_color_hover['dark']);

$nav_color_hover     = Style::getColor($params->get('nav_color_hover', ''));
$element->style->child('.uk-testimonials-nav:hover')->addCss('color', $nav_color_hover['light']);
$element->style_dark->child('.uk-testimonials-nav:hover')->addCss('color', $nav_color_hover['dark']);

$nav_border_hover    =   json_decode($params->get('slider_nav_border_hover', ''), true);
if (!empty($nav_border_hover)) {
    Style::addBorderStyle('#'. $element->id . ' .uk-testimonials-nav:hover', $nav_border_hover, 'global', $element->isRoot);
}
$nav_icon_size        =   $params->get('nav_icon_size', '16');
$element->style->child('.uk-testimonials-nav')->addCss('font-size', $nav_icon_size.'px');

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
    if (!empty($card_radius_custom)) {
        Style::setSpacingStyle($element->style->child('.uk-card'), $card_radius_custom,'radius');
    }

}