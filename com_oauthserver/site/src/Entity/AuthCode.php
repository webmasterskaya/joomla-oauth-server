<?php
/**
 * @package         Joomla.Site
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Site\Entity;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

\defined('_JEXEC') or die;

class AuthCode implements AuthCodeEntityInterface
{
    use AuthCodeTrait;
    use EntityTrait;
    use TokenEntityTrait;

    public function getData(): array
    {
        return [
            'identifier'        => $this->getIdentifier(),
            'expiry'            => $this->getExpiryDateTime(),
            'user_id'           => $this->getUserIdentifier(),
            'scopes'            => $this->getScopes(),
            'client_identifier' => $this->getClient()->getIdentifier()
        ];
    }
}
