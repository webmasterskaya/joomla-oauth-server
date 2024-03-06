<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

interface RevokedModelInterface
{
    public function revoke(&$identifiers): bool;
}