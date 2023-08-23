<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldLayout extends JFormField
{

   protected $type = 'layout';

   public function getLabel()
   {
      return '';
   }

   private function renderData($astroidElement) {
       $form = $astroidElement->getForm();
       $fieldsets = $form->getFieldsets();
       $form_content = array();
       foreach ($fieldsets as $key => $fieldset) {
           $fields = $form->getFieldset($key);
           $groups = [];
           foreach ($fields as $key => $field) {
               if ($field->type == 'astroidgroup') {
                   $groups[$field->fieldname] = ['title' => $field->getAttribute('title', ''), 'icon' => $field->getAttribute('icon', ''), 'description' => $field->getAttribute('description', ''), 'fields' => []];
               }
           }
           foreach ($fields as $key => $field) {
               if ($field->type == 'astroidgroup') {
                   continue;
               }
               $field_group = $field->getAttribute('astroidgroup', 'none');
               $field_tmp  =   [
                   'id'            =>  $field->id,
                   'name'          =>  $field->fieldname,
                   'value'         =>  $field->value,
                   'label'         =>  JText::_($field->getAttribute('label')),
                   'description'   =>  JText::_($field->getAttribute('description')),
                   'type'          =>  $field->type,
                   'group'         =>  $fieldset->name,
                   'ngShow'        =>  Astroid\Helper::replaceRelationshipOperators($field->getAttribute('ngShow'))
               ];
               $groups[$field_group]['fields'][] = $field_tmp;
           }
           $fieldset->label    = JText::_($fieldset->label);
           $fieldset->childs   = $groups;
           $form_content[] = $fieldset;
       }
       return $form_content;
   }

   public function getInput()
   {
       $form_template = array();
       $astroidElements = Astroid\Helper::getAllAstroidElements();
       foreach ($astroidElements as $astroidElement) {
           $form_template[$astroidElement->type] = $this->renderData($astroidElement);
       }
       $sectionElement = new AstroidElement('section');
       $form_template['section'] = $this->renderData($sectionElement);
       $rowElement = new AstroidElement('row');
       $form_template['row'] = $this->renderData($rowElement);
       $columnElement = new AstroidElement('column');
       $form_template['column'] = $this->renderData($columnElement);

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
//      $fieldset = $this->element['data-fieldset'];
//      $renderer = new JLayoutFile('fields.astroidlayout', JPATH_LIBRARIES . '/astroid/framework/layouts');
//      return $renderer->render(array('fieldname' => $this->fieldname, 'name' => $this->name, 'options' => $options, 'fieldset' => $fieldset));
//      return $output;
   }
}
