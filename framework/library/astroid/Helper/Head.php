<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Framework;
use Astroid\Helper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class Head
{
    public static function meta()
    {
        $document = Framework::getDocument();

        $document->addMeta('', 'IE=edge', ['http-equiv' => 'X-UA-Compatible']);
        $document->addMeta('viewport', 'width=device-width, initial-scale=1');
        $document->addMeta('HandheldFriendly', 'true');
        $document->addMeta('apple-mobile-web-app-capable', 'YES');
    }

    public static function favicon()
    {
        $params = Framework::getTemplate()->getParams();
        $document = Factory::getApplication()->getDocument();
        $wa = $document->getWebAssetManager();
        $favicon = $params->get('favicon', '');

        if (!empty($favicon) && file_exists(JPATH_ROOT.'/'. Media::getPath() . '/' . $favicon)) {
            $image_type =   getimagesize(JPATH_ROOT.'/'. Media::getPath() . '/' . $favicon);
            $wa->registerAndUseStyle('astroid.favicon', Media::getPath() . '/' . $favicon, ['version' => 'auto'], ['rel' => 'shortcut icon', 'type' => $image_type['mime'], 'sizes' => 'any']);
        }
        $apple_touch_icon = $params->get('apple_touch_icon', '');
        if (!empty($apple_touch_icon) && ($apple_touch_icon != $favicon) && file_exists(JPATH_ROOT.'/'. Media::getPath() . '/' . $apple_touch_icon)) {
            $image_type =   getimagesize(JPATH_ROOT.'/'. Media::getPath() . '/' . $apple_touch_icon);
            $wa->registerAndUseStyle('astroid.apple-touch-icon', Media::getPath() . '/' . $apple_touch_icon, ['version' => 'auto'], ['rel' => 'apple-touch-icon', 'type' => $image_type['mime'], 'sizes' => 'any']);
        }

        if (!empty($site_webmanifest)) {
            if ( (strpos( $site_webmanifest, 'http://' ) !== false) || (strpos( $site_webmanifest, 'https://' ) !== false) ) {
                $site_webmanifest = $params->get('site_webmanifest', '');
            } else {
                $site_webmanifest = Uri::root() . $params->get('site_webmanifest', '');
            }
            $wa->registerAndUseStyle('astroid.manifest', $site_webmanifest, ['version' => 'auto'], ['rel' => 'manifest', 'type' => 'application/json']);
        }
    }

    public static function scripts()
    {
        $app = Factory::getApplication();
        $template = Framework::getTemplate();
        $params = $template->getParams();
        $wa = $app->getDocument()->getWebAssetManager();
        if (Framework::isSite()) {
            $bootstrap_js = json_decode($params->get('bootstrap_js', '[]'), true);
            if (count($bootstrap_js)) {
                foreach ($bootstrap_js as $bootstrap) {
                    $wa->useScript('bootstrap.'.$bootstrap['value']);
                }
            }
            $wa->registerAndUseScript('astroid.script', 'astroid/script.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
        }
        if (Helper::getPluginParams()->get('astroid_debug', 0)) {
            $wa->useScript('bootstrap.tab');
            $wa->registerAndUseScript('astroid.debug', 'astroid/debug.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
        }
        $color_mode = $template->getColorMode();
        if ($color_mode) {
            $wa->addInlineScript('var TEMPLATE_HASH = "'. md5($template->template).'", ASTROID_COLOR_MODE ="'.$color_mode.'";');
        }
    }

    public static function styles()
    {
        $document = Framework::getDocument();
        $document->loadFontAwesome();
        $document->astroidCSS();
        return '';
    }
}
