<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Framework;
use Astroid\Helper;

defined('_JEXEC') or die;

class Form
{
    protected $form;
    public function __construct($name)
    {
        $this->form = new \JForm($name);
        $template = Framework::getTemplate();
        Helper::triggerEvent('onBeforeAstroidFormLoad', [&$template, &$this->form]);
    }

    public function loadOptions($dir = '')
    {
        $forms = array_filter(glob($dir . '/' . '*.xml'), 'is_file');
        \JForm::addFormPath($dir);
        foreach ($forms as $fname) {
            $fname = pathinfo($fname)['filename'];
            $this->form->loadFile($fname, true);
        }
    }

    protected static function _ording($a, $b)
    {
        if ($a->order == $b->order) {
            return 0;
        }

        if ($a->order == '' || $b->order == '') {
            return 1;
        }

        return ($a->order < $b->order) ? -1 : 1;
    }

    public function getFieldsets()
    {
        $astroidfieldsets = $this->form->getFieldsets();
        usort($astroidfieldsets, 'self::_ording');
        $fieldsets = [];
        foreach ($astroidfieldsets as $af) {
            $fieldsets[$af->name] = $af;
        }
        return $fieldsets;
    }

    public function getFields($key)
    {
        return $this->form->getFieldset($key);
    }

    public function loadParams(\JRegistry $params)
    {
        foreach ($params->toArray() as $key => $value) {
            $this->form->setValue($key, 'params', $value);
        }
    }
}
