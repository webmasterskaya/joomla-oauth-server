<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * @property int $id
 * @property string $name
 * @property string $identifier
 * @property string|null $secret
 * @property int $public
 * @property string|null $redirect_uri
 * @property int $allow_plain_text_pkce
 *
 * @since version
 */
class ClientTable extends Table implements ClientEntityInterface
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

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }

    public function isConfidential()
    {
        return !$this->public;
    }
}