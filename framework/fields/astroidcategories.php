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
use Joomla\CMS\Uri\Uri;

class JFormFieldAstroidCategories extends FormField {

    protected $type = 'AstroidCategories';
    protected $ordering;

    public function getInput() {
        $json =   [
            'id'         =>  $this->id,
            'name'       =>  $this->name,
            'value'      =>  $this->value,
            'ajax'       =>  Uri::root().'administrator/index.php?option=com_ajax&astroid=getcategories',
            'type'       =>  strtolower($this->type),
        ];
        return json_encode($json);
    }
}