<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * @property int $id;
 * @property string $identifier;
 * @property string $name;
 * @property ?string $secret;
 * @property string|array|null $redirect_uris;
 * @property string|array|null $grants;
 * @property string|array|null $scopes;
 * @property int $active;
 * @property int $public;
 * @property int $allow_plain_text_pkce;
 *
 * @since version
 */
class ClientTable extends Table
{
    /**
     * Indicates that columns fully support the NULL value in the database
     *
     * @var    boolean
     * @since  3.10.0
     */
    protected $_supportNullValue = true;

    /**
     * An array of key names to be json encoded in the bind function
     *
     * @var    array
     * @since  3.3
     */
    protected $_jsonEncode = ['redirect_uris', 'grants', 'scopes'];

    /**
     * Constructor.
     *
     * @param DatabaseDriver $db Database connector object
     *
     * @since  1.0.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__webmasterskaya_oauthserver_clients', 'id', $db);
    }
}