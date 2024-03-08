<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Helper;

abstract class ComponentHelper
{
    public static function registerComponentDependencies(): void
    {
        static $registered;

        if (!isset($registered)) {
            /** @var \Composer\Autoload\ClassLoader $loader */
            $loader = require JPATH_ADMINISTRATOR . '/components/com_oauthserver/vendor/autoload.php';

            $loader->unregister();

            if (spl_autoload_register([new \Joomla\CMS\Autoload\ClassLoader($loader), 'loadClass'])) {
                $registered = true;
            }
        }
    }
}