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
            foreach (self::$rename as $file) {
                if (is_dir($path . $file)) {
                    Folder::move($path . $file, $path . (str_replace(basename($file), basename($file) . '-' . date('Y-m-d'), $file)));
                } else if (file_exists($path . $file)) {
                    File::move($path . $file, $path . (str_replace(basename($file), basename($file, '.php') . '-' . date('Y-m-d') . '.php', $file)));
                }
            }

            if (ASTROID_JOOMLA_VERSION == 4) {
                if (is_dir($path . 'com_config')) {
                    Folder::move($path . 'com_config', $path . (str_replace(basename('com_config'), basename('com_config') . '-' . date('Y-m-d'), 'com_config')));
                }
                if (is_dir($path . 'layouts/joomla/editors')) {
                    Folder::delete($path . 'layouts/joomla/editors');
                }
                //Fix module issue from Joomla 4.2
                if (file_exists(JPATH_LIBRARIES.'/astroid/framework/layouts/modules/mod_login/default.php') && file_exists($path.'mod_login/default.php')) {
                    File::copy(JPATH_LIBRARIES.'/astroid/framework/layouts/modules/mod_login/default.php', $path.'mod_login/default.php');
                }
            }

            //Fix alert issue.
            if (file_exists(JPATH_LIBRARIES.'/astroid/framework/layouts/system/message.php') && file_exists($path.'layouts/joomla/system/message.php')) {
                File::copy(JPATH_LIBRARIES.'/astroid/framework/layouts/system/message.php', $path.'layouts/joomla/system/message.php');
            }

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
