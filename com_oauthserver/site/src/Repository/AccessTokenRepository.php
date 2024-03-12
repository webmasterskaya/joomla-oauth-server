<?php
/**
 * @package         Joomla.Site
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Site\Repository;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Webmasterskaya\Component\OauthServer\Administrator\Model\AccessTokenModel;
use Webmasterskaya\Component\OauthServer\Administrator\Model\ClientModel;
use Webmasterskaya\Component\OauthServer\Site\Entity\AccessToken;

\defined('_JEXEC') or die;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    private AccessTokenModel $accessTokenModel;

    private ClientModel $clientModel;

    public function __construct(AccessTokenModel $accessTokenModel, ClientModel $clientModel)
    {
        $this->accessTokenModel = $accessTokenModel;
        $this->clientModel      = $clientModel;
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
    {
        $accessToken = new AccessToken();
        $accessToken->setClient($clientEntity);
        $accessToken->setUserIdentifier($userIdentifier);

        foreach ($scopes as $scope)
        {
            $accessToken->addScope($scope);
        }

        return $accessToken;
    }

    /**
     * @param   AccessToken  $accessTokenEntity
     *
     * @return void
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws \Exception
     * @since version
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $accessToken = $this->accessTokenModel->getItemByIdentifier($accessTokenEntity->getIdentifier());

        if ($accessToken !== false)
        {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $data = $accessTokenEntity->getData();

        $client = $this->clientModel->getItemByIdentifier($data['client_identifier']);

        if ($client === false)
        {
            throw new \RuntimeException($this->clientModel->getError());
        }

        $data['client_id'] = $client->id;
        unset($data['client_identifier']);

        $this->accessTokenModel->save($data);
    }

    public function revokeAccessToken($tokenId): void
    {
        $this->accessTokenModel->revoke($tokenId);
    }

    /**
     * @param   string  $tokenId
     *
     * @throws \Exception
     * @since version
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function isAccessTokenRevoked($tokenId): bool
    {
        $accessToken = $this->accessTokenModel->getItemByIdentifier($tokenId);

        if ($accessToken === false)
        {
            return true;
        }

        return (bool) $accessToken->revoked;
    }
}
