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
 * @property int                       $revoked
 * @property int|null                  $access_token_id
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
