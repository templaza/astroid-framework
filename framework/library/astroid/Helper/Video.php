<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

defined('_JEXEC') or die;

class Video
{
    public static function _id($type, $url)
    {
        $parts = parse_url($url);
        $id = '';
        $query = null;
        switch ($type) {
            case 'youtube':
                if (isset($parts['query'])) {
                    parse_str($parts['query'], $query);
                    $id = (isset($query['v']) ? $query['v'] : '');
                }
                break;
            case 'vimeo':
                $id = (isset($parts['path']) ? str_replace('/', '', $parts['path']) : '');
                break;
        }
        return $id;
    }

    public static function _youtube($id, $meta = false)
    {
        $content = '';
        if ($meta) {
            $content .= '<meta itemprop="thumbnailURL" content="https://i.ytimg.com/vi/' . $id . '/maxresdefault.jpg" /><meta itemprop="embedURL" content="https://youtube.googleapis.com/v/' . $id . '" />';
        }
        $content = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $id . '?rel=0&amp;showinfo=0" style="border:0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
        return $content;
    }

    public static function _vimeo($id, $meta = false)
    {
        $content = '';
        if ($meta) {
            $content .= '<meta itemprop="thumbnailURL" content="http://i.vimeocdn.com/video/' . $id . '.jpg" /><meta itemprop="embedURL" content="https://vimeo.com/' . $id . '" />';
        }
        $content = '<iframe src="https://player.vimeo.com/video/' . $id . '" width="640" height="269" style="border:0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        return $content;
    }

    public static function getVideoByTypeUrl($type = '', $url = '', $meta = false)
    {
        $id = self::_id($type, $url);
        if (empty($id)) {
            return;
        }
        $method = '_' . $type;
        if (!method_exists(self::class, $method)) {
            return;
        }
        return self::$method($id, $meta);
    }

    public static function getVideoThumbnailByTypeUrl($type = '', $url = '')
    {
        $thumbnail = '';
        $id = self::_id($type, $url);
        if (empty($id)) return $thumbnail;

        switch ($type) {
            case 'youtube':
                $thumbnail = 'https://i.ytimg.com/vi/' . $id . '/maxresdefault.jpg';
                break;
            case 'vimeo':
                $thumbnail = 'https://i.vimeocdn.com/video/' . $id . '_640.webp';
                break;
        }

        return $thumbnail;
    }
}
