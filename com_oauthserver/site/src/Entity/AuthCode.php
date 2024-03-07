<?php

namespace Webmasterskaya\Component\OauthServer\Site\Entity;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

class AuthCode implements AuthCodeEntityInterface
{
    use AuthCodeTrait;
    use EntityTrait;
    use TokenEntityTrait;

    public function getData(): array
    {
        return [
            'identifier' => $this->getIdentifier(),
            'expiry' => $this->getExpiryDateTime(),
            'user_id' => $this->getUserIdentifier(),
            'scopes' => $this->getScopes(),
            'client_identifier' => $this->getClient()->getIdentifier()
        ];
    }
}