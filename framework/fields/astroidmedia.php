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
//      $renderer = new JLayoutFile($this->layout, JPATH_LIBRARIES . '/astroid/framework/layouts');
//      $data = $this->getLayoutData();
//      $data['fieldname'] = $this->fieldname;
//      $data['media'] = empty($this->element['media']) ? 'images' : $this->element['media'];
//      return $renderer->render($data);
       $mediaType   =   empty($this->element['media']) ? 'images' : $this->element['media'];
       $json =   [
           'id'      =>  $this->id,
           'name'    =>  $this->name,
           'value'   =>  $this->value,
           'media'   =>  $mediaType,
           'ajax'    =>  JUri::root().'administrator/index.php?option=com_ajax&astroid=media',
           'type'    =>  strtolower($this->type),
           'lang'    =>  [
               'select_media'   =>  JText::_('TPL_ASTROID_SELECT_'.strtoupper($mediaType)),
               'change_media'   =>  JText::_('TPL_ASTROID_CHANGE_'.strtoupper($mediaType)),
               'clear'          =>  JText::_('ASTROID_CLEAR')
           ]
       ];
       return json_encode($json);
   }

}
