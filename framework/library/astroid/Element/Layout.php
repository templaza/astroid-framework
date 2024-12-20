<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Element;

use Astroid\Framework;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Path;
use Joomla\Filesystem\File;

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
        $options['layout_type'] = $type;
        $options['source'] = $source;
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
            $template = Framework::getTemplate()->template;
        }

        $layouts = array_merge(
            self::readLayoutsFromPath(JPATH_SITE . "/media/templates/site/{$template}/astroid/{$type}/", $template, $type),
            self::readLayoutsFromPath(JPATH_SITE . "/media/templates/site/{$template}/params/{$type}/", $template, $type)
        );

        return self::mergeLayouts($layouts);
    }

    public static function readLayoutsFromPath($path, $template, $type)
    {
        if (!file_exists($path)) {
            return [];
        }

        $files = array_filter(glob($path . '*.json'), 'is_file');
        return array_map(function ($file) use ($template, $type) {
            $json = file_get_contents($file);
            $data = \json_decode($json, true);
            return [
                'title' => Text::_($data['title'] ?? pathinfo($file, PATHINFO_FILENAME)),
                'desc' => Text::_($data['desc'] ?? ''),
                'thumbnail' => !empty($data['thumbnail']) ? Uri::root() . "media/templates/site/{$template}/images/{$type}/" . $data['thumbnail'] : '',
                'name' => pathinfo($file, PATHINFO_FILENAME)
            ];
        }, $files);
    }

    private static function mergeLayouts($layouts)
    {
        $merged = [];
        foreach ($layouts as $layout) {
            $key = $layout['name'];
            if (isset($merged[$key])) {
                $merged[$key] = $layout;
            } else {
                $merged[$key] = $layout;
            }
        }
        return array_values($merged);
    }

    public static function getDataLayout($filename = '', $template = '', $type = '')
    {
        if (!$template) {
            $template   =   Framework::getTemplate()->template;
        }
        if (!$filename) {
            if ($type == 'article_layouts') {
                if (file_exists(Path::clean(JPATH_SITE . "/media/templates/site/{$template}/params/{$type}/default.json"))) {
                    $layout_path = Path::clean(JPATH_SITE . "/media/templates/site/{$template}/params/{$type}/default.json");
                } elseif (file_exists(Path::clean(JPATH_SITE . "/media/templates/site/{$template}/astroid/{$type}/default.json"))) {
                    $layout_path = Path::clean(JPATH_SITE . "/media/templates/site/{$template}/astroid/{$type}/default.json");
                } else {
                    $layout_path = Path::clean(JPATH_SITE . '/media/astroid/assets/json/article_layouts/default.json');
                }
            } else {
                return [];
            }
        } else {
            if (file_exists(Path::clean(JPATH_SITE . "/media/templates/site/{$template}/params/{$type}/" . $filename . '.json'))){
                $layout_path = Path::clean(JPATH_SITE . "/media/templates/site/{$template}/params/{$type}/" . $filename . '.json');
            } elseif (file_exists(Path::clean(JPATH_SITE . "/media/templates/site/{$template}/astroid/{$type}/" . $filename . '.json'))){
                $layout_path = Path::clean(JPATH_SITE . "/media/templates/site/{$template}/astroid/{$type}/" . $filename . '.json');
            } else {
                return [];
            }
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
        if (empty($layouts)) {
            return false;
        }
        if (!$template) {
            $template = Framework::getTemplate()->template;
        }

        $layouts_params = Path::clean(JPATH_SITE . "/media/templates/site/{$template}/params/{$type}/");
        $layouts_path = Path::clean(JPATH_SITE . "/media/templates/site/{$template}/astroid/{$type}/");
        $images_path = Path::clean(JPATH_SITE . "/media/templates/site/{$template}/images/{$type}/");

        $deleteFile = function ($path, $layout) use ($images_path) {
            if (file_exists($path . $layout . '.json')) {
                $json = file_get_contents($path . $layout . '.json');
                $data = \json_decode($json, true);
                File::delete($path . $layout . '.json');
                if (!empty($data['thumbnail']) && file_exists($images_path . $data['thumbnail'])) {
                    File::delete($images_path . $data['thumbnail']);
                }
            }
        };

        array_map(function ($layout) use ($layouts_params, $layouts_path, $deleteFile) {
            $deleteFile($layouts_params, $layout);
            $deleteFile($layouts_path, $layout);
        }, $layouts);

        return true;
    }

    public static function loadModuleLayout($id)
    {
        $layout_path = Path::clean(JPATH_SITE . '/media/mod_astroid_layout/params/' . $id . '.json');
        if (empty($id) || !file_exists($layout_path)) {
            return ['sections' => []];
        }
        $json = file_get_contents($layout_path);
        $data = \json_decode($json, true);

        return $data;
    }
}
