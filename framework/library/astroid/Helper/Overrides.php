<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;
use Astroid\Helper;
use Joomla\Filesystem\Folder;

defined('_JEXEC') or die;

class Overrides
{
    public static $rename = [];

    private static function generateExtensionPath(string $path) : string {
        if (empty($path)) {
            return '';
        }

        $path = explode('/', trim($path, '/'));

        $version = JVERSION;
        $extension = $path[0];

        if (\strpos($extension, 'com_') === 0) {
            if ($version < 4) {
                \array_splice($path, 1, 0, ['views']);
                \array_splice($path, 3, 0, ['tmpl']);
            }
            else {
                \array_splice($path, 1, 0, ['tmpl']);
            }
            return JPATH_ROOT . '/components/' . \implode('/', $path);
        }

        elseif (\strpos($extension, 'mod_') === 0) {
            \array_splice($path, 1, 0, ['tmpl']);
            return JPATH_ROOT . '/modules/' . \implode('/', $path);
        }

        elseif (\strpos($extension, 'plg_') === 0) {
            $pluginPath = \explode('_', $extension);
            \array_splice($pluginPath, 0, 1);
            \array_push($pluginPath, 'tmpl');

            \array_splice($path, 0, 1, $pluginPath);
            return JPATH_ROOT . '/plugins/' . \implode('/', $path);
        }
        elseif ($extension === 'layouts') {
            return JPATH_ROOT . '/' . \implode('/', $path);
        }

        return \implode('/', $path);
    }

    public static function getHTMLTemplate(): string
    {
        $backtrace = \debug_backtrace();
        $callPath = $backtrace[0]['file'] ?? '';
        preg_match('/'. addcslashes(JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . '.*?' . DIRECTORY_SEPARATOR . 'html', DIRECTORY_SEPARATOR) . '/' , $callPath, $match);

        if ($match) {
            $htmlTemplatePath = $match[0];
        } else {
            $htmlTemplatePath = JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . ASTROID_TEMPLATE_NAME . DIRECTORY_SEPARATOR .'html';
        }

        $htmlAstroidPath = JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'astroid' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'html';

        if (\strpos($callPath, $htmlTemplatePath) === 0) {
            $relativePath = \substr($callPath, \strlen($htmlTemplatePath));
        }

        if (empty($relativePath)) {
            // Check if template is child-template and file is not exist then select html from parent
            $isChildTemplate    = Helper::isChildTemplate(ASTROID_TEMPLATE_NAME);
            if ($isChildTemplate && isset($isChildTemplate['isChild']) && $isChildTemplate['isChild']) {
                $htmlTemplatePath = JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $isChildTemplate['parent'] . DIRECTORY_SEPARATOR .'html';
            }
            if (\strpos($callPath, $htmlTemplatePath) === 0) {
                $relativePath = \substr($callPath, \strlen($htmlTemplatePath));
            }
            if (empty($relativePath)) {
                return self::generateExtensionPath(\substr($callPath, stripos($callPath, DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR) + 5));
            }
        }

        $astroidOverridePath = $htmlAstroidPath . $relativePath;
        if (\file_exists($astroidOverridePath)) {
            return $astroidOverridePath;
        }

        return self::generateExtensionPath($relativePath);
    }

    public static function getHTMLSystem($filename = ''): string
    {
        $layoutAstroidSystem =   JPATH_LIBRARIES.DIRECTORY_SEPARATOR
            .'astroid'.DIRECTORY_SEPARATOR
            .'framework'.DIRECTORY_SEPARATOR
            .'layouts'.DIRECTORY_SEPARATOR
            .'system'.DIRECTORY_SEPARATOR;
        $layoutSystemFolder =   JPATH_THEMES.DIRECTORY_SEPARATOR
            .'system'.DIRECTORY_SEPARATOR;

        if (empty($filename)) {
            $backtrace = \debug_backtrace();
            $callPath = $backtrace[0]['file'] ?? '';
            $basename = basename($callPath);
            if (\file_exists($layoutAstroidSystem . $basename)) {
                return $layoutAstroidSystem . $basename;
            } else {
                return $layoutSystemFolder . $basename;
            }
        } else {
            if (\file_exists($layoutAstroidSystem . $filename)) {
                return $layoutAstroidSystem . $filename;
            } else {
                return $layoutSystemFolder . $filename;
            }
        }
    }

    public static function fix()
    {
        self::rename();
    }

    public static function rename()
    {
        $templates = Template::getAstroidTemplates(true);
        $templates = array_unique(array_column($templates, 'template'));

        foreach ($templates as $template) {
            $path = JPATH_ROOT . '/templates/' . $template . '/html/';
            $path_template  =   JPATH_ROOT . '/templates/' . $template;
            $path_template_media    =   JPATH_ROOT . '/media/templates/site/' . $template;

            //Since Version 2.6.0
            if (file_exists($path_template . '/astroid')) {
                Folder::move($path_template . '/astroid', $path_template_media . '/astroid');
            }
            if (file_exists($path_template . '/params')) {
                if (file_exists($path_template_media . '/params')) {
                    Folder::delete($path_template_media . '/params');
                }
                Folder::move($path_template . '/params', $path_template_media . '/params');
            }
            if (file_exists($path_template . '/fonts')) {
                Folder::move($path_template . '/fonts', $path_template_media . '/fonts');
            }
        }
    }
}
