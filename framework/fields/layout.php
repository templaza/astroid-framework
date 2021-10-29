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

class JFormFieldLayout extends JFormField
{

   protected $type = 'layout';

   public function getLabel()
   {
      return '';
   }

   public function getInput()
   {
      $value = $this->value;
      if (empty($value)) {
         $options = \json_decode(\file_get_contents(JPATH_SITE . '/' . 'media' . '/' . 'astroid' . '/' . 'assets' . '/' . 'json' . '/' . 'layouts' . '/' . 'default.json'), TRUE);
      } else {
         $options = \json_decode($value, true);
      }
      $fieldset = $this->element['data-fieldset'];
      $renderer = new JLayoutFile('fields.astroidlayout', JPATH_LIBRARIES . '/astroid/framework/layouts');
      return $renderer->render(array('fieldname' => $this->fieldname, 'name' => $this->name, 'options' => $options, 'fieldset' => $fieldset));
      return $output;
   }
}
