<?php

namespace Webmasterskaya\Component\OauthServer\Site\Repository;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use Wamania\Snowball\NotFoundException;
use Webmasterskaya\Component\OauthServer\Administrator\Model\AuthCodeModel;
use Webmasterskaya\Component\OauthServer\Administrator\Model\ClientModel;
use Webmasterskaya\Component\OauthServer\Site\Entity\AuthCode;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    private AuthCodeModel $authCodeModel;

    private ClientModel $clientModel;

    /**
     * @param \Webmasterskaya\Component\OauthServer\Administrator\Model\AuthCodeModel $authCodeModel
     * @param \Webmasterskaya\Component\OauthServer\Administrator\Model\ClientModel $clientModel
     * @since version
     */
    public function __construct(AuthCodeModel $authCodeModel, ClientModel $clientModel)
    {
        $this->authCodeModel = $authCodeModel;
        $this->clientModel = $clientModel;
    }

    public function getNewAuthCode(): AuthCode
    {
        return new AuthCode();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $found = false;
        try {
            $authCode = $this->authCodeModel->getItemByIdentifier($authCodeEntity->getIdentifier());

            if ($authCode->id > 0) {
                $found = true;

            }
        } catch (\Throwable $e) {
        }

        if ($found) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $data = $authCodeEntity->getData();

        $client = $this->clientModel->getItemByIdentifier($authCodeEntity->getClient()->getIdentifier());

        $data['client_id'] = $client->id;
        unset($data['client_identifier']);

        $this->authCodeModel->save($data);
    }

    public function revokeAuthCode($codeId)
    {
        $this->authCodeModel->revoke($codeId);
    }

    public function isAuthCodeRevoked($codeId)
    {
        $authCode = $this->authCodeModel->getItemByIdentifier($codeId);

        if (empty($authCode->id)) {
            return true;
        }

        return !!$authCode->revoked;
    }
}