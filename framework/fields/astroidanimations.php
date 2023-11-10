<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Form\FormField;

/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldAstroidanimations extends FormField {

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
      $groups = Astroid\Helper\Constants::$animations;
      $options = array();
      foreach ($groups as $group => $animations) {
         foreach ($animations as $key => $value) {
             $options[] = [
                 'value' => $key,
                 'text'  => $value
             ];
         }
      }
       $json =   [
           'id'      =>  $this->id,
           'name'    =>  $this->name,
           'value'   =>  htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
           'options' =>  $options,
           'attr'    =>  trim($attr),
           'type'    =>  strtolower($this->type),
       ];
       return json_encode($json);
   }

}