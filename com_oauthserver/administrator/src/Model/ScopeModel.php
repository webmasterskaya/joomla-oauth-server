<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\MVC\Model\ItemModelInterface;
use Joomla\CMS\MVC\Model\ListModelInterface;
use Webmasterskaya\Component\OauthServer\Administrator\ValueObject\Scope;

\defined('_JEXEC') or die;

class ScopeModel extends BaseModel implements ItemModelInterface, ListModelInterface
{
    private const PREDEFINED_SCOPES = ['userinfo', 'email'];
    private static array $_storage;

    public function getItem($pk = null)
    {
        // TODO: Implement getItem() method.
    }

    private function fillStorage(): array
    {
        if (isset(self::$_storage))
        {
            return self::$_storage;
        }

        self::$_storage = [
            'userinfo' => new Scope(
                'userinfo',
                'COM_OAUTHSERVER_SCOPE_USERINFO',
                'COM_OAUTHSERVER_SCOPE_USERINFO_DESCRIPTION'
            ),
            'email'    => new Scope(
                'email',
                'COM_OAUTHSERVER_SCOPE_EMAIL',
                'COM_OAUTHSERVER_SCOPE_EMAIL_DESCRIPTION'
            )
        ];



        return [];
    }

    public function getItems()
    {
        // TODO: Implement getItems() method.
    }
}
