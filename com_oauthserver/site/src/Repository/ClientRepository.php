<?php

namespace Webmasterskaya\Component\OauthServer\Site\Repository;

use Joomla\CMS\Object\CMSObject;
use Joomla\Utilities\ArrayHelper;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Webmasterskaya\Component\OauthServer\Administrator\Model\ClientModel;
use Webmasterskaya\Component\OauthServer\Site\Entity\Client;

class ClientRepository implements ClientRepositoryInterface
{
    private ClientModel $clientModel;

    public function __construct(ClientModel $clientModel)
    {
        $this->clientModel = $clientModel;
    }

    /**
     * @param $clientIdentifier
     * @return \League\OAuth2\Server\Entities\ClientEntityInterface|null
     * @throws \Exception
     * @since version
     */
    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        $item = $this->clientModel->getItemByIdentifier($clientIdentifier);

        if (empty($item->id)) {
            return null;
        }

        return $this->buildClientEntity($item);
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $item = $this->clientModel->getItemByIdentifier($clientIdentifier);

        if (empty($item->id)) {
            return false;
        }

        if (!$item->active) {
            return false;
        }

        if (!$this->isGrantSupported($item, $grantType)) {
            return false;
        }

        if (!!$item->public || hash_equals((string)$item->secret, (string)$clientSecret)) {
            return true;
        }

        return false;
    }

    private function buildClientEntity(\stdClass|CMSObject $client): Client
    {
        $clientEntity = new Client();
        $clientEntity->setName($client->name);
        $clientEntity->setIdentifier($client->identifier);
        $clientEntity->setRedirectUri(ArrayHelper::getColumn((array)$client->redirect_uris, 'uri'));
        $clientEntity->setConfidential(!$client->public);
        $clientEntity->setAllowPlainTextPkce((bool)$client->allow_plain_text_pkce);

        return $clientEntity;
    }

    private function isGrantSupported(\stdClass|CMSObject $client, ?string $grant): bool
    {
        if (null === $grant) {
            return true;
        }

        $grants = array_map('strval', (array)$client->grants);

        if (empty($grants)) {
            return true;
        }

        return \in_array($grant, $grants);
    }
}