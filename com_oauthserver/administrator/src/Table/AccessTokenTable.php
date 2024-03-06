<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Table;

use DateTimeImmutable;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class AccessTokenTable extends Table
{
    /**
     * Indicates that columns fully support the NULL value in the database
     *
     * @var    boolean
     * @since  version
     */
    protected $_supportNullValue = true;

    /**
     * An array of key names to be json encoded in the bind function
     *
     * @var    array
     * @since  version
     */
    protected $_jsonEncode = ['scopes'];

    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__webmasterskaya_oauthserver_access_tokens', 'id', $db);
    }
}