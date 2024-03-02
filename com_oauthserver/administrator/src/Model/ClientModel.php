<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;

class ClientModel extends AdminModel
{
    /**
     * Model context string.
     *
     * @var  string
     *
     * @since  1.0.0
     */
    protected string $context = 'com_oauthserver.client';

    /**
     * Client item.
     *
     * @var  array|null
     *
     * @since  1.0.0
     */
    protected ?array $_item = null;

    /**
     * @param array $data
     * @param bool $loadData
     * @return \Joomla\CMS\Form\Form|bool
     * @throws \Exception
     * @since version
     */
    public function getForm($data = [], $loadData = true): Form|bool
    {
        return $this->loadForm('com_oauthserver.client', 'client',
            ['control' => 'jform', 'load_data' => $loadData]);
    }
}