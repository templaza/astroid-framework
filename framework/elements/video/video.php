<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/templates/YOUR_ASTROID_TEMPLATE/astroid/elements/module_position/module_position.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;
extract($displayData);
use Joomla\CMS\Uri\Uri;
use Astroid\Helper\Media;

$video_type             = $params->get('video_type', 'url');
$url                    = $params->get('url', '');
$mp4_video              = $params->get('mp4_video', '');
$ogv_video              = $params->get('ogv_video', '');

$no_cookie              = $params->get('no_cookie', 0);
$show_rel_video         = $params->get('show_rel_video', 0);
$youtube_shorts         = $params->get('youtube_shorts', 0);
$aspect_ratio           = $params->get('aspect_ratio', '16x9');

$vimeo_show_author      = 'byline=' . $params->get('vimeo_show_author', 0);
$vimeo_mute_video       = 'muted=' . $params->get('vimeo_mute_video', 1);
$vimeo_show_author_profile = 'portrait=' . $params->get('vimeo_show_author_profile', 0);
$vimeo_show_video_title = 'title=' . $params->get('vimeo_show_video_title', 0);

$show_control           = $params->get('show_control', 1);
$video_loop             = $params->get('video_loop', 1);
$video_mute             = $params->get('video_mute', 1);
$autoplay_video         = $params->get('autoplay_video', 1);
$video_poster           = $params->get('video_poster', '');
if ($video_poster) {
    $video_poster       = Uri::base(true) . '/' . Media::getPath() . '/' . $video_poster;
}
$video_title 	        = "";

if (!(!empty($mp4_video) && (strpos($mp4_video, "http://") !== false || strpos($mp4_video, "https://") !== false)))
{
    $mp4_video = Uri::base(true) . '/' . Media::getPath() . '/' . $mp4_video;
}

if (!(!empty($ogv_video) && (strpos($ogv_video, "http://") !== false || strpos($ogv_video, "https://") !== false)))
{
    $ogv_video = Uri::base(true) . '/' . Media::getPath() . '/' . $ogv_video;
}

if ($url)
{
    $video = parse_url($url);

    $youtube_no_cookie = $no_cookie ? '-nocookie' : '';

    if(array_key_exists('host', $video)) {
        switch ($video['host'])
        {
            case 'youtu.be':
                $id 		 = trim($video['path'], '/');
                $src 		 = '//www.youtube' . $youtube_no_cookie . '.com/embed/' . $id . '?iv_load_policy=3' . $show_rel_video;
                $video_title = $params->get('video_title', 'Youtube Video');
                break;

            case 'www.youtube.com':
            case 'youtube.com':
                $query = [];
                $playlist_id = null;
                if(array_key_exists('query', $video)) {
                    parse_str($video['query'], $query);
                }

                if($video['path'] === '/playlist') {
                    if (preg_match('/\blist=([^&]+)/', $video['query'], $matches)) {
                        $playlist_id = $matches[1];
                    }
                }

                $src 		 = '';

                if($playlist_id) {
                    $src 	 = '//www.youtube.com/embed/?listType=playlist&list=' . $playlist_id;
                } else {
                    $id  		 = ($youtube_shorts) ? str_replace('/shorts/', "", $video['path']) : $query['v'];
                    $src 	 = '//www.youtube' . $youtube_no_cookie . '.com/embed/' . $id . '?iv_load_policy=3' . $show_rel_video;
                }

                $video_title = $params->get('video_title', 'Youtube Video');
                break;

            case 'vimeo.com':
            case 'www.vimeo.com':
            case 'player.vimeo.com':
                $initialSrc = $url;

                if($video['host'] !== 'player.vimeo.com') {
                    $id = trim($video['path'], '/');
                    $initialSrc = "//player.vimeo.com/video/{$id}";
                }

                $src = $initialSrc . '?' . implode('&', array($vimeo_mute_video, $vimeo_show_author, $vimeo_show_author_profile, $vimeo_show_video_title, 'autoplay='. $autoplay_video, 'loop='. $video_loop, 'controls='. $show_control));
                break;
        }
    }
}

if ($video_type == 'url')
{
    echo '<div class="as-video-url-block ratio ratio-'.$aspect_ratio.'">';
    echo '<iframe class="as-video-item" src="' . $src . '" ' . ($video_title ? 'title="'.$video_title. '"' : '') . ' allow="accelerometer" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
    echo '</div>';
}
else
{
    if ($mp4_video || $ogv_video)
    {
        echo '<div class="as-video-local-wrap">';
        echo '<video class="as-video-local-source ratio ratio-'.$aspect_ratio.'"' . ($video_loop != 0 ? ' loop' : '') . ($autoplay_video != 0 ? ' autoplay' : '') . ($show_control != 0 ? ' controls' : '') . ($video_mute != 0 ? ' muted' : '') . ' poster="' . $video_poster . '" controlsList="nodownload" playsinline>';
        if (!empty($mp4_video))
        {
            echo '<source src="' . $mp4_video . '" type="video/mp4">';
        }
        if (!empty($ogv_video))
        {
            echo '<source src="' . $ogv_video . '" type="video/ogg">';
        }
        echo '</video>';
        echo '</div>';
    }
}