<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/media/templates/site/{YOUR_TEMPLATE_NAME}/astroid/elements/toast/toast.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;
use Astroid\Helper\Style;
use Joomla\CMS\Language\Text;
use Astroid\Helper\SubForm;
use Astroid\Framework;
extract($displayData);

$always_display = $params->get('always_display', 0);
$document = Framework::getDocument();
if (!$always_display && $document->getApp()->input->cookie->get("astroid_toast_" . $element->id, 0)) {
    return;
}

$document->getWA()->useScript('bootstrap.toast');
$toast_position = $params->get('toast_position', '');
$toast_maxwidth = $params->get('toast_maxwidth', '500');
$toast_padding  = $params->get('toast_padding', '');

$title          = $params->get('heading', '');
$html_element   = $params->get('html_element', 'h5');
$font_style     = $params->get('heading_font_style');

$meta           = $params->get('meta', '');
$meta_font_style    = $params->get('meta_font_style');
$meta_html_element  = $params->get('meta_html_element', 'div');

$content        = Astroid\Helper\Video::getVideoFromContent($params->get('content', ''));
$content_font_style= $params->get('content_font_style');

$style = $element->style;

$position = '';
if (!empty($toast_position)) {
    $position = ' position-fixed ' . $toast_position;
}
echo !empty($toast_position) ? '<div class="toast-container' . $position . ' p-lg-5 p-md-3">' : '';
echo '<div id="astroid-toast-' . $element->id . '" class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">';
if (!empty($title)) {
    echo '<div class="toast-header">';
    echo '<'.$html_element.' class="astroid-content-heading me-auto my-2">'. $title . '</'.$html_element.'>';
    if (!empty($meta)) {
        echo '<'.$meta_html_element.' class="astroid-content-meta">'. $meta . '</'.$meta_html_element.'>';
    }
    echo '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="'.Text::_('ASTROID_TEMPLATE_CLOSE').'"></button>';
    echo '</div>';
}
if (!empty($content)) {
    echo '<div class="toast-body">';
    echo '<div class="astroid-content-text">'. $content . '</div>';

    $buttons     = new SubForm($params->get('buttons', ''));
    if (count($buttons->getData())) {
        $button_group   =   intval($params->get('button_group', 0));
        $gutter         =   $params->get('gutter', 'lg');
        $border_radius  =   $params->get('btn_border_radius', '');
        $bd_radius      =   $border_radius ? ' ' . $border_radius : '';
        $button_size    =   $params->get('button_size', '');

        $button_size    =   $button_size ? ' '. $button_size : '';

        // Item Padding
        if (trim($button_size) == 'custom') {
            $item_padding   =   $params->get('btn_padding', '');
            if (!empty($item_padding)) {
                Style::setSpacingStyle($element->style->child('.btn'), $item_padding);
            }
            $button_font_style =   $params->get('button_font_style');
            if (!empty($button_font_style)) {
                Style::renderTypography('#'.$element->id.' .btn', $button_font_style, null, $element->isRoot);
            }
        }

        echo '<div class="'.($button_group ? 'btn-group' : 'as-gutter-' . $gutter).'" role="group">';
        foreach ($buttons->getData() as $key => $button) {
            if ($button_group && $border_radius === 'rounded-pill') {
                if ($key === 0) {
                    $bd_radius = ' rounded-start-pill';
                } elseif ($key === count($buttons->getData()) - 1) {
                    $bd_radius = ' rounded-end-pill';
                } else {
                    $bd_radius = '';
                }
            }
            $title = $button->params->get('title', '');
            if ($button->params->get('icon', '')) {
                $title      =   $button->params->get('icon_position', '') === 'first' ? '<i class="'.$button->params->get('icon', '').' me-2"></i>' . $title : $title . '<i class="'.$button->params->get('icon', '').' ms-2"></i>';
            }
            $btn_element_size = $button_size;
            if ($button->params->get('button_size', '')) {
                $btn_element_size = ' ' . $button->params->get('button_size', '');
                // Item Padding
                if (trim($button->params->get('button_size', '')) == 'custom') {
                    $item_padding   =   $button->params->get('btn_padding', '');
                    if (!empty($item_padding)) {
                        Style::setSpacingStyle($element->style->child('#btn-'.$button->id), $item_padding);
                    }
                }
            }

            // Button Custom Style
            $button_style   =   $button->params->get('button_style', '');
            if ($button_style === 'custom') {
                $color          =   Style::getColor($button->params->get('color', ''));
                $color_hover    =   Style::getColor($button->params->get('color_hover', ''));
                $bgcolor        =   Style::getColor($button->params->get('bgcolor', ''));
                $bgcolor_hover  =   Style::getColor($button->params->get('bgcolor_hover', ''));

                // Color style
                $element->style->child('#btn-'.$button->id)->addCss('color', $color['light']);
                $element->style_dark->child('#btn-'.$button->id)->addCss('color', $color['dark']);
                $element->style->child('#btn-'.$button->id)->hover()->addCss('color', $color_hover['light']);
                $element->style_dark->child('#btn-'.$button->id)->hover()->addCss('color', $color_hover['dark']);

                if (intval($button->params->get('button_outline', ''))) {
                    // Background color style
                    $element->style->child('#btn-'.$button->id)->addCss('border-color', $bgcolor['light']);
                    $element->style_dark->child('#btn-'.$button->id)->addCss('border-color', $bgcolor['dark']);
                    $element->style->child('#btn-'.$button->id)->hover()->addCss('border-color', $bgcolor_hover['light']);
                    $element->style_dark->child('#btn-'.$button->id)->hover()->addCss('border-color', $bgcolor_hover['dark']);
                } else {
                    // Background color style
                    $element->style->child('#btn-'.$button->id)->addCss('background-color', $bgcolor['light']);
                    $element->style_dark->child('#btn-'.$button->id)->addCss('background-color', $bgcolor['dark']);
                    $element->style->child('#btn-'.$button->id)->hover()->addCss('background-color', $bgcolor_hover['light']);
                    $element->style_dark->child('#btn-'.$button->id)->hover()->addCss('background-color', $bgcolor_hover['dark']);
                }
            }

            $link_target    =   !empty($button->params->get('link_target', '')) ? ' target="'.$button->params->get('link_target', '').'"' : '';
            $button_class   =   $button_style !== 'text' ? 'btn btn-' . (intval($button->params->get('button_outline', '')) ? 'outline-' : '') . $button_style . $btn_element_size. $bd_radius : 'as-btn-text text-uppercase text-reset';
            $btn_title      =   $button_style == 'text' ? '<small>'. $title . '</small>' : $title;
            $button_attributes  = new SubForm($button->params->get('button_attributes', ''));
            $btn_attrs = '';
            foreach ($button_attributes->getData() as $attr) {
                $name = trim((string) $attr->params->get('attribute', ''));
                $value = (string) $attr->params->get('value', '');

                if ($name === '' || preg_match('/^on/i', $name)) {
                    continue;
                }

                // Keep only valid attribute name characters (incl. data-* / aria-*)
                $name = preg_replace('/[^a-zA-Z0-9_:\-\.]/', '', $name);
                if ($name === '') {
                    continue;
                }

                $btn_attrs .= ' ' . $name . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            }
            echo '<a id="btn-'.$button->id.'" href="' .$button->params->get('link', ''). '" class="' .$button_class . '"'.$link_target.$btn_attrs.'>'.$btn_title.'</a>';
            $btn_font_style =   $button->params->get('btn_font_style');
            if (!empty($btn_font_style)) {
                Style::renderTypography('#'.$element->id.' #btn-' . $button->id , $btn_font_style, null, $element->isRoot);
            }
        }
        echo '</div>';
    }

    echo '</div>'; // end toast-body
}
echo '</div>';
echo !empty($toast_position) ? '</div>' : '';

if (!$always_display) {
    $document->getWA()->addInlineScript('document.addEventListener(\'DOMContentLoaded\', function (){const astroidToast = document.getElementById("astroid-toast-' . $element->id . '");astroidToast.addEventListener(\'hidden.bs.toast\', () => {astroid.setCookie("astroid_toast_' . $element->id . '", 1, 1);})});');
}

if (!empty($toast_maxwidth)) {
    $style->child('.toast')->addCss('--bs-toast-max-width', $toast_maxwidth . 'px');
}

if (!empty($toast_padding)) {
    $toast_padding = \json_decode($toast_padding, true);
    $style->child('.toast')->addResponsiveCSS('--bs-toast-padding-x', $toast_padding, $toast_padding['postfix']);
}

if (!empty($font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-content-heading', $font_style, null, $element->isRoot);
}
if (!empty($meta_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-content-meta', $meta_font_style, null, $element->isRoot);
}

if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-content-text', $content_font_style, null, $element->isRoot);
    Style::renderTypography('#'.$element->id.' .astroid-content-text *', $content_font_style, null, $element->isRoot);
}