<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

/**
 * @property int $id
 * @property string $identifier
 * @property \DateTimeImmutable|\DateTime|string $expiry
 * @property int|null $user_id
 * @property string|array|null $scopes
 * @property int $revoked
 * @property int $client_id
 *
 * @since version
 */
class AuthCodeTable extends Table implements RevokedTableInterface
{
    use RevokedTableTrait;

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
        parent::__construct('#__webmasterskaya_oauthserver_authorization_codes', 'id', $db);
    }
}