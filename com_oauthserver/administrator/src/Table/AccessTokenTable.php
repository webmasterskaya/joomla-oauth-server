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
 * @property int                       $id
 * @property string                    $identifier
 * @property \DateTimeInterface|string $expiry
 * @property int|null                  $userId
 * @property string|array              $scopes
 * @property int                       $clientId
 * @property bool|int                  $revoked
 *
 * @since version
 */
class AccessTokenTable extends Table implements RevokedTableInterface
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
        parent::__construct('#__webmasterskaya_oauthserver_access_tokens', 'id', $db);

        $this->setColumnAlias('client_id', 'clientId');
        $this->setColumnAlias('user_id', 'userId');
    }
}
