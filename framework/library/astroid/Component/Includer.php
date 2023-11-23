<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Component;

use Astroid\Framework;
use Astroid\Helper;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class Includer
{
    public static $params;
    public static function run($content = null)
    {
        if ($content === null) {
            $app = Factory::getApplication();
            $body = $app->getBody();
        } else {
            $body = $content;
        }
        $includers = [];
        $body = preg_replace_callback('/(<astroid:include\s[^>]*type=")([^"]*)("[^>]* \/>)/siU', function ($matches) use (&$includers) {
            $html = $matches[0];
            $method = Helper::classify($matches[2]);
            if (method_exists(self::class, '_' . $method)) {
                $includers[] = [
                    'name' => $matches[2],
                    'replace' => $matches[0],
                    'func' => '_' . $method
                ];
            }
            return $html;
        }, $body);

        $includers = array_reverse($includers);

        foreach ($includers as $includer) {
            $func           =   $includer['func'];
            $func_content   =   self::$func();
            if (!is_string($func_content)) {
                $func_content = '';
            }
            $body = ($includer['replace']) ? str_replace($includer['replace'], $func_content, $body) : $body;
        }

        if (Framework::isSite()) {
            $getPluginParams        =   Helper::getPluginParams();
            $remove_generator       =   $getPluginParams->get('astroid_remove_generator', 0);
            if ($remove_generator) {
                $body = self::removeGenerateTag($body);
            }
        }

        if ($content === null) {
            $app->setBody($body);
        } else {
            return $body;
        }
    }

    public static function _headMeta()
    {
        $document = Framework::getDocument();
        return $document->renderMeta();
    }

    public static function _headStyles()
    {
        $document = Framework::getDocument();
        $customCSS= $document->astroidCustomCSS();
        $content  = $document->renderLinks();
        $content .= $document->getStylesheets();
        $content .= $customCSS;
        return $content;
    }

    public static function _headScripts()
    {
        $document = Framework::getDocument();
        $content = $document->getScripts('head');
        $content .= $document->getCustomTags('head');
        return $content;
    }

    public static function _bodyScripts()
    {
        $document = Framework::getDocument();
        $content = $document->getScripts('body');
        $content .= $document->getCustomTags('body');
        return $content;
    }

    public static function removeGenerateTag(string $content) : string
    {
        $patterns = [
            '/<meta name="generator".*?>\n/',
            '/<generator.*?<\/generator>\n/',
            '/<!-- generator=".*?" -->\n/',
        ];
        return preg_replace($patterns, '', $content);
    }

    public static function _debug()
    {
        return Helper::debug();
    }
}
