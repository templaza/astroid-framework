<?php

/**
 * @package   Astroid Framework
 * @author    TemPlaza https://www.templaza.com
 * @copyright Copyright (C) 201 - 2022 TemPlaza.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

use Astroid\Helper\Overrides;

// no direct access
defined('_JEXEC') or die;
jimport('joomla.filesystem.file');

class astroidInstallerScript
{

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

		if ($type == "update") {
			Overrides::fix();
		}
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
