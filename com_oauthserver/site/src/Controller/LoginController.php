<?php

namespace Webmasterskaya\Component\OauthServer\Site\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\Input\Input;
use Joomla\CMS\Uri\Uri;
use Webmasterskaya\Component\OauthServer\Site\Repository\ClientRepository;

class LoginController extends BaseController
{
    public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
    {
        parent::__construct($config, $factory, $app, $input);
    }

    public function authorize()
    {
        $app = $this->app;
        $user = $app->getIdentity();

        if (!$user->id) {
            $return = http_build_query(['return' => base64_encode(Uri::getInstance()->toString(['scheme', 'user', 'pass', 'host', 'port', 'path']))]);
            $this->app->setUserState('oauthserver.login.authorize.request', Uri::getInstance()->getQuery(true));
            $this->app->enqueueMessage('Необходимо авторизоваться!');
            $this->app->redirect(Route::_('index.php?option=com_users&view=login&' . $return));
        }

        $clientRepository = new ClientRepository($this->factory);
        var_dump($this->app->getUserState('oauthserver.login.authorize.request'));
        die();
    }
}