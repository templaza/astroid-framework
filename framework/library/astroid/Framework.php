<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;

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

    public static function init(): void
    {
        define('_ASTROID', 1); // define astroid
        self::check(); // check for astroid redirection

        self::$debugger = new Debugger(); // Debuuger
        self::$template = new Template(); // Template
        self::$document = new Document(); // Document

        self::constants();
        PluginHelper::importPlugin('astroid');
        Helper::triggerEvent('onAstroidAfterInitialise', [&self::$template]);
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
        define('ASTROID_MEDIA_URL', Uri::root() . 'media/astroid/assets/');
        define('ASTROID_LAYOUTS', JPATH_LIBRARIES . '/astroid/framework/layouts');
        define('ASTROID_ELEMENTS', JPATH_LIBRARIES . '/astroid/framework/elements');
        define('ASTROID_CACHE', JPATH_SITE . '/cache/astroid');

        $version = new Version;
        $version = $version->getShortVersion();
        $version = substr($version, 0, 1);
        define('ASTROID_JOOMLA_VERSION', $version);
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
        if (self::$document === null) {
            self::$document = new Document();
        }
        return self::$document;
    }

    public static function getTemplate($id = null): Template
    {
        if ($id !== null) {
            self::$template = new Template($id);
        } elseif (self::$template === null) {
            self::$template = new Template();
        }
        return self::$template;
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
        if (self::isAdmin() && Factory::getApplication()->getIdentity()->id) {
            $app = Factory::getApplication();
            $redirect = $app->input->get->get('ast', '', 'RAW');
            if (!empty($redirect)) {
                $app->redirect(base64_decode(urldecode($redirect)));
            }
        }
    }

    public static function getClientType()
    {
        $app = Factory::getApplication();
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

    public static function getForm(): Helper\AstroidForm
    {
        if (self::$form === null) {
            self::$form = new Helper\AstroidForm('template');
        }
        return self::$form;
    }
}
