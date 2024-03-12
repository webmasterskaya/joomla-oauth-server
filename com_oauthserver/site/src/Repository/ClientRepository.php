<?php
/**
 * @package         Joomla.Site
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Site\Repository;

use Joomla\CMS\Object\CMSObject;
use Joomla\Utilities\ArrayHelper;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Webmasterskaya\Component\OauthServer\Administrator\Model\ClientModel;
use Webmasterskaya\Component\OauthServer\Site\Entity\Client;

\defined('_JEXEC') or die;

class ClientRepository implements ClientRepositoryInterface
{
    private ClientModel $clientModel;

    public function __construct(ClientModel $clientModel)
    {
        $this->clientModel = $clientModel;
    }

    /**
     * @param   string  $clientIdentifier
     *
     * @return ClientEntityInterface|null
     * @throws \Exception
     * @since version
     */
    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        $item = $this->clientModel->getItemByIdentifier($clientIdentifier);

        if ($item === false)
        {
            return null;
        }

        return $this->bind($item);
    }

    /**
     * @param $clientIdentifier
     * @param $clientSecret
     * @param $grantType
     *
     * @return bool
     * @throws \Exception
     * @since        version
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $item = $this->clientModel->getItemByIdentifier($clientIdentifier);

        if ($item === false
            || !$item->active
            || !$this->isGrantSupported($item, $grantType))
        {
            return false;
        }

        if (!!$item->public
            || hash_equals((string) $item->secret, (string) $clientSecret))
        {
            return true;
        }

        return false;
    }

    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
    private function bind(CMSObject $client): Client
    {
        $clientEntity = new Client();
        $clientEntity->setName($client->name);
        $clientEntity->setIdentifier($client->identifier);

        if (!empty($client->redirect_uris))
        {
            $redirect_uris = json_decode($client->redirect_uris, true, 3, JSON_OBJECT_AS_ARRAY);
            $redirect_uris = ArrayHelper::getColumn($redirect_uris, 'uri');
        }
        else
        {
            $redirect_uris = [];
        }
        $clientEntity->setRedirectUri($redirect_uris);

        $clientEntity->setConfidential(!$client->public);
        $clientEntity->setAllowPlainTextPkce(!!$client->allow_plain_text_pkce);

        return $clientEntity;
    }

    private function isGrantSupported(CMSObject $client, ?string $grant): bool
    {
        if (null === $grant)
        {
            return true;
        }

        if (!empty($client->grants))
        {
            $grants = json_decode($client->grants, true, 3, JSON_OBJECT_AS_ARRAY);
            $grants = ArrayHelper::getColumn($grants, 'grant');
        }
        else
        {
            $grants = [];
        }

        if (empty($grants))
        {
            return true;
        }

        return \in_array($grant, $grants);
    }
}
