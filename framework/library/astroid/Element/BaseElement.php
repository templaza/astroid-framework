<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2025 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Element;

use Astroid\Helper;
use Astroid\Helper\Media;
use Astroid\Helper\Style;
use Astroid\Framework;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class BaseElement
{
    protected $_data, $_tag = 'div', $_classes = [], $_attributes = [];
    public $id, $unqid, $params, $type, $style, $style_dark, $content = '';
    public int $state = 1;
    public bool $isAssigned = true;
    public array $devices = [];
    public array $options = [];
    public string $role = '';
    public bool $isRoot = false;
    public function __construct($data, $devices, $options = array(), $role = '')
    {
        $this->_data    = $data;
        $this->devices  = $devices;
        $this->options  = $options;
        $this->role     = $role;
        $this->id       = $data['id'];
        $this->unqid    = $data['id'];
        $this->type     = isset($data['type']) ? $data['type'] : 'element';
        $this->state    = isset($data['state']) ? intval($data['state']) : 1;
        $this->params   = new Registry();
        if (isset($data['params']) && !empty($data['params'])) {
            $params = [];
            foreach ($data['params'] as $param) {
                $params[$param['name']] = $param['value'];
            }
            $this->params->loadArray($params);
        }

        $this->addClass('astroid-' . Helper::slugify($this->type));

        $this->_id();
        $this->_tag         =   $this->params->get('astroid_element_tag', 'div');
        $this->isRoot       =   $this->role === 'root';
        $this->isAssigned   =   $this->_checkAssignments();
        $this->style        =   new Style('#' . $this->getAttribute('id'), '', $this->isRoot);
        $this->style_dark   =   new Style('#' . $this->getAttribute('id'), 'dark', $this->isRoot);
    }

    protected function wrap(): string
    {
        if (empty($this->content) || !$this->state || !$this->isAssigned) {
            return '';
        }
        $assignment_type =   $this->params->get('assignment_type', 1);
        if ($assignment_type == 0) {
            return '';
        }
        $app = Factory::getApplication();
        $jinput = $app->input;
        $menuId = $jinput->get('Itemid', 0, 'INT');

        $assignment =   $this->params->get('assignment', "");
        if ($assignment_type == 2 && $assignment) {
            $assignment =   \json_decode($assignment, true);
            if ((isset($assignment[$menuId]) && !$assignment[$menuId]) || !isset($assignment[$menuId])) {
                return '';
            }
        }
        $astroid_element_visibility =   $this->params->get('astroid_element_visibility', "allPage");
        if ($astroid_element_visibility == "currentPage") {
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
        if ($max_width) {
            $class_maxwidth         =   'as-width' . ($max_width_breakpoint ? '-' . $max_width_breakpoint : '') . '-' . $max_width;
        } else {
            $class_maxwidth         =   '';
        }
        Helper::triggerEvent('onAstroidPrepareContent', [&$this, 'layout.element.content']);
        $content    =   '';
        if ($class_maxwidth) {
            if ($this->type === 'row') {
                $block_align                =   $this->params->get('block_align','');
                $block_align_breakpoint     =   $this->params->get('block_align_breakpoint','');
                $block_align_fallback       =   $this->params->get('block_align_fallback','');

                $block_align_class          =   '';
                if ($max_width && $block_align) {
                    $block_align_class      =   'w-100 d-flex justify-content' . ($block_align_breakpoint ? '-' . $block_align_breakpoint : '') . '-' . $block_align . ($block_align_fallback ? ' justify-content-' . $block_align_fallback : '');
                }
                $content            .=  '<div class="'.$block_align_class.'"><div class="'.$class_maxwidth.'">' . "<{$this->_tag}{$this->_attrbs()}>" . $this->content . "</{$this->_tag}>" . '</div></div>';
            } else {
                $content            .=  "<{$this->_tag}{$this->_attrbs()}>".'<div class="'.$class_maxwidth.'">'. $this->content .'</div>'."</{$this->_tag}>";
            }
        } else {
            $content                .=  "<{$this->_tag}{$this->_attrbs()}>" . $this->content . "</{$this->_tag}>";
        }
        return $content;
    }

    protected function _checkAssignments(): bool
    {
        $assignment_type =   $this->params->get('assignment_type', 1);
        if ($assignment_type == 0) {
            return false;
        }
        $app = Factory::getApplication();
        $jinput = $app->input;
        $menuId = $jinput->get('Itemid', 0, 'INT');

        $assignment =   $this->params->get('assignment', "");
        if ($assignment_type == 2 && $assignment) {
            $assignment =   \json_decode($assignment, true);
            if ((isset($assignment[$menuId]) && !$assignment[$menuId]) || !isset($assignment[$menuId])) {
                return false;
            }
        }

        return true;
    }

    protected function _attrbs(): string
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

    protected function _id(): void
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

    public function addClass($class): void
    {
        if (!empty($class)) {
            $this->_classes[] = $class;
        }
    }

    protected function addAttribute($prop, $value): void
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

    protected function _getclasses(): void
    {
        $max_width                  =   $this->params->get('max_width','');
        $block_align                =   $this->params->get('block_align','');
        $block_align_breakpoint     =   $this->params->get('block_align_breakpoint','');
        $block_align_fallback       =   $this->params->get('block_align_fallback','');

        if ($max_width && $block_align && $this->type !== 'row') {
            if ($this->type !== 'column') {
                $this->addClass('w-100');
            }
            $this->addClass('d-flex justify-content' . ($block_align_breakpoint ? '-' . $block_align_breakpoint : '') . '-' . $block_align . ($block_align_fallback ? ' justify-content-' . $block_align_fallback : ''));
        }

        $text_alignment             =   $this->params->get('text_alignment','');
        $text_alignment_breakpoint  =   $this->params->get('text_alignment_breakpoint','');
        $text_alignment_fallback    =   $this->params->get('text_alignment_fallback','');

        if ($text_alignment) {
            $this->addClass('text' . ($text_alignment_breakpoint ? '-' . $text_alignment_breakpoint : '') . '-' . $text_alignment . ($text_alignment_fallback ? ' text-' . $text_alignment_fallback : ''));
        }

        $this->addClass($this->params->get('customclass', ''));
        $this->addClass($this->params->get('hideonxs', 0) ? 'hideonxs' : '');
        $this->addClass($this->params->get('hideonsm', 0) ? 'hideonsm' : '');
        $this->addClass($this->params->get('hideonmd', 0) ? 'hideonmd' : '');
        $this->addClass($this->params->get('hideonlg', 0) ? 'hideonlg' : '');
        $this->addClass($this->params->get('hideonxl', 0) ? 'hideonxl' : '');
        $this->addClass($this->params->get('hideonxxl', 0) ? 'hideonxxl' : '');
    }

    protected function _sticky(): void
    {
        $sticky_effect          =   $this->params->get('astroid_element_sticky_effect','');
        if (!empty($sticky_effect)) {
            $sticky_effect_breakpoint   =   $this->params->get('astroid_element_sticky_effect_breakpoint','');
            $sticky_effect_offset       =   $this->params->get('astroid_element_sticky_effect_offset', '');
            $this->addClass('sticky' . ($sticky_effect_breakpoint ? '-' . $sticky_effect_breakpoint : '') . '-' . $sticky_effect);
            if (!empty($sticky_effect_offset)) {
                $sticky_effect_offset   =   json_decode($sticky_effect_offset, true);
                $this->style->addResponsiveCSS($sticky_effect, $sticky_effect_offset, $sticky_effect_offset['postfix']);
            }
        }
    }

    protected function _styles(): void
    {
        $this->_background();
        $this->_border();
        $this->_marginPadding();
        $this->_sticky();
        $this->_typography();
        $this->_animation();
        $this->style->render();
        $this->style_dark->render();
    }

    protected function _border(): void
    {
        $border = json_decode($this->params->get('border_style', ''), true);
        if (!empty($border)) {
            $this->style->addBorder($border, 'global', $this->isRoot);
        }
        $border_radius = $this->params->get('border_radius', '');
        $this->style->addResponsiveCSS('border-radius', $border_radius, 'px');
    }

    protected function _background(): void
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
                    $this->style->addCss('background-image', 'url(' . Uri::base(true) . '/' . Media::getPath() . '/' . $image . ')');
                    $this->style->addCss('background-repeat', $this->params->get('background_repeat', ''));
                    $this->style->addCss('background-size', $this->params->get('background_size', ''));
                    $this->style->addCss('background-attachment', $this->params->get('background_attchment', ''));
                    $this->style->addCss('background-position', $this->params->get('background_position', ''));
                    $this->addOverlayColor();
                }
                break;
            case 'video': // if video background
                $video = $this->params->get('background_video', '');
                $poster = $this->params->get('background_image', '');
                if (!empty($video)) {
                    $this->addAttribute('data-as-video-bg', Uri::base(true) . '/' . Media::getPath() . '/' . $video);
                    $this->addAttribute('data-as-video-poster', Uri::base(true) . '/' . Media::getPath() . '/' . $poster);
                    Framework::getDocument()->loadVideoBG();
                    $this->addOverlayColor();
                }
                break;
            case 'gradient': // if gradient background
                $this->style->addCss('background-image', Style::getGradientValue($this->params->get('background_gradient', '')));
                break;
        }
    }

    protected function addOverlayColor(): void {
        $overlay_type   =   $this->params->get('background_image_overlay', '');
        if (!empty($overlay_type)) {
            $background = $this->params->get('background_setting', '');
            $overlay_style_cls      =   '.astroid-element-overlay';
            if ($background == 'video') {
                $this->addClass('position-relative');
                $overlay_style_cls  =   ' > ' . $overlay_style_cls;
            } else {
                $this->addClass('position-relative astroid-element-overlay');
            }

            switch ($overlay_type) {
                case 'color':
                    $background_image_overlay_color     =   Style::getColor($this->params->get('background_image_overlay_color', ''));
                    if (!empty($background_image_overlay_color)) {
                        $overlay_style   =   new Style('#' . $this->getAttribute('id') . $overlay_style_cls . ':before', '', $this->isRoot);
                        $overlay_style->addCss('background-color', $background_image_overlay_color['light']);
                        $overlay_style->render();

                        $overlay_style   =   new Style('#' . $this->getAttribute('id') . $overlay_style_cls . ':before', 'dark', $this->isRoot);
                        $overlay_style->addCss('background-color', $background_image_overlay_color['dark']);
                        $overlay_style->render();
                    }
                    break;
                case 'gradient':
                    $background_image_overlay_gradient  =   $this->params->get('background_image_overlay_gradient', '');
                    if (!empty($background_image_overlay_gradient)) {
                        $overlay_style   =   new Style('#' . $this->getAttribute('id') . $overlay_style_cls . ':before', '', $this->isRoot);
                        $overlay_style->addCss('background-image', Style::getGradientValue($background_image_overlay_gradient));
                        $overlay_style->render();
                    }
                    break;
                case 'pattern':
                    $background_image_overlay_pattern   =   $this->params->get('background_image_overlay_pattern', '');
                    $background_image_overlay_color     =   Style::getColor($this->params->get('background_image_overlay_color', ''));
                    if (!empty($background_image_overlay_pattern)) {
                        $overlay_style   =   new Style('#' . $this->getAttribute('id') . $overlay_style_cls . ':before', '', $this->isRoot);
                        if ($background_image_overlay_color) {
                            $overlay_style_dark   =   new Style('#' . $this->getAttribute('id') . $overlay_style_cls . ':before', 'dark', $this->isRoot);
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

    protected function _marginPadding(): void
    {
        $margin = $this->params->get('margin', '');
        $padding = $this->params->get('padding', '');
        Style::setSpacingStyle($this->style, $margin, 'margin');
        Style::setSpacingStyle($this->style, $padding, 'padding');
    }

    protected function _typography(): void
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

    protected function _animation(): void
    {
        $animation = $this->params->get('animation', '');
        if (empty($animation)) {
            return;
        }
        $document = Framework::getDocument();

        $this->addAttribute('data-animation', $animation);

        $delay = $this->params->get('animation_delay', '');
        if (!empty($delay)) {
            $this->addAttribute('data-animation-delay', $delay);
        }

        $duration = $this->params->get('animation_duration', '');
        if (!empty($duration)) {
            $this->addAttribute('data-animation-duration', $duration);
        }

        $this->addAttribute('style', 'visibility: hidden;');
        if (Helper::isPro()) {
            $animation_element = $this->params->get('animation_element', '');
            if (!empty($animation_element)) {
                $this->addAttribute('data-animation-element', $animation_element);
            }
            $this->addAttribute('data-animation-loop', $this->params->get('animation_loop', 0));
            $this->addAttribute('data-animation-stagger', $this->params->get('animation_stagger', 200));
        }
        $document->loadAnimation();
    }
}
