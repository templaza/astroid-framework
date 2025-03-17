<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2025 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Astroid\Helper\Constants;

class JFormFieldDynamicContent extends FormField
{
    //The field class must know its own type through the variable $type.
    protected $type = 'DynamicContent';

    public function getLabel()
    {
        return false;
    }

    public function getInput()
    {
        if (!is_array($this->value) && empty($this->value)) {
            $value = [];
        } else {
            $value = (array) $this->value;
        }

        $plugin_params  =   Astroid\Helper::getPluginParams();

        $defaults = [
            'source' => 'none',
            'start' => '1',
            'quantity' => '',
            'conditions' => [],
            'order' => 'publish_up',
            'order_dir' => 'DESC',
        ];

        $options  =     [
            'sources' => Constants::$dynamic_sources,
            'fields' => Constants::$dynamic_source_fields,
        ];

        $json     =   [
            'id'                  =>  $this->id,
            'name'                =>  $this->name,
            'value'               =>  [
                'source'           =>  isset($value['source']) && (string) $value['source'] != '' ? (string) $value['source'] : (string) $defaults['source'],
                'start'            =>  isset($value['start']) && (string) $value['start'] != '' ? (string) $value['start'] : (string) $defaults['start'],
                'quantity'         =>  isset($value['quantity']) && (string) $value['quantity'] != '' ? (string) $value['quantity'] : (string) $defaults['quantity'],
                'conditions'       =>  isset($value['conditions']) && is_array($value['conditions']) ? $value['conditions'] : $defaults['conditions'],
                'order'            =>  isset($value['order']) && (string) $value['order'] != '' ? (string) $value['order'] : (string) $defaults['order'],
                'order_dir'        =>  isset($value['order_dir']) && (string) $value['order_dir'] != '' ? (string) $value['order_dir'] : (string) $defaults['order_dir'],
            ],
            'options'             =>  $options,
            'type'                =>  strtolower($this->type),
        ];
        return json_encode($json);
    }
}