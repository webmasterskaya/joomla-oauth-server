<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

/**
 * @property int $id
 * @property string $name
 * @property string $identifier
 * @property string|null $secret
 * @property bool $public
 * @property string|null $redirect_uri
 * @property bool $allow_plain_text_pkce
 *
 * @since version
 */
class ClientTable extends Table
{
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