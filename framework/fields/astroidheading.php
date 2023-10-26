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
/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldAstroidHeading extends FormField
{

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'astroidheading';

   /**
    * Method to get the field input markup for a generic list.
    * Use the multiple attribute to enable multiselect.
    *
    * @return  string  The field input markup.
    *
    * @since   3.7.0
    */
   protected function getInput()
   {
      $helpLink = '';
      if (!empty($this->element['help'])) {
         $helpLink = (string)$this->element['help'];
      }

       $json =   [
           'id'         =>  $this->id,
           'name'       =>  $this->name,
           'title'              =>  Text::_((string)$this->element['title']),
           'description'        =>  Text::_($this->description),
           'icon'       =>  $this->element['icon'],
           'help'       =>  $helpLink,
           'type'       =>  strtolower($this->type),
       ];
       return json_encode($json);
   }
}
