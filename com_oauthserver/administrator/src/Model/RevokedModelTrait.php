<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\User;

trait RevokedModelTrait
{
    /**
     * @var string
     * @since version
     * @noinspection PhpMissingFieldTypeInspection
     */
    protected $option;

    /**
     * @var string
     * @since version
     * @noinspection PhpMissingFieldTypeInspection
     */
    protected $name;

    /**
     * @var array
     * @since version
     * @noinspection PhpMissingFieldTypeInspection
     */
    protected $events_map;

    /**
     * @var string
     * @since version
     * @noinspection PhpMissingFieldTypeInspection
     */
    protected $event_before_change_state;

    /**
     * @var string
     * @since version
     * @noinspection PhpMissingFieldTypeInspection
     */
    protected $event_change_state;

    abstract public function getTable($name = '', $prefix = '', $options = []);

    abstract protected function getCurrentUser(): User;

    abstract public function setError($error);

    abstract protected function cleanCache($group = null);

    public function revoke(&$identifiers): bool
    {
        $user = $this->getCurrentUser();
        /** @var \Joomla\CMS\Table\Table $table */
        $table = $this->getTable();
        $identifiers = (array)$identifiers;
        $pks = [];

        $context = $this->option . '.' . $this->name;

        // Include the plugins for the change of state event.
        PluginHelper::importPlugin($this->events_map['change_state']);

        foreach ($identifiers as $i => $identifier) {
            $table->reset();

            if ($table->load(['identifier' => $identifier])) {
                $revokedColumnName = $table->getColumnAlias('revoked');

                if (property_exists($table, $revokedColumnName) && $table->get($revokedColumnName, 1) == 0) {
                    unset($identifiers[$i]);
                } else {
                    $pks[] = $table->get('id');
                }
            }
        }

        // Check if there are items to change
        if (!\count($pks)) {
            return true;
        }

        // Trigger the before change state event.
        $result = Factory::getApplication()->triggerEvent($this->event_before_change_state, [$context, $pks, 0]);

        if (\in_array(false, $result, true)) {
            $this->setError($table->getError());

            return false;
        }

        // Attempt to change the state of the records.
        if (!$table->revoke($pks, $user->id)) {
            $this->setError($table->getError());

            return false;
        }

        // Trigger the change state event.
        $result = Factory::getApplication()->triggerEvent($this->event_change_state, [$context, $pks, 0]);

        if (\in_array(false, $result, true)) {
            $this->setError($table->getError());

            return false;
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }
}