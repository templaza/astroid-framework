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

class JFormFieldAstroidGradient extends JFormField {

   //The field class must know its own type through the variable $type.
   protected $type = 'AstroidGradient';

   public function getInput() {
      $renderer = new JLayoutFile('fields.astroidgradient', JPATH_LIBRARIES . '/astroid/framework/layouts');
      $data = $this->getLayoutData();

      $extraData = array(
          'value' => $this->value,
          'fieldname' => $this->fieldname,
          'ngShow' => $this->element['ngShow'],
          'ngHide' => $this->element['ngHide'],
      );

      $data = array_merge($data, $extraData);

      return $renderer->render($data);
   }

}
