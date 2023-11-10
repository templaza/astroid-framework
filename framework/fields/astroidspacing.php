<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Form\FormField;
/**
 * Color Form Field class for the Joomla Platform.
 * This implementation is designed to be compatible with HTML5's `<input type="color">`
 *
 * @link   https://www.w3.org/TR/html-markup/input.color.html
 * @since  11.3
 */
class JFormFieldAstroidSpacing extends FormField
{

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.3
     */
    protected $type = 'AstroidSpacing';

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
