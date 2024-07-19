<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Element;

use Astroid\Framework;
use Astroid\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;
use Joomla\Filesystem\Path;

defined('_JEXEC') or die;

class Element extends BaseElement
{
    public $section, $row, $column;
    public function __construct($data, $section, $row, $column)
    {
        $this->section = $section;
        $this->row = $row;
        $this->column = $column;
        parent::__construct($data, $section->devices, $section->options);
    }

    public function render()
    {
        $this->_decorateSection();
        if ($this->type == 'sublayout') {
            $this->content = Layout::renderSublayout($this->params->get('source', ''));
        } else {
            $this->content = $this->_content();
        }
        return $this->wrap();
    }

    public function _content()
    {
        $app            = Factory::getApplication();
        $option         = $app->input->get('option', '', 'RAW');
        $view           = $app->input->get('view', '', 'RAW');
        $id             = $app->input->get('id', null, 'RAW');
        if ($option === 'com_content' && $view === 'article' && !empty($id)) {
            $template_name = Framework::getTemplate()->template;
            $layout_path = Path::clean(JPATH_SITE . "/media/templates/site/$template_name/astroid/article_widget_data/". $id . '_' . $this->unqid . '.json');
            if (file_exists($layout_path)) {
                $article_json = file_get_contents($layout_path);
                $article_data = json_decode($article_json, true);
                $article_params = Helper::loadParams($article_data['params']);
                $this->params->merge($article_params);
            }
        }
        $layout = Framework::getTemplate()->getElementLayout($this->type);
        $pathinfo = pathinfo($layout);
        $layout = new FileLayout($pathinfo['filename'], $pathinfo['dirname']);
        return $layout->render(['params' => $this->params, 'element' => $this, 'options' => $this->options]);
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
