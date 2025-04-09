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
use Astroid\Helper\SubForm;

extract($displayData);
$slides     = new SubForm($params->get('slides', ''));
if (!count($slides->getData())) {
    return false;
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
foreach ($slides->getData() as $key => $slide) {
    echo '<button type="button" data-bs-target="#slide-'.$element->id.'" data-bs-slide-to="'.$key.'" aria-label="'.$slide->params->get('title').'"'.($key == 0 ? ' class="active" aria-current="true"' : '').'></button>';
}
echo '</div>';
}
echo '<div class="carousel-inner">';
foreach ($slides->getData() as $key => $slide) {
    echo '<div id="' . $slide->id . '" class="carousel-item'.($key == 0 ? ' active' : '').'" data-bs-interval="'.$interval.'">';
    echo '<div class="position-absolute top-0 start-0 end-0 bottom-0 astroid-image-overlay-cover"><img src="'. Astroid\Helper\Media::getMediaPath($slide->params->get('image')) .'" class="object-fit-cover w-100 h-100" alt="'.$slide->params->get('title').'"></div>';
    echo '<div class="carousel-caption d-flex card-img-overlay'.$overlay_position.'"><div class="overlay-inner'.$overlay_max_width.'">';
    if (!empty($slide->params->get('meta')) && $meta_position == 'before') {
        echo '<div class="astroid-meta">' . $slide->params->get('meta') . '</div>';
    }
    if (!empty($slide->params->get('title'))) {
        echo '<'.$title_html_element.' class="astroid-heading">'. $slide->params->get('title') . '</'.$title_html_element.'>';
    }
    if (!empty($slide->params->get('meta')) && $meta_position == 'after') {
        echo '<div class="astroid-meta">' . $slide->params->get('meta') . '</div>';
    }
    if (!empty($slide->params->get('description'))) {
        echo '<div class="astroid-text">' . $slide->params->get('description') . '</div>';
    }
    $target = !empty($slide->params->get('link_target')) ? ' target="'.$slide->params->get('link_target').'"' : '';
    if (!empty($slide->params->get('link'))) {
        echo '<div class="astroid-button mt-5"><a class="btn btn-' .(intval($params->get('button_outline', 0)) ? 'outline-' : ''). $params->get('button_style', '') . $button_size . $btn_radius . '" href="' . $slide->params->get('link') . '"'.$target.'>' . $slide->params->get('link_title') . '</a></div>';
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