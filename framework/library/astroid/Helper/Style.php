<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Framework;
use Astroid\Helper;
use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class Style
{
    public $_selector, $_css = ['mobile' => [], 'landscape_mobile' => [], 'tablet' => [], 'desktop' => [], 'large_desktop' => [], 'larger_desktop' => []], $_styles = ['mobile' => [], 'landscape_mobile' => [], 'tablet' => [], 'desktop' => [], 'large_desktop' => [], 'larger_desktop' => []], $_child = [];

    protected $_hover = null, $_focus = null, $_active = null, $_link = null;
    public bool $_onFile = false;
    public string $_mode = '';
    public function __construct($selectors, $mode = '', $onFile = false)
    {
        if (is_array($selectors)) {
            for ($key = 0; $key < count($selectors); $key ++) {
                $selectors[$key]    =   $mode ? '[data-bs-theme='.$mode.'] '. $selectors[$key] : $selectors[$key];
            }
            $this->_selector    =   implode(', ', $selectors);
        } else {
            $this->_selector    =   $mode ? '[data-bs-theme='.$mode.'] '. $selectors : $selectors;
        }
        $this->_mode = $mode;
        $this->_onFile = $onFile;
    }

    protected function _selectorize($postfix = null, $prefix = null)
    {
        $return = [];
        $selectors = explode(',', $this->_selector);
        if ($postfix !== null) {
            $postfixes = explode(',', $postfix);
            foreach ($selectors as $selector) {
                foreach ($postfixes as $postfix) {
                    $return[] = trim($selector) . $postfix;
                }
            }
        }
        if ($prefix !== null) {
            $prefixes = explode(',', $prefix);
            foreach ($selectors as $selector) {
                foreach ($prefixes as $prefix) {
                    $return[] = $prefix . trim($selector);
                }
            }
        }
        return implode(', ', $return);
    }

    public function hover($class = '')
    {
        if ($this->_hover === null) {
            $this->_hover = new Style($this->_selectorize(':hover' . (empty($class) ? '' : ',' . $class)), '', $this->_onFile);
        }
        return $this->_hover;
    }

    public function focus($class = '')
    {
        if ($this->_focus === null) {
            $this->_focus = new Style($this->_selectorize(':focus' . (empty($class) ? '' : ',' . $class)), '', $this->_onFile);
        }
        return $this->_focus;
    }

    public function active($class = '')
    {
        if ($this->_active === null) {
            $this->_active = new Style($this->_selectorize(':active' . (empty($class) ? '' : ',' . $class)), '', $this->_onFile);
        }
        return $this->_active;
    }

    public function link($ref = 'child', $subfix = '')
    {
        if ($this->_link === null) {
            if ($ref == 'child') {
                $this->_link = new Style($this->_selectorize(' a'. $subfix), '', $this->_onFile);
            } else if ($ref == 'self') {
                $this->_link = new Style($this->_selectorize(null, 'a'. $subfix), '', $this->_onFile);
            } else {
                $this->_link = new Style($this->_selectorize(null, 'a'.$subfix.' '), '', $this->_onFile);
            }
        }
        return $this->_link;
    }

    public function child($selector)
    {
        if ($this->_hasChild($selector)) {
            return $this->_getChild($selector);
        } else {
            return $this->addChild($selector);
        }
    }

    protected function _hasChild($selector)
    {
        $selector = $this->_childSelector($selector);
        return isset($this->_child[Helper::slugify($selector)]);
    }

    protected function _getChild($selector)
    {
        $selector = $this->_childSelector($selector);
        if (isset($this->_child[Helper::slugify($selector)])) {
            return $this->_child[Helper::slugify($selector)];
        } else {
            return null;
        }
    }

    public function addCss($property, $value, $device = 'mobile')
    {
        if (empty($value)) {
            return $this;
        }
        $this->_css[$device][$property] = $value;
        return $this;
    }

    public function addResponsiveCSS($property, $value, $unit = '')
    {
        if (empty($value)) {
            return $this;
        }
        if (is_string($value)) {
            $json = json_decode($value, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->_css['mobile'][$property] = $value . $unit;
                return $this;
            } else {
                $value = $json;
            }
        }
        if (is_array($value)) {
            foreach (['mobile', 'landscape_mobile', 'tablet', 'desktop', 'large_desktop', 'larger_desktop'] as $device) {
                if (isset($value[$device]) && !empty($value[$device])) {
                    $this->_css[$device][$property] = $value[$device] . (is_array($unit) && isset($unit[$device]) ? $unit[$device] : (is_string($unit) ? $unit : ''));
                }
            }
        } elseif (is_object($value)) {
            foreach (['mobile', 'landscape_mobile', 'tablet', 'desktop', 'large_desktop', 'larger_desktop'] as $device) {
                if (isset($value->{$device}) && !empty($value->{$device})) {
                    $this->_css[$device][$property] = $value->{$device} . (is_array($unit) && isset($unit[$device]) ? $unit[$device] : (is_string($unit) ? $unit : ''));
                }
            }
        } else {
            $this->_css['mobile'][$property] = $value . $unit;
        }
        return $this;
    }

    public function addBorder($value, $device = 'mobile', $onFile = false) {
        self::addBorderStyle($this->_selector, $value, $device, $onFile);
    }

    public function addStyle($css, $device = 'mobile')
    {
        if (empty($css)) {
            return;
        }
        $this->_styles[$device][] = $css;
    }

    public function addChild($selector)
    {
        $selector = $this->_childSelector($selector);
        $this->_child[Helper::slugify($selector)] = new Style($selector, '', $this->_onFile);
        return $this->_child[Helper::slugify($selector)];
    }

    protected function _childSelector($selector)
    {
        $selector = explode(',', $selector);
        foreach ($selector as &$element) {
            if (strpos(trim($element), ':') === 0) {
                $element = $this->_selectorize($element);
            } else {
                $element = $this->_selectorize(' ' . trim($element));
            }
        }

        $selector = implode(', ', $selector);
        return $selector;
    }

    public function render()
    {
        $css = ['mobile' => '', 'landscape_mobile' => '', 'tablet' => '', 'desktop' => '', 'large_desktop' => '', 'larger_desktop' => ''];
        foreach ($this->_css as $device => $styles) {
            foreach ($styles as $property => $value) {
                $css[$device] .= $property . ':' . $value . ';';
            }
        }

        foreach ($this->_styles as $device => $cssScript) {
            if (!empty($cssScript)) {
                $css[$device] .= implode(';', $cssScript);
            }
        }

        if (!empty($css)) {
            $document = Framework::getDocument();
            $cssContent = '';
            foreach ($css as $device => $styles) {
                if (!empty($styles)) {
                    if ($document->canWriteFile() && $this->_onFile) {
                        $document->addStyleDeclaration($this->_selector . '{' . $styles . '}', $device);
                    } else {
                        $cssContent .= self::getCss($this->_selector . '{' . $styles . '}', $device);
                    }
                }
            }
            if (!empty($cssContent) && $document->canUseWA()) {
                $document->getWA()->addInlineStyle($cssContent);
            }
        }

        $this->_css = ['mobile' => [], 'landscape_mobile' => [], 'tablet' => [], 'desktop' => [], 'large_desktop' => [], 'larger_desktop' => []];
        $this->_styles = ['mobile' => [], 'landscape_mobile' => [], 'tablet' => [], 'desktop' => [], 'large_desktop' => [], 'larger_desktop' => []];

        if ($this->_hover !== null) {
            $this->_hover->render();
        }

        if ($this->_focus !== null) {
            $this->_focus->render();
        }

        if ($this->_active !== null) {
            $this->_active->render();
        }

        if ($this->_link !== null) {
            $this->_link->render();
        }

        foreach ($this->_child as $child) {
            $child->render();
        }
    }

    public static function getCss($content, $device = 'mobile', $breakpoints = ['landscape_mobile' => '576px', 'tablet' => '768px', 'desktop' => '992px', 'large_desktop' => '1200px', 'larger_desktop' => '1400px'])
    {
        return match ($device) {
            'landscape_mobile' => '@media (min-width: '.$breakpoints['landscape_mobile'].') {' . $content . '}',
            'tablet' => '@media (min-width: '.$breakpoints['tablet'].') {' . $content . '}',
            'desktop' => '@media (min-width: '.$breakpoints['desktop'].') {' . $content . '}',
            'large_desktop' => '@media (min-width: '.$breakpoints['large_desktop'].') {' . $content . '}',
            'larger_desktop' => '@media (min-width: '.$breakpoints['larger_desktop'].') {' . $content . '}',
            default => $content,
        };
    }

    public static function addBorderStyle($selector, $border, $device = 'mobile', $onFile = false) {
        $style      = new Style($selector, '', $onFile);
        $style_dark = new Style($selector, 'dark', $onFile);
        if (isset($border['border_width'])) {
            $style->addCss('border-width', $border['border_width']. 'px', $device);
        }
        if (isset($border['border_style'])) {
            $style->addCss('border-style', $border['border_style'], $device);
        }
        if (isset($border['border_color'])) {
            if (isset($border['border_color']['light'])) {
                $style->addCss('border-color', $border['border_color']['light'], $device);
            }
            if (isset($border['border_color']['dark'])) {
                $style_dark->addCss('border-color', $border['border_color']['dark'], $device);
            }
        }
        $style->render();
        $style_dark->render();
    }

    public static function addCssBySelector($selector, $property, $value, $device = 'mobile', $mode = '', $onFile = false)
    {
        $style = new Style($selector, $mode, $onFile);
        $style->addCss($property, $value, $device);
        $style->render();
        return $style;
    }

    public static function renderTypography($selector, $object, $defaultObject = null, $onFile = false)
    {
        $typography = new Registry();
        $typography->loadObject($object);

        $globalParams = Helper::getPluginParams();
        $style = new Style($selector, '', $onFile);
        $style_dark = new Style($selector, 'dark', $onFile);

        // font color, weight and transfrom
        $font_color = Style::getColor($typography->get('font_color', ''));
        $style->addCss('color', $font_color['light']);
        $style_dark->addCss('color', $font_color['dark']);
        $style->addCss('font-weight', $typography->get('font_weight', ''));
        $style->addCss('text-transform', $typography->get('text_transform', ''));

        // font size
        $font_size = $typography->get('font_size', '');
        $font_size_unit = $typography->get('font_size_unit', '');

        if (!empty($font_size)) {
            if (is_object($font_size)) {
                $default_value = '';
                foreach (['larger_desktop', 'large_desktop', 'desktop', 'tablet', 'landscape_mobile', 'mobile'] as $device) {
                    if (isset($font_size->{$device}) && $font_size->{$device}) {
                        $unit = $font_size_unit->{$device} ?? 'em';
                        $style->addCss('font-size', $font_size->{$device} . $unit, $device);
                        if ($globalParams->get('astroid_safemode', 0)) {
                            $default_value = $font_size->{$device} . $unit;
                        }
                    } elseif ($device == 'mobile') {
                        $style->addCss('font-size', $default_value, $device);
                    }
                }
            } elseif ($font_size) {
                $style->addCss('font-size', $font_size . $font_size_unit);
            }
        }

        // font styles
        $font_styles = $typography->get('font_style', []);
        if (is_array($font_styles) && count($font_styles)) {
            foreach ($font_styles as $font_style) {
                switch ($font_style) {
                    case 'bold':
                        $style->addCss('font-weight', 'bold');
                        break;
                    case 'italic':
                        $style->addCss('font-style', 'italic');
                        break;
                    case 'underline':
                        $style->addCss('text-decoration', 'underline');
                        break;
                }
            }
        }

        // letter spacing
        $letter_spacing = $typography->get('letter_spacing', '');
        $letter_spacing_unit = $typography->get('letter_spacing_unit', '');

        if (!empty($letter_spacing)) {
            if (is_object($letter_spacing)) {
                $default_value = '';
                foreach (['larger_desktop', 'large_desktop', 'desktop', 'tablet', 'landscape_mobile', 'mobile'] as $device) {
                    if (isset($letter_spacing->{$device}) && !empty($letter_spacing->{$device})) {
                        $letter_spacing_unit_value = $letter_spacing_unit->{$device} ?? 'em';
                        $style->addCss('letter-spacing', $letter_spacing->{$device} . $letter_spacing_unit_value, $device);
                        if ($globalParams->get('astroid_safemode', 0)) {
                            $default_value = $letter_spacing->{$device} . $letter_spacing_unit_value;
                        }
                    } elseif ($device == 'mobile') {
                        $style->addCss('letter-spacing', $default_value, $device);
                    }
                }
            } else {
                $style->addCss('letter-spacing', $letter_spacing . $letter_spacing_unit);
            }
        }

        // line height
        $line_height = $typography->get('line_height', '');
        $line_height_unit = $typography->get('line_height_unit', '');

        if (!empty($line_height)) {
            if (is_object($line_height)) {
                $default_value = '';
                foreach (['larger_desktop', 'large_desktop', 'desktop', 'tablet', 'landscape_mobile', 'mobile'] as $device) {
                    if (isset($line_height->{$device}) && $line_height->{$device}) {
                        $line_height_unit_value = $line_height_unit->{$device} ?? 'em';
                        $style->addCss('line-height', $line_height->{$device} . $line_height_unit_value, $device);
                        if ($globalParams->get('astroid_safemode', 0)) {
                            $default_value = $line_height->{$device} . $line_height_unit_value;
                        }
                    } elseif ($device == 'mobile') {
                        $style->addCss('line-height', $default_value, $device);
                    }
                }
            } elseif ($line_height) {
                $style->addCss('line-height', $line_height . $line_height_unit);
            }
        }

        // font family
        $font_face = $typography->get('font_face', '');
        $alt_font_face = $typography->get('alt_font_face', '');

        if ($defaultObject !== null) {
            $defaultTypography = new Registry();
            $defaultTypography->loadObject($defaultObject);
            $font_face = ($font_face == '__default' ? $defaultTypography->get('font_face', '') : $font_face);
            $alt_font_face = ($alt_font_face == '__default' ? $defaultTypography->get('alt_font_face', '') : $alt_font_face);
        }
        $style->addCss('font-family', self::getFontFamilyValue($font_face, $alt_font_face));
        $style->render();
        $style_dark->render();
    }

    public static function addBackgroundCSS ($obj, $obj_params, $prefix = '', $onFile = false): void
    {
        $background = $obj_params->get($prefix . 'background_setting', '');
        if (!empty($background)) {
            $style = new Style($obj, '', $onFile);
            $style_dark = new Style($obj, 'dark', $onFile);
            switch ($background) {
                case 'color': // if color background
                    $background_color   =   Style::getColor($obj_params->get($prefix . 'background_color', ''));
                    $style->addCss('background-color', $background_color['light']);
                    $style_dark->addCss('background-color', $background_color['dark']);
                    break;
                case 'image': // if image background
                    $background_color   =   Style::getColor($obj_params->get($prefix . 'img_background_color', ''));
                    $style->addCss('background-color', $background_color['light']);
                    $style_dark->addCss('background-color', $background_color['dark']);
                    $image = $obj_params->get($prefix . 'background_image', '');
                    if (!empty($image)) {
                        $style->addCss('background-image', 'url(' . Uri::base(true) . '/' . Helper\Media::getPath() . '/' . $image . ')');
                        $style->addCss('background-repeat', $obj_params->get($prefix . 'background_repeat', ''));
                        $style->addCss('background-size', $obj_params->get($prefix . 'background_size', ''));
                        $style->addCss('background-attachment', $obj_params->get($prefix . 'background_attchment', ''));
                        $style->addCss('background-position', $obj_params->get($prefix . 'background_position', ''));
                        self::addOverlayColor($obj, $obj_params, $prefix);
                    }
                    break;
                case 'video': // if video background
                    $video = $obj_params->get($prefix . 'background_video', '');
                    if (!empty($video)) {
                        self::addOverlayColor($obj, $obj_params, $prefix);
                    }
                    break;
                case 'gradient': // if gradient background
                    $style->addCss('background-image', Style::getGradientValue($obj_params->get($prefix . 'background_gradient', '')));
                    break;
            }
            $style->render();
            $style_dark->render();
        }
    }

    public static function addOverlayColor($obj, $obj_params, $prefix = '', $onFile = false): void
    {
        $overlay_type   =   $obj_params->get($prefix . 'background_image_overlay', '');
        if (!empty($overlay_type)) {
            $background = $obj_params->get($prefix . 'background_setting', '');
            $overlay_style_cls      =   '.astroid-element-overlay';
            if ($background == 'video') {
                $overlay_style_cls  =   ' > ' . $overlay_style_cls;
            }

            switch ($overlay_type) {
                case 'color':
                    $background_image_overlay_color     =   Style::getColor($obj_params->get($prefix . 'background_image_overlay_color', ''));
                    if (!empty($background_image_overlay_color)) {
                        $overlay_style   =   new Style($obj . $overlay_style_cls . ':before', '', $onFile);
                        $overlay_style->addCss('background-color', $background_image_overlay_color['light']);
                        $overlay_style->render();

                        $overlay_style   =   new Style($obj . $overlay_style_cls . ':before', 'dark', $onFile);
                        $overlay_style->addCss('background-color', $background_image_overlay_color['dark']);
                        $overlay_style->render();
                    }
                    break;
                case 'gradient':
                    $background_image_overlay_gradient  =   $obj_params->get($prefix . 'background_image_overlay_gradient', '');
                    if (!empty($background_image_overlay_gradient)) {
                        $overlay_style   =   new Style($obj . $overlay_style_cls . ':before');
                        $overlay_style->addCss('background-image', Style::getGradientValue($background_image_overlay_gradient));
                        $overlay_style->render();
                    }
                    break;
                case 'pattern':
                    $background_image_overlay_pattern   =   $obj_params->get($prefix . 'background_image_overlay_pattern', '');
                    $background_image_overlay_color     =   Style::getColor($obj_params->get($prefix . 'background_image_overlay_color', ''));
                    if (!empty($background_image_overlay_pattern)) {
                        $overlay_style   =   new Style($obj . $overlay_style_cls . ':before', '', $onFile);
                        if ($background_image_overlay_color) {
                            $overlay_style_dark   =   new Style($obj . $overlay_style_cls . ':before', 'dark', $onFile);
                            $overlay_style->addCss('background-color', $background_image_overlay_color['light']);
                            $overlay_style_dark->addCss('background-color', $background_image_overlay_color['dark']);
                            $overlay_style_dark->render();
                        }
                        $overlay_style->addCss('background-image', 'url(' . Uri::root() . Helper\Media::getPath() . '/' . $background_image_overlay_pattern . ')');
                        $overlay_style->render();
                    }
                    break;
            }
        }
    }

    public static function getFontFamilyValue($value, $alt_font = '')
    {
        $value = ($value == '__default' ? '' : $value);
        if (empty($value) && empty($alt_font)) {
            return '';
        }

        $return = [];
        $font = Helper\Font::getFontFamily($value);
        if (!empty($font)) {
            $return[] = $font;
        }
        $alt_font = Helper\Font::getFontFamily($alt_font);
        if (!empty($alt_font)) {
            $return[] = $alt_font;
        }
        return implode(', ', $return);
    }

    public static function getColor($color)
    {
        $result = json_decode($color);
        if (json_last_error() === JSON_ERROR_NONE) {
            return ['light'=>$result->light, 'dark'=>$result->dark];
        } else {
            return ['light'=>$color, 'dark'=>$color];
        }
    }

    public static function getGradientValue($value)
    {
        if (empty($value)) {
            return '';
        }
        $gradient = \json_decode($value, true);
        if (isset($gradient['type']) && $gradient['start'] && $gradient['stop']) {
            if ($gradient['type'] == 'linear') {
                return $gradient['type'] . '-gradient('. (isset($gradient['angle']) ? $gradient['angle'].'deg,' : '') . $gradient['start'] . (isset($gradient['start_pos']) ? ' ' . $gradient['start_pos'].'%' : '') . ',' . $gradient['stop'] . (isset($gradient['stop_pos']) ?  ' ' . $gradient['stop_pos'].'%' : '') . ')';
            } else {
                return $gradient['type'] . '-gradient('. (isset($gradient['position']) && $gradient['position'] ? $gradient['position'].',' : '') . $gradient['start'] . (isset($gradient['start_pos']) ? ' ' . $gradient['start_pos'].'%' : '') . ',' . $gradient['stop'] . (isset($gradient['stop_pos']) ? ' ' . $gradient['stop_pos'].'%' : '') . ')';
            }
        } else {
            return '';
        }
    }

    public static function setSpacingStyle($style, $value, $type = 'padding'): void
    {
        if (!empty($value)) {
            $globalParams = Helper::getPluginParams();
            $object = \json_decode($value, false);
            $default_value = ['top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'Custom'];
            foreach ($object as $device => $props) {
                if ($globalParams->get('astroid_safemode', 0)) {
                    $style->addStyle(Style::spacingValue($props, $type, ($device == 'mobile' ? $default_value : [])), $device);
                    Style::setDefaultSpace($props, $default_value);
                } else {
                    $style->addStyle(Style::spacingValue($props, $type), $device);
                }
            }
        }
    }

    public static function setDefaultSpace ($props, &$default_value): void
    {
        foreach (['top', 'right', 'bottom', 'left'] as $direction) {
            if (isset($props->{$direction}) && (!empty($props->{$direction}) || is_numeric($props->{$direction}))) {
                $default_value[$direction] = $props->{$direction} . ($props->unit == 'Custom' ? '' : $props->unit);
            }
        }
    }

    public static function spacingValue($value = null, $property = "padding", $default = [])
    {
        $return = [];
        $values = [];
        if (!empty($value) && isset($value->unit)) {
            $unit = $value->unit == 'Custom' ? '' : $value->unit;
            if ( $value->lock && (($value->unit == 'Custom' && isset($value->top)) || is_numeric($value->top)) ) {
                foreach (['top', 'right', 'bottom', 'left'] as $position) {
                    $return[$position] = self::getPropertySubset($property, $position) . ":{$value->top}{$unit}";
                    $values[$position] = "{$value->top}{$unit}";
                }
            } else {
                foreach (['top', 'right', 'bottom', 'left'] as $position) {
                    $pvalue = $value->{$position};
                    if (($value->unit == 'Custom' && isset($pvalue) && $pvalue !== '') || is_numeric($pvalue)) {
                        $return[$position] = self::getPropertySubset($property, $position) . ":{$pvalue}{$unit}";
                        $values[$position] = "{$pvalue}{$unit}";
                    }
                }
            }
        }

        if (!isset($default['unit'])) {
            $default['unit'] = 'px';
        }
        if ($default['unit'] == 'Custom') {
            $default['unit'] = '';
        }

        foreach (array_keys($default) as $position) {
            if ($position == "unit") {
                continue;
            }
            if (!isset($return[$position]) && $default[$position] !== '') {
                $return[$position] = self::getPropertySubset($property, $position) . ":{$default[$position]}{$default['unit']}";
                $values[$position] = "{$default[$position]}{$default['unit']}";
            }
        }

        if (count(array_keys($values)) === 4) {
            $return = [];
            $return[] = self::getPropertySet($property) . ':' . implode(' ', $values);
        }

        return implode(";", $return);
    }

    public static function getSubFormParams($params)
    {
        $return_array = array();
        if (is_array($params) && count($params)) {
            foreach ($params as $param) {
                $return_array[$param->name] = $param->value;
            }
        }
        return $return_array;
    }

    public static function getPropertySubset($property, $position)
    {
        switch ($property) {
            case "radius":
                switch ($position) {
                    case "top":
                        return 'border-top-left-radius';
                        break;
                    case "left":
                        return 'border-bottom-left-radius';
                        break;
                    case "right":
                        return 'border-top-right-radius';
                        break;
                    case "bottom":
                        return 'border-bottom-right-radius';
                        break;
                }
                break;
            case "border":
                return $property . '-' . $position . '-width';
                break;
            default:
                return $property . '-' . $position;
                break;
        }
    }

    public static function getPropertySet($property)
    {
        switch ($property) {
            case "radius":
                return "border-radius";
                break;
            case "border":
                return "border-width";
                break;
            default:
                return $property;
                break;
        }
    }
}
