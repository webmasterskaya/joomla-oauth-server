<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

trait GetItemByIdentifierTrait
{
    abstract public function getState($property = null, $default = null);

    abstract public function getName();

    abstract public function getTable($name = '', $prefix = '', $options = []);

    public function getItemByIdentifier($identifier = null): object
    {
        $identifier = (!empty($identifier)) ? $identifier : (int)$this->getState($this->getName() . '.identifier');
        /** @var \Joomla\CMS\Table\Table $table */
        $table = $this->getTable();

        if (!empty($identifier)) {
            $return = $table->load(['identifier' => $identifier]);

            if ($return === false) {
                if (method_exists($table, 'getError') && $table->getError()) {
                    throw new \RuntimeException($table->getError());
                }
                throw new \RuntimeException(Text::_('JLIB_APPLICATION_ERROR_NOT_EXIST'));
            }
        }

        // Convert to the CMSObject before adding other data.
        $properties = $table->getProperties(true);
        $all_properties = $table->getProperties(false);

        if (!empty($all_properties['_jsonEncode'])) {
            foreach ($all_properties['$_jsonEncode'] as $prop) {
                if (array_key_exists($prop, $properties) && is_string($properties[$prop])) {
                    $properties[$prop] = json_decode($properties[$prop]);
                }
            }
        }

        return ArrayHelper::toObject($properties, CMSObject::class, true);
    }
}