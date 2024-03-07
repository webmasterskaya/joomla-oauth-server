<?php

namespace Webmasterskaya\Component\OauthServer\Site\Controller;

use Joomla\CMS\Application\CMSApplication;
use Webmasterskaya\Component\OauthServer\Site\Entity\User as UserEntity;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\Input\Input;
use Joomla\CMS\Uri\Uri;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use Webmasterskaya\Component\OauthServer\Site\Repository\AccessTokenRepository;
use Webmasterskaya\Component\OauthServer\Site\Repository\AuthCodeRepository;
use Webmasterskaya\Component\OauthServer\Site\Repository\ClientRepository;
use Webmasterskaya\Component\OauthServer\Site\Repository\RefreshTokenRepository;
use Webmasterskaya\Component\OauthServer\Site\Repository\ScopeRepository;

class LoginController extends BaseController
{
    public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
    {
        parent::__construct($config, $factory, $app, $input);
    }

    /**
     * @return void
     * @throws \Exception
     * @since version
     */
    public function authorize(): void
    {
        $app = $this->app;
        $user = $app->getIdentity();
        $uri = Uri::getInstance();

        if (!$user->id) {
            $return = http_build_query(['return' => base64_encode($uri->toString(['scheme', 'user', 'pass', 'host', 'port', 'path']))]);
            $this->app->setUserState('oauthserver.login.authorize.request', $uri->getQuery(true));
            $this->app->enqueueMessage('Необходимо авторизоваться!');
            $this->app->redirect(Route::_('index.php?option=com_users&view=login&' . $return));
        }

        $state_request = $this->app->getUserState('oauthserver.login.authorize.request');
        if (!empty($state_request) && empty($uri->getQuery(true))) {
            foreach ($state_request as $k => $v) {
                $uri->setVar($k, $v);
            }
        }
        $this->app->setUserState('oauthserver.login.authorize.request', []);

        /** @var \Webmasterskaya\Component\OauthServer\Administrator\Model\ClientModel $clientModel */
        $clientModel = $this->factory->createModel('Client', 'Administrator', ['request_ignore' => true]);
        $clientRepository = new ClientRepository($clientModel);

        /** @var \Webmasterskaya\Component\OauthServer\Administrator\Model\AccessTokenModel $accessTokenModel */
        $accessTokenModel = $this->factory->createModel('AccessToken', 'Administrator', ['request_ignore' => true]);
        $accessTokenRepository = new AccessTokenRepository($accessTokenModel, $clientModel);

        $scopeRepository = new ScopeRepository($clientModel);
        $scopeRepository->setDispatcher($this->getDispatcher());

        /** @var \Webmasterskaya\Component\OauthServer\Administrator\Model\AuthCodeModel $authCodeModel */
        $authCodeModel = $this->factory->createModel('AuthCode', 'Administrator', ['request_ignore' => true]);
        $authCodeRepository = new AuthCodeRepository($authCodeModel, $clientModel);

        /** @var \Webmasterskaya\Component\OauthServer\Administrator\Model\RefreshTokenModel $refreshTokenModel */
        $refreshTokenModel = $this->factory->createModel('RefreshToken', 'Administrator', ['request_ignore' => true]);
        $refreshTokenRepository = new RefreshTokenRepository($refreshTokenModel, $accessTokenModel);

        $key = openssl_pkey_new([
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);

        $ppk = '';
        openssl_pkey_export($key, $ppk);

        // Extract the public key from $res to $pubKey
//        $pub = openssl_pkey_get_details($key);
//        $pub = $pub["key"];

//        var_dump($this->app->getUserState('oauthserver.login.authorize.request'));

        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $ppk,
            $this->app->get('secret')
        );

        $grant = new AuthCodeGrant(
            $authCodeRepository,
            $refreshTokenRepository,
            new \DateInterval('PT10M') // authorization codes will expire after 10 minutes
        );

        $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month

        $server->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );

        $serverRequest = ServerRequestFactory::fromGlobals();
        $serverResponse = $this->app->getResponse();

//        var_dump($serverRequest->getQueryParams()); die();

        $authRequest = $server->validateAuthorizationRequest($serverRequest);
        $authRequest->setUser(new UserEntity($user));
        $authRequest->setAuthorizationApproved(true);

        $this->app->setResponse($server->completeAuthorizationRequest($authRequest, $serverResponse));

        return;

        echo "<pre>";

        var_dump();

        die();
    }
}