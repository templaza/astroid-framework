<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldAstroidMultiSelect extends FormField {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'astroidmultiselect';
    protected $ordering;

    /**
     * Method to get the field input markup for a generic list.
     * Use the multiple attribute to enable multiselect.
     *
     * @return  string  The field input markup.
     *
     * @since   3.7.0
     */
    protected function getInput() {
        // Get the field options.
        $options = $this->getOptions();

        $json =   [
            'id'      =>  $this->id,
            'name'    =>  $this->name,
            'value'   =>  $this->value,
            'options' =>  $options,
            'hint'    =>  Text::_($this->hint),
            'type'    =>  strtolower($this->type),
        ];
        return json_encode($json);
    }

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   3.7.0
     */
    protected function getOptions() {
        $options = array();
        foreach ($this->element->xpath('option') as $option) {

            $value = (string) $option['value'];
            $text = trim((string) $option) != '' ? trim((string) $option) : $value;

            $tmp = array(
                'value' => $value,
                'text' => Text::_($text),
            );

            // Add the option object to the result set.
            $options[] = $tmp;
        }
        return $options;
    }
}
