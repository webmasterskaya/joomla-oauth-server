<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\MVC\Model\ItemModelInterface;

\defined('_JEXEC') or die;

class ScopeModel extends BaseModel implements ItemModelInterface
{
    private const PREDEFINED_SCOPES = ['userinfo', 'email'];
    private static array $_storage;

    public function getItem($pk = null)
    {
        // TODO: Implement getItem() method.
    }

    private function fillStorage(): void
    {
        $config = ComponentHelper::getParams('com_oauthserver');


    }
}
