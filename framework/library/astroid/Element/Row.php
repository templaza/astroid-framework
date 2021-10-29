<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Element;

use Astroid\Framework;

defined('_JEXEC') or die;

class Row extends BaseElement
{
    public $section;
    public function __construct($data, $section)
    {
        $this->section = $section;
        parent::__construct($data);
    }

    public function render()
    {
        $columns = $this->_data['cols'];
        $bufferSize = 0;
        $componentIndex = 0;
        $prevColIndex = null;

        foreach ($this->_data['cols'] as $colIndex => $col) {
            $column = new Column($col, $this->section, $this);
            $columns[$colIndex] = $column;
            $column->render();
            if ($column->component) {
                $componentIndex = $colIndex;
            }
        }

        foreach ($columns as $colIndex => $column) {
            if (empty($column->content)) {
                $bufferSize += $column->size;
                unset($columns[$colIndex]);
            } else {
                if ($this->section->hasComponent) {
                    $columns[$componentIndex]->size = $columns[$componentIndex]->size + $bufferSize;
                    $bufferSize = 0;
                } else {
                    if (isset($columns[$prevColIndex])) {
                        $columns[$prevColIndex]->size = $columns[$prevColIndex]->size + $bufferSize;
                    } else {
                        $columns[$colIndex]->size = $columns[$colIndex]->size + $bufferSize;
                    }
                    $bufferSize = 0;
                }
                $prevColIndex = $colIndex;
            }
        }

        if (!empty($columns)) {
            if ($bufferSize) {
                if ($this->section->hasComponent) {
                    $columns[$componentIndex]->size = $columns[$componentIndex]->size + $bufferSize;
                } else if ($prevColIndex !== null) {
                    $columns[$prevColIndex]->size = $columns[$prevColIndex]->size + $bufferSize;
                }
            }
            foreach ($columns as $column) {
                $this->content .= $column->wrap();
            }
        }
        return $this->wrap();
    }

    protected function _getclasses()
    {
        $this->addClass('row');

        $layout_type = (Framework::getDocument()->isBuilder() && $this->section->hasComponent) ? 'no-container' : $this->section->params->get('layout_type', '');

        if (in_array($layout_type, ['no-container', 'custom-container', 'container-with-no-gutters', 'container-fluid-with-no-gutters'])) {
            $this->addClass('no-gutters');
        }

        parent::_getclasses();
    }
}
