<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

defined('_JEXEC') or die;

// #1 `classname` and it's function `funcname` is deprecated and no longer suppoerted. use `funcname` instead of. see #link
// #2 `classname` is deprecated and no longer suppoerted. use `classname` instead of. see #link

class Migration
{
    public static $funcmap = [
        // AstroidFramework
        'AstroidFramework_getTemplate' => [1, 'Astroid\Framework::getTemplate()'],
        'AstroidFramework_addStyleSheet' => [1, 'Astroid\Framework::getDocument()->addStyleSheet($url, $attribs)'],
        'AstroidFramework_addStyleDeclaration' => [1, 'Astroid\Framework::getDocument()->addStyleDeclaration($css)'],
        'AstroidFramework_addScript' => [1, 'Astroid\Framework::getDocument()->addScript($js, $position)'],
        'AstroidFramework_addScriptDeclaration' => [1, 'Astroid\Framework::getDocument()->addScriptDeclaration($js, $position)'],

        // AstroidFrameworkConstants
        'AstroidFrameworkConstants' => [2, 'Astroid\Helper\Constants'],

        // AstroidFrameworkTemplate
        'AstroidFrameworkTemplate_params' => [3, 'Astroid\Framework::getTemplate()->getParams()'],
        'AstroidFrameworkTemplate_loadLayout' => [1, 'Astroid\Framework::getDocument()->include()'],
        '$template = new AstroidFrameworkTemplate(JFactory::getApplication()->getTemplate(true));' => ''
    ];

    public static function check($classname = '', $method = '', $delimeter = '__')
    {
        if (!isset(self::$funcmap[$classname . $delimeter . $method])) {
            return self::_default($classname, $method);
        }
        return self::resolve(self::$funcmap[$classname . $delimeter . $method], $classname, $method);
    }

    public static function checkStatic($classname = '', $method = '', $delimeter = '__')
    {
        return self::check($classname, $method, '_');
    }

    public static function _default($classname, $method)
    {
        return self::resolve('', $classname, $method);
    }

    public static function resolve($map, $classname, $method)
    {
        switch ($map[0]) {
            case 1:
                return sprintf('<code>%s</code> and it\'s function <code>%s</code> is deprecated and no longer suppoerted. use <code>%s</code> instead of. see #link', $classname, $method, $map[1]);
                break;
            case 2:
                return sprintf('<code>%s</code> is deprecated and no longer suppoerted. use <code>%s</code> instead of. see #link', $classname, $map[1]);
                break;
            case 3:
                return sprintf('Variable <code>%s</code> and class <code>%s</code> is deprecated and no longer suppoerted. use <code>%s</code> instead of. see #link', $method, $classname, $map[1]);
                break;
            default:
                return sprintf('<code>%s</code> is deprecated and no longer suppoerted. Please check astroid documentation to fix the issue.', $classname);
                break;
        }
    }
}
