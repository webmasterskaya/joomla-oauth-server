<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Controller;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class ClientsController extends AdminController
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * @since  1.6
     */
    protected $text_prefix = 'COM_OAUTHSERVER_CLIENT';

    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel  The model.
     *
     * @since   1.6
     */
    public function getModel($name = 'Client', $prefix = 'Administrator', $config = array('ignore_request' => true)): BaseDatabaseModel
    {
        return parent::getModel($name, $prefix, $config);
    }

}