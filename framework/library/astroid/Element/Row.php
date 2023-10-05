<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
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
        $data['fill'] = $data['fill'] ?? true;
        parent::__construct($data, $section->devices);
    }

    public function render()
    {
        $columns = $this->_data['cols'];
        $bufferSize = [
            'xxl' => 0,
            'xl' => 0,
            'lg' => 0,
            'md' => 0,
            'sm' => 0,
            'xs' => 0,
        ];
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

        if (isset($this->_data['fill']) && $this->_data['fill']) {
            foreach ($columns as $colIndex => $column) {
                if (empty($column->content)) {
                    foreach ($column->size as $key => $size) {
                        $bufferSize[$key] += $column->size[$key];
                    }
                    unset($columns[$colIndex]);
                } else {
                    if ($this->section->hasComponent) {
                        foreach ($columns[$componentIndex]->size as $key => $size) {
                            $columns[$componentIndex]->size[$key] += $bufferSize[$key];
                            if ($columns[$componentIndex]->size[$key] > 12) $columns[$componentIndex]->size[$key] = 12;
                        }
                        $bufferSize = [
                            'xxl' => 0,
                            'xl' => 0,
                            'lg' => 0,
                            'md' => 0,
                            'sm' => 0,
                            'xs' => 0,
                        ];
                    } else {
                        if (isset($columns[$prevColIndex])) {
                            foreach ($columns[$prevColIndex]->size as $key => $size) {
                                $columns[$prevColIndex]->size[$key] += $bufferSize[$key];
                                if ($columns[$prevColIndex]->size[$key] > 12) $columns[$prevColIndex]->size[$key] = 12;
                            }
                        } else {
                            foreach ($columns[$colIndex]->size as $key => $size) {
                                $columns[$colIndex]->size[$key] += $bufferSize[$key];
                                if ($columns[$colIndex]->size[$key] > 12) $columns[$colIndex]->size[$key] = 12;
                            }
                        }
                        $bufferSize = [
                            'xxl' => 0,
                            'xl' => 0,
                            'lg' => 0,
                            'md' => 0,
                            'sm' => 0,
                            'xs' => 0,
                        ];
                    }
                    $prevColIndex = $colIndex;
                }
            }
        }

        if (!empty($columns)) {
            if (isset($this->_data['fill']) && $this->_data['fill']) {
                if ($this->section->hasComponent) {
                    foreach ($columns[$componentIndex]->size as $key => $size) {
                        if ($bufferSize[$key]) {
                            $columns[$componentIndex]->size[$key] += $bufferSize[$key];
                            if ($columns[$componentIndex]->size[$key] > 12) $columns[$componentIndex]->size[$key] = 12;
                        }
                    }
                } else if ($prevColIndex !== null) {
                    foreach ($columns[$prevColIndex]->size as $key => $size) {
                        if ($bufferSize[$key]) {
                            $columns[$prevColIndex]->size[$key] += $bufferSize[$key];
                            if ($columns[$prevColIndex]->size[$key]>12) $columns[$prevColIndex]->size[$key] = 12;
                        }
                    }
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
            $this->addClass('no-gutters gx-0');
        } else {
            $sizes = ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'];
            foreach ($sizes as $size) {
                $gutter = $this->params->get('gutter_'.$size, '');
                if ($gutter !== '') {
                    if ($size == 'xs') {
                        $this->addClass('gx-' . $gutter);
                    } else {
                        $this->addClass('gx-' . $size . '-' . $gutter);
                    }
                }
            }
        }
        parent::_getclasses();
    }
}
