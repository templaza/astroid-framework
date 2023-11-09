<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\Component\Modules\Administrator\Helper\ModulesHelper;
use Joomla\CMS\Language\Text;
FormHelper::loadFieldClass('list');

/**
 * Modules Position field.
 *
 * @since  3.4.2
 */
class JFormFieldAstroidModulesStyle extends ListField
{

    /**
     * The form field type.
     *
     * @var    string
     * @since  3.4.2
     */
    protected $type = 'AstroidModulesStyle';
    protected $ordering;
    protected function getInput()
    {
        // Get the field options.
        $options = Astroid\Helper::getModuleStyles();
        if ($this->value) {
            $value = $this->value;
        } else {
            $value = 'astroidxhtml';
        }

        $json =   [
            'id'      =>  $this->id,
            'name'    =>  $this->name,
            'value'   =>  $value,
            'options' =>  $options,
            'type'    =>  strtolower($this->type),
        ];
        return json_encode($json);
    }
}