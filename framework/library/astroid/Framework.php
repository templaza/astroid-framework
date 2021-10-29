<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;

defined('_JEXEC') or die;

abstract class Framework
{
    protected static $document = null;
    protected static $template = null;
    protected static $debugger = null;
    protected static $auditor = null;
    protected static $form = null;
    protected static $reporters = [];
    protected static $client = null;
    public static $isAstroid = false;
    public static $version = null;

    public static function init()
    {
        define('_ASTROID', 1); // define astroid
        self::check(); // check for astroid redirection

        self::$debugger = new Debugger(); // Debuuger
        self::$template = new Template(); // Template
        self::$document = new Document(); // Document

        self::constants();
        self::audit();
    }

    public static function getVersion()
    {
        if (self::$version === null) {
            self::$version = Helper::frameworkVersion();
        }
        return self::$version;
    }

    public static function constants()
    {
        define('ASTROID_MEDIA', JPATH_SITE . '/media/astroid/assets');
        define('ASTROID_MEDIA_URL', \JURI::root() . 'media/astroid/assets/');
        define('ASTROID_LAYOUTS', JPATH_LIBRARIES . '/astroid/framework/layouts');
        define('ASTROID_ELEMENTS', JPATH_LIBRARIES . '/astroid/framework/elements');
        define('ASTROID_CACHE', JPATH_SITE . '/cache/astroid');

        $version = new \JVersion;
        $version = $version->getShortVersion();
        $version = substr($version, 0, 1);
        define('ASTROID_JOOMLA_VERSION', $version);

        $template = Framework::getTemplate();
        define('ASTROID_TEMPLATE_PATH', JPATH_SITE . '/templates/' . $template->template);
    }

    public static function addReporter(Reporter $reporter)
    {
        self::$reporters[$reporter->id] = $reporter;
    }

    public static function getReporter($name)
    {
        if (isset(self::$reporters[Helper::slugify($name) . '-reporter'])) {
            return self::$reporters[Helper::slugify($name) . '-reporter'];
        }
        return new Reporter($name);
    }

    public static function getReporters()
    {
        return self::$reporters;
    }

    public static function getDocument(): Document
    {
        return self::$document;
    }

    public static function getTemplate($id = null): Template
    {
        if ($id !== null) {
            self::$template = new Template($id);
        }
        return self::$template;
    }

    public static function getAuditor($id = null): Auditor
    {
        if ($id !== null) {
            self::$auditor = new Auditor($id);
        }
        return self::$auditor;
    }

    public static function getClient(): Helper\Client
    {
        if (self::$client === null) {
            self::$client = self::getClientType() == 'site' ? new Site() : new Admin();
        }
        return self::$client;
    }

    public static function getDebugger(): Debugger
    {
        if (self::$debugger === null) {
            self::$debugger = new Debugger();
        }
        return self::$debugger;
    }

    public static function check()
    {
        if (self::isAdmin() && \JFactory::getUser()->id) {
            $app = \JFactory::getApplication();
            $redirect = $app->input->get->get('ast', '', 'RAW');
            if (!empty($redirect)) {
                $app->redirect(base64_decode(urldecode($redirect)));
            }
        }
    }

    public static function audit()
    {
    }

    public static function getClientType()
    {
        $app = \JFactory::getApplication();
        return $app->isClient('administrator') ? 'administrator' : 'site';
    }

    public static function isAdmin()
    {
        return self::getClientType() == 'administrator';
    }

    public static function isSite()
    {
        return self::getClientType() == 'site';
    }

    public static function getForm(): Helper\Form
    {
        if (self::$form === null) {
            self::$form = new Helper\Form('template');
        }
        return self::$form;
    }
}
