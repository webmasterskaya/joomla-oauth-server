<?php

namespace Webmasterskaya\Plugin\System\OauthServer\Extension;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\SubscriberInterface;

class Plugin extends CMSPlugin implements SubscriberInterface
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = false;

    /**
     * The application object
     *
     * @var    CMSApplicationInterface
     *
     * @since  4.2.0
     */
    private $application;

    public static function getSubscribedEvents(): array
    {
        return ['onAfterInitialise' => 'onAfterInitialise'];
    }

    public function onAfterInitialise(): void
    {
        if (!$this->app->isClient('site')) {
            return;
        }

        $uri = Uri::getInstance();
        $path = $uri->getPath();

        // Адрес сервера аутентификации должен быть статичным,
        // чтобы гарантировать 100% доступность сервера
        if (str_starts_with($path, '/login/oauth/') === false) {
            return;
        }

        $parts = explode('/', $path);

        if(empty($parts[2])){
            // TODO: Проверить, как стандартный роутер обработает этот вопрос и как отреагируют приложения на 404 от Joomla
            return;
        }

        $option = 'com_oauthserver';
        $task = $parts[2];

        // TODO: Ставим в input option, task и view и запускаем компонент com_oauthserver
    }
}