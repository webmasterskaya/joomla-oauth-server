<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Extension;

use Joomla\CMS\Association\AssociationServiceInterface;
use Joomla\CMS\Association\AssociationServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;
use Webmasterskaya\Component\OauthServer\Administrator\Helper\ComponentHelper;

\defined('_JEXEC') or die;

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
     * @param   \Psr\Container\ContainerInterface  $container  The container
     *
     * @throws \Exception
     * @since version
     */
    public function boot(ContainerInterface $container): void
    {
        ComponentHelper::registerComponentDependencies();
    }
}
