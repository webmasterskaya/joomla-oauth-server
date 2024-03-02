<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

class ClientsModel extends ListModel
{
    /**
     * Model context string.
     *
     * @var  string
     *
     * @since  1.0.0
     */
    protected $context = 'com_oauthserver.clients';

    /**
     * Constructor.
     *
     * @param array $config An optional associative array of configuration settings.
     * @param MVCFactoryInterface|null $factory The factory.
     *
     * @throws \Exception
     *
     * @since  1.0.0
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null)
    {
        // Add the ordering filtering fields whitelist
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'client.id'
            ];
        }

        parent::__construct($config, $factory);
    }

    /**
     * Method to auto-populate the model state.
     *
     * @param string $ordering An optional ordering field.
     * @param string $direction An optional direction (asc|desc).
     *
     * @throws  \Exception
     *
     * @since  1.0.0
     */
    protected function populateState($ordering = null, $direction = null): void
    {
        $app = Factory::getApplication();

        // Adjust the context to support modal layouts.
        if ($layout = $app->input->get('layout')) {
            $this->context .= '.' . $layout;
        }

        // List state information
        $ordering = empty($ordering) ? 'client.id' : $ordering;

        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * @param string $id A prefix for the store id.
     *
     * @return  string  A store id.
     *
     * @since  1.0.0
     */
    protected function getStoreId($id = ''): string
    {
        $id .= ':' . $this->getState('filter.search');

        return parent::getStoreId($id);
    }

    /**
     * Method to get a DatabaseQuery object for retrieving the data set from a database.
     *
     * @return  \Joomla\Database\QueryInterface  A QueryInterface object to retrieve the data set.
     *
     * @throws  \Exception
     *
     * @since  1.0.0
     */
    protected function getListQuery(): \Joomla\Database\QueryInterface
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true);

        $query->select(['client.id', 'client.client_name', 'client.client_token', 'client.client_id',])
            ->from($db->qn('#__webmasterskaya_oauthserver_clients', 'client'));

        // Filter by search state
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $query->where('client.name LIKE :search')
                ->bind(':search', $search, ParameterType::STRING);
        }

        // Add the list ordering clause
        $ordering = $this->state->get('list.ordering', 'client.id');
        $direction = $this->state->get('list.direction', 'desc');
        $query->order($db->escape($ordering) . ' ' . $db->escape($direction));

        return $query;
    }
}