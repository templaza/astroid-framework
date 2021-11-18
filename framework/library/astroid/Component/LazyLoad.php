<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Component;

use Astroid\Framework;
use Astroid\Helper;

defined('_JEXEC') or die;

class LazyLoad
{
    public static function run()
    {
        Framework::getDebugger()->log('Lazy Load');
        $app = \JFactory::getApplication();
        $template = Framework::getTemplate();
        $document = Framework::getDocument();
        $params = $template->getParams();
        $run = $params->get('lazyload', 0);

        // Stop Lazy Load for RSSFeeds
        if ($document->getType() == 'feed') $run = false;

        // Stop Lazy Load
        if (!$run) return;

        Helper::createDir(ASTROID_CACHE . '/lazy-load/' . $template->id);
        $document->addScript('vendor/astroid/js/lazyload.min.js');

        if ($params->get('lazyload_components', '')) {
            $run = self::selectedComponents($params->get('lazyload_components', ''), $params->get('lazyload_components_action', 'include'));
            if (!$run) {
                return;
            }
        }

        if ($params->get('lazyload_urls', '')) {
            $run = self::selectedURLs($params->get('lazyload_urls', ''), $params->get('lazyload_urls_action', 'include'));
            if (!$run) {
                return;
            }
        }

        if ($params->get('lazyload_exclude_views', '')) {
            $run = self::exclidedViews($params->get('lazyload_exclude_views', ''));
            if (!$run) {
                return;
            }
        }

        $blankImage = \JURI::root() . 'media/astroid/assets/images/blank.png';
        $patternImage = "@<img[^>]*src=[\"\']([^\"\']*)[\"\'][^>]*>@";
        $body = $inputString = $app->getBody(false);

        preg_match_all($patternImage, $inputString, $matches);

        if ($params->get('lazyload_imgs', '') && !empty($matches)) {
            self::selectedImages($matches, $params->get('lazyload_imgs', ''), $params->get('lazyload_imgs_action', 'include'));
        }

        if ($params->get('lazyload_classes', '') && !empty($matches)) {
            self::selectedClasses($matches, $params->get('lazyload_classes', ''), $params->get('lazyload_classes_action', 'include'));
        }

        if (!empty($matches[0])) {

            $imageMapFile = ASTROID_CACHE . '/lazy-load/' . $template->id . '.json';
            $imageMapChanged = false;
            $imageMap = [];
            if (file_exists($imageMapFile)) {
                $imageMap = \json_decode(\file_get_contents($imageMapFile), true);
            }

            $base = \JUri::base();
            $basePath = \JUri::base(true);

            foreach ($matches[0] as $key => $match) {
                if (strpos($matches[1][$key], 'http://') === false && strpos($matches[1][$key], 'https://') === false) {
                    if (!empty($basePath)) {
                        if (strpos($matches[1][$key], $basePath) === false) {
                            $match = str_replace($matches[1][$key], $basePath . '/' . $matches[1][$key], $match);
                        }
                    } else {
                        if (strpos($matches[1][$key], $base) === false) {
                            $match = str_replace($matches[1][$key], $base . $matches[1][$key], $match);
                        }
                    }
                }

                if (Framework::getDebugger()->debug) {
                    Framework::getReporter('Lazy Load Images')->add('<a href="' . $matches[1][$key] . '" target="_blank"><code>' . $document->beutifyURL($matches[1][$key]) . '</code></a>');
                }

                if (!isset($imageMap[md5($matches[1][$key])])) {
                    $base64thumbnail = self::getBase64Thumbnail($matches[1][$key]);
                    $imageMap[md5($matches[1][$key])] = $base64thumbnail;
                    $imageMapChanged = true;
                }

                $base64Image = $imageMap[md5($matches[1][$key])];

                if ($base64Image !== false) {
                    $blankImage = $base64Image;
                }

                $matchLazy = str_replace('src=', 'src="' . $blankImage . '" data-astroid-lazyload=', $match);

                $body = str_replace($matches[0][$key], $matchLazy, $body);
            }

            if ($imageMapChanged) {
                file_put_contents($imageMapFile, \json_encode($imageMap));
            }

            $app->setBody($body);
            Framework::getDebugger()->log('Lazy Load');
        }
    }

