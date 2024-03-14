<?php
/**
 * @package         Joomla.Site
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Site\Repository;

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\DispatcherAwareInterface;
use Joomla\Event\DispatcherAwareTrait;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Webmasterskaya\Component\OauthServer\Administrator\Event\ScopeResolveEvent;
use Webmasterskaya\Component\OauthServer\Administrator\Model\ClientModel;
use Webmasterskaya\Component\OauthServer\Site\Entity\Scope;

\defined('_JEXEC') or die;

class ScopeRepository implements ScopeRepositoryInterface, DispatcherAwareInterface
{
    use DispatcherAwareTrait;

    private ClientModel $clientModel;

    public function __construct(ClientModel $clientModel)
    {
        $this->clientModel = $clientModel;
    }

    public function getScopeEntityByIdentifier($identifier): ?Scope
    {
        $defined = ['userinfo', 'email'];

        if (!in_array($identifier, $defined))
        {
            return null;
        }

        $scope = new Scope();
        $scope->setIdentifier($identifier);

        return $scope;
    }

    /**
     * @param   Scope[]                $scopes
     * @param   string                 $grantType
     * @param   ClientEntityInterface  $clientEntity
     * @param   null                   $userIdentifier
     *
     * @return mixed
     * @throws OAuthServerException
     * @throws \Exception
     * @since version
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null): array
    {
        $client = $this->clientModel->getItemByIdentifier($clientEntity->getIdentifier());

        if ($client === false)
        {
            throw new \RuntimeException($this->clientModel->getError());
        }

        $scopes = $this->setupScopes($client, array_values($scopes));

        PluginHelper::importPlugin('oauthserver');

        $event = new ScopeResolveEvent(
            'onScopeResolve',
            [
                'scopes' => $scopes,
                'grant' => $grantType,
                'client' => $client,
                'userId' => $userIdentifier
            ]
        );

        return $this->getDispatcher()
            ->dispatch($event->getName(), $event)
            ->getArgument('scopes', []);
    }

    /**
     * @param   object  $client
     * @param   array   $requestedScopes
     *
     * @return array
     * @throws OAuthServerException
     * @since version
     */
    private function setupScopes(object $client, array $requestedScopes): array
    {
        $clientScopes = $client->scopes;

        if (empty($clientScopes))
        {
            return $requestedScopes;
        }

        $clientScopes = array_map(function ($item) {
            $scope = new Scope();
            $scope->setIdentifier((string) $item);

            return $scope;
        }, $clientScopes);

        if (empty($requestedScopes))
        {
            return $clientScopes;
        }

        $finalizedScopes       = [];
        $clientScopesAsStrings = array_map('strval', $clientScopes);

        foreach ($requestedScopes as $requestedScope)
        {
            $requestedScopeAsString = (string) $requestedScope;
            if (!\in_array($requestedScopeAsString, $clientScopesAsStrings, true))
            {
                throw OAuthServerException::invalidScope($requestedScopeAsString);
            }

            $finalizedScopes[] = $requestedScope;
        }

        return $finalizedScopes;
    }
}
