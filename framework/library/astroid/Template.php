<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;
use \Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Folder;

defined('_JEXEC') or die;

class Template
{
    public $template = null;
    public $isAstroid = false;
    public $language = '';
    public $direction = '';
    public $id = 0;
    public $hash = '';
    public $params = null;
    public $title = '';
    public $version = null;
    protected static $presets = null;
    protected static $fonts = null;

    public function __construct($id = 0)
    {
        $app = Factory::getApplication();
        $menu = $app->getMenu()->getActive();

        if (!empty($id)) {
            $template_id = $id;
        } else {
            $template_id = isset($menu->template_style_id) ? $menu->template_style_id : $id;
        }

        if (!empty($template_id)) {
            $jtemplate = $this->_getById($template_id);
        } else {
            $jtemplate = $app->getTemplate(true);
        }
        $this->template = $jtemplate->template;

        $this->language = $app->getLanguage()->getTag();
        $this->direction = $app->getLanguage()->isRtl() ? 'rtl' : 'ltr';

        $this->params = $jtemplate->params;
        $this->title = '';
        if (!empty($this->params->get('astroid', 0))) {
            $this->isAstroid = true;
        }

        if (Framework::isSite()) {
            $this->_set($jtemplate->id);
        } else if (Framework::isAdmin()) {
            $option = $app->input->get('option', '');
            $view = $app->input->get('view', '');
            $layout = $app->input->get('layout', '');
            $astroid = $app->input->get('astroid', '');
            $template = $app->input->get('template', '');
            $id = $app->input->get('id', '', 'INT');

            if ($option == 'com_ajax' && $astroid == 'manager' && !empty($id)) {
                $this->_upload($id);
            } else if ($option == 'com_templates' && $view == 'style' && $layout == 'edit' && !empty($id)) {
                $this->_upload($id);
            } else if ($option == 'com_ajax' && !empty($astroid) && !empty($template)) {
                @list($template, $id) = explode('-', $template);
                if (!empty($id)) {
                    $this->_upload($id);
                }
            } else {
                $jtemplate = $this->getDefault();
                if (!empty($jtemplate) && !empty($jtemplate->id)) {
                    $this->_upload($jtemplate -> id);
                }
            }
        }

        if (Framework::isSite()) {
            $preset = $app->input->get('preset', '');
            if (!empty($preset)) {
                $this->setPreset($preset);
            }
        }
    }

    private function _set($id)
    {
        $this->id = $id;
        $path = JPATH_SITE . "/media/templates/site/{$this->template}/params/" . $this->id . '.json';
        if (file_exists($path)) {
            $json = file_get_contents($path);
            $this->params->loadString($json, 'JSON');
            $this->hash = md5($this->params->toString() . $this->id);
            $this->isAstroid = true;
        } else if (!empty($this->params->get('astroid', 0))) {
            Helper\Template::setTemplateDefaults($this->template, $this->id);
            $json = file_get_contents($path);
            $this->params->loadString($json, 'JSON');
            $this->hash = md5($this->params->toString() . $this->id);
            $this->isAstroid = true;
        }
        if ($this->isAstroid) {
            if (!defined('ASTROID_TEMPLATE_NAME')) {
                define('ASTROID_TEMPLATE_NAME', $this->template);
            }
            Helper::loadLanguage('astroid');
        }
    }

