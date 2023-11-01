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
class JFormFieldAstroidpreloaders extends FormField {

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.1
    */
   protected $type = 'astroidpreloaders';
    protected $ordering;

   /**
    * Method to get the field input markup for a generic list.
    * Use the multiple attribute to enable multiselect.
    *
    * @return  string  The field input markup.
    *
    * @since   3.7.0
    */
   protected function getInput() {
      $selected = Astroid\Helper\Constants::$preloaders['circle'];
      if (empty($this->value)) {
         $this->value = $selected['name'];
      }
       $json =   [
           'id'         =>  $this->id,
           'name'       =>  $this->name,
           'value'      =>  $this->value,
           'preloader'  =>  ($this->element['astroid-preload-type'] == 'fontawesome') ? Astroid\Helper\Constants::$preloadersFont : Astroid\Helper\Constants::$preloaders,
           'style'      =>  ($this->element['astroid-preload-type'] == 'fontawesome') ? 'fontawesome' : 'animation',
           'type'       =>  strtolower($this->type),
       ];
       return json_encode($json);
   }
}
