<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;

class ClientController extends FormController
{
    /**
     * The prefix to use with controller messages.
     *
     * @var  string
     *
     * @since  1.0.0
     */
    protected $text_prefix = 'COM_OAUTHSERVER_CLIENT';

    /**
     * @param array $config
     * @param \Joomla\CMS\MVC\Factory\MVCFactoryInterface|null $factory
     * @param \Joomla\CMS\Application\CMSApplication|null $app
     * @param \Joomla\Input\Input|null $input
     * @param \Joomla\CMS\Form\FormFactoryInterface|null $formFactory
     * @throws \Exception
     * @since version
     */
    public function __construct($config = array(), MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null, FormFactoryInterface $formFactory = null)
    {
        parent::__construct($config, $factory, $app, $input, $formFactory);

        $this->registerTask('save2reset', 'save');
    }

}