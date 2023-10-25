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

class Column extends BaseElement
{
    public $section, $row, $size = 12, $component = false;
    public function __construct($data, $section, $row)
    {
        $this->section = $section;
        $this->row = $row;
        if (is_int($data['size']) || is_string($data['size'])) {
            $tmp = intval($data['size']);
            $this->size = [
                'xxl' => $tmp,
                'xl' => $tmp,
                'lg' => $tmp,
                'md' => 12,
                'sm' => 12,
                'xs' => 12,
            ];
        } else {
            $this->size = $data['size'];
        }
        parent::__construct($data, $section->devices);
    }

    public function render()
    {
        foreach ($this->_data['elements'] as $element) {
            $element = new Element($element, $this->section, $this->row, $this);
            $element_content = $element->render();
            if (!empty($element->content)) {
                $this->content .= $element_content;
            }
        }
        return $this->wrap();
    }

    protected function _getclasses()
    {
        foreach ($this->devices as $device) {
            $size = $device['code'];
            if ($size != 'xs') {
                if (isset($this->size[$size]) && $this->size[$size]) {
                    $this->addClass('col-' . $size . '-' . $this->size[$size]);
                }
            } else {
                if (isset($this->size[$size]) && $this->size[$size]) {
                    $this->addClass('col-' . $this->size[$size]);
                }
            }
            if ($this->params->get('hideon'.$size, 0)) {
                $this->addClass('hideon' . $size);
            }
        }

        //Column Order
        $column_order_xl     =   intval($this->params->get('column_order_xl', 0));
        $column_order_lg     =   intval($this->params->get('column_order_lg', 0));
        $column_order_md     =   intval($this->params->get('column_order_md', 0));
        $column_order_sm     =   intval($this->params->get('column_order_sm', 0));
        $column_order_xs     =   intval($this->params->get('column_order_xs', 0));
        if ($column_order_xl || $column_order_lg || $column_order_md || $column_order_sm || $column_order_xs) {
            $this->addClass('order-xl-'.$column_order_xl);
            $this->addClass('order-lg-'.$column_order_lg);
            $this->addClass('order-md-'.$column_order_md);
            $this->addClass('order-sm-'.$column_order_sm);
            $this->addClass('order-'.$column_order_xs);
        }
        parent::_getclasses();
    }
}
