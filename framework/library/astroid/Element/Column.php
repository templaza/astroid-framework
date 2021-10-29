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

class Column extends BaseElement
{
    public $section, $row, $size = 12, $component = false;
    public function __construct($data, $section, $row)
    {
        $this->section = $section;
        $this->row = $row;
        $this->size = $data['size'];
        parent::__construct($data);
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
        $responsive = $this->params->get('responsive', '');
        if (!empty($responsive)) {
            $responsive = \json_decode($responsive, true);
        } else {
            $responsive = [];
        }

        $responsive_utilities = [];
        foreach ($responsive as $responsive_utility) {
            if (array_key_exists('name', $responsive_utility)) {
                $responsive_utilities[$responsive_utility['name']] = $responsive_utility['value'];
            }
        }

        $sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
        foreach ($sizes as $size) {
            if ($size == 'lg') {
                $this->addClass('col-' . $size . '-' . $this->size);
                if (isset($responsive_utilities['hide_' . $size]) && $responsive_utilities['hide_' . $size] != 1) {
                    $this->addClass('hideon' . $size);
                }
            } else {
                if (isset($responsive_utilities['size_' . $size]) && $responsive_utilities['size_' . $size] != 'inherit') {
                    $this->addClass($size == 'xs' ? 'col-' . $responsive_utilities['size_' . $size] : 'col-' . $size . '-' . $responsive_utilities['size_' . $size]);
                }
                if (isset($responsive_utilities['hide_' . $size]) && $responsive_utilities['hide_' . $size] != 1) {
                    $this->addClass('hideon' . $size);
                }
            }
        }

        parent::_getclasses();
    }
}
