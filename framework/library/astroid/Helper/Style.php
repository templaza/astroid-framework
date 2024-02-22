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

defined('_JEXEC') or die;

class Style
{
    public $_selector, $_css = ['desktop' => [], 'tablet' => [], 'mobile' => []], $_styles = ['desktop' => [], 'tablet' => [], 'mobile' => []], $_child = [];
    protected $_hover = null, $_focus = null, $_active = null, $_link = null;
    public function __construct($selectors, $mode = '')
    {
        if (is_array($selectors)) {
            for ($key = 0; $key < count($selectors); $key ++) {
                $selectors[$key]    =   $mode ? '[data-bs-theme='.$mode.'] '. $selectors[$key] : $selectors[$key];
            }
            $this->_selector    =   implode(', ', $selectors);
        } else {
            $this->_selector    =   $mode ? '[data-bs-theme='.$mode.'] '. $selectors : $selectors;
        }
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
            $this->_hover = new Style($this->_selectorize(':hover' . (empty($class) ? '' : ',' . $class)));
        }
        return $this->_hover;
    }

    public function focus($class = '')
    {
        if ($this->_focus === null) {
            $this->_focus = new Style($this->_selectorize(':focus' . (empty($class) ? '' : ',' . $class)));
        }
        return $this->_focus;
    }

    public function active($class = '')
    {
        if ($this->_active === null) {
            $this->_active = new Style($this->_selectorize(':active' . (empty($class) ? '' : ',' . $class)));
        }
        return $this->_active;
    }

    public function link($ref = 'child', $subfix = '')
    {
        if ($this->_link === null) {
            if ($ref == 'child') {
                $this->_link = new Style($this->_selectorize(' a'. $subfix));
            } else if ($ref == 'self') {
                $this->_link = new Style($this->_selectorize(null, 'a'. $subfix));
            } else {
                $this->_link = new Style($this->_selectorize(null, 'a'.$subfix.' '));
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

    public function addCss($property, $value, $device = 'desktop')
    {
        if (empty($value)) {
            return $this;
        }
        $this->_css[$device][$property] = $value;
        return $this;
    }

    public static function addCssBySelector($selector, $property, $value, $device = 'desktop')
    {
        $style = new Style($selector);
        $style->addCss($property, $value, $device);
        $style->render();
        return $style;
    }

    public function addStyle($css, $device = 'desktop')
    {
        if (empty($css)) {
            return;
        }
        $this->_styles[$device][] = $css;
    }

    public function addChild($selector)
    {
        $selector = $this->_childSelector($selector);
        $this->_child[Helper::slugify($selector)] = new Style($selector);
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
        $css = ['desktop' => '', 'tablet' => '', 'mobile' => ''];
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
            foreach ($css as $device => $styles) {
                if (!empty($styles)) {
                    $document->addStyleDeclaration($this->_selector . '{' . $styles . '}', $device);
                }
            }
        }

        $this->_css = ['desktop' => [], 'tablet' => [], 'mobile' => []];
        $this->_styles = ['desktop' => [], 'tablet' => [], 'mobile' => []];

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

    public static function renderTypography($selector, $object, $defaultObject = null)
    {
        $typography = new Registry();
        $typography->loadObject($object);

        $style = new Style($selector);
        $style_dark = new Style($selector, 'dark');

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
                foreach (['desktop', 'tablet', 'mobile'] as $device) {
                    if ($font_size->{$device}) {
                        $unit = isset($font_size_unit->{$device}) ? $font_size_unit->{$device} : 'em';
                        $style->addCss('font-size', $font_size->{$device} . $unit, $device);
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
                foreach (['desktop', 'tablet', 'mobile'] as $device) {
                    if (!empty($letter_spacing->{$device})) {
                        $letter_spacing_unit_value = isset($letter_spacing_unit->{$device}) ? $letter_spacing_unit->{$device} : 'em';
                        $style->addCss('letter-spacing', $letter_spacing->{$device} . $letter_spacing_unit_value, $device);
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
                foreach (['desktop', 'tablet', 'mobile'] as $device) {
                    if ($line_height->{$device}) {
                        $line_height_unit_value = isset($line_height_unit->{$device}) ? $line_height_unit->{$device} : 'em';
                        $style->addCss('line-height', $line_height->{$device} . $line_height_unit_value, $device);
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
                    if (($value->unit == 'Custom' && isset($pvalue)) || is_numeric($pvalue)) {
                        $return[$position] = self::getPropertySubset($property, $position) . ":{$pvalue}{$unit}";
                        $values[$position] = "{$pvalue}{$unit}";
                    }
                }
            }
        }

        if (!isset($default['unit'])) {
            $default['unit'] = 'px';
        }

        foreach (array_keys($default) as $position) {
            if ($position == "unit") {
                continue;
            }
            if (!isset($return[$position])) {
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
