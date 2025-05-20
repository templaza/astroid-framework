<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

\defined('_JEXEC') or die;
require_once JPATH_ROOT . DIRECTORY_SEPARATOR .'libraries'
    .DIRECTORY_SEPARATOR.'astroid'
    .DIRECTORY_SEPARATOR.'framework'
    .DIRECTORY_SEPARATOR.'library'
    .DIRECTORY_SEPARATOR.'vendor'
    .DIRECTORY_SEPARATOR.'autoload.php';
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\System\Astroid\Extension\AstroidPlugin;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.4.0
     */
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $dispatcher = $container->get(DispatcherInterface::class);
                $plugin     = new AstroidPlugin(
                    $dispatcher,
                    (array) PluginHelper::getPlugin('system', 'astroid')
                );
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
