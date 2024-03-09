<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Helper;

\defined('_JEXEC') or die;

abstract class ComponentHelper
{
    /**
     * Creates a Composer autoloader, unregisters it, and registers it again at the end of the autoloader stack.
     *
     * @return void
     * @since version
     */
    public static function registerComponentDependencies(): void
    {
        static $registered;

        if (!isset($registered))
        {
            /** @var \Composer\Autoload\ClassLoader $loader */
            $loader = require JPATH_ADMINISTRATOR . '/components/com_oauthserver/vendor/autoload.php';

            $loader->unregister();

            $loader->register();
        }
    }
}
