<?php

/**
 * @package   Astroid Framework
 * @author    TemPlaza https://www.templaza.com
 * @copyright Copyright (C) 201 - 2022 TemPlaza.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

use Astroid\Helper\Overrides;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;

// no direct access
defined('_JEXEC') or die;
jimport('joomla.filesystem.file');

class astroidInstallerScript
{
    public static $rename = [
        'com_content/form',
        'layouts/joomla/form',
        'layouts/joomla/content/icons/email.php',
        'layouts/joomla/content/icons/print_popup.php',
        'layouts/joomla/content/icons/print_screen.php'
    ];
	/**
	 *
	 * Function to run before installing the component
	 */
	public function preflight($type, $parent)
	{
		$plugin_dir = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'plugins' . '/';
		$plugins = array_filter(glob($plugin_dir . '*'), 'is_dir');
		foreach ($plugins as $plugin) {
			if ($type == "uninstall") {
				$this->uninstallPlugin($plugin, $plugin_dir);
			}
		}

		if (JVERSION >= 4) {
			$module_dir = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'modules' . '/';
			$modules = array_filter(glob($module_dir . '*'), 'is_dir');
			foreach ($modules as $module) {
				if ($type == "uninstall") {
					$this->uninstallModule($module, $module_dir);
				}
			}
		}
	}

	/**
	 *
	 * Function to run after installing the component
	 */
	public function postflight($type, $parent)
	{
		$plugin_dir = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'plugins' . '/';
		$plugins = array_filter(glob($plugin_dir . '*'), 'is_dir');
		foreach ($plugins as $plugin) {
			if ($type == "install" || $type == "update") {
				$this->installPlugin($plugin, $plugin_dir);
			}
		}

		if (JVERSION >= 4) {
			$module_dir = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'modules' . '/';
			$modules = array_filter(glob($module_dir . '*'), 'is_dir');
			foreach ($modules as $module) {
				if ($type == "install" || $type == "update") {
					$this->installModule($module, $module_dir);
				}
			}
		}

        $templates = self::getAstroidTemplates(true);
        $templates = array_unique(array_column($templates, 'template'));

        foreach ($templates as $template) {
            $path = JPATH_ROOT . '/templates/' . $template . '/html/';
            foreach (self::$rename as $file) {
                if (is_dir($path . $file)) {
                    Folder::move($path . $file, $path . (str_replace(basename($file), basename($file) . '-' . date('Y-m-d'), $file)));
                } else if (file_exists($path . $file)) {
                    File::move($path . $file, $path . (str_replace(basename($file), basename($file, '.php') . '-' . date('Y-m-d') . '.php', $file)));
                }
            }
            if (JVERSION >= 4) {
                if (is_dir($path . 'com_config')) {
                    Folder::move($path . 'com_config', $path . (str_replace(basename('com_config'), basename('com_config') . '-' . date('Y-m-d'), 'com_config')));
                }
                if (is_dir($path . 'layouts/joomla/editors')) {
                    Folder::delete($path . 'layouts/joomla/editors');
                }
                //Fix module issue from Joomla 4.2
                if (file_exists(JPATH_LIBRARIES.'/astroid/framework/layouts/modules/mod_login/default.php') && file_exists($path.'mod_login/default.php')) {
                    File::copy(JPATH_LIBRARIES.'/astroid/framework/layouts/modules/mod_login/default.php', $path.'mod_login/default.php');
                }
            }

            //Fix alert issue.
            if (file_exists(JPATH_LIBRARIES.'/astroid/framework/layouts/system/message.php') && file_exists($path.'layouts/joomla/system/message.php')) {
                File::copy(JPATH_LIBRARIES.'/astroid/framework/layouts/system/message.php', $path.'layouts/joomla/system/message.php');
            }
        }
	}

    public static function getAstroidTemplates($full = false)
    {
        $db = \JFactory::getDbo();
        $query = $db
            ->getQuery(true)
            ->select('s.id, s.template, s.title')
            ->from('#__template_styles as s')
            ->where('s.client_id = 0')
            ->where('e.enabled = 1')
            ->leftJoin('#__extensions as e ON e.element=s.template AND e.type=' . $db->quote('template') . ' AND e.client_id=s.client_id');

        $db->setQuery($query);
        $templates = $db->loadObjectList();
        $return = [];

        foreach ($templates as $template) {
            $astroidTemplate = self::isAstroidTemplate($template->template);
            if ($astroidTemplate !== false) {
                if (!$full) {
                    $return[] = $template->id;
                } else {
                    $return[] = $template;
                }
            }
        }
        return $return;
    }

    public static function isAstroidTemplate($template)
    {
        if (!file_exists(JPATH_SITE . "/templates/{$template}/templateDetails.xml")) {
            return false;
        }
        $xml = self::getXML(JPATH_SITE . "/templates/{$template}/templateDetails.xml");
        $version    =   $xml->version;
        $isAstroid  =   $xml->config->fields->fieldset->field['type'];
        $return = false;
        $item = array();
        if ((string)$isAstroid === 'astroidmanagerlink') {
            $item['version'] = $version;
            $return = $item;
        }
        return $return;
    }

    public static function getXml($url)
    {
        if (!file_exists($url)) return;
        return simplexml_load_file($url, 'SimpleXMLElement');
    }

	public function installPlugin($plugin, $plugin_dir)
	{
		$db = JFactory::getDbo();
		$plugin_name = str_replace($plugin_dir, '', $plugin);

		$installer = new JInstaller;
		$installer->install($plugin);

		$query = $db->getQuery(true);
		$query->update('#__extensions');
		$query->set($db->quoteName('enabled') . ' = 1');
		$query->where($db->quoteName('element') . ' = ' . $db->quote($plugin_name));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$db->execute();
		return true;
	}

	public function installModule($module, $module_dir)
	{
		$db = JFactory::getDbo();
		$module_name = str_replace($module_dir, '', $module);

		$installer = new JInstaller;
		$installer->install($module);

		$query = $db->getQuery(true);
		$query->update('#__extensions');
		$query->set($db->quoteName('enabled') . ' = 1');
		$query->where($db->quoteName('element') . ' = ' . $db->quote($module_name));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('module'));
		$db->setQuery($query);
		$db->execute();

		$query = $db->getQuery(true);
		$query->update('#__modules');
		$query->set($db->quoteName('published') . ' = 1');
		$query->set($db->quoteName('position') . ' = ' . $db->quote('status'));
		$query->set($db->quoteName('params') . ' = ' . $db->quote('{"layout":"_:default","moduleclass_sfx":"","style":"0","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":""}'));
		$query->where($db->quoteName('module') . ' = ' . $db->quote($module_name));
		$db->setQuery($query);
		$db->execute();

		// Retrieve ID
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__modules'));
		$query->where($db->quoteName('module') . ' = ' . $db->quote($module_name));
		$db->setQuery($query);
		$id = (int) $db->loadResult();

		if ($id) {
			$query = $db->getQuery(true);
			$query->select($db->quoteName('moduleid'));
			$query->from($db->quoteName('#__modules_menu'));
			$query->where($db->quoteName('moduleid') . ' = ' . $id);
			$db->setQuery($query);
			if (!$db->loadResult()) {
				$db->getQuery(true);
				$db->setQuery("INSERT INTO #__modules_menu (`moduleid`,`menuid`) VALUES (".$id.", 0)");
				$db->execute();
			}
		}
		return true;
	}

	public function uninstallPlugin($plugin, $plugin_dir)
	{
		$db = JFactory::getDbo();
		$plugin_name = str_replace($plugin_dir, '', $plugin);
		$query = $db->getQuery(true);
		$query->update('#__extensions');
		$query->set($db->quoteName('enabled') . ' = 0');
		$query->where($db->quoteName('element') . ' = ' . $db->quote($plugin_name));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$db->execute();
		return true;
	}

	public function uninstallModule($module, $module_dir)
	{
		$db = JFactory::getDbo();
		$module_name = str_replace($module_dir, '', $module);
		$query = $db->getQuery(true);
		$query->update('#__extensions');
		$query->set($db->quoteName('enabled') . ' = 0');
		$query->where($db->quoteName('element') . ' = ' . $db->quote($module_name));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('module'));
		$db->setQuery($query);
		$db->execute();
		return true;
	}
}
