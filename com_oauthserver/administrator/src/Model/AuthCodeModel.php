<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\MVC\Factory\LegacyFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\AdminModel;

class AuthCodeModel extends AdminModel implements RevokedModelInterface
{
    use GetItemByIdentifierTrait;
    use RevokedModelTrait;

    public function __construct($config = [], MVCFactoryInterface $factory = null, FormFactoryInterface $formFactory = null)
    {
        $this->name = 'AuthCode';
        parent::__construct($config, $factory, $formFactory);
    }

    public function getTable($name = 'AuthCode', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getForm($data = [], $loadData = true): Form|bool
    {
        $form = $this->loadForm('com_oauthserver.auth_code', 'auth_code', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData(): mixed
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_oauthserver.edit.auth_code.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_oauthserver.auth_code', $data);

        return $data;
    }

    /**
     * @param \Webmasterskaya\Component\OauthServer\Administrator\Table\AuthCodeTable $table
     * @return void
     * @since version
     */
    protected function prepareTable($table)
    {
        if ($table->expiry instanceof \DateTime || $table->expiry instanceof \DateTimeImmutable) {
            $table->expiry = $table->expiry->format($table->getDbo()->getDateFormat());
        }
    }
}