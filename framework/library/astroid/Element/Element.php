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

class Element extends BaseElement
{
    public $section, $row, $column;
    public function __construct($data, $section, $row, $column)
    {
        $this->section = $section;
        $this->row = $row;
        $this->column = $column;
        parent::__construct($data);
    }

    public function render()
    {
        $this->_decorateSection();
        $this->content = $this->_content();
        return $this->wrap();
    }

    public function _content()
    {
        $layout = Framework::getTemplate()->getElementLayout($this->type);
        $pathinfo = pathinfo($layout);
        $layout = new \JLayoutFile($pathinfo['filename'], $pathinfo['dirname']);
        return $layout->render(['params' => $this->params, 'element' => $this]);
    }

    public function _decorateSection()
    {
        $params = Framework::getTemplate()->getParams();
        if ($this->type == "module_position") {
            if ($params->get('header_module_position', '') === $this->params->get('position', '')) {
                $this->section->hasHeader = true;
                $this->section->addClass('astroid-header-section');
            }
            if ($params->get('footer_module_position', '') === $this->params->get('position', '')) {
                $this->section->hasFooter = true;
                $this->section->addClass('astroid-footer-section');
            }
        }

        if ($this->type == "component") {
            $this->section->hasComponent = true;
            $this->column->component = true;
            $this->section->addClass('astroid-component-section');
        }
    }
}
