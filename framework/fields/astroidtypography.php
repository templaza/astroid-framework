<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

class JFormFieldAstroidTypography extends FormField
{
    //The field class must know its own type through the variable $type.
    protected $type = 'AstroidTypography';

    public function getLabel()
    {
        return false;
    }

    public function getInput()
    {
        if (!is_array($this->value) && empty($this->value)) {
            $value = [];
        } else {
            $value = (array) $this->value;
        }

        foreach (['font_size', 'font_size_unit', 'letter_spacing', 'letter_spacing_unit', 'line_height', 'line_height_unit'] as $responsiveField) {
            if (isset($value[$responsiveField]) && is_string($value[$responsiveField])) {
                $object = new \stdClass();
                $object->desktop = $value[$responsiveField];
                $object->tablet = $value[$responsiveField];
                $object->mobile = $value[$responsiveField];
                $value[$responsiveField] = $object;
            }
        }

        $plugin_params  =   Astroid\Helper::getPluginParams();
        $color_mode     =   $plugin_params->get('astroid_color_mode_enable', 0);

        $defaults = [
            'font_face' => '',
            'alt_font_face' => '',
            'font_size' => \json_decode('{"desktop":"","mobile":"","tablet":""}', false),
            'font_size_unit' => \json_decode('{"desktop":"em","mobile":"em","tablet":"em"}', false),
            'font_color' => '{"light":"","dark":""}',
            'letter_spacing' => \json_decode('{"desktop":"","mobile":"","tablet":""}', false),
            'letter_spacing_unit' => \json_decode('{"desktop":"em","mobile":"em","tablet":"em"}', false),
            'line_height' => \json_decode('{"desktop":"","mobile":"","tablet":""}', false),
            'line_height_unit' => \json_decode('{"desktop":"em","mobile":"em","tablet":"em"}', false),
            'font_style' => [],
            'font_weight' => '',
            'text_transform' => '',
        ];

        $extraData = array();

        if (isset($this->element['font-face'])) {
            $defaults['font_face'] = $this->element['font-face'];
        }

        if (isset($this->element['alt-font-face'])) {
            $defaults['alt_font_face'] = $this->element['alt-font-face'];
        }

        if (isset($this->element['font-size'])) {
            $object = new \stdClass();
            $object->desktop = (string) $this->element['font-size'];
            $object->tablet = (string) $this->element['font-size'];
            $object->mobile = (string) $this->element['font-size'];
            $defaults['font_size'] = $object;
        }

        if (isset($this->element['font-size-unit'])) {
            $object = new \stdClass();
            $object->desktop = (string) $this->element['font-size-unit'];
            $object->tablet = (string) $this->element['font-size-unit'];
            $object->mobile = (string) $this->element['font-size-unit'];
            $defaults['font_size_unit'] = $object;
        }

        if (isset($this->element['letter-spacing'])) {
            $object = new \stdClass();
            $object->desktop = (string) $this->element['letter-spacing'];
            $object->tablet = (string) $this->element['letter-spacing'];
            $object->mobile = (string) $this->element['letter-spacing'];
            $defaults['letter_spacing'] = $object;
        }

        if (isset($this->element['letter-spacing-unit'])) {
            $object = new \stdClass();
            $object->desktop = (string) $this->element['letter-spacing-unit'];
            $object->tablet = (string) $this->element['letter-spacing-unit'];
            $object->mobile = (string) $this->element['letter-spacing-unit'];
            $defaults['letter_spacing_unit'] = $object;
        }

        if (isset($this->element['line-height'])) {
            if (!is_object($this->element['line-height'])) {
                $object = new \stdClass();
                $object->desktop = (string) $this->element['line-height'];
                $object->tablet = (string) $this->element['line-height'];
                $object->mobile = (string) $this->element['line-height'];
                $defaults['line_height'] = $object;
            }
        }

        if (isset($this->element['line-height-unit'])) {
            $object = new \stdClass();
            $object->desktop = (string) $this->element['line-height-unit'];
            $object->tablet = (string) $this->element['line-height-unit'];
            $object->mobile = (string) $this->element['line-height-unit'];
            $defaults['line_height_unit'] = $object;
        }

        if (isset($this->element['font-style'])) {
            $defaults['font_style'] = explode(',', (string) $this->element['font-style']);
        }

        if (isset($this->element['font-weight'])) {
            $defaults['font_weight'] = (string) $this->element['font-weight'];
        }

        if (isset($this->element['text-transform'])) {
            $defaults['text_transform'] = (string) $this->element['text-transform'];
        }

        if ($this->element['color-picker'] == 'false') {
            $extraData['colorpicker'] = false;
        } else {
            $extraData['colorpicker'] = true;
        }

        if ($this->element['font-picker'] == 'false') {
            $extraData['fontpicker'] = false;
        } else {
            $extraData['fontpicker'] = true;
        }

        if ($this->element['font-size-picker'] == 'false') {
            $extraData['sizepicker'] = false;
        } else {
            $extraData['sizepicker'] = true;
        }

        if ($this->element['letter-spacing-picker'] == 'false') {
            $extraData['letterspacingpicker'] = false;
        } else {
            $extraData['letterspacingpicker'] = true;
        }

        if ($this->element['line-height-picker'] == 'false') {
            $extraData['lineheightpicker'] = false;
        } else {
            $extraData['lineheightpicker'] = true;
        }

        if ($this->element['font-style-picker'] == 'false') {
            $extraData['stylepicker'] = false;
        } else {
            $extraData['stylepicker'] = true;
        }

        if ($this->element['font-weight-picker'] == 'false') {
            $extraData['weightpicker'] = false;
        } else {
            $extraData['weightpicker'] = true;
        }

        if ($this->element['text-transform-picker'] == 'false') {
            $extraData['transformpicker'] = false;
        } else {
            $extraData['transformpicker'] = true;
        }

        if (isset($this->element['columns']) && $this->element['columns'] != '') {
            $extraData['columns'] = (int) $this->element['columns'];
        } else {
            $extraData['columns'] = 3;
        }

        if (isset($this->element['preview']) && $this->element['preview'] == 'false') {
            $extraData['preview'] = false;
        } else {
            $extraData['preview'] = true;
        }

        $extraData['colormode'] = $color_mode;
        $system_fonts = array();
        foreach (Astroid\Helper\Font::$system_fonts as $s_font_value => $s_font_title) {
            $system_fonts[]  =   [
                'value'  =>  $s_font_value,
                'text'   =>  $s_font_title
            ];
        }
        $extraData['system_fonts']   =   $system_fonts;
        $extraData['text_transform_options'] =   array(
            'none' => Text::_('JGLOBAL_INHERIT'),
            'uppercase' => Text::_('JGLOBAL_UPPERCASE'),
            'lowercase' => Text::_('JGLOBAL_LOWERCASE'),
            'capitalize' => Text::_('JGLOBAL_CAPITALIZE')
        );
        $color_light = $color_dark = $color_value = isset($value['font_color']) && $value['font_color'] ? $value['font_color'] : $defaults['font_color'];
        $color_json = json_decode($color_value);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $value['font_color'] = json_encode(['light'=>$color_light, 'dark'=>$color_dark]);
        } else {
            $value['font_color'] = $color_value;
        }
        $json     =   [
            'id'                  =>  $this->id,
            'name'                =>  $this->name,
            'value'               =>  [
                'font_face'           =>  isset($value['font_face']) && (string) $value['font_face'] != '' ? (string) $value['font_face'] : (string) $defaults['font_face'],
                'alt_font_face'       =>  isset($value['alt_font_face']) &&(string) $value['alt_font_face'] != '' ? (string) $value['alt_font_face'] : (string) $defaults['alt_font_face'],
                'font_size'           =>  isset($value['font_size']) && property_exists((object) $value['font_size'], 'desktop') ? $value['font_size'] : $defaults['font_size'],
                'font_size_unit'      =>  isset($value['font_size_unit']) && property_exists((object) $value['font_size_unit'], 'desktop') ? $value['font_size_unit'] : $defaults['font_size_unit'],
                'font_color'          =>  $value['font_color'],
                'letter_spacing'      =>  isset($value['letter_spacing']) && property_exists((object) $value['letter_spacing'], 'desktop') ? $value['letter_spacing'] : $defaults['letter_spacing'],
                'letter_spacing_unit' =>  isset($value['letter_spacing_unit']) && property_exists((object) $value['letter_spacing_unit'], 'desktop') ? $value['letter_spacing_unit'] : $defaults['letter_spacing_unit'],
                'line_height'         =>  isset($value['line_height']) && property_exists((object) $value['line_height'], 'desktop') ? $value['line_height'] : $defaults['line_height'],
                'line_height_unit'    =>  isset($value['line_height_unit']) && property_exists((object) $value['line_height_unit'], 'desktop') ? $value['line_height_unit'] : $defaults['line_height_unit'],
                'font_style'          =>  isset($value['font_style']) && $value['font_style'] && is_array($value['font_style']) ? $value['font_style'] : $defaults['font_style'],
                'font_weight'         =>  isset($value['font_weight']) && (string) $value['font_weight'] != '' ? (string) $value['font_weight'] : (string) $defaults['font_weight'],
                'text_transform'      =>  isset($value['text_transform']) &&(string) $value['text_transform'] != '' ? (string) $value['text_transform'] : (string) $defaults['text_transform'],
            ],
            'options'             =>  $extraData,
            'lang'                =>  [
                'font_family'        =>  Text::_('TPL_ASTROID_FONT_FAMILY_LABEL'),
                'font_family_alt'    =>  Text::_('TPL_ASTROID_ALT_FONT_FAMILY_LABEL'),
                'font_weight'        =>  Text::_('TPL_ASTROID_FONT_WEIGHT_LABEL'),
                'font_size'          =>  Text::_('TPL_ASTROID_FONT_SIZE_LABEL'),
                'letter_spacing'     =>  Text::_('TPL_ASTROID_LETTER_SPACING_LABEL'),
                'line_height'        =>  Text::_('TPL_ASTROID_LINE_HEIGHT_LABEL'),
                'font_color'         =>  Text::_('TPL_ASTROID_FONT_COLOR_LABEL'),
                'font_style'         =>  Text::_('TPL_ASTROID_FONT_STYLE_LABEL'),
                'text_transform'     =>  Text::_('TPL_ASTROID_TEXT_TRANSFORM_LABEL'),
                'preview'            =>  Text::_('TPL_ASTROID_OPTIONS_PREVIEW_LABEL'),
                'inherit'            =>  Text::_('JGLOBAL_INHERIT'),
            ],
            'type'                =>  strtolower($this->type),
        ];
        return json_encode($json);
    }
}