<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Framework;
use Joomla\CMS\Filesystem\Folder;
use Joomla\Filesystem\File;

defined('_JEXEC') or die;

class Overrides
{
    public static $rename = [
        'com_content/form',
        'layouts/joomla/form',
        'layouts/joomla/content/icons/email.php',
        'layouts/joomla/content/icons/print_popup.php',
        'layouts/joomla/content/icons/print_screen.php'
    ];

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
            }
        }
    }
}
