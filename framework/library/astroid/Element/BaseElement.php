<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Element;

use Astroid\Framework;
use Astroid\Helper;
use Astroid\Helper\Media;
use Astroid\Helper\Style;

defined('_JEXEC') or die;

class BaseElement
{
    protected $_data, $_tag = 'div', $_classes = [], $_attributes = [];
    public $id, $params, $type, $style, $content = '';
    public function __construct($data)
    {
        $this->_data = $data;
        $this->id = $data['id'];
        $this->type = isset($data['type']) ? $data['type'] : 'element';
        $this->params = new \JRegistry();
        if (isset($data['params']) && !empty($data['params'])) {
            $params = [];
            foreach ($data['params'] as $param) {
                $params[$param['name']] = $param['value'];
            }
            $this->params->loadArray($params);
        }
        $this->addClass('astroid-' . Helper::slugify($this->type));
        $this->_id();
        $this->style = new Style('#' . $this->getAttribute('id'));
    }

    protected function wrap()
    {
        if (empty($this->content)) {
            return '';
        }
        $this->_styles();
        return "<{$this->_tag}{$this->_attrbs()}>" . $this->content . "</{$this->_tag}>";
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
        $id = '';
        $customid = $this->params->get('customid', '');
        if (!empty($customid)) {
            $id = $customid;
        } else {
            $prefix = !empty($this->params->get('title')) ? $this->params->get('title') : 'astroid-' . $this->type;
            $id = Helper::shortify($prefix) . '-' . $this->id;
        }
        if (!empty($id)) {
            $this->addAttribute('id', $id);
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
        $this->addClass($this->params->get('customclass', ''));
        $this->addClass($this->params->get('hideonxs', 0) ? 'hideonxs' : '');
        $this->addClass($this->params->get('hideonsm', 0) ? 'hideonsm' : '');
        $this->addClass($this->params->get('hideonmd', 0) ? 'hideonmd' : '');
        $this->addClass($this->params->get('hideonlg', 0) ? 'hideonlg' : '');
        $this->addClass($this->params->get('hideonxl', 0) ? 'hideonxl' : '');
    }

    protected function _styles()
    {
        $this->_background();
        $this->_marginPadding();
        $this->_typography();
        $this->_animation();
        $this->style->render();
    }

    protected function _background()
    {
        $background = $this->params->get('background_setting', '');
        if (empty($background)) {
            return;
        }
        switch ($background) {
            case 'color': // if color background
                $this->style->addCss('background-color', $this->params->get('background_color', ''));
                break;
            case 'image': // if image background
                $this->style->addCss('background-color', $this->params->get('img_background_color', ''));

                $image = $this->params->get('background_image', '');
                if (!empty($image)) {
                    $this->style->addCss('background-image', 'url(' . \JURI::root() . Media::getPath() . '/' . $image . ')');
                    $this->style->addCss('background-repeat', $this->params->get('background_repeat', ''));
                    $this->style->addCss('background-size', $this->params->get('background_size', ''));
                    $this->style->addCss('background-attachment', $this->params->get('background_attchment', ''));
                    $this->style->addCss('background-position', $this->params->get('background_position', ''));
                }

                break;
            case 'video': // if video background
                $video = $this->params->get('background_video', '');
                if (!empty($video)) {
                    $this->addAttribute('data-jd-video-bg', \JURI::root() . Media::getPath() . '/' . $video);
                    Framework::getDocument()->addScript('vendor/astroid/js/videobg.js', 'body');
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
        $this->style->addCss('color', $this->params->get('text_color', ''));

        $link = $this->style->addChild('a');
        $linkHover = $this->style->addChild('a:hover');
        $link->addCss('color', $this->params->get('link_color', ''));
        $linkHover->addCss('color', $this->params->get('link_hover_color', ''));
    }

    protected function _animation()
    {
        $animation = $this->params->get('animation', '');
        if (empty($animation)) {
            return;
        }
        $document = Framework::getDocument();
        $document->addStyleSheet('css/animate.min.css');
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
