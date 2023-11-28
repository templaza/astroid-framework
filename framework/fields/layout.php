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
use Astroid\Element;

class JFormFieldLayout extends FormField
{

   protected $type = 'layout';

   public function getLabel()
   {
      return '';
   }

   public function getInput()
   {
       $form_template = array();
       $astroidElements = Astroid\Helper::getAllAstroidElements();
       foreach ($astroidElements as $astroidElement) {
           $form_template[$astroidElement->type] = $astroidElement->renderJson('addon');
       }
       $sectionElement = new Element('section');
       $form_template['section'] = $sectionElement->renderJson();
       $rowElement = new Element('row');
       $form_template['row'] = $rowElement->renderJson();
       $columnElement = new Element('column');
       $form_template['column'] = $columnElement->renderJson();

       $value = $this->value;
       if (empty($value)) {
           $options = \json_decode(\file_get_contents(JPATH_SITE . '/' . 'media' . '/' . 'astroid' . '/' . 'assets' . '/' . 'json' . '/' . 'layouts' . '/' . 'default.json'), TRUE);
       } else {
           $options = \json_decode($value, true);
       }
       $json =   [
           'id'      =>  $this->id,
           'name'    =>  $this->name,
           'value'   =>  $options,
           'type'    =>  strtolower($this->type),
           'form'    =>  $form_template
       ];
       return json_encode($json);
   }
}
