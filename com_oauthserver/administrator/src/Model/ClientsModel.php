<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\Database\QueryInterface;

\defined('_JEXEC') or die;

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
     * @inheritDoc
     * @since version
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null)
    {
        // Add the ordering filtering fields whitelist
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = [
                'id', 'client.id'
            ];
        }

        parent::__construct($config, $factory);
    }

    /**
     * @inheritDoc
     * @since version
     */
    protected function populateState($ordering = null, $direction = null): void
    {
        // List state information
        $ordering = empty($ordering) ? 'client.id' : $ordering;

        parent::populateState($ordering, $direction);
    }

    /**
     * @inheritDoc
     * @since version
     */
    protected function getStoreId($id = ''): string
    {
        $id .= ':' . $this->getState('filter.search');

        return parent::getStoreId($id);
    }

    /**
     * @inheritDoc
     * @since version
     */
    public function getItems()
    {
        $result = parent::getItems();

        if (!$result)
        {
            return $result;
        }

        // Get a storage key.
        $store = $this->getStoreId();

        /** @var \stdClass $row */
        foreach ($result as &$row)
        {
            // Convert `public` field to bool
            $row->public = !empty($row->public);

            if (!empty($row->scopes))
            {
                $row->scopes = json_decode($row->scopes, true);
            }

            if (!empty($row->grants))
            {
                $row->grants = json_decode($row->grants, true);
            }
        }

        $this->cache[$store] = $result;

        return $this->cache[$store];
    }

    /**
     * @inheritDoc
     * @since version
     */
    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true);

        $query->select(['client.id', 'client.name', 'client.secret', 'client.identifier', 'client.public', 'client.active', 'client.scopes', 'client.grants'])
            ->from($db->qn('#__webmasterskaya_oauthserver_clients', 'client'));

        // Filter by search state
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $query->where('client.name LIKE :search')
                ->bind(':search', $search, ParameterType::STRING);
        }

        // Add the list ordering clause
        $ordering  = $this->state->get('list.ordering', 'client.id');
        $direction = $this->state->get('list.direction', 'desc');
        $query->order($db->escape($ordering) . ' ' . $db->escape($direction));

        return $query;
    }
}
