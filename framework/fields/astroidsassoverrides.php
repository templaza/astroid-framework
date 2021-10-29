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
class JFormFieldAstroidsassoverrides extends JFormField {

//The field class must know its own type through the variable $type.
   protected $type = 'astroidsassoverrides';

   public function getLabel() {
      return false;
   }

   public function getInput() {
      $renderer = new JLayoutFile('fields.astroidsassoverrides', JPATH_LIBRARIES . '/astroid/framework/layouts');
      return $renderer->render($this->getLayoutData());
   }

}
