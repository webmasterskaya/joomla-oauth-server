<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

\defined('_JEXEC') or die;

/**
 * @property int          $id
 * @property string       $identifier
 * @property string       $name
 * @property string|null  $secret
 * @property string|array $redirect_uris
 * @property string|array $grants
 * @property string|array $scopes
 * @property int          $active
 * @property int          $public
 * @property int          $allow_plain_text_pkce
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
     * @param   DatabaseDriver  $db  Database connector object
     *
     * @since  1.0.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__webmasterskaya_oauthserver_clients', 'id', $db);

        $this->setColumnAlias('published', 'active');
    }
}
