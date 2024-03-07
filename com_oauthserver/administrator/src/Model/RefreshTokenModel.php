<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;

class RefreshTokenModel extends AdminModel implements RevokedModelInterface
{
    use GetItemByIdentifierTrait;
    use RevokedModelTrait;

    public function getForm($data = [], $loadData = true): Form|bool
    {
        $form = $this->loadForm('com_oauthserver.refresh_token', 'refresh_token', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData(): mixed
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_oauthserver.edit.refresh_token.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_oauthserver.refresh_token', $data);

        return $data;
    }
}