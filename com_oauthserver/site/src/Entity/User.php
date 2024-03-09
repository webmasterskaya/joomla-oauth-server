<?php
/**
 * @package         Joomla.Site
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Site\Entity;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

\defined('_JEXEC') or die;

class User implements UserEntityInterface
{
    use EntityTrait;

    public function __construct(\Joomla\CMS\User\User $user)
    {
        $this->setIdentifier($user->id);
    }
}
