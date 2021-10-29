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

class Section extends BaseElement
{
    protected $_tag = 'section';
    public $hasComponent = false;
    public $hasHeader = false;
    public $hasFooter = false;

    public function render()
    {
        $content = '';
        foreach ($this->_data['rows'] as $row) {
            $row = new Row($row, $this);
            $content .= $row->render();
        }
        $container = $this->_container();
        if (!empty($content) && !empty($container)) {
            $this->content .= '<div class="' . $container . '">';
        }
        $this->content .= $content;
        if (!empty($content) && !empty($container)) {
            $this->content .= '</div>';
        }
        return $this->wrap();
    }

    protected function _container()
    {
        if (Framework::getDocument()->isBuilder() && $this->hasComponent) {
            return '';
        }
        $container = $this->params->get('layout_type', '');
        $custom_class = $this->params->get('custom_container_class', '');
        switch ($container) {
            case '':
                $container = 'container';
                break;
            case 'no-container':
                $container = '';
                break;
            case 'container-with-no-gutters':
                $container = 'container';
                break;
            case 'container-fluid-with-no-gutters':
                $container = 'container-fluid';
                break;
        }
        if (!empty($container) && !empty($custom_class)) {
            $container .= ' ' . $custom_class;
        }
        return $container;
    }
}
