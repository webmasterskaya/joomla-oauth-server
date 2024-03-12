<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

\defined('_JEXEC') or die;

trait GetItemByIdentifierTrait
{
    /**
     * @param $identifier
     *
     * @return CMSObject|bool
     * @throws \Exception
     * @since version
     */
    public function getItemByIdentifier($identifier): object|bool
    {
        /** @var Table $table */
        $table = $this->getTable();

        $return = $table->load(['identifier' => $identifier]);

        if ($return === false)
        {
            // If there was no underlying error, then the false means there simply was not a row in the db for this $pk.
            if (!$table->getError())
            {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_NOT_EXIST'));
            }
            else
            {
                $this->setError($table->getError());
            }

            return false;
        }

        // Convert to the CMSObject before adding other data.
        $properties = $table->getProperties(1);
        $item       = ArrayHelper::toObject($properties, CMSObject::class);

        if (property_exists($item, 'params'))
        {
            $registry     = new Registry($item->params);
            $item->params = $registry->toArray();
        }

        return $item;
    }

    abstract public function getTable($name = '', $prefix = '', $options = []);
}
