<?php

namespace Webmasterskaya\Component\OauthServer\Site\Entity;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

class User implements UserEntityInterface
{
    use EntityTrait;

    public function __construct(\Joomla\CMS\User\User $user)
    {
        $this->setIdentifier($user->id);
    }
}