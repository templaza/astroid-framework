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
 * Color Form Field class for the Joomla Platform.
 * This implementation is designed to be compatible with HTML5's `<input type="color">`
 *
 * @link   https://www.w3.org/TR/html-markup/input.color.html
 * @since  11.3
 */
class JFormFieldAstroidColor extends FormField {

   /**
    * The form field type.
    *
    * @var    string
    * @since  11.3
    */
   protected $type = 'AstroidColor';
    protected $ordering;

   /**
    * Method to get the field input markup.
    *
    * @return  string  The field input markup.
    *
    * @since   11.3
    */
   protected function getInput() {
       $plugin_params  =   Astroid\Helper::getPluginParams();
       $color_mode     =   $plugin_params->get('astroid_color_mode_enable', 0);
       $value_light    =   $value_dark =   $value = $this->value;
       if (!empty($value)) {
           $result = json_decode($value);
           if (json_last_error() !== JSON_ERROR_NONE) {
               $value          =   json_encode(['light'=>$value_light, 'dark'=>$value_dark]);
           }
       }
       $json =   [
           'id'      =>  $this->id,
           'name'    =>  $this->name,
           'value'   =>  $value,
           'type'    =>  strtolower($this->type),
           'colormode'  =>  $color_mode,
       ];
       return json_encode($json);
   }
}