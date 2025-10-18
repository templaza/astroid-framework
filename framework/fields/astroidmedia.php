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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Astroid\Helper\Media;

class JFormFieldAstroidMedia extends FormField {

   protected $type = 'AstroidMedia';
    protected $ordering;
   protected $layout = 'fields.astroidmedia';

   public function getInput() {
       $mediaType   =   empty($this->element['media']) ? 'images' : $this->element['media'];
       $json =   [
           'id'         =>  $this->id,
           'name'       =>  $this->name,
           'value'      =>  $this->value,
           'media'      =>  (string) $mediaType,
           'dynamic' => isset($this->element['dynamic']) && (bool)$this->element['dynamic'],
           'mediaPath'  =>  Media::getPath(),
           'ajax'       =>  Uri::root('base').'/administrator/index.php?option=com_ajax&astroid=media',
           'type'       =>  strtolower($this->type)
       ];
       return json_encode($json);
   }

}