    private function _upload($id)
    {
        $object = $this->_getById($id);

        $this->template = $object->template;
        $this->title = $object->title;
        $this->_set($id);
        $this->version = Helper::templateVersion($this->template);
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getThemeVariables()
    {
        $variables = [];
        $variables['blue'] = $this->params->get('theme_blue', '#007bff');
        $variables['indigo'] = $this->params->get('theme_indigo', '#6610f2');
        $variables['purple'] = $this->params->get('theme_purple', '#6f42c1');
        $variables['pink'] = $this->params->get('theme_pink', '#e83e8c');
        $variables['red'] = $this->params->get('theme_red', '#dc3545');
        $variables['orange'] = $this->params->get('theme_orange', '#fd7e14');
        $variables['yellow'] = $this->params->get('theme_yellow', '#ffc107');
        $variables['green'] = $this->params->get('theme_green', '#28a745');
        $variables['teal'] = $this->params->get('theme_teal', '#20c997');
        $variables['cyan'] = $this->params->get('theme_cyan', '#17a2b8');
        $variables['white'] = $this->params->get('theme_white', '#fff');
        $variables['gray100'] = $this->params->get('theme_gray100', '#f8f9fa');
        $variables['gray600'] = $this->params->get('theme_gray600', '#6c757d');
        $variables['gray800'] = $this->params->get('theme_gray800', '#343a40');

        $primary = $this->params->get('theme_primary', '');
        if (!empty($primary)) {
            $variables['primary'] = ($primary == 'custom' ? $this->params->get('theme_primary_custom', $variables['blue']) : $variables[$primary]);
        }

        $secondary = $this->params->get('theme_secondary', '');
        if (!empty($secondary)) {
            $variables['secondary'] = ($secondary == 'custom' ? $this->params->get('theme_secondary_custom', $variables['gray600']) : $variables[$secondary]);
        }

        $success = $this->params->get('theme_success', '');
        if (!empty($success)) {
            $variables['success'] = ($success == 'custom' ? $this->params->get('theme_success_custom', $variables['green']) : $variables[$success]);
        }

        $info = $this->params->get('theme_info', '');
        if (!empty($info)) {
            $variables['info'] = ($info == 'custom' ? $this->params->get('theme_info_custom', $variables['cyan']) : $variables[$info]);
        }

        $warning = $this->params->get('theme_warning', '');
        if (!empty($warning)) {
            $variables['warning'] = ($warning == 'custom' ? $this->params->get('theme_warning_custom', $variables['yellow']) : $variables[$warning]);
        }

        $danger = $this->params->get('theme_danger', '');
        if (!empty($danger)) {
            $variables['danger'] = ($danger == 'custom' ? $this->params->get('theme_danger_custom', $variables['red']) : $variables[$danger]);
        }

        $light = $this->params->get('theme_light', '');
        if (!empty($light)) {
            $variables['light'] = ($light == 'custom' ? $this->params->get('theme_light_custom', $variables['gray100']) : $variables[$light]);
        }

        $dark = $this->params->get('theme_dark', '');
        if (!empty($dark)) {
            $variables['dark'] = ($dark == 'custom' ? $this->params->get('theme_dark_custom', $variables['gray800']) : $variables[$dark]);
        }

        $variables = $this->_variableOverrides($variables);

        return $variables;
    }

    public function isDefault($id = 0)
    {
        if (!$id) {
            $id = $this->id;
        }
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db
            ->getQuery(true)
            ->select('`home`')
            ->from('#__template_styles')
            ->where('`id`=' . $id);
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result == 1)  {
            return true;
        } else {
            return false;
        }
    }

    protected function _variableOverrides($variables)
    {
        $sass_overrides = $this->params->get('sass_overrides', '{}');
        $sass_overrides = \json_decode($sass_overrides, true);
        if (empty($sass_overrides)) {
            return $variables;
        }

        foreach ($sass_overrides as $sass_override) {
            $variable = $sass_override['variable'];
            if (!empty($variable) && !empty($sass_override['value'])) {
                if (substr($variable, 0, 1) === "$") {
                    $variable = ltrim($variable, '$');
                }
                $variables[$variable] = $sass_override['value'];
            }
        }
        return $variables;
    }

    private function _getById($id)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db
            ->getQuery(true)
            ->select('`template`,`id`,`title`,`params`,`home`')
            ->from('#__template_styles')
            ->where('`id`=' . $id);
        $db->setQuery($query);
        $result = $db->loadObject();

        $params = new Registry();
        $params->loadString($result->params);

