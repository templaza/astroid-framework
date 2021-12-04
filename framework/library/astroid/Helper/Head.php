<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

use Astroid\Framework;
use Astroid\Helper;
use Joomla\CMS\HTML\HTMLHelper;

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
        $favicon = $params->get('favicon', '');
        if (!empty($favicon)) {
            Framework::getDocument()->addLink(\JURI::base(true) . '/' . Media::getPath() . '/' . $favicon, 'shortcut icon', array(
                'type'  => image_type_to_mime_type(exif_imagetype(JPATH_BASE.'/'. Media::getPath() . '/' . $favicon)),
                'sizes' => 'any'
            ));
        }
        $apple_touch_icon = $params->get('apple_touch_icon', '');
        if (!empty($apple_touch_icon) && ($apple_touch_icon != $favicon)) {
            Framework::getDocument()->addLink(\JURI::base(true) . '/' . Media::getPath() . '/' . $apple_touch_icon, 'apple-touch-icon', array(
                'type'  => image_type_to_mime_type(exif_imagetype(JPATH_BASE.'/'. Media::getPath() . '/' . $apple_touch_icon)),
                'sizes' => 'any'
            ));
        }
        $site_webmanifest = $params->get('site_webmanifest', '');
        if (!empty($site_webmanifest)) {
            if ( (strpos( $site_webmanifest, 'http://' ) !== false) || (strpos( $site_webmanifest, 'https://' ) !== false) ) {
                $site_webmanifest = $site_webmanifest;
            } else {
                $site_webmanifest = \JURI::base( true ) . '/' . $site_webmanifest;
            }

            Framework::getDocument()->addLink($site_webmanifest, 'manifest', array(
                'type'  => 'application/json'
            ));
        }
    }

    public static function scripts()
    {
        $document = Framework::getDocument();
        $app = \JFactory::getApplication();
        $layout = $app->input->get('layout', '', 'STRING');
        $getPluginParams = Helper::getPluginParams();
        $document->addScript('vendor/jquery/jquery-3.5.1.min.js', 'body');
        if ($layout !== 'edit' && $getPluginParams->get('astroid_bootstrap_js', 1)) {
            if (ASTROID_JOOMLA_VERSION < 4) {
                $document->addScript('vendor/bootstrap/js/bootstrap.bundle.min.js', 'body');
            } else {
                // Depends on Bootstrap
                HTMLHelper::_('bootstrap.framework');
            }
        }
        $document->addScript('vendor/jquery/jquery.noConflict.js', 'body');
    }

    public static function styles()
    {
        $styles = '';
        $document = Framework::getDocument();
        $document->loadFontAwesome();
        if (ASTROID_JOOMLA_VERSION != 4) {
            $document->addStyleSheet('media/jui/css/icomoon.css');
        } else {
            $document->addStyleSheet('media/astroid/assets/vendor/fontawesome/css/all.min.css');
            if ($document->isFrontendEditing()) {
                $document->addStyleSheet('templates/cassiopeia/css/template.css');
                $document->addStyleSheet('media/astroid/assets/css/frontend-editing-j4.css');
            }
        }
        $styles .= $document->astroidCSS();
        return $styles;
    }
}
