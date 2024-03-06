<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Crypt\Crypt;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;

class ClientModel extends AdminModel
{
    use GetItemByIdentifierTrait;

    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  version
     */
    public $typeAlias = 'com_oauthserver.client';

    /**
     * @param array $data
     * @param bool $loadData
     * @return \Joomla\CMS\Form\Form|bool
     * @throws \Exception
     * @since version
     */
    public function getForm($data = [], $loadData = true): Form|bool
    {
        $form = $this->loadForm('com_oauthserver.client', 'client', ['control' => 'jform', 'load_data' => $loadData]);

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
        $data = Factory::getApplication()->getUserState('com_oauthserver.edit.client.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_oauthserver.client', $data);

        return $data;
    }

    public function validate($form, $data, $group = null): bool|array
    {
        unset($data['identifier'], $data['secret']);

        return parent::validate($form, $data, $group);
    }

    /**
     * @param \Webmasterskaya\Component\OauthServer\Administrator\Table\ClientTable $table
     * @return void
     * @throws \Exception
     * @since version
     */
    protected function prepareTable($table): void
    {
        $app = Factory::getApplication();
        $input = $app->getInput();
        $task = strtolower($input->getCmd('task', ''));

        if ($task === 'save2reset' || empty($table->id)) {
            $table->identifier = $this->generateNewIdentifier();
            $table->secret = !!$table->public ? '' : $this->generateNewSecret();
        }

        if (!!$table->public) {
            $table->secret = '';
        } else {
            if (empty($table->secret)) {
                $table->secret = $this->generateNewSecret();
            }
        }

        $table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);

        parent::prepareTable($table);
    }

    protected function generateNewIdentifier(): string
    {
        return hash('md5', Crypt::genRandomBytes(16));
    }

    protected function generateNewSecret(): string
    {
        return hash('sha512', Crypt::genRandomBytes(32));
    }
}