<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Framework;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;

defined('_JEXEC') or die;

class Overrides
{
    public static $rename = [];

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
