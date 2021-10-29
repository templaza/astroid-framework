<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldAstroidanimations extends JFormField {

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'astroidanimations';

   /**
    * Method to get the field input markup for a generic list.
    * Use the multiple attribute to enable multiselect.
    *
    * @return  string  The field input markup.
    *
    * @since   3.7.0
    */
   protected function getInput() {
      $html = array();
      $attr = '';

      // Initialize some field attributes.
      $attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
      $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
      $attr .= $this->multiple ? ' multiple' : '';
      $attr .= $this->required ? ' required aria-required="true"' : '';
      $attr .= $this->autofocus ? ' autofocus' : '';

      // To avoid user's confusion, readonly="true" should imply disabled="true".
      if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1' || (string) $this->disabled == 'true') {
         $attr .= ' disabled="disabled"';
      }
      // Initialize JavaScript field attributes.
      $attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

      $html = '<select name="' . $this->name . '" ng-model="' . $this->id . '" animation-selector ' . $attr . '>';
      $groups = Astroid\Helper\Constants::$animations;
      $options = array();
      foreach ($groups as $group => $animations) {
         if (!empty($group)) {
            $html .= '<optgroup label="' . $group . '">';
         }
         foreach ($animations as $key => $value) {
            $html .= '<option value="' . $key . '">' . $value . '</option>';
         }
         if (!empty($group)) {
            $html .= '</optgroup>';
         }
      }
      $html .= '</select>';

      return $html;
   }

   /**
    * Method to get the field options.
    *
    * @return  array  The field option objects.
    *
    * @since   3.7.0
    */
   protected function getOptions() {

      $groups = Astroid\Helper\Constants::$animations;
      $options = array();
      foreach ($groups as $group => $animations) {
         foreach ($animations as $key => $value) {
            $options[] = array('text' => $value, 'value' => $key, '');
         }
      }

      return $options;
   }

   /**
    * Method to add an option to the list field.
    *
    * @param   string  $text        Text/Language variable of the option.
    * @param   array   $attributes  Array of attributes ('name' => 'value' format)
    *
    * @return  JFormFieldList  For chaining.
    *
    * @since   3.7.0
    */
   public function addOption($text, $attributes = array()) {
      if ($text && $this->element instanceof SimpleXMLElement) {
         $child = $this->element->addChild('option', $text);

         foreach ($attributes as $name => $value) {
            $child->addAttribute($name, $value);
         }
      }

      return $this;
   }

   /**
    * Method to get certain otherwise inaccessible properties from the form field object.
    *
    * @param   string  $name  The property name for which to get the value.
    *
    * @return  mixed  The property value or null.
    *
    * @since   3.7.0
    */
   public function __get($name) {
      if ($name == 'options') {
         return $this->getOptions();
      }

      return parent::__get($name);
   }

}