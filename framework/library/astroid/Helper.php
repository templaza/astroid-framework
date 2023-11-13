<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Filesystem\Folder;
use Joomla\Registry\Registry;
use Joomla\CMS\Version;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once __DIR__ . '/../vendor/autoload.php';

class Helper
{
    public static function loadLanguage($extension, $client = 'site')
    {
        $lang = Factory::getApplication()->getLanguage();
        $lang->load($extension, ($client == 'site' ? JPATH_SITE : JPATH_ADMINISTRATOR));
    }

    public static function getPluginParams()
    {
        $plugin = PluginHelper::getPlugin('system', 'astroid');
        return new Registry($plugin->params);
    }

    public static function getJoomlaUrl()
    {
        $app = Factory::getApplication();
        $atm = $app->input->get('atm', 0, 'INT');
        $id = $app->input->get('id', 0, 'INT');
        if ($atm) {
            return Route::_('index.php?option=com_advancedtemplates&view=style&layout=edit&id=' . $id);
        } else {
            return Route::_('index.php?option=com_templates&view=style&layout=edit&id=' . $id);
        }
    }

    public static function getAstroidUrl($task, $params = [])
    {
        $query = [];
        foreach ($params as $key => $value) {
            $query[] = $key . '=' . $value;
        }
        $query = empty($query) ? '' : '&' . implode('&', $query);
        return Route::_('index.php?option=com_ajax&astroid=save' . $query);
    }