    public static function getBase64Thumbnail($sourceImage)
    {
        if (!is_file($sourceImage)) return false;
        error_reporting(E_ERROR | E_PARSE);
        try {
            $info = getimagesize($sourceImage);
            if (!is_array($info) || !in_array($info['mime'], ['image/jpeg', 'image/gif', 'image/png'])) {
                return false;
            }

            list($origWidth, $origHeight) = $info;
            $image = imagecreatetruecolor($origWidth, $origHeight);

            imagesavealpha($image, true);
            $transparent = imagecolorallocatealpha($image, 255, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);

            ob_start();
            imagepng($image);
            $blankImageBuffer = ob_get_clean();
            if (ob_get_length() > 0) {
                ob_end_clean();
            }
            $blankImage = 'data:image/png;base64,' . base64_encode($blankImageBuffer);
            return $blankImage;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function selectedImages(&$matches, $images = '', $toggle = '')
    {
        $images = array_map('trim', explode("\n", $images));
        $matchesTemp = array();
        $matches1Temp = array();

        foreach ($images as $image) {
            $count = 0;

            foreach ($matches[1] as $match) {
                if (preg_match('@' . preg_quote($image) . '@', $match)) {
                    if ($toggle == 'exclude') {
                        unset($matches[0][$count]);
                        unset($matches[1][$count]);
                    } else {
                        $matchesTemp[] = $matches[0][$count];
                        $matches1Temp[] = $matches[1][$count];
                    }
                }

                $count++;
            }
        }

        if ($toggle == 'include') {
            unset($matches[0]);
            unset($matches[1]);
            $matches[0] = $matchesTemp;
            $matches[1] = $matches1Temp;
        }
    }

    public static function selectedComponents($components = '', $toggle = '')
    {
        $option = \JFactory::getApplication()->input->getWord('option');
        $components = array_map('trim', explode("\n", $components));
        $hit = false;
        $return = true;
        foreach ($components as $component) {
            if ($option === $component) {
                $hit = true;
                break;
            }
        }

        if ($toggle == 'include') {
            if ($hit === false) {
                $return = false;
            }
            return $return;
        }

        if ($hit === true) {
            $return = false;
        }

        return $return;
    }

    public static function selectedURLs($surls = '', $toggle = '')
    {
        $url = \JUri::getInstance()->toString();
        $surls = array_map('trim', explode("\n", $surls));
        $hit = false;
        $return = true;

        foreach ($surls as $surl) {
            if ($url === $surl) {
                $hit = true;
                break;
            }
        }

        if ($toggle == 'include') {
            if ($hit === false) {
                $return = false;
            }

            return $return;
        }

        if ($hit === true) {
            $return = false;
        }

        return $return;
    }

    public static function exclidedViews($views = '')
    {
        $view = \JFactory::getApplication()->input->getWord('tmpl', '');
        $views = array_map('trim', explode(",", $views));
        $return = true;

        if (in_array($view, $views)) {
            $return = false;
        }

        return $return;
    }

    public static function selectedClasses(&$matches, $classes = '', $toggle = '')
    {
        $classes = array_map('trim', explode("\n", $classes));
        foreach ($matches[0] as $key => $match) {
            $classExists = false;

            foreach ($classes as $classname) {
                $exists = preg_match('@class=[\"\'].*' . $classname . '.*[\"\']@Ui', $match);
                if (!empty($exists)) {
                    $classExists = true;
                    break;
                }
            }

            if ($toggle == 'include') {
                if (!$classExists) {
                    unset($matches[0][$key]);
                    unset($matches[1][$key]);
                }

                continue;
            }

            if (!empty($classExists)) {
                unset($matches[0][$key]);
                unset($matches[1][$key]);
            }
        }
    }
}
