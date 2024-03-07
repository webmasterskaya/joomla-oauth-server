<?php

namespace Webmasterskaya\Component\OauthServer\Site\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use Webmasterskaya\Component\OauthServer\Site\Entity\User as UserEntity;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\Input\Input;
use Joomla\CMS\Uri\Uri;
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
    private AuthorizationServer $authorizationServer;

    public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
    {
        parent::__construct($config, $factory, $app, $input);

        $this->setupAuthorizationServer();
    }

    /**
     * @return void
     * @throws \Exception
     * @since version
     */
    private function setupAuthorizationServer()
    {
        if (isset($authorizationServer)) {
            return;
        }

        // Init our repositories
        /**
         * @var \Webmasterskaya\Component\OauthServer\Administrator\Model\ClientModel $clientModel
         * @var \Webmasterskaya\Component\OauthServer\Administrator\Model\AccessTokenModel $accessTokenModel
         * @var \Webmasterskaya\Component\OauthServer\Administrator\Model\AuthCodeModel $authCodeModel
         * @var \Webmasterskaya\Component\OauthServer\Administrator\Model\RefreshTokenModel $refreshTokenModel
         */
        $clientModel = $this->factory->createModel('Client', 'Administrator', ['request_ignore' => true]);
        $clientRepository = new ClientRepository($clientModel);

        $accessTokenModel = $this->factory->createModel('AccessToken', 'Administrator', ['request_ignore' => true]);
        $accessTokenRepository = new AccessTokenRepository($accessTokenModel, $clientModel);

        $scopeRepository = new ScopeRepository($clientModel);
        $scopeRepository->setDispatcher($this->getDispatcher());

        $authCodeModel = $this->factory->createModel('AuthCode', 'Administrator', ['request_ignore' => true]);
        $authCodeRepository = new AuthCodeRepository($authCodeModel, $clientModel);

        $refreshTokenModel = $this->factory->createModel('RefreshToken', 'Administrator', ['request_ignore' => true]);
        $refreshTokenRepository = new RefreshTokenRepository($refreshTokenModel, $accessTokenModel);

        $params = ComponentHelper::getParams('com_oauthserver');

        //TODO: Этот код нужно вынести в отдельный хелпер, для генерации закрытого и открытого ключей
        if (false) {
            /** @noinspection PhpUnreachableStatementInspection */
            $key = openssl_pkey_new([
                "digest_alg" => "sha512",
                "private_key_bits" => 4096,
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
            ]);
            openssl_pkey_export($key, $private_key);
            // Extract the public key from $res to $pubKey
            $pub = openssl_pkey_get_details($key);
            $pub = $pub["key"];
        }

        if ($params->get('key_method_paste')) {
            $private_key = $params->get('private_key_raw');
        } else {
            $private_key = $params->get('private_key_path');
        }

        if (!!($private_key_passphrase = $params->get('private_key_passphrase'))) {
            $private_key = new CryptKey($private_key, $private_key_passphrase);
        }

        $encryption_key = $this->app->get('secret');

        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $private_key,
            $encryption_key
        );

        $access_token_ttl = $params->get('access_token_ttl', 'PT1H');

        if (!!$params->get('enable_auth_code_grant', true)) {
            $grant = new AuthCodeGrant(
                $authCodeRepository,
                $refreshTokenRepository,
                new \DateInterval($params->get('auth_code_ttl', 'PT10M')) // authorization codes will expire after 10 minutes
            );

            $grant->setRefreshTokenTTL(new \DateInterval($params->get('refresh_token_ttl', 'P1M')));

            $server->enableGrantType(
                $grant,
                new \DateInterval($access_token_ttl)
            );
        }

        if (!!$params->get('enable_refresh_token_grant', false)) {
            $grant = new RefreshTokenGrant($refreshTokenRepository);

            $grant->setRefreshTokenTTL(new \DateInterval($params->get('refresh_token_ttl', 'P1M')));

            $server->enableGrantType(
                $grant,
                new \DateInterval($access_token_ttl)
            );
        }

        if (!!$params->get('enable_client_credentials_grant', false)) {
            $server->enableGrantType(
                new ClientCredentialsGrant(),
                new \DateInterval($access_token_ttl)
            );
        }

        if (!!$params->get('enable_implicit_grant', false)) {
            $server->enableGrantType(
                new ImplicitGrant(new \DateInterval($access_token_ttl)),
                new \DateInterval($access_token_ttl)
            );
        }

        $this->authorizationServer = $server;
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

        $server = $this->authorizationServer;
        $serverRequest = ServerRequestFactory::fromGlobals();
        $serverResponse = $app->getResponse();

        // Validate the HTTP request and return an AuthorizationRequest object.
        $authRequest = $server->validateAuthorizationRequest($serverRequest);

        // The auth request object can be serialized and saved into a user's session.
        // You will probably want to redirect the user at this point to a login endpoint.

        // Once the user has logged in set the user on the AuthorizationRequest
        $authRequest->setUser(new UserEntity($user)); // an instance of UserEntityInterface

        // At this point you should redirect the user to an authorization page.
        // This form will ask the user to approve the client and the scopes requested.

        // Once the user has approved or denied the client update the status
        // (true = approved, false = denied)
        $authRequest->setAuthorizationApproved(true);

        $app->setResponse($server->completeAuthorizationRequest($authRequest, $serverResponse));
    }

    /**
     * @return void
     * @throws \Exception
     * @since version
     */
    public function token(): void
    {
        $app = $this->app;
        $server = $this->authorizationServer;
        $serverRequest = ServerRequestFactory::fromGlobals();
        $serverResponse = $app->getResponse();
        $app->setResponse($server->respondToAccessTokenRequest($serverRequest, $serverResponse));
    }
}