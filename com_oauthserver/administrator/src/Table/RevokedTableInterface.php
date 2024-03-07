<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Table;

interface RevokedTableInterface
{
    public function revoke($pks = null): bool;
}