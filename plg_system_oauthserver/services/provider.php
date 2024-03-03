<?php

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Webmasterskaya\Plugin\System\OauthServer\Extension\Plugin;

\defined('_JEXEC') or die;

return new class implements ServiceProviderInterface {

    /**
     * @param \Joomla\DI\Container $container
     * @return void
     * @since version
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $dispatcher = $container->get(DispatcherInterface::class);
                $plugin     = new Plugin(
                    $dispatcher,
                    (array) PluginHelper::getPlugin('system', 'oauthserver')
                );
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};