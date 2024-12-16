<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\Captcha\AstroidCaptcha\Extension\AstroidCaptcha;
use Joomla\CMS\Captcha\Google\HttpBridgePostRequestMethod;

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
                $plugin     = new AstroidCaptcha(
                    $dispatcher,
                    (array) PluginHelper::getPlugin('captcha', 'astroidcaptcha'),
                    new HttpBridgePostRequestMethod()
                );
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};