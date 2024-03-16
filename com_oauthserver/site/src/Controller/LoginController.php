<?php
/**
 * @package         Joomla.Site
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Site\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Input\Input;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\RequestEvent as LeagueRequestEvent;
use League\OAuth2\Server\ResourceServer;
use Webmasterskaya\Component\OauthServer\Administrator\Event\RequestAccessTokenEvent;
use Webmasterskaya\Component\OauthServer\Administrator\Event\RequestEvent;
use Webmasterskaya\Component\OauthServer\Administrator\Event\RequestRefreshTokenEvent;
use Webmasterskaya\Component\OauthServer\Administrator\Event\ResolveTokenRequestEvent;
use Webmasterskaya\Component\OauthServer\Administrator\Model\AccessTokenModel;
use Webmasterskaya\Component\OauthServer\Administrator\Model\AuthCodeModel;
use Webmasterskaya\Component\OauthServer\Administrator\Model\ClientModel;
use Webmasterskaya\Component\OauthServer\Administrator\Model\RefreshTokenModel;
use Webmasterskaya\Component\OauthServer\Site\Entity\User;
use Webmasterskaya\Component\OauthServer\Site\Repository\AccessTokenRepository;
use Webmasterskaya\Component\OauthServer\Site\Repository\AuthCodeRepository;
use Webmasterskaya\Component\OauthServer\Site\Repository\ClientRepository;
use Webmasterskaya\Component\OauthServer\Site\Repository\RefreshTokenRepository;
use Webmasterskaya\Component\OauthServer\Site\Repository\ScopeRepository;

\defined('_JEXEC') or die;

class LoginController extends BaseController
{
    private AuthorizationServer $authorizationServer;

    /**
     * @throws \Exception
     * @since version
     */
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
        if (isset($authorizationServer))
        {
            return;
        }

        // Init our repositories
        /**
         * @var ClientModel       $clientModel
         * @var AccessTokenModel  $accessTokenModel
         * @var AuthCodeModel     $authCodeModel
         * @var RefreshTokenModel $refreshTokenModel
         */
        $clientModel      = $this->factory->createModel('Client', 'Administrator', ['request_ignore' => true]);
        $clientRepository = new ClientRepository($clientModel);

        $accessTokenModel      = $this->factory->createModel('AccessToken', 'Administrator', ['request_ignore' => true]);
        $accessTokenRepository = new AccessTokenRepository($accessTokenModel, $clientModel);

        $scopeRepository = new ScopeRepository($clientModel);
        $scopeRepository->setDispatcher($this->getDispatcher());

        $authCodeModel      = $this->factory->createModel('AuthCode', 'Administrator', ['request_ignore' => true]);
        $authCodeRepository = new AuthCodeRepository($authCodeModel, $clientModel);

        $refreshTokenModel      = $this->factory->createModel('RefreshToken', 'Administrator', ['request_ignore' => true]);
        $refreshTokenRepository = new RefreshTokenRepository($refreshTokenModel, $accessTokenModel);

        $params = ComponentHelper::getParams('com_oauthserver');

        if ($params->get('key_method_paste'))
        {
            $private_key = $params->get('private_key_raw');
        }
        else
        {
            $private_key = $params->get('private_key_path');
        }

        if (!!($private_key_passphrase = $params->get('private_key_passphrase')))
        {
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

        if (!!$params->get('enable_auth_code_grant', true))
        {
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

        if (!!$params->get('enable_refresh_token_grant', false))
        {
            $grant = new RefreshTokenGrant($refreshTokenRepository);

            $grant->setRefreshTokenTTL(new \DateInterval($params->get('refresh_token_ttl', 'P1M')));

            $server->enableGrantType(
                $grant,
                new \DateInterval($access_token_ttl)
            );
        }

        if (!!$params->get('enable_client_credentials_grant', false))
        {
            $server->enableGrantType(
                new ClientCredentialsGrant(),
                new \DateInterval($access_token_ttl)
            );
        }

        if (!!$params->get('enable_implicit_grant', false))
        {
            $server->enableGrantType(
                new ImplicitGrant(new \DateInterval($access_token_ttl)),
                new \DateInterval($access_token_ttl)
            );
        }

        $server->getEmitter()
            ->addListener(
                LeagueRequestEvent::USER_AUTHENTICATION_FAILED,
                function (LeagueRequestEvent $event) {
                    $name = 'onOauthUserAuthenticationFailed';
                    $this->getDispatcher()->dispatch($name, new RequestEvent(
                        $name,
                        ['request' => $event->getRequest()]
                    ));
                }
            )->addListener(
                LeagueRequestEvent::CLIENT_AUTHENTICATION_FAILED,
                function (LeagueRequestEvent $event) {
                    $name = 'onOauthClientAuthenticationFailed';
                    $this->getDispatcher()->dispatch($name, new RequestEvent(
                        $name,
                        ['request' => $event->getRequest()]
                    ));
                }
            )->addListener(
                LeagueRequestEvent::REFRESH_TOKEN_CLIENT_FAILED,
                function (LeagueRequestEvent $event) {
                    $name = 'onOauthRefreshTokenClientFailed';
                    $this->getDispatcher()->dispatch($name, new RequestEvent(
                        $name,
                        ['request' => $event->getRequest()]
                    ));
                }
            )->addListener(
                LeagueRequestEvent::REFRESH_TOKEN_ISSUED,
                function (LeagueRequestEvent $event) {
                    /** @var \League\OAuth2\Server\RequestRefreshTokenEvent $event */
                    $name = 'onOauthRefreshTokenIssued';
                    $this->getDispatcher()->dispatch($name, new RequestRefreshTokenEvent(
                        $name,
                        [
                            'request'      => $event->getRequest(),
                            'refreshToken' => $event->getRefreshToken()
                        ]
                    ));
                }
            )->addListener(
                LeagueRequestEvent::ACCESS_TOKEN_ISSUED,
                function (LeagueRequestEvent $event) {
                    /** @var \League\OAuth2\Server\RequestAccessTokenEvent $event */
                    $name = 'onOauthAccessTokenIssued';
                    $this->getDispatcher()->dispatch($name, new RequestAccessTokenEvent(
                        $name,
                        [
                            'request'     => $event->getRequest(),
                            'accessToken' => $event->getAccessToken()
                        ]
                    ));
                }
            );

        $this->authorizationServer = $server;
    }

    /**
     * @return LoginController
     * @throws OAuthServerException
     * @since version
     */
    public function authorize(): static
    {
        $app          = $this->app;
        $input        = $app->getInput();
        $user         = $app->getIdentity();
        $uri          = Uri::getInstance();
        $state_prefix = 'oauthserver.login.authorize.request';

        // Create PSR-7 Request object and store all query params in user state, to use it after user login is it required.
        $serverRequest = (new ServerRequest([], [], $app->getUserState("$state_prefix.uri", (string) $uri)))
            ->withQueryParams([
                'response_type'         => $app->getUserStateFromRequest("$state_prefix.response_type", 'response_type'),
                'client_id'             => $app->getUserStateFromRequest("$state_prefix.client_id", 'client_id', $input->server->get('PHP_AUTH_USER')),
                'redirect_uri'          => $app->getUserStateFromRequest("$state_prefix.redirect_uri", 'redirect_uri'),
                'scope'                 => $app->getUserStateFromRequest("$state_prefix.scope", 'scope'),
                'code_challenge'        => $app->getUserStateFromRequest("$state_prefix.code_challenge", 'code_challenge'),
                'code_challenge_method' => $app->getUserStateFromRequest("$state_prefix.code_challenge_method", 'code_challenge_method', 'plain'),
            ]);

        if (!$user->id)
        {
            if ($app->getUserState("$state_prefix.uri") === null)
            {
                $app->setUserState("$state_prefix.uri", (string) $uri);
            }

            // Build the cleared current uri and encode to pass it to the login form as a callback uri.
            $return   = http_build_query(['return' => base64_encode($uri->toString(['scheme', 'user', 'pass', 'host', 'port', 'path']))]);
            $redirect = Route::_('index.php?option=com_users&view=login&' . $return);

            // The current page is not tied to any menu item, so the main page item id will be added to the route. It needs to be removed.
            $redirect = preg_replace('/((&|&amp;)itemid=\d+)/i', '', $redirect);

            $app->enqueueMessage('Необходимо авторизоваться!');
            $app->redirect($redirect);

            return $this;
        }

        // Clean user state after login checks
        $app->setUserState($state_prefix, null);

        $server = $this->authorizationServer;

        // Validate the HTTP request and return an AuthorizationRequest object.
        $authRequest = $server->validateAuthorizationRequest($serverRequest);

        // The auth request object can be serialized and saved into a user's session.
        // You will probably want to redirect the user at this point to a login endpoint.

        // Once the user has logged in set the user on the AuthorizationRequest
        $authRequest->setUser(new User($user)); // an instance of UserEntityInterface

        // At this point you should redirect the user to an authorization page.
        // This form will ask the user to approve the client and the scopes requested.

        // Once the user has approved or denied the client update the status
        // (true = approved, false = denied)
        $authRequest->setAuthorizationApproved(true);

        $app->setResponse($server->completeAuthorizationRequest($authRequest, $app->getResponse()));

        return $this;
    }

    /**
     * @return LoginController
     * @throws \Exception
     * @since version
     */
    public function token(): static
    {
        $server        = $this->authorizationServer;
        $serverRequest = ServerRequestFactory::fromGlobals();
        $response      = $this->app->getResponse();
        $response      = $server->respondToAccessTokenRequest($serverRequest, $response);
        $event         = new ResolveTokenRequestEvent('onResolveTokenRequest', ['response' => $response]);

        $this->getDispatcher()->dispatch($event->getName(), $event);
        $this->app->setResponse($event->getArgument('response'));

        echo $this->app->getResponse()->getBody();

        return $this;
    }

    /**
     * @throws OAuthServerException
     * @throws \Exception
     * @since  version
     */
    public function profile(): LoginController
    {
        /**
         * @var ClientModel      $clientModel
         * @var AccessTokenModel $accessTokenModel
         */
        $clientModel           = $this->factory->createModel('Client', 'Administrator', ['request_ignore' => true]);
        $accessTokenModel      = $this->factory->createModel('AccessToken', 'Administrator', ['request_ignore' => true]);
        $accessTokenRepository = new AccessTokenRepository($accessTokenModel, $clientModel);
        $params                = ComponentHelper::getParams('com_oauthserver');

        if ($params->get('key_method_paste'))
        {
            $public_key = $params->get('public_key_raw');
        }
        else
        {
            $public_key = $params->get('public_key_path');
        }

        $server  = new ResourceServer($accessTokenRepository, $public_key);
        $request = ServerRequestFactory::fromGlobals();
        $request = $server->validateAuthenticatedRequest($request);
        /** @var \Joomla\CMS\User\User $user */
        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($request->getAttribute('oauth_user_id'));

        $this->app->loadIdentity($user);

        $data = [
            'full_name' => $user->name,
            'email'     => $user->email,
            'login'     => $user->username,
            'id'        => $user->id,
        ];

        echo json_encode($data);

        return $this;
    }
}
