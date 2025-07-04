<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2025 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class JFormFieldAstroidLayout extends FormField
{
    protected $type = 'astroidlayout';

    protected function getInput()
    {
        $json =   [
            'id'      =>  $this->id,
            'name'    =>  $this->name,
            'value'   =>  $this->value,
        ];
        $html = '<script type="application/json" id="' . $this->id . '-data" class="astroid-select-layout">' . json_encode($json) . '</script>';
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->registerAndUseScript('astroid.select_layout', 'astroid/selectlayout.min.js', ['relative' => true, 'version' => 'auto']);
        Text::script('JOPTION_USE_DEFAULT');
        return $html;
    }
}