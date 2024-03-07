<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;

class AccessTokenModel extends AdminModel implements RevokedModelInterface
{
    use GetItemByIdentifierTrait;
    use RevokedModelTrait;

    public function getForm($data = [], $loadData = true): Form|bool
    {
        $form = $this->loadForm('com_oauthserver.access_token', 'access_token', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @throws \Exception
     * @since   version
     */
    protected function loadFormData(): mixed
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_oauthserver.edit.access_token.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_oauthserver.access_token', $data);

        return $data;
    }

    /**
     * @param \Webmasterskaya\Component\OauthServer\Administrator\Table\AccessTokenTable $table
     * @return void
     * @since version
     */
    protected function prepareTable($table)
    {
        if ($table->expiry instanceof \DateTime || $table->expiry instanceof \DateTimeImmutable) {
            $table->expiry = $table->expiry->format($table->getDbo()->getDateFormat());
        }
    }

    public function getTable($name = 'AccessToken', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }
}