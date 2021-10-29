<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

// The class name must always be the same as the filename (in camel case)
class JFormFieldAstroidRange extends JFormField {

//The field class must know its own type through the variable $type.
   protected $type = 'AstroidRange';

   public function getInput() {
      $sassVariable = $this->element['astroid-scss-variable'];
      $html = '';

      $html .= '<div style="padding-top: 35px" class="position-relative"><span class="range-slider-value d-none"></span><div class="clearfix"></div><input id="' . $this->id . '_slider" data-slider-id="' . $this->id . '_slider" type="number" data-slider-max="' . $this->element['max'] . '" data-slider-tooltip="false" data-slider-step="' . $this->element['step'] . '" data-slider-value="' . $this->value . '" value="' . $this->value . '" name="' . $this->name . '" range-slider ng-model="' . $this->id . '" data-postfix=" ' . $this->element['postfix'] . '" data-prefix="' . $this->element['prefix'] . '" />' . (!empty($this->element['postfix']) ? '<span style="position: absolute;top: 0;left: 70px;">' . $this->element['postfix'] . '</span>' : '') . '</div>';
      if (!empty($sassVariable)) {
         $html .= '<input type="hidden" name="params[sass_variables][' . $sassVariable . ']" value="' . $this->fieldname . '" />';
      }

      return $html;
   }

}
