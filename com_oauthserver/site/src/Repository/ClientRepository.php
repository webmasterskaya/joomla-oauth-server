<?php

namespace Webmasterskaya\Component\OauthServer\Site\Repository;

use Joomla\CMS\MVC\Factory\MVCFactoryAwareTrait;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    use MVCFactoryAwareTrait;

    public function __construct(MVCFactoryInterface $MVCFactory)
    {
        $this->setMVCFactory($MVCFactory);
    }

    /**
     * @param $clientIdentifier
     * @return \League\OAuth2\Server\Entities\ClientEntityInterface
     * @throws \Exception
     * @since version
     */
    public function getClientEntity($clientIdentifier): ClientEntityInterface
    {
        /** @var \Webmasterskaya\Component\OauthServer\Administrator\Table\ClientTable $table */
        $table = $this->getMVCFactory()->createTable('Client', 'Administrator');
        $table->load(['identifier' => $clientIdentifier]);
        return $table;
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        // TODO: Implement validateClient() method.
    }
}