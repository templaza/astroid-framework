<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldAstroidTypography extends JFormField
{

   //The field class must know its own type through the variable $type.
   protected $type = 'AstroidTypography';

   public function getLabel()
   {
      return false;
   }

   public function getInput()
   {
      $renderer = new JLayoutFile('fields.astroidtypography', JPATH_LIBRARIES . '/astroid/framework/layouts');
      $data = $this->getLayoutData();

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

      $defaults = [
         'font_face' => '',
         'alt_font_face' => '',
         'font_size' => \json_decode('{"desktop":1,"mobile":1,"tablet":1}', false),
         'font_size_unit' => \json_decode('{"desktop":"em","mobile":"em","tablet":"em"}', false),
         'font_color' => '',
         'letter_spacing' => \json_decode('{"desktop":1,"mobile":1,"tablet":1}', false),
         'letter_spacing_unit' => \json_decode('{"desktop":"em","mobile":"em","tablet":"em"}', false),
         'line_height' => \json_decode('{"desktop":1,"mobile":1,"tablet":1}', false),
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
      if (isset($this->element['font-color'])) {
         $defaults['font_color'] = (string) $this->element['font-color'];
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
       $system_fonts = array();
       foreach (Astroid\Helper\Font::$system_fonts as $s_font_value => $s_font_title) {
           $system_fonts[]  =   [
               'value'  =>  $s_font_value,
               'text'   =>  $s_font_title
           ];
       }
       $extraData['system_fonts']   =   $system_fonts;
       $extraData['text_transform_options'] =   array(
           'none' => JText::_('JGLOBAL_INHERIT'),
           'uppercase' => JText::_('JGLOBAL_UPPERCASE'),
           'lowercase' => JText::_('JGLOBAL_LOWERCASE'),
           'capitalize' => JText::_('JGLOBAL_CAPITALIZE')
       );
       $json     =   [
           'id'                  =>  $this->id,
           'name'                =>  $this->name,
           'value'               =>  [
               'font_face'           =>  isset($value['font_face']) && (string) $value['font_face'] != '' ? (string) $value['font_face'] : (string) $defaults['font_face'],
               'alt_font_face'       =>  isset($value['alt_font_face']) &&(string) $value['alt_font_face'] != '' ? (string) $value['alt_font_face'] : (string) $defaults['alt_font_face'],
               'font_size'           =>  isset($value['font_size']) && property_exists((object) $value['font_size'], 'desktop') ? $value['font_size'] : $defaults['font_size'],
               'font_size_unit'      =>  isset($value['font_size_unit']) && property_exists((object) $value['font_size_unit'], 'desktop') ? $value['font_size_unit'] : $defaults['font_size_unit'],
               'font_color'          =>  isset($value['font_color']) ? (string) $value['font_color'] : (string) $defaults['font_color'],
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
               'font_family'        =>  JText::_('TPL_ASTROID_FONT_FAMILY_LABEL'),
               'font_family_alt'    =>  JText::_('TPL_ASTROID_ALT_FONT_FAMILY_LABEL'),
               'font_weight'        =>  JText::_('TPL_ASTROID_FONT_WEIGHT_LABEL'),
               'font_size'          =>  JText::_('TPL_ASTROID_FONT_SIZE_LABEL'),
               'letter_spacing'     =>  JText::_('TPL_ASTROID_LETTER_SPACING_LABEL'),
               'line_height'        =>  JText::_('TPL_ASTROID_LINE_HEIGHT_LABEL'),
               'font_color'         =>  JText::_('TPL_ASTROID_FONT_COLOR_LABEL'),
               'font_style'         =>  JText::_('TPL_ASTROID_FONT_STYLE_LABEL'),
               'text_transform'     =>  JText::_('TPL_ASTROID_TEXT_TRANSFORM_LABEL'),
               'preview'            =>  JText::_('TPL_ASTROID_OPTIONS_PREVIEW_LABEL'),
               'inherit'            =>  JText::_('JGLOBAL_INHERIT'),
           ],
           'type'                =>  strtolower($this->type),
       ];
       return json_encode($json);

//      return $renderer->render($data);
   }
}