        $result->params = $params;
        return $result;
    }

    public function getDefault()
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db
            ->getQuery(true)
            ->select('`template`,`id`,`title`,`params`,`home`')
            ->from('#__template_styles')
            ->where('`home`= 1 AND `client_id` = 0');
        $db->setQuery($query);
        $result = $db->loadObject();

        $params = new Registry();
        $params->loadString($result->params);

        $result->params = $params;
        return $result;
    }

    protected function _getPresets()
    {
        $presets_path = JPATH_SITE . "/media/templates/site/{$this->template}/astroid/presets/";
        if (!file_exists($presets_path)) {
            return [];
        }
        $files = array_filter(glob($presets_path . '/' . '*.json'), 'is_file');
        $presets = [];
        foreach ($files as $file) {
            $json = file_get_contents($file);
            $data = \json_decode($json, true);
            $preset = ['title' => pathinfo($file)['filename'], 'colors' => [], 'preset' => [], 'thumbnail' => '', 'name' => pathinfo($file)['filename']];
            if (isset($data['title']) && !empty($data['title'])) {
                $preset['title'] = Text::_($data['title']);
            }
            if (isset($data['thumbnail']) && !empty($data['thumbnail'])) {
                $preset['thumbnail'] = Uri::root() . 'templates/' . $this->template . '/' . $data['thumbnail'];
            }
            if (isset($data['preset'])) {
                $properties =   [];
                $preset_data=   \json_decode($data['preset'], true);
                foreach ($preset_data as $prop => $value) {
                    if (is_array($value)) {
                        foreach ($value as $subprop => $value2) {
                            if (!empty($value2)) {
                                $properties[$prop][$subprop] = $value2;
                            }
                        }
                    } else {
                        if (!empty($value)) {
                            $properties[$prop] = $value;
                        }
                    }
                }
                $preset['preset'] = $properties;
            }
            $presets[$preset['name']] = $preset;
        }
        return $presets;
    }

    public function getPresets()
    {
        if (self::$presets === null) {
            self::$presets = $this->_getPresets();
        }

        return self::$presets;
    }

    protected function loadParams()
    {
        $path = JPATH_SITE . "/media/templates/site/{$this->template}/params/" . $this->id . '.json';
        $json = file_get_contents($path);
        $this->params->loadString($json, 'JSON');
    }

    public function setPreset($preset)
    {
        $presets = $this->getPresets();
        if (!in_array($preset, array_keys($presets))) {
            return;
        }

        $data = $presets[$preset]['preset'];
        foreach ($data as $attr => $val) {
            if (is_array($val)) {
                $obj = $this->params->get($attr);
                if ($obj == null) {
                    $obj = new \stdClass();
                }
                foreach ($val as $subattr => $subval) {
                    $obj->{$subattr} = $subval;
                }
                $this->params->set($attr, $obj);
            } else {
                $this->params->set($attr, $val);
            }
        }
    }

    public function getFonts()
    {
        if (self::$fonts === null) {
            self::$fonts = Helper\Font::getUploadedFonts($this->template);
        }
        return self::$fonts;
    }

    public function setLog($text, $type = 'success')
    {
    }

    public function getLayout()
    {
        $active = Factory::getApplication()->getMenu()->getActive();
        $layout = '';
        if ($active) {
            $params = $active->getParams();
            $layout = $params->get('astroidlayout', '');
        }
        if (empty($layout)) {
            $layout = $this->params->get('layout', NULL);
        }
        if ($layout === NULL) {
            $value = \file_get_contents(ASTROID_MEDIA . '/json/layouts/default.json');
            $layout = \json_decode($value, true);
        } else {
            if (Helper::isJsonString($layout)) {
                $layout = \json_decode($layout, true);
            } else {
                $layout = Element\Layout::getDataLayout($layout, $this->template, 'main_layouts');
                if (is_string($layout['data'])) {
                    return \json_decode($layout['data'], true);
                } else {
                    return $layout['data'];
                }
            }
        }
        return $layout;
    }

    public function getElementLayout($type)
    {
        $template_path = JPATH_SITE . "/media/templates/site/{$this->template}/astroid/elements";
        if (file_exists(Path::clean($template_path . '/' . $type . '/' . $type . '.php'))) {
            return Path::clean($template_path . '/' . $type . '/' . $type . '.php');
        }

        $plugin_path = JPATH_SITE . "/plugins/astroid";
        if (file_exists($plugin_path)) {
            $plugin_folders = Folder::folders($plugin_path);
            if (count($plugin_folders)) {
                foreach ($plugin_folders as $plugin_folder) {
                    if (file_exists(Path::clean($plugin_path . '/' . $plugin_folder . '/elements/' . $type . '/' . $type . '.php'))) {
                        return Path::clean($plugin_path . '/' . $plugin_folder . '/elements/' . $type . '/' . $type . '.php');
                    }
                }
            }
        }

        if (file_exists(Path::clean(ASTROID_ELEMENTS . '/' . $type . '/' . $type . '.php'))) {
            return Path::clean(ASTROID_ELEMENTS . '/' . $type . '/' . $type . '.php');
        }

        throw new \Exception("Astroid can not found layout for `" . $type . "` element.");
    }

    public function getColorMode() {
        $colorMode = $this->params->get('astroid_color_mode_enable', 0);
        if ($colorMode == 2) {
            return 'dark';
        }

        $colorModeDefault = $this->params->get('astroid_color_mode_default', 'auto');

        if ($colorMode == 1) {
            if ($this->params->get('enable_color_mode_transform', 0)) {
                return $this->params->get('colormode_transform_type', 'light_dark') === 'light_dark' ? 'light' : 'dark';
            }
            $app = Factory::getApplication();
            $clientColor = $app->input->get('color_mode', '', 'ALNUM');
            return !empty($clientColor)
                ? $clientColor
                : $app->input->cookie->get('astroid-color-mode-' . md5($this->template), $colorModeDefault);
        }

        return 'light';
    }

    public function getActualColorMode()
    {
        $colorMode = $this->getColorMode();
        return ($colorMode == 'auto') ? 'light' : $colorMode;
    }
}