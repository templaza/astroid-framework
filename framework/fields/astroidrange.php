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

   protected $type = 'AstroidRange';
    protected $ordering;

   public function getInput() {
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
   }

}
