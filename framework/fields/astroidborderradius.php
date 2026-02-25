<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2026 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;
use Joomla\CMS\Form\FormField;
class JFormFieldAstroidBorderRadius extends FormField
{

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.3
     */
    protected $type = 'AstroidBorderRadius';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   11.3
     */
    protected function getInput()
    {
        $json =   [
            'id'      =>  $this->id,
            'name'    =>  $this->name,
            'value'   =>  $this->value,
            'type'    =>  strtolower($this->type),
        ];
        return json_encode($json);
    }
}
