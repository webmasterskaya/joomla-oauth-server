<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\MVC\Model\ItemModelInterface;

class ScopeModel extends BaseModel implements ItemModelInterface
{
    private static array $_storage;

    private const PREDEFINED_SCOPES = ['userinfo', 'email'];

    public function getItem($pk = null)
    {
        // TODO: Implement getItem() method.
    }

    private function fillStorage(): void
    {
        $config = ComponentHelper::getParams('com_oauthserver');


    }
}