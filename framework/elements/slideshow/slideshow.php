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
use Joomla\CMS\Factory;

extract($displayData);
$slides         = $params->get('slides', '');
if (empty($slides)) {
    return false;
}
$slides          = json_decode($slides);

if (!count($slides)) {
    return false;
}

foreach ($slides as $key => &$slide) {
    $slide->params  =   Style::getSubFormParams($slide->params);
}

$style = $element->style;
$style_dark = $element->style_dark;

$card_size          =   $params->get('overlay_padding', '');
$card_size          =   $card_size ? ' card-size-' . $card_size : '';

$media_position     =   $params->get('media_position', 'top');

$slide_rounded_size =   $params->get('slide_rounded_size', '3');
$border_radius      =   $params->get('slide_border_radius', '');
$bd_radius          =   $border_radius != '' ? ' rounded-' . $border_radius : ' rounded-' . $slide_rounded_size;

$overlay_text_color =   $params->get('overlay_text_color', '');
$overlay_text_color =   $overlay_text_color !== '' ? ' ' . $overlay_text_color : '';
$min_height         =   $params->get('min_height', 0);
$overlay_max_width  =   $params->get('overlay_max_width', '');
$overlay_max_width  =   $overlay_max_width !== '' ? ' as-width-'. $overlay_max_width : '';
$autoplay           =   $params->get('autoplay', 0);
$interval           =   $params->get('interval', 3);
$interval           =   $interval * 1000;
$overlay_type       =   $params->get('overlay_type', '');
$overlay_color      =   $params->get('overlay_color', '');
$effect_type        =   $params->get('effect_type', '');
$effect_type        =   $effect_type !== '' ? ' ' . $effect_type : '';
$overlay_position   =   $params->get('overlay_position', 'justify-content-center align-items-center');
$overlay_position   =   $overlay_position !== '' ? ' ' . $overlay_position : '';

$box_shadow         =   $params->get('box_shadow', '');
$box_shadow         =   $box_shadow ? ' ' . $box_shadow : '';
$box_shadow_hover   =   $params->get('box_shadow_hover', '');
$box_shadow_hover   =   $box_shadow_hover ? ' ' . $box_shadow_hover : '';

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
$meta_position      =   $params->get('meta_position', 'before');
$meta_heading_margin=   $params->get('meta_heading_margin', '');

$content_font_style =   $params->get('content_font_style');
if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-text', $content_font_style, null, $element->isRoot);
}

$button_size        =   $params->get('button_size', '');
$button_size        =   $button_size ? ' '. $button_size : '';

$btn_radius         =   $params->get('btn_border_radius', '');
$btn_radius         =   $btn_radius ? ' '. $btn_radius : '';

echo '<div id="slide-'.$element->id.'" class="carousel slide overflow-hidden'. $overlay_text_color . $effect_type . $box_shadow . $box_shadow_hover .$bd_radius .'"'. (intval($autoplay) ? ' data-bs-ride="carousel"' : '') .'>';
if (!empty($params->get('indicators', 1))) {
echo '<div class="carousel-indicators">';
for ($key = 0 ; $key < count($slides); $key ++) {
    echo '<button type="button" data-bs-target="#slide-'.$element->id.'" data-bs-slide-to="'.$key.'" aria-label="'.$slides[$key]->params['title'].'"'.($key == 0 ? ' class="active" aria-current="true"' : '').'></button>';
}
echo '</div>';
}
echo '<div class="carousel-inner">';
for ($key = 0 ; $key < count($slides); $key ++) {
    echo '<div id="' . $slides[$key]->id . '" class="carousel-item'.($key == 0 ? ' active' : '').'" data-bs-interval="'.$interval.'">';
    echo '<div class="position-absolute top-0 start-0 end-0 bottom-0 astroid-image-overlay-cover"><img src="'. Astroid\Helper\Media::getPath() . '/' . $slides[$key]->params['image'].'" class="object-fit-cover w-100 h-100" alt="'.$slides[$key]->params['title'].'"></div>';
    echo '<div class="carousel-caption d-flex card-img-overlay'.$overlay_position.'"><div class="overlay-inner'.$overlay_max_width.'">';
    if (!empty($slides[$key]->params['meta']) && $meta_position == 'before') {
        echo '<div class="astroid-meta">' . $slides[$key]->params['meta'] . '</div>';
    }
    if (!empty($slides[$key]->params['title'])) {
        echo '<'.$title_html_element.' class="astroid-heading">'. $slides[$key]->params['title'] . '</'.$title_html_element.'>';
    }
    if (!empty($slides[$key]->params['meta']) && $meta_position == 'after') {
        echo '<div class="astroid-meta">' . $slides[$key]->params['meta'] . '</div>';
    }
    if (!empty($slides[$key]->params['description'])) {
        echo '<div class="astroid-text">' . $slides[$key]->params['description'] . '</div>';
    }
    $target = !empty($slides[$key]->params['link_target']) ? ' target="'.$slides[$key]->params['link_target'].'"' : '';
    if (!empty($slides[$key]->params['link'])) {
        echo '<div class="astroid-button mt-5"><a class="btn btn-' .(intval($params->get('button_outline', 0)) ? 'outline-' : ''). $params->get('button_style', '') . $button_size . $btn_radius . '" href="' . $slides[$key]->params['link'] . '"'.$target.'>' . $slides[$key]->params['link_title'] . '</a></div>';
    }
    echo '</div></div>';
    echo '</div>';
}
echo '</div>';
if (!empty($params->get('controls', 1))) {
echo '<button class="carousel-control-prev" type="button" data-bs-target="#slide-'.$element->id.'" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span></button>';
echo '<button class="carousel-control-next" type="button" data-bs-target="#slide-'.$element->id.'" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span></button>';
}
echo '</div>';

$mainframe = Factory::getApplication();
$wa = $mainframe->getDocument()->getWebAssetManager();
$wa->useScript('bootstrap.carousel');

$style->child('.carousel-item')->addCss('height', $min_height . 'px');

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