    public static function classify($word)
    {
        return str_replace([' ', '_', '-'], '', ucwords(str_replace('.', '_', $word), ' _-'));
    }

    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }

    public static function shortify($text)
    {
        $text = self::slugify($text);
        $return = [];
        $text = explode('-', $text);
        foreach ($text as $t) {
            $return[] = substr($t, 0, 1);
        }
        return implode('', $return);
    }

    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0) {
                return true;
            }
        }

        return false;
    }

    public static function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    public static function joomlaMediaVersion()
    {
        return Factory::getDocument()->getMediaVersion();
    }

    public static function refreshVersion()
    {
        $version = new Version;
        $version->refreshMediaVersion();
    }

    public static function getJSONData($name)
    {
        $json = file_get_contents(ASTROID_MEDIA . '/json/' . $name . '.json');
        return \json_decode($json, true);
    }

    public static function createDir($dir)
    {
        $dir = pathinfo($dir)['dirname'];
        $dirs = [];
        while (!file_exists($dir)) {
            $dirs[] = $dir;
            $dir = pathinfo($dir)['dirname'];
        }
        if (empty($dirs)) {
            return;
        }
        $dirs = array_reverse($dirs);
        foreach ($dirs as $dir) {
            mkdir($dir, 0777);
        }
    }

    public static function getXml($url)
    {
        if (!file_exists($url)) return;
        return simplexml_load_file($url, 'SimpleXMLElement');
    }

    public static function triggerEvent($name, $data = [])
    {
        PluginHelper::importPlugin('astroid');
        Factory::getApplication()->triggerEvent($name, $data);
    }

    public static function clearCacheByTemplate($template)
    {
        return self::clearCache($template, ['style', 'astroid', 'preset', 'compiled']);
    }

    public static function clearCache($template = '', $prefix = 'style')
    {
        $template_media_dir = JPATH_SITE . '/media/templates/site/' . $template . '/' . 'css';
        $template_dir = JPATH_SITE . '/templates/' . $template . '/' . 'css';
        $version = new Version;
        $version->refreshMediaVersion();
        if (!file_exists($template_dir) && !file_exists($template_media_dir)) {
            throw new \Exception("Template not found.", 404);
        }
        if (file_exists($template_media_dir)) {
            self::clearCSS($template_media_dir, $prefix);
        } else {
            self::clearCSS($template_dir, $prefix);
        }
        return true;
    }

    public static function clearCSS($dir, $prefix = 'style') {
        if (is_array($prefix)) {
            foreach ($prefix as $pre) {
                $styles = preg_grep('~^' . $pre . '.*\.(css)$~', scandir($dir));
                foreach ($styles as $style) {
                    unlink($dir . '/' . $style);
                }
            }
        } else {
            $styles = preg_grep('~^' . $prefix . '.*\.(css)$~', scandir($dir));
            foreach ($styles as $style) {
                unlink($dir . '/' . $style);
            }
        }
    }

    public static function isChildTemplate($template) {
        $xml = self::getXML(JPATH_SITE . "/templates/{$template}/templateDetails.xml");
        if (!$xml || !isset($xml->inheritable)) return false;
        $inheritable = (int) $xml->inheritable;
        if ($inheritable) {
            return [
                'isChild'   =>  false,
                'parent'    =>  ''
            ];
        } else {
            return [
                'isChild'   =>  true,
                'parent'    =>  (string) $xml->parent
            ];
        }
    }

    public static function clearJoomlaCache()
    {
        $app = Factory::getApplication();
        $conf = $app->getConfig();
        $options = array(
            'cachebase' => $conf->get('cache_path', JPATH_SITE . '/cache')
        );
        $cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)
            ->createCacheController('callback', $options);
        $cache->clean(null, 'notgroup');
        $app->getDispatcher()->dispatch('onAfterPurge');
    }

    public static function getFileHash($file)
    {
        $content = file_get_contents($file);
        $content = str_replace(array("\n", "\r"), "", $content);
        return md5($content);
    }

    public static function putContents($file, $content, $append = false)
    {
        Framework::getReporter('Logs')->add('Saved Cached to <code>' . str_replace(JPATH_SITE . '/', '', $file) . '</code>');
        self::createDir($file);
        if ($append) {
            file_put_contents($file, $content, FILE_APPEND);
        } else {
            file_put_contents($file, $content);
        }
    }

    public static function minifyCSS($css)
    {
        return str_replace(';}', '}', str_replace('; ', ';', str_replace(' }', '}', str_replace('{ ', '{', str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), "", preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css))))));
    }

    // from AstroidFrameworkHelper

    public static function replaceRelationshipOperators($str)
    {
        $str = $str ? str_replace(" AND ", " && ", $str) : '';
        $str = $str ? str_replace(" OR ", " || ", $str) : '';
        return $str;
    }

    public static function getJoomlaVersion()
    {
        $version = new Version;
        $version = $version->getShortVersion();
        $version = substr($version, 0, 1);
        return $version;
    }

    public static function getAstroidFieldsets($form)
    {
        $astroidfieldsets = $form->getFieldsets();
        usort($astroidfieldsets, "self::fieldsetOrding");

        $fieldsets = [];

        foreach ($astroidfieldsets as $af) {
            $fieldsets[$af->name] = $af;
        }

        return $fieldsets;
    }

    public static function fieldsetOrding($a, $b)
    {
        if ($a->order == $b->order) {
            return 0;
        }

        if ($a->order == '' || $b->order == '') {
            return 1;
        }

        return ($a->order < $b->order) ? -1 : 1;
    }

    public static function getModules()
    {
        $db = Factory::getDbo();
        $query = "SELECT #__modules.*, #__usergroups.title as access_title FROM #__modules JOIN #__usergroups ON #__usergroups.id=#__modules.access WHERE #__modules.client_id=0";

        $db->setQuery($query);
        $results = $db->loadObjectList();

        $return = [];
        foreach ($results as $result) {
            $return[] = ['id' => $result->id, 'title' => $result->title, 'module' => $result->module, 'type' => 'module', 'published' => $result->published, 'access_title' => $result->access_title, 'position' => $result->position, 'showtitle' => $result->showtitle];
        }

        return $return;
    }

    public static function getAllAstroidElements()
    {
        $template = Framework::getTemplate();
        // Template Directories
        $elements_dir = JPATH_LIBRARIES . '/astroid/framework/elements/';
        $template_elements_dir = JPATH_SITE . '/media/templates/site/' . $template->template . '/astroid/elements/';

        // Getting Elements from Template Directories
        $elements = array_filter(glob($elements_dir . '*'), 'is_dir');
        $template_elements = array_filter(glob($template_elements_dir . '*'), 'is_dir');

        // Merging Elements
        $elements = array_merge($elements, $template_elements);

        $return = array();

        foreach ($elements as $element_dir) {
            $xmlfile = $element_dir . '/' . (str_replace($template_elements_dir, '', str_replace($elements_dir, '', $element_dir))) . '.xml';
            if (file_exists($xmlfile)) {
                $type = str_replace($template_elements_dir, '', str_replace($elements_dir, '', $element_dir));
                $element = new Element($type, [], $template);
                $return[] = $element;
            }
        }
        //exit;
        return $return;
    }

    public static function getPositions()
    {
        $templateXML = \JPATH_SITE . '/templates/' . ASTROID_TEMPLATE_NAME . '/templateDetails.xml';
        $template = simplexml_load_file($templateXML);
        $positions = [];
        foreach ($template->positions[0] as $position) {
            $p = (string) $position;
            $positions[$p] = $p;
        }
        return $positions;
    }

    public static function getModuleStyles()
    {
        $template_name      = ASTROID_TEMPLATE_NAME;
        $options            = array();
        $isChildTemplate    = self::isChildTemplate($template_name);

        if ($isChildTemplate && isset($isChildTemplate['isChild']) && $isChildTemplate['isChild']) {
            $template_name = $isChildTemplate['parent'];
        }

        if (file_exists(\JPATH_SITE . '/templates/' . $template_name . '/html/layouts/chromes')) {
            $styles = Folder::files(\JPATH_SITE . '/templates/' . $template_name . '/html/layouts/chromes', '.php');
            if (count($styles)) {
                foreach ($styles as $style) {
                    $tmp = new \stdClass();
                    $tmp->value = basename($style,".php");
                    $tmp->text  = basename($style,".php");
                    $options[] = $tmp;
                }
            }
        }

        $systems = Folder::files(\JPATH_SITE . '/layouts/chromes', '.php');
        foreach ($systems as $system) {
            $tmp = new \stdClass();
            $tmp->value = basename($system,".php");
            $tmp->text  = basename($system,".php");
            $options[] = $tmp;
        }
        return $options;
    }

    public static function frameworkVersion()
    {
        Framework::getDebugger()->log('Getting Framework Version');
        $xml = self::getXML(JPATH_ADMINISTRATOR . '/manifests/libraries/astroid.xml');
        $version = (string) $xml->version;
        Framework::getDebugger()->log('Getting Framework Version');
        return $version;
    }

    public static function templateVersion($template)
    {
        Framework::getDebugger()->log('Getting Template Version');
        $xml = self::getXML(JPATH_SITE . "/templates/{$template}/templateDetails.xml");
        if (!$xml) return;
        $version = (string) $xml->version;
        Framework::getDebugger()->log('Getting Template Version');
        return $version;
    }

    public static function debug()
    {
        $reporters = Framework::getReporters();
        if (empty($reporters)) {
            return;
        }
        $tabs = [];
        $contents = [];
        $active = true;
        foreach ($reporters as $reporter) {
            if (empty($reporter->reports)) {
                continue;
            }
            $tabs[] = '<li class="nav-item"><a class="nav-link' . ($active ? ' active' : '') . '" href="#" id="' . $reporter->id . '-tab" data-bs-toggle="tab" data-bs-target="#' . $reporter->id . '" role="tab" aria-controls="' . $reporter->id . '" aria-selected="' . ($active ? 'true' : 'false') . '">' . $reporter->title . '</a></li>';
            $content = '<div class="tab-pane fade' . ($active ? ' show active' : '') . '" id="' . $reporter->id . '" role="tabpanel" aria-labelledby="' . $reporter->id . '-tab"><div>';
            foreach ($reporter->reports as $report) {
                $content .= '<div class="astroid-reporter-item">' . $report . '</div>';
            }
            $content .= '</div></div>';
            $contents[] = $content;
            $active = false;
        }

        if (empty($tabs)) {
            return;
        }

        $html = '';
        $html .= '<div id="astroid-reporter"><div class="astroid-reporter-heading">Astroid Framework</div><ul class="nav nav-tabs" id="astroid-debug-tabs" role="tablist">' . implode('', $tabs) . '</ul><div class="tab-content">' . implode('', $contents) . '</div></div>';
        return $html;
    }

    public static function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }

    public static function matchFilename($haystack, $needles)
    {
        $status = false;
        $needles = !is_array($needles) ? [$needles] : $needles;
        foreach ($needles as $string) {
            $string = trim($string);
            if (self::startsWith($string, '*') && self::endsWith($string, '*')) {
                $string = preg_replace('/' . preg_quote('*', '/') . '/', '', $string, -1);
                $string = preg_replace('/' . preg_quote('*', '/') . '/', '', $string, 1);
                if (self::contains($haystack, $string)) {
                    $status = true;
                    break;
                }
            } else if (self::startsWith($string, '*')) {
                $string = preg_replace('/' . preg_quote('*', '/') . '/', '', $string, -1);
                if (self::endsWith($haystack, $string)) {
                    $status = true;
                    break;
                }
            } else if (self::endsWith($string, '*')) {
                $string = preg_replace('/' . preg_quote('*', '/') . '/', '', $string, 1);
                if (self::startsWith($haystack, $string)) {
                    $status = true;
                    break;
                }
            } else if ($string == $haystack) {
                $status = true;
                break;
            }
        }
        return $status;
    }

    public static function orderingFields($a, $b)
    {
        return (($a->ordering < $b->ordering) ? -1 : 1);
    }

    /**
     * Get Preset data
     * @return array
     */
    public static function getPresets() {
        $template   =   Framework::getTemplate();
        $presets_path = JPATH_SITE . "/media/templates/site/{$template->template}/astroid/presets/";

        if (!file_exists($presets_path)) {
            return [];
        }
        $files = array_filter(glob($presets_path . '*.json'), 'is_file');
        $presets    =   [];
        foreach ($files as $file) {
            $json = file_get_contents($file);
            $data = \json_decode($json, true);
            $preset = ['title' => pathinfo($file)['filename'], 'desc' => '', 'thumbnail' => '', 'demo' => '', 'preset' => [], 'name' => pathinfo($file)['filename']];
            if (isset($data['title']) && !empty($data['title'])) {
                $preset['title'] = Text::_($data['title']);
            }
            if (isset($data['desc'])) {
                $preset['desc'] = Text::_($data['desc']);
            }
            if (isset($data['thumbnail']) && !empty($data['thumbnail'])) {
                $preset['thumbnail'] = Uri::root() . 'media/templates/site/' . $template->template . '/' . $data['thumbnail'];
            }
            if (isset($data['demo'])) {
                $preset['demo'] = $data['demo'];
            }
            if (isset($data['preset'])) {
                $preset['preset'] = $data['preset'];
            }
            $presets[] = $preset;
        }
        return $presets;
    }
}
