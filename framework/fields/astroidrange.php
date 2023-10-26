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

// The class name must always be the same as the filename (in camel case)
class JFormFieldAstroidRange extends FormField {

//The field class must know its own type through the variable $type.
   protected $type = 'AstroidRange';
    protected $ordering;

   public function getInput() {
//       var_dump($this->element['min']); die();
       $json =   [
           'id'      =>  $this->id,
           'name'    =>  $this->name,
           'value'   =>  $this->value,
           'min'     =>  (string)$this->element['min'],
           'max'     =>  (string)$this->element['max'],
           'step'    =>  (string)$this->element['step'],
           'postfix' =>  (string)$this->element['postfix'],
           'type'    =>  strtolower($this->type),
       ];

       return json_encode($json);
//      $sassVariable = $this->element['astroid-scss-variable'];
//      $html = '';
//
//      $html .= '<div style="padding-top: 35px" class="position-relative"><span class="range-slider-value d-none"></span><div class="clearfix"></div><input id="' . $this->id . '_slider" data-slider-id="' . $this->id . '_slider" type="number" data-slider-max="' . $this->element['max'] . '" data-slider-tooltip="false" data-slider-step="' . $this->element['step'] . '" data-slider-value="' . $this->value . '" value="' . $this->value . '" name="' . $this->name . '" range-slider ng-model="' . $this->id . '" data-postfix=" ' . $this->element['postfix'] . '" data-prefix="' . $this->element['prefix'] . '" />' . (!empty($this->element['postfix']) ? '<span style="position: absolute;top: 0;left: 70px;">' . $this->element['postfix'] . '</span>' : '') . '</div>';
//      if (!empty($sassVariable)) {
//         $html .= '<input type="hidden" name="params[sass_variables][' . $sassVariable . ']" value="' . $this->fieldname . '" />';
//      }
//
//      return $html;
   }

}
