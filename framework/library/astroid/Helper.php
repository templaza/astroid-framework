<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;
use Astroid\Element\Layout;
use Astroid\Helper\Constants;
use Astroid\Helper\Style;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Event\Cache\AfterPurgeEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Joomla\CMS\Version;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\Component\Menus\Administrator\Helper\MenusHelper;
use Joomla\Database\DatabaseInterface;

defined('_JEXEC') or die;

require_once __DIR__ . '/../vendor/autoload.php';

class Helper
{
    public static function loadLanguage($extension, $client = 'site')
    {
        $lang = Factory::getApplication()->getLanguage();
        $lang->load($extension, ($client == 'site' ? JPATH_SITE : JPATH_ADMINISTRATOR));
    }

    public static function getPluginParams($group = 'system', $plugin = 'astroid')
    {
        $plugin = PluginHelper::getPlugin($group, $plugin);
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
        $text = iconv('utf-8', 'us-ascii//TRANSLIT//IGNORE', $text);
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
            $key        =   substr($t, 0, 1);
            if (count($return) == 0 && preg_match('/[^a-z]/', $key)) {
                $key    =   'as';
            }
            $return[]   =   $key;
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
        return Factory::getApplication()->getDocument()->getMediaVersion();
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
        $event     = new Helper\Events($name, $data);
        Factory::getApplication()->getDispatcher()->dispatch($name, $event);
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

    public static function isPro(): bool
    {
        if (defined('ASTROID_PRO')) {
            return true;
        } else {
            return false;
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
        $app->getDispatcher()->dispatch('onAfterPurge', new AfterPurgeEvent('onAfterPurge'));
    }

    public static function cleanImageUrl($url): object
    {
        $obj = new \stdClass();

        $obj->attributes = [
            'width'  => 0,
            'height' => 0,
        ];

        if ($url === null) {
            $url = '';
        }

        $mediaUri = new Uri($url);

        // Old image URL format
        if ($mediaUri->hasVar('joomla_image_height')) {
            $height = (int) $mediaUri->getVar('joomla_image_height');
            $width  = (int) $mediaUri->getVar('joomla_image_width');

            $mediaUri->delVar('joomla_image_height');
            $mediaUri->delVar('joomla_image_width');
        } else {
            // New Image URL format
            $fragmentUri = new Uri($mediaUri->getFragment());
            $width       = (int) $fragmentUri->getVar('width', 0);
            $height      = (int) $fragmentUri->getVar('height', 0);
        }

        if ($width > 0) {
            $obj->attributes['width'] = $width;
        }

        if ($height > 0) {
            $obj->attributes['height'] = $height;
        }

        $mediaUri->setFragment('');
        $obj->url = $mediaUri->toString();

        return $obj;
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
        File::write($file, $content, false, $append);
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
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = "SELECT #__modules.*, #__usergroups.title as access_title FROM #__modules JOIN #__usergroups ON #__usergroups.id=#__modules.access WHERE #__modules.client_id=0";

        $db->setQuery($query);
        $results = $db->loadObjectList();

        $return = [];
        foreach ($results as $result) {
            $return[] = ['id' => $result->id, 'title' => $result->title, 'module' => $result->module, 'type' => 'module', 'published' => $result->published, 'access_title' => $result->access_title, 'position' => $result->position, 'showtitle' => $result->showtitle];
        }

        return $return;
    }

    public static function getAllAstroidElements($mode = '', $template_id = null)
    {
        $template = Framework::getTemplate($template_id);
        $template_name = '';
        if ($template->isAstroid) {
            $template_name = $template->template;
            if (!defined('ASTROID_TEMPLATE_NAME')) {
                define('ASTROID_TEMPLATE_NAME', $template_name);
            }
        } elseif (defined('ASTROID_TEMPLATE_NAME')) {
            $template_name = ASTROID_TEMPLATE_NAME;
        }

        // Template Directories
        $elements_dir = JPATH_LIBRARIES . '/astroid/framework/elements/';
        $plugin_elements_dir = JPATH_SITE . "/plugins/astroid";
        $template_elements_dir = JPATH_SITE . '/media/templates/site/' . $template_name . '/astroid/elements/';

        // Getting Elements from Template Directories
        $elements = Folder::folders($elements_dir, '.', false, true);

        if (file_exists(Path::clean($plugin_elements_dir))) {
            $plugin_folders = Folder::folders($plugin_elements_dir);
            if (count($plugin_folders)) {
                foreach ($plugin_folders as $plugin_folder) {
                    if (PluginHelper::isEnabled('astroid', $plugin_folder) && file_exists(Path::clean($plugin_elements_dir . '/' . $plugin_folder . '/elements/'))) {
                        // Merging Plugin Elements
                        $elements = array_merge($elements, Folder::folders($plugin_elements_dir . '/' . $plugin_folder . '/elements/', '.', false, true));
                    }
                }
            }
        }

        if ($template_name && file_exists(Path::clean($template_elements_dir))) {
            $template_elements = Folder::folders($template_elements_dir, '.', false, true);
            // Merging Elements
            $elements = array_merge($elements, $template_elements);
        }

        $return = array();

        foreach ($elements as $element_dir) {
            // String manipulation should be faster than pathinfo() on newer PHP versions.
            $slash = strrpos($element_dir, DIRECTORY_SEPARATOR);

            if ($slash === false) {
                continue;
            }

            $type = substr($element_dir, $slash + 1);
            $xmlfile = $element_dir . '/' . $type . '.xml';
            if (file_exists($xmlfile)) {
                $element = new Element($type, [], $template, $mode);
                $return[] = $element;
            }
        }
        //exit;
        return $return;
    }

    public static function getElement($unqid, $template = null, $options = []) {
        if (empty($template)) {
            $template   =   Framework::getTemplate();
        }
        $layout_type    =   'templates';
        if (isset($options['source']) && !empty($options['source'])) {
            $sublayout =   Layout::getDataLayout($options['source'], (isset($options['template']) && !empty($options['template']) ? $options['template'] : ''), (isset($options['layout_type']) && !empty($options['layout_type']) ? $options['layout_type'] : 'layouts'));
            if (!isset($sublayout['data']) || !$sublayout['data']) {
                return false;
            }
            $layout_type = isset($options['layout_type']) && !empty($options['layout_type']) ? $options['layout_type'] : 'layouts';
            $layout     = \json_decode($sublayout['data'], true);
        } else {
            $layout =   $template->getLayout();
        }

        foreach ($layout['sections'] as $section) {
            if ($section['id'] == $unqid) {
                $section['params'] = self::loadParams($section['params']);
                return $section;
            } else {
                foreach ($section['rows'] as $row) {
                    if ($row['id'] == $unqid) {
                        $row['params'] = self::loadParams($row['params']);
                        return $row;
                    } else {
                        foreach ($row['cols'] as $col) {
                            if ($col['id'] == $unqid) {
                                $col['params'] = self::loadParams($col['params']);
                                return $col;
                            } else {
                                foreach ($col['elements'] as $element) {
                                    if ($element['id'] == $unqid) {
                                        $element['params'] = self::loadParams($element['params']);
                                        if ($layout_type == 'article_layouts') {
                                            $template_name = isset($options['template']) && !empty($options['template']) ? $options['template'] : $template->template;
                                            $article_id = isset($options['article_id']) && !empty($options['article_id']) ? $options['article_id'] : 0;
                                            $layout_path = Path::clean(JPATH_SITE . "/media/templates/site/$template_name/params/article_widget_data/". $article_id . '_' . $unqid . '.json');
                                            if (!file_exists($layout_path)) {
                                                $layout_path = Path::clean(JPATH_SITE . "/media/templates/site/$template_name/astroid/article_widget_data/". $article_id . '_' . $unqid . '.json');
                                            }
                                            if (file_exists($layout_path)) {
                                                $article_json = file_get_contents($layout_path);
                                                $article_data = json_decode($article_json, true);
                                                $article_params = self::loadParams($article_data['params']);
                                                $element['params']->merge($article_params);
                                            }
                                        }
                                        return $element;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    public static function loadParams($data) {
        $params_data   = new Registry();
        if (isset($data) && !empty($data)) {
            $params = [];
            foreach ($data as $param) {
                if (is_array($param)) {
                    $params[$param['name']] = $param['value'];
                } elseif (is_object($param)) {
                    $params[$param->name] = $param->value;
                }
            }
            $params_data->loadArray($params);
        }
        return $params_data;
    }

    public static function getPositions()
    {
        $template_name = defined('ASTROID_TEMPLATE_NAME') ? ASTROID_TEMPLATE_NAME : Framework::getTemplate()->template;
        $templateXML = \JPATH_SITE . '/templates/' . $template_name . '/templateDetails.xml';
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
        $template_name      = defined('ASTROID_TEMPLATE_NAME') ? ASTROID_TEMPLATE_NAME : Framework::getTemplate()->template;
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

    public static function getNonMinifiedPath($path, $url) : string
    {
        $position = strrpos($url, '.min.');
        if ($position !== false) {
            $nonMinifiedPath = substr_replace($url, '', $position, 4);
            if (file_exists($path.$nonMinifiedPath)) {
                return $nonMinifiedPath;
            }
        }
        return $url;
    }

    public static function getMenuLinks() {
        $menuTypes = MenusHelper::getMenuLinks();
        $menus  =   array();
        if (!empty($menuTypes)) {
            foreach ($menuTypes as $type) {
                $menutype = new \stdClass();
                $menutype->id = $type->id;
                $menutype->menutype = $type->menutype;
                $menutype->title = $type->title;
                $menutype->description = $type->description;
                $menutype->links = array();
                $i = 0;
                $prevlevel = 0;
                $parentstack = array();
                while ($i < count($type->links)) {
                    $link = $type->links[$i];
                    if ($link->level !== 0) {
                        $tmp_link = new \stdClass();
                        $tmp_link->id = $link->value;
                        $tmp_link->text = $link->text;
                        $tmp_link->level = $link->level;
                        $tmp_link->type = $link->type;
                        $tmp_link->language = $link->language;
                        $tmp_link->links = array();
                        if ($tmp_link->level === 1) {
                            if (!empty($parentstack)) {
                                array_pop($parentstack);
                            }
                            $menutype->links[] = $tmp_link;
                            $parentstack[] = end($menutype->links);
                        } elseif ($prevlevel < $tmp_link->level && !empty($parentstack)) {
                            $parent = end($parentstack);
                            $parent->links[] = $tmp_link;
                            $parentstack[] = end($parent->links);
                        } elseif ($prevlevel == $tmp_link->level) {
                            if (!empty($parentstack)) {
                                array_pop($parentstack);
                            }
                            if (!empty($parentstack)) {
                                $parent = end($parentstack);
                                $parent->links[] = $tmp_link;
                                $parentstack[] = end($parent->links);
                            }
                        } else {
                            if (!empty($parentstack)) {
                                array_pop($parentstack);
                            }
                            if (!empty($parentstack)) {
                                array_pop($parentstack);
                            }
                            if (!empty($parentstack)) {
                                $parent = end($parentstack);
                                $parent->links[] = $tmp_link;
                                $parentstack[] = end($parent->links);
                            }
                        }
                        $prevlevel = $tmp_link->level;
                    }
                    $i++;
                }
                $menus[]    =   $menutype;
            }
        }
        return $menus;
    }

    public static function frameworkVersion()
    {
        Framework::getDebugger()->log('Getting Framework Version');
        $version = Constants::$astroid_version;
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

    public static function getFormTemplate($mode = '', $template_id = null) {
        $form_template = array();
        $astroidElements = Helper::getAllAstroidElements($mode, $template_id);
        foreach ($astroidElements as $astroidElement) {
            $form_template[$astroidElement->type] = $astroidElement->renderJson('addon');
        }
        if ($mode !== 'article_data') {
            $template = $template_id !== null ? Framework::getTemplate($template_id) : Framework::getTemplate();
            foreach (['section', 'row', 'column', 'sublayout'] as $form_type) {
                $form = new Element($form_type, [], $template, $mode);
                $form_template[$form_type] = $form_type == 'sublayout' ? $form->renderJson('sublayout') : $form->renderJson();
            }
        }
        return $form_template;
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

    public static function coming_soon() : void
    {
        $app = Factory::getApplication();
        $document = Framework::getDocument();
        $params = Framework::getTemplate()->getParams();
        $wa = $app->getDocument()->getWebAssetManager();

        $wa->registerAndUseScript('astroid.coundown.moment', 'astroid/moment/moment.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
        $wa->registerAndUseScript('astroid.coundown.timezone', 'astroid/moment/moment-timezone.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
        $wa->registerAndUseScript('astroid.coundown.timezone.10years', 'astroid/moment/moment-timezone-with-data-10-year-range.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
        $document->addScript('vendor/astroid/js/countdown.min.js', 'body');
        $document->addScript('vendor/astroid/js/countdown_cls.min.js', 'body');

        $background_setting = $params->get('background_setting');
        $comming_soon_style         =   new Style('.comingsoon-wrap', '', true);
        $comming_soon_style_dark    =   new Style('.comingsoon-wrap', 'dark', true);

        $styles = [];
        $video = [];
        if ($background_setting) {
            if ($background_setting == "color") {
                $background_color = Style::getColor($params->get('background_color', ''));
                $comming_soon_style->addCss('background-color', $background_color['light']);
                $comming_soon_style_dark->addCss('background-color', $background_color['dark']);
            }
            if ($background_setting == "image") {

                $img_background_color = Style::getColor($params->get('img_background_color', ''));
                $comming_soon_style->addCss('background-color', $img_background_color['light']);
                $comming_soon_style_dark->addCss('background-color', $img_background_color['dark']);

                $background_image = $params->get('background_image', '');
                if (!empty($background_image)) {
                    $comming_soon_style->addCss('background-image', 'url(' . Uri::root() . \Astroid\Helper\Media::getPath() . '/' . $background_image . ')');
                    $background_repeat = $params->get('background_repeat', '');
                    $background_repeat = empty($background_repeat) ? 'inherit' : $background_repeat;
                    $comming_soon_style->addCss('background_repeat', $background_repeat);

                    $background_size = $params->get('background_size', '');
                    $background_size = empty($background_size) ? 'inherit' : $background_size;
                    $comming_soon_style->addCss('background-size', $background_size);

                    $background_attchment = $params->get('background_attchment', '');
                    $background_attchment = empty($background_attchment) ? 'inherit' : $background_attchment;
                    $comming_soon_style->addCss('background-attachment', $background_attchment);

                    $background_position = $params->get('background_position', '');
                    $background_position = empty($background_position) ? 'inherit' : $background_position;
                    $comming_soon_style->addCss('background-position', $background_position);
                }
            }

            if ($background_setting == "gradient") {
                $background_gradient = $params->get('background_gradient', '');
                if (!empty($background_gradient)) {
                    $comming_soon_style->addCss('background-image', $background_gradient->gradient_type . '-gradient(' . $background_gradient->start_color . ',' . $background_gradient->stop_color);
                }
            }

            if ($background_setting == "video") {
                $attributes = [];
                $background_video = $params->get('background_video', '');
                if (!empty($background_video)) {
                    $attributes['data-as-video-bg'] = Uri::root() . \Astroid\Helper\Media::getPath() . '/' . $background_video;
                    $wa->registerAndUseScript('astroid.videobg', 'astroid/videobg.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
                }

                $return = [];
                foreach ($attributes as $key => $value) {
                    $return[] = $key . '="' . $value . '"';
                }
                $video =  $return;
            }
        }

        $comming_soon_style->render();
        $comming_soon_style_dark->render();
    }
}
