<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
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
}
