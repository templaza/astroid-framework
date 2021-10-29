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

class JFormFieldAstroidResponsive extends JFormField {

   protected $type = 'AstroidResponsive';

   public function getLabel() {
      return false;
   }

   public function getInput() {
      return '<div class="astroidresponsive"><textarea class="d-none" astroidresponsive ng-model="' . $this->name . '" name="' . $this->name . '" id="' . $this->id . '">' . $this->value . '</textarea></div>';
   }

}
