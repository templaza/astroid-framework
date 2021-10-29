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

class JFormFieldAstroidMedia extends JFormField {

   protected $type = 'AstroidMedia';
   protected $layout = 'fields.astroidmedia';

   public function getInput() {
      $renderer = new JLayoutFile($this->layout, JPATH_LIBRARIES . '/astroid/framework/layouts');
      $data = $this->getLayoutData();
      $data['fieldname'] = $this->fieldname;
      $data['media'] = empty($this->element['media']) ? 'images' : $this->element['media'];
      return $renderer->render($data);
   }

}
