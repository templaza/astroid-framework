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

class JFormFieldLayouts extends FormField
{

    protected $type = 'layouts';

    public function getInput()
    {
        $json =   [
            'id'      =>  $this->id,
            'name'    =>  $this->name,
            'category' => isset($this->element['category']) ? (string) $this->element['category'] : 'layouts',
            'type'    =>  strtolower($this->type),
            'value'   =>  $this->value ?? '',
        ];
        return json_encode($json);
    }
}