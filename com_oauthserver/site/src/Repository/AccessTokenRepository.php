<?php

namespace Webmasterskaya\Component\OauthServer\Site\Repository;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Webmasterskaya\Component\OauthServer\Administrator\Model\AccessTokenModel;
use Webmasterskaya\Component\OauthServer\Administrator\Model\ClientModel;
use Webmasterskaya\Component\OauthServer\Site\Entity\AccessToken;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    private AccessTokenModel $accessTokenModel;
    private ClientModel $clientModel;

    /**
     * @param \Webmasterskaya\Component\OauthServer\Administrator\Model\AccessTokenModel $accessTokenModel
     * @param \Webmasterskaya\Component\OauthServer\Administrator\Model\ClientModel $clientModel
     *
     * @since version
     */
    public function __construct(AccessTokenModel $accessTokenModel, ClientModel $clientModel)
    {
        $this->accessTokenModel = $accessTokenModel;
        $this->clientModel = $clientModel;
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
    {
        $accessToken = new AccessToken();
        $accessToken->setClient($clientEntity);
        $accessToken->setUserIdentifier($userIdentifier);

        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $found = false;
        try {
            /** @var AccessToken $accessTokenEntity */
            $accessToken = $this->accessTokenModel->getItemByIdentifier($accessTokenEntity->getIdentifier());
            if ($accessToken->id > 0) {
                $found = true;
            }
        } catch (\Throwable $e) {
        }

        if ($found) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        /** @var AccessToken $accessTokenEntity */
        $data = $accessTokenEntity->getData();

        $client = $this->clientModel->getItemByIdentifier($accessTokenEntity->getClient()->getIdentifier());

        $data['client_id'] = $client->id;
        unset($data['client_identifier']);

        $this->accessTokenModel->save($data);
    }

    public function revokeAccessToken($tokenId): void
    {
        $this->accessTokenModel->revoke($tokenId);
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        $accessToken = $this->accessTokenModel->getItemByIdentifier($tokenId);

        if (!$accessToken->id) {
            return true;
        }

        return !!$accessToken->revoked;
    }
}