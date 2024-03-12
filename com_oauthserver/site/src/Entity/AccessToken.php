<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license     MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Site\Entity;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

\defined('_JEXEC') or die;

class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait;
    use EntityTrait;
    use TokenEntityTrait;

    public function getData(): array
    {
        $scopes = array_map(fn($scope) => ['scope' => (string) $scope], $this->getScopes());

        return [
            'identifier'        => $this->getIdentifier(),
            'expiry'            => $this->getExpiryDateTime(),
            'user_id'           => $this->getUserIdentifier(),
            'scopes'            => $scopes,
            'client_identifier' => $this->getClient()->getIdentifier()
        ];
    }
}
