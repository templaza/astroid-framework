<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;

defined('_JEXEC') or die;

class Auditor
{
    protected static $_instructions = [];

    public static function audit($template)
    {
        return self::checking($template);
    }

    public static function migrate($template)
    {
        $checked = self::checking($template);
        $mergable = $checked['mergable'];
        foreach ($mergable as $file) {
            $path = JPATH_SITE . '/templates/' . $template . '/' . $file;
            if (file_exists($path)) {
                $pathinfo = pathinfo($path);
                $name = pathinfo($path, PATHINFO_FILENAME);
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $dir = pathinfo($path, PATHINFO_DIRNAME);
                rename($path, $dir . '/' . $name . '.' . $extension . '.archived');
            }
        }
        return 'Template successfully migrated.';
    }

    protected static function checking($template)
    {
        $report = [
            'mergable' => [],
            'unmergable' => [],
            'notfound' => [],
        ];
        $hashes = \json_decode(file_get_contents('http://plugins.local/checksum/astroid-hash.json'), true);
        $templateHashes = isset($hashes[$template]) ? $hashes[$template] : $hashes['astroid_template_zero'];
        foreach ($templateHashes as $filePath => $fileHashes) {
            $file = JPATH_SITE . '/templates/' . $template . '/' . $filePath;
            if (file_exists($file)) {
                $hash = self::getFileHash($file);
                if (in_array($hash, $fileHashes)) {
                    $report['mergable'][] = $filePath;
                } else {
                    $report['unmergable'][] = $filePath;
                }
            } else {
                $report['notfound'][] = $filePath;
            }
        }
        return $report;
    }

    protected static function getFileHash($file)
    {
        $content = file_get_contents($file);
        $content = str_replace(array("\n", "\r"), "", $content);
        return md5($content);
    }

    public function instruct($html = null)
    {
        $this->_instructions[] = $html;
    }
}
