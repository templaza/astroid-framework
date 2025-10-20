<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2025 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;

class JFormFieldAstroidMainlayouts extends FormField
{
    protected $type = 'astroidmainlayouts';

    protected function getInput()
    {
        $value = $this->value ?? '';
        if (empty($value)) {
            $options = \file_get_contents(JPATH_SITE . '/' . 'media' . '/' . 'astroid' . '/' . 'assets' . '/' . 'json' . '/' . 'layouts' . '/' . 'default.json');
        } else {
            $options = $value;
        }
        $json = [
            'id'      => $this->id,
            'name'    => $this->name,
            'type'    => strtolower($this->type),
            'value'   => $options
        ];
        return json_encode($json);
    }
}