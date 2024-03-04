<?php

use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Webmasterskaya\Component\OauthServer\Administrator\Extension\Component;

\defined('_JEXEC') or die;

require_once JPATH_LIBRARIES . '/lib_oauthserver/vendor/autoload.php';

return new class implements ServiceProviderInterface {

    public function register(Container $container): void
    {
        $container->registerServiceProvider(new MVCFactory('\\Webmasterskaya\\Component\\OauthServer'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\Webmasterskaya\\Component\\OauthServer'));
        $container->registerServiceProvider(new RouterFactory('\\Webmasterskaya\\Component\\OauthServer'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new Component($container->get(ComponentDispatcherFactoryInterface::class));

                $component->setRegistry($container->get(Registry::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setRouterFactory($container->get(RouterFactoryInterface::class));

                return $component;
            }
        );
    }
};