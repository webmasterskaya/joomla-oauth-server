<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

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