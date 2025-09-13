<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;
use Joomla\CMS\Form\FormField;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Factory;
/**
 * Color Form Field class for the Joomla Platform.
 * This implementation is designed to be compatible with HTML5's `<input type="color">`
 *
 * @link   https://www.w3.org/TR/html-markup/input.color.html
 * @since  11.3
 */
class JFormFieldAstroidSQL extends FormField
{

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.3
     */
    protected $type = 'AstroidSQL';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   11.3
     */
    protected function getInput()
    {
        $sql = isset($this->element['query']) ? (string) $this->element['query'] : '';
        $key_field = isset($this->element['key_field']) ? (string) $this->element['key_field'] : 'value';
        $value_field = isset($this->element['value_field']) ? (string) $this->element['value_field'] : 'text';
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $db->setQuery($sql);
        $list = $db->loadObjectList();
        $options = [];
        if (!empty($list)) {
            foreach ($list as $item) {
                if (!isset($item->$key_field) || !isset($item->$value_field)) {
                    continue;
                }
                $options[] = [
                    'value' => $item->$key_field,
                    'text'  => $item->$value_field
                ];
            }
        }
        $json =   [
            'id'      =>  $this->id,
            'name'    =>  $this->name,
            'value'   =>  $this->value,
            'options' =>  $options,
            'type'    =>  strtolower($this->type),
        ];
        return json_encode($json);
    }
}
