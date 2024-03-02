<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Extension;

use Joomla\CMS\Association\AssociationServiceInterface;
use Joomla\CMS\Association\AssociationServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;

\defined('JPATH_PLATFORM') or die;

class Component extends MVCComponent implements
    BootableExtensionInterface, AssociationServiceInterface, RouterServiceInterface
{
    use AssociationServiceTrait;
    use HTMLRegistryAwareTrait;
    use RouterServiceTrait;

    /**
     * Booting the extension. This is the function to set up the environment of the extension like
     * registering new class loaders, etc.
     *
     *
     * @param \Psr\Container\ContainerInterface $container The container
     *
     * @throws \Exception
     * @since 1.0.0
     */
    public function boot(ContainerInterface $container): void
    {
    }
}