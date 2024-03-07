<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Table;

use Joomla\CMS\Event\AbstractEvent;
use Joomla\CMS\Language\Text;

trait RevokedTableTrait
{
    /**
     * @var    string
     * @since  version
     * @noinspection PhpMissingFieldTypeInspection
     */
    protected $_tbl = '';

    /**
     * @var    string
     * @since  version
     * @noinspection PhpMissingFieldTypeInspection
     */
    protected $_tbl_key = '';

    /**
     * @var    array
     * @since  version
     * @noinspection PhpMissingFieldTypeInspection
     */
    protected $_tbl_keys = [];

    /**
     * @var    \Joomla\Database\DatabaseDriver
     * @since  version
     * @noinspection PhpMissingFieldTypeInspection
     */
    protected $_db;

    abstract public function getDispatcher();

    abstract public function setError($error);

    abstract public function getColumnAlias($column);

    abstract public function getDbo();

    abstract public function appendPrimaryKeys($query, $pk = null);

    public function revoke($pks = null): bool
    {
        // Pre-processing by observers
        $event = AbstractEvent::create(
            'onTableBeforeRevoke',
            [
                'subject' => $this,
                'pks' => $pks,
            ]
        );
        $this->getDispatcher()->dispatch('onTableBeforeRevoke', $event);

        if (!\is_null($pks)) {
            if (!\is_array($pks)) {
                $pks = [$pks];
            }

            foreach ($pks as $key => $pk) {
                if (!\is_array($pk)) {
                    $pks[$key] = [$this->_tbl_key => $pk];
                }
            }
        }

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks)) {
            $pk = [];

            foreach ($this->_tbl_keys as $key) {
                if ($this->$key) {
                    $pk[$key] = $this->$key;
                } else {
                    // We don't have a full primary key - return false
                    $this->setError(Text::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

                    return false;
                }
            }

            $pks = [$pk];
        }

        $revokedField = $this->getColumnAlias('revoked');

        foreach ($pks as $pk) {
            $query = $this->_db->getQuery(true)
                ->update($this->_db->quoteName($this->_tbl))
                ->set($this->_db->quoteName($revokedField) . ' = 0');

            // Build the WHERE clause for the primary keys.
            $this->appendPrimaryKeys($query, $pk);

            $this->_db->setQuery($query);

            try {
                $this->_db->execute();
            } catch (\RuntimeException $e) {
                $this->setError($e->getMessage());

                return false;
            }

            // If the Table instance value is in the list of primary keys that were set, set the instance.
            $ours = true;

            foreach ($this->_tbl_keys as $key) {
                if ($this->$key != $pk[$key]) {
                    $ours = false;
                }
            }

            if ($ours) {
                $this->$revokedField = 0;
            }
        }

        $this->setError('');

        // Pre-processing by observers
        $event = AbstractEvent::create(
            'onTableAfterRevoke',
            [
                'subject' => $this,
                'pks' => $pks
            ]
        );
        $this->getDispatcher()->dispatch('onTableAfterRevoke', $event);

        return true;
    }
}