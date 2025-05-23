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
use Astroid\Helper\Constants;
use Joomla\CMS\Language\Text;

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

        $defaults = [
            'source' => 'none',
            'start' => '1',
            'quantity' => '10',
            'conditions' => [],
            'order' => '',
            'order_dir' => 'DESC',
            'dynamic_content' => new stdClass(),
            'options' => '{}'
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
                'dynamic_content'  =>  isset($value['dynamic_content']) && (object) $value['dynamic_content'] != '' ? (object) $value['dynamic_content'] : $defaults['dynamic_content'],
                'options'          =>  isset($value['options']) && (string)$value['options'] ? (string) $value['options'] : $defaults['options'],
            ],
            'type'                =>  strtolower($this->type),
            'pro_msg'             =>  [
                'title'   =>  Text::_('ASTROID_GET_PRO_TITLE'),
                'desc'    =>  Text::sprintf('ASTROID_GET_PRO_DESCRIPTION', Text::_($this->getAttribute('label')), Constants::$go_pro . '?utm_source=feature_links&utm_medium=getpro_link&utm_campaign=go_pro&utm_id=astroid_signup')
            ],
        ];
        return json_encode($json);
    }
}