<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Joomla\Registry\Registry;
use Joomla\CMS\Form\Form;

defined('_JEXEC') or die;

class AstroidForm
{
    protected $form;
    public function __construct($name)
    {
        $this->form = new Form($name);
    }

    public function loadOptions($dir = '')
    {
        $forms = array_filter(glob($dir . '/' . '*.xml'), 'is_file');
        Form::addFormPath($dir);
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

        usort($astroidfieldsets, self::class.'::_ording');
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

    public function loadParams(Registry $params)
    {
        foreach ($params->toArray() as $key => $value) {
            $this->form->setValue($key, 'params', $value);
        }
    }

    public function getForm()
    {
        return $this->form;
    }
}
