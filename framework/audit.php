<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;
jimport('astroid.framework.astroid');

class AstroidFrameworkAudit
{
    public static function check()
    {
        try {
            $config = AstroidFramework::getConfig();
            $auditVersion = $config->get('auditVersion', null);
            $frameworkVersion = self::getAstroidVersion();
            return ($auditVersion !== $frameworkVersion);;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getAstroidVersion()
    {
        $astroidManifest = JPATH_ADMINISTRATOR . '/manifests/libraries/astroid.xml';
        if (!file_exists($astroidManifest)) {
            return false;
        }
        $xml = Astroid\Helper::getXML($astroidManifest);
        $version = (string) $xml->version;
        return $version;
    }

    public static function logAuditFile($version)
    {
        AstroidFramework::setConfig('auditVersion', $version);
        AstroidFramework::setConfig('auditTime', date('Y-m-d H:i:s'));
    }

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

    public static function auditFix()
    {
        $astroidVersion = self::getAstroidVersion();
        return "Astroid audit done for `{$astroidVersion}`";
    }

    public static function checking($template)
    {
        $report = [
            'mergable' => [],
            'unmergable' => [],
            'notfound' => [],
        ];
        $hashes = \json_decode(file_get_contents(JPATH_SITE . '/media/astroid/assets/json/hash.json'), true);
        foreach ($hashes as $item) {
            $file = JPATH_SITE . '/templates/' . $template . '/' . $item['file'];
            if (file_exists($file)) {
                $hash = self::getFileHash($file);
                if (in_array($hash, $item['hash'])) {
                    $report['mergable'][] = $item['file'];
                } else {
                    $report['unmergable'][] = $item['file'];
                }
            } else {
                $report['notfound'][] = $item['file'];
            }
        }
        return $report;
    }

    public static function getFileHash($file)
    {
        $content = file_get_contents($file);
        $content = str_replace(array("\n", "\r"), "", $content);
        return md5($content);
    }
}
