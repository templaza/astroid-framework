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
class JFormFieldAstroidicon extends JFormField {

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'astroidicon';

   /**
    * Method to get the field input markup for a generic list.
    * Use the multiple attribute to enable multiselect.
    *
    * @return  string  The field input markup.
    *
    * @since   3.7.0
    */
   protected function getInput() {
      $html = '<div class="form-control ui fluid search selection dropdown" select-ui-div><input type="hidden" value="' . $this->value . '" name="' . $this->name . '"><i class="dropdown icon"></i><div class="default text">Select Icon</div><div class="menu">';
      $groups = Astroid\Helper\Constants::$icons;
      $options = array();
      foreach ($groups as $group => $icons) {
         foreach ($icons as $key => $value) {
            $html .= '<div class="item" data-value="' . $key . '"><i class="' . $key . '"></i> ' . $value . '</div>';
         }
      }
      $html .= '</div></div>';
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

      $groups = Astroid\Helper\Constants::$icons;
      $options = array();
      foreach ($groups as $group => $icons) {
         foreach ($icons as $key => $value) {
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
