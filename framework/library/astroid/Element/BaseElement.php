<?php

/**
 * @package   Astroid Framework
 * @author    TemPlaza https://www.templaza.com
 * @copyright Copyright (C) 2011 - 2021 TemPlaza.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or Later
 */
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Element;

use Astroid\Helper;
use Astroid\Helper\Media;
use Astroid\Helper\Style;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class BaseElement
{
    protected $_data, $_tag = 'div', $_classes = [], $_attributes = [];
    public $id, $params, $type, $style, $style_dark, $content = '';
    public int $state = 1;
    public array $devices = [];
    public function __construct($data, $devices)
    {
        $this->_data = $data;
        $this->devices = $devices;
        $this->id = $data['id'];
        $this->type = isset($data['type']) ? $data['type'] : 'element';
        $this->state = isset($data['state']) ? intval($data['state']) : 1;
        $this->params = new Registry();
        if (isset($data['params']) && !empty($data['params'])) {
            $params = [];
            foreach ($data['params'] as $param) {
                $params[$param['name']] = $param['value'];
            }
            $this->params->loadArray($params);
        }

        $this->addClass('astroid-' . Helper::slugify($this->type));

        $this->_id();
        $this->style        =   new Style('#' . $this->getAttribute('id'));
        $this->style_dark   =   new Style('#' . $this->getAttribute('id'), 'dark');
    }

    protected function wrap()
    {
        if (empty($this->content) || !$this->state) {
            return '';
        }
        $astroid_element_visibility =   $this->params->get('astroid_element_visibility', "allPage");
        if ($astroid_element_visibility == "currentPage") {
            $app = Factory::getApplication();
            $jinput = $app->input;
            $menuId = $jinput->get('Itemid', 0, 'INT');

            $menu = $app->getMenu();
            $item = $menu->getItem($menuId);
            if (empty($item)) {
                return '';
            }
            if ((isset($item->query['option']) && $item->query['option'] != $jinput->get('option', '')) || (isset($item->query['view']) && $item->query['view'] != $jinput->get('view', '')) || (isset($item->query['layout']) && $item->query['layout'] != $jinput->get('layout', ''))) {
                return '';
            }
        }
        $this->_styles();
        $max_width                  =   $this->params->get('max_width','');
        $max_width_breakpoint       =   $this->params->get('max_width_breakpoint','');
        $class_maxwidth             =   $max_width ? 'as-width' . ($max_width_breakpoint ? '-' . $max_width_breakpoint : '') . '-' . $max_width : '';
        $content                    =   "<{$this->_tag}{$this->_attrbs()}>";
        if ($class_maxwidth) {
            $content                .=  '<div class="'.$class_maxwidth.'">'. $this->content .'</div>';
        } else {
            $content                .=  $this->content;
        }
        $content                    .=  "</{$this->_tag}>";
        return $content;
    }

    protected function _attrbs()
    {
        $this->_getclasses();
        $attributes = [];
        if (!empty($this->_classes)) {
            $classes = array_unique($this->_classes);
            $attributes[] = 'class="' . implode(' ', $classes) . '"';
        }
        if (!empty($this->_attributes)) {
            foreach ($this->_attributes as $prop => $value) {
                $attributes[] = $prop . '="' . $value . '"';
            }
        }
        return !empty($attributes) ? ' ' . implode(' ', $attributes) : '';
    }

    protected function _id()
    {
        $customid = $this->params->get('customid', '');
        if (!empty($customid)) {
            $this->id = $customid;
        } else {
            $prefix = !empty($this->params->get('title')) ? $this->params->get('title') : 'astroid-' . $this->type;
            $this->id = Helper::shortify($prefix) . '-' . $this->id;
        }
        if (!empty($this->id)) {
            $this->addAttribute('id', $this->id);
        }
    }

    protected function addClass($class)
    {
        if (!empty($class)) {
            $this->_classes[] = $class;
        }
    }

    protected function addAttribute($prop, $value)
    {
        $this->_attributes[$prop] = $value;
    }

    protected function getAttribute($prop)
    {
        if (isset($this->_attributes[$prop])) {
            return $this->_attributes[$prop];
        }
        return null;
    }

    protected function _getclasses()
    {
        $max_width                  =   $this->params->get('max_width','');
        $block_align                =   $this->params->get('block_align','');
        $block_align_breakpoint     =   $this->params->get('block_align_breakpoint','');
        $block_align_fallback       =   $this->params->get('block_align_fallback','');

        $classes                    =   array();
        if ($max_width && $block_align) {
            $classes[]              =   'd-flex justify-content' . ($block_align_breakpoint ? '-' . $block_align_breakpoint : '') . '-' . $block_align . ($block_align_fallback ? ' justify-content-' . $block_align_fallback : '');
        }

        $text_alignment             =   $this->params->get('text_alignment','');
        $text_alignment_breakpoint  =   $this->params->get('text_alignment_breakpoint','');
        $text_alignment_fallback    =   $this->params->get('text_alignment_fallback','');

        if ($text_alignment) {
            $classes[]              =   'text' . ($text_alignment_breakpoint ? '-' . $text_alignment_breakpoint : '') . '-' . $text_alignment . ($text_alignment_fallback ? ' text-' . $text_alignment_fallback : '');
        }

        $class                      =   implode(' ', $classes);
        $this->addClass($class);
        $this->addClass($this->params->get('customclass', ''));
        $this->addClass($this->params->get('hideonxs', 0) ? 'hideonxs' : '');
        $this->addClass($this->params->get('hideonsm', 0) ? 'hideonsm' : '');
        $this->addClass($this->params->get('hideonmd', 0) ? 'hideonmd' : '');
        $this->addClass($this->params->get('hideonlg', 0) ? 'hideonlg' : '');
        $this->addClass($this->params->get('hideonxl', 0) ? 'hideonxl' : '');
        $this->addClass($this->params->get('hideonxxl', 0) ? 'hideonxxl' : '');
    }

    protected function _styles()
    {
        $this->_background();
        $this->_marginPadding();
        $this->_typography();
        $this->_animation();
        $this->style->render();
        $this->style_dark->render();
    }

    protected function _background()
    {
        $background = $this->params->get('background_setting', '');
        if (empty($background)) {
            return;
        }
        switch ($background) {
            case 'color': // if color background
                $background_color   =   Style::getColor($this->params->get('background_color', ''));
                $this->style->addCss('background-color', $background_color['light']);
                $this->style_dark->addCss('background-color', $background_color['dark']);
                break;
            case 'image': // if image background
                $background_color   =   Style::getColor($this->params->get('img_background_color', ''));
                $this->style->addCss('background-color', $background_color['light']);
                $this->style_dark->addCss('background-color', $background_color['dark']);
                $image = $this->params->get('background_image', '');
                if (!empty($image)) {
                    $this->style->addCss('background-image', 'url(' . Uri::root() . Media::getPath() . '/' . $image . ')');
                    $this->style->addCss('background-repeat', $this->params->get('background_repeat', ''));
                    $this->style->addCss('background-size', $this->params->get('background_size', ''));
                    $this->style->addCss('background-attachment', $this->params->get('background_attchment', ''));
                    $this->style->addCss('background-position', $this->params->get('background_position', ''));
                    $overlay_type   =   $this->params->get('background_image_overlay', '');
                    if (!empty($overlay_type)) {
                        $this->addClass('position-relative astroid-element-overlay');
                        switch ($overlay_type) {
                            case 'color':
                                $background_image_overlay_color     =   Style::getColor($this->params->get('background_image_overlay_color', ''));
                                if (!empty($background_image_overlay_color)) {
                                    $overlay_style   =   new Style('#' . $this->getAttribute('id'). '.astroid-element-overlay:before');
                                    $overlay_style->addCss('background-color', $background_image_overlay_color['light']);
                                    $overlay_style->render();

                                    $overlay_style   =   new Style('#' . $this->getAttribute('id'). '.astroid-element-overlay:before', 'dark');
                                    $overlay_style->addCss('background-color', $background_image_overlay_color['dark']);
                                    $overlay_style->render();
                                }
                                break;
                            case 'gradient':
                                $background_image_overlay_gradient  =   $this->params->get('background_image_overlay_gradient', '');
                                if (!empty($background_image_overlay_gradient)) {
                                    $overlay_style   =   new Style('#' . $this->getAttribute('id'). '.astroid-element-overlay:before');
                                    $overlay_style->addCss('background-image', Style::getGradientValue($background_image_overlay_gradient));
                                    $overlay_style->render();
                                }
                                break;
                            case 'pattern':
                                $background_image_overlay_pattern   =   $this->params->get('background_image_overlay_pattern', '');
                                $background_image_overlay_color     =   Style::getColor($this->params->get('background_image_overlay_color', ''));
                                if (!empty($background_image_overlay_pattern)) {
                                    $overlay_style   =   new Style('#' . $this->getAttribute('id'). '.astroid-element-overlay:before');
                                    if ($background_image_overlay_color) {
                                        $overlay_style_dark   =   new Style('#' . $this->getAttribute('id'). '.astroid-element-overlay:before', 'dark');
                                        $overlay_style->addCss('background-color', $background_image_overlay_color['light']);
                                        $overlay_style_dark->addCss('background-color', $background_image_overlay_color['dark']);
                                        $overlay_style_dark->render();
                                    }
                                    $overlay_style->addCss('background-image', 'url(' . Uri::root() . Media::getPath() . '/' . $background_image_overlay_pattern . ')');
                                    $overlay_style->render();
                                }
                                break;
                        }
                    }
                }
                break;
            case 'video': // if video background
                $video = $this->params->get('background_video', '');
                if (!empty($video)) {
                    $this->addAttribute('data-jd-video-bg', Uri::root() . Media::getPath() . '/' . $video);
                    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
                    $wa->registerAndUseScript('astroid.videobg', 'astroid/videobg.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
                }
                break;
            case 'gradient': // if gradient background
                $this->style->addCss('background-image', Style::getGradientValue($this->params->get('background_gradient', '')));
                break;
        }
    }

    protected function _marginPadding()
    {
        $margin = $this->params->get('margin', '');
        $padding = $this->params->get('padding', '');

        if (!empty($margin)) {
            $margin = \json_decode($margin, false);
            foreach ($margin as $device => $props) {
                $this->style->addStyle(Style::spacingValue($props, "margin"), $device);
            }
        }

        if (!empty($padding)) {
            $padding = \json_decode($padding, false);
            foreach ($padding as $device => $props) {
                $this->style->addStyle(Style::spacingValue($props, "padding"), $device);
            }
        }
    }

    protected function _typography()
    {
        if (!$this->params->get('custom_colors', 0)) {
            return;
        }
        $text_color =   Style::getColor($this->params->get('text_color', ''));
        $this->style->addCss('color', $text_color['light']);
        $this->style_dark->addCss('color', $text_color['dark']);

        $link_color         =   Style::getColor($this->params->get('link_color', ''));
        $link_hover_color   =   Style::getColor($this->params->get('link_hover_color', ''));
        $link = $this->style->addChild('a');
        $linkHover = $this->style->addChild('a:hover');
        $link->addCss('color', $link_color['light']);
        $linkHover->addCss('color', $link_hover_color['light']);

        $link_dark      = $this->style_dark->addChild('a');
        $linkHover_dark = $this->style_dark->addChild('a:hover');
        $link_dark->addCss('color', $link_color['dark']);
        $linkHover_dark->addCss('color', $link_hover_color['dark']);
    }

    protected function _animation()
    {
        $animation = $this->params->get('animation', '');
        if (empty($animation)) {
            return;
        }

        $app = Factory::getApplication();
        $wa = $app->getDocument()->getWebAssetManager();
        $wa->registerAndUseStyle('astroid.animate', 'astroid/animate.min.css');

        $this->addAttribute('style', 'visibility: hidden;');
        $this->addAttribute('data-animation', $animation);

        $delay = $this->params->get('animation_delay', '');
        if (!empty($delay)) {
            $this->addAttribute('data-animation-delay', $delay);
        }

        $duration = $this->params->get('animation_duration', '');
        if (!empty($duration)) {
            $this->addAttribute('data-animation-duration', $duration);
        }
    }
}
