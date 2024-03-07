<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Table;

use DateTimeImmutable;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

/**
 * @property int $id
 * @property string $identifier
 * @property \DateTimeImmutable|\DateTime|string $expiry
 * @property int $revoked
 * @property int|null $access_token_id
 *
 * @since version
 */
class RefreshTokenTable extends Table implements RevokedTableInterface
{
    use RevokedTableTrait;

    /**
     * Indicates that columns fully support the NULL value in the database
     *
     * @var    boolean
     * @since  version
     */
    protected $_supportNullValue = true;

    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__webmasterskaya_oauthserver_refresh_tokens', 'id', $db);
    }
}