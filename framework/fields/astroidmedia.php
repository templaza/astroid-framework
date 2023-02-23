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
