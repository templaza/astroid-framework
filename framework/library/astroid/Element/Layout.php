<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Element;

use Astroid\Framework;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class Layout
{
    public static function render()
    {
        Framework::getDebugger()->log('Render Layout');
        $template = Framework::getTemplate();
        $layout = $template->getLayout();
        $devices = isset($layout['devices']) && $layout['devices'] ? $layout['devices'] : [
            [
                'code'=> 'lg',
                'icon'=> 'fa-solid fa-computer',
                'title'=> 'title'
            ]
        ];
        $content = '';
        foreach ($layout['sections'] as $section) {
            $section = new Section($section, $devices);
            $content .= $section->render();
        }
        Framework::getDebugger()->log('Render Layout');
        return $content;
    }

    public static function renderSublayout($source, $template = '', $type = 'layouts', $options = array())
    {
        Framework::getDebugger()->log('Render '.$source.' Layout');
        $sublayout  = self::getDataLayout($source, $template, $type);
        if (!isset($sublayout['data']) || !$sublayout['data']) {
            return '';
        }
        $layout     = \json_decode($sublayout['data'], true);
        $devices    = isset($layout['devices']) && $layout['devices'] ? $layout['devices'] : [
            [
                'code'=> 'lg',
                'icon'=> 'fa-solid fa-computer',
                'title'=> 'title'
            ]
        ];
        $options['layout_type'] = 'sublayout';
        $content = '';
        foreach ($layout['sections'] as $section) {
            $section = new Section($section, $devices, $options);
            $content .= $section->render();
        }
        Framework::getDebugger()->log('Render '.$source.' Layout');
        return $content;
    }

    public static function getDatalayouts($template = '', $type = '')
    {
        if (!$template) {
            $template   =   Framework::getTemplate()->template;
        }

        $layouts_path = JPATH_SITE . "/media/templates/site/{$template}/astroid/{$type}/";
        if (!file_exists($layouts_path)) {
            return [];
        }
        $files = array_filter(glob($layouts_path . '*.json'), 'is_file');
        $layouts    =   [];
        foreach ($files as $file) {
            $json = file_get_contents($file);
            $data = \json_decode($json, true);
            $layout = ['title' => pathinfo($file)['filename'], 'desc' => '', 'thumbnail' => '', 'name' => pathinfo($file)['filename']];
            if (isset($data['title']) && !empty($data['title'])) {
                $layout['title'] = Text::_($data['title']);
            }
            if (isset($data['desc'])) {
                $layout['desc'] = Text::_($data['desc']);
            }
            if (isset($data['thumbnail']) && !empty($data['thumbnail'])) {
                $layout['thumbnail'] = Uri::root() . 'media/templates/site/' . $template . '/images/' . $type . '/' . $data['thumbnail'];
            }
            $layouts[] = $layout;
        }
        return $layouts;
    }

    public static function getDataLayout($filename = '', $template = '', $type = '')
    {
        if (!$template) {
            $template   =   Framework::getTemplate()->template;
        }
        if (!$filename) {
            if ($type == 'article_layouts') {
                if (file_exists(JPATH_SITE . "/media/templates/site/{$template}/astroid/{$type}/default.json")) {
                    $layout_path = JPATH_SITE . "/media/templates/site/{$template}/astroid/{$type}/default.json";
                } else {
                    $layout_path = JPATH_SITE . '/media/astroid/assets/json/article_layouts/default.json';
                }
            } else {
                return [];
            }
        } else {
            $layout_path = JPATH_SITE . "/media/templates/site/{$template}/astroid/{$type}/" . $filename . '.json';
        }

        if (!file_exists($layout_path)) {
            return [];
        }
        $json = file_get_contents($layout_path);
        $data = \json_decode($json, true);

        return $data;
    }

    public static function deleteDatalayouts($layouts = [], $template = '', $type = '')
    {
        if (!count($layouts)) {
            return false;
        }
        if (!$template) {
            $template   =   Framework::getTemplate()->template;
        }

        $layouts_path   = JPATH_SITE . "/media/templates/site/{$template}/astroid/{$type}/";
        $images_path    = JPATH_SITE . "/media/templates/site/{$template}/images/{$type}/";
        foreach ($layouts as $layout) {
            if (file_exists($layouts_path . $layout . '.json')) {
                $json = file_get_contents($layouts_path . $layout . '.json');
                $data = \json_decode($json, true);
                unlink($layouts_path . $layout . '.json');
                if (file_exists($images_path . $data['thumbnail'])) {
                    unlink($images_path . $data['thumbnail']);
                }
            }
        }
        return true;
    }
}
