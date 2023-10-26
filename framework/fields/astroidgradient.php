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

class JFormFieldAstroidGradient extends FormField {

   //The field class must know its own type through the variable $type.
   protected $type = 'AstroidGradient';

   public function getInput() {
       $json =   [
           'id'      =>  $this->id,
           'name'    =>  $this->name,
           'value'   =>  json_decode($this->value),
           'type'    =>  strtolower($this->type),
       ];
       return json_encode($json);
   }
}
