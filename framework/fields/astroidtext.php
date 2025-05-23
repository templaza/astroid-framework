<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @link   http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since  11.1
 */
class JFormFieldAstroidtext extends FormField {

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'astroidtext';
    protected $ordering;

   /**
    * Method to get the field input markup.
    *
    * @return  string  The field input markup.
    *
    * @since   11.1
    */
   protected function getInput() {
       $json =   [
           'id'      =>  $this->id,
           'name'    =>  $this->name,
           'value'   =>  $this->value,
           'hint'    =>  $this->hint,
           'dynamic' => isset($this->element['dynamic']) && (bool)$this->element['dynamic'],
           'type'    =>  strtolower($this->type),
       ];

       return json_encode($json);
   }
}
