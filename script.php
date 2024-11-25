<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

// no direct access
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Exception\FilesystemException;
use Astroid\Helper\Overrides;
use Joomla\CMS\Installer\Installer;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

return new class () implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container->set(
            InstallerScriptInterface::class,
            new class (
                $container->get(AdministratorApplication::class),
                $container->get(DatabaseInterface::class)
            ) implements InstallerScriptInterface {
                private AdministratorApplication $app;
                private DatabaseInterface $db;

                public function __construct(AdministratorApplication $app, DatabaseInterface $db)
                {
                    $this->app = $app;
                    $this->db  = $db;
                }

                public function install(InstallerAdapter $parent): bool
                {
                    $this->app->enqueueMessage('Astroid Library has been successful installed.');

                    return true;
                }

                public function update(InstallerAdapter $parent): bool
                {
                    $this->app->enqueueMessage('Astroid Library has been successful updated.');

                    return true;
                }

                public function uninstall(InstallerAdapter $parent): bool
                {
                    $this->app->enqueueMessage('Astroid Library has been successful uninstalled.');

                    return true;
                }

                public function preflight(string $type, InstallerAdapter $parent): bool
                {
                    $plugin_dir = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'plugins' . '/';
                    $plugins = array_filter(glob($plugin_dir . '*'), 'is_dir');
                    foreach ($plugins as $plugin) {
                        if ($type == "uninstall") {
                            $this->uninstallPlugin($plugin, $plugin_dir);
                        }
                    }

                    $module_dir = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'modules' . '/';
                    $modules = array_filter(glob($module_dir . '*'), 'is_dir');
                    foreach ($modules as $module) {
                        if ($type == "uninstall") {
                            $this->uninstallModule($module, $module_dir);
                        }
                    }
                    return true;
                }

                public function postflight(string $type, InstallerAdapter $parent): bool
                {
                    $plugin_dir = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'plugins' . '/';
                    $plugins = array_filter(glob($plugin_dir . '*'), 'is_dir');
                    foreach ($plugins as $plugin) {
                        if ($type == "install" || $type == "update") {
                            $this->installPlugin($plugin, $plugin_dir);
                        }
                    }

                    $module_dir = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'modules' . '/';
                    $modules = array_filter(glob($module_dir . '*'), 'is_dir');
                    foreach ($modules as $module) {
                        if ($type == "install" || $type == "update") {
                            $this->installModule($module, $module_dir);
                        }
                    }

                    if ($type == "update") {
                        Overrides::fix();
                    }

                    return true;
                }

                private function installPlugin($plugin, $plugin_dir)
                {
                    $db = $this->db;
                    $plugin_name = str_replace($plugin_dir, '', $plugin);

                    $installer = new Installer;
                    if ($installer->install($plugin)) {
                        $this->app->enqueueMessage('Astroid Plugin has been successfully installed.');
                    } else {
                        $this->app->enqueueMessage('Astroid Plugin has been failed to install.');
                    }

                    $query = $db->getQuery(true);
                    $query->update('#__extensions');
                    $query->set($db->quoteName('enabled') . ' = 1');
                    $query->where($db->quoteName('element') . ' = ' . $db->quote($plugin_name));
                    $query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
                    $db->setQuery($query);
                    $db->execute();
                    return true;
                }

                private function installModule($module, $module_dir)
                {
                    $db = $this->db;
                    $module_name = str_replace($module_dir, '', $module);

                    $installer = new Installer;
                    if ($installer->install($module)) {
                        $this->app->enqueueMessage(Text::_(strtoupper($module_name)) . ' Module has been successfully installed.');
                    } else {
                        $this->app->enqueueMessage(Text::_(strtoupper($module_name)) . ' Module has been failed to install.');
                    }

                    $query = $db->getQuery(true);
                    $query->update('#__extensions');
                    $query->set($db->quoteName('enabled') . ' = 1');
                    $query->where($db->quoteName('element') . ' = ' . $db->quote($module_name));
                    $query->where($db->quoteName('type') . ' = ' . $db->quote('module'));
                    $db->setQuery($query);
                    $db->execute();

                    if ($module_name === 'mod_astroid_clear_cache') {
                        $query = $db->getQuery(true);
                        $query->update('#__modules');
                        $query->set($db->quoteName('published') . ' = 1');
                        $query->set($db->quoteName('position') . ' = ' . $db->quote('status'));
                        $query->set($db->quoteName('params') . ' = ' . $db->quote('{"layout":"_:default","moduleclass_sfx":"","style":"0","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":""}'));
                        $query->where($db->quoteName('module') . ' = ' . $db->quote($module_name));
                        $db->setQuery($query);
                        $db->execute();
                    }

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

                private function uninstallPlugin($plugin, $plugin_dir)
                {
                    try {
                        $db = $this->db;
                        $plugin_name = str_replace($plugin_dir, '', $plugin);
                        $query = $db->getQuery(true);
                        $query->update('#__extensions');
                        $query->set($db->quoteName('enabled') . ' = 0');
                        $query->where($db->quoteName('element') . ' = ' . $db->quote($plugin_name));
                        $query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
                        $db->setQuery($query);
                        if ($db->execute()) {
                            $this->app->enqueueMessage('Astroid Plugins has been successfully uninstalled.');
                        } else {
                            $this->app->enqueueMessage('Astroid Plugins has been failed to uninstall.');
                        }
                    } catch (\Exception $e) {
                        echo $e->getMessage() . '<br>';
                    }

                    return true;
                }

                private function uninstallModule($module, $module_dir)
                {
                    try {
                        $db = $this->db;
                        $module_name = str_replace($module_dir, '', $module);
                        $query = $db->getQuery(true);
                        $query->update('#__extensions');
                        $query->set($db->quoteName('enabled') . ' = 0');
                        $query->where($db->quoteName('element') . ' = ' . $db->quote($module_name));
                        $query->where($db->quoteName('type') . ' = ' . $db->quote('module'));
                        $db->setQuery($query);
                        if ($db->execute()) {
                            $this->app->enqueueMessage('Astroid Modules has been successfully uninstalled.');
                        } else {
                            $this->app->enqueueMessage('Astroid Modules has been failed to uninstall.');
                        }
                    } catch (\Exception $e) {
                        echo $e->getMessage() . '<br>';
                    }
                    return true;
                }
            }
        );
    }
};