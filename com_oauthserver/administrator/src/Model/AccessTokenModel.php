<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Webmasterskaya\Component\OauthServer\Administrator\Table\AccessTokenTable;

\defined('_JEXEC') or die;

class AccessTokenModel extends AdminModel implements RevokedModelInterface
{
    use GetItemByIdentifierTrait;
    use RevokedModelTrait;

    /**
     * @inheritdoc
     * @since version
     */
    public function getForm($data = [], $loadData = true): Form|bool
    {
        $form = $this->loadForm('com_oauthserver.access_token', 'access_token', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * @inheritdoc
     * @since   version
     */
    protected function loadFormData(): mixed
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_oauthserver.edit.access_token.data', []);

        if (empty($data))
        {
            $data = $this->getItem();
        }

        $this->preprocessData('com_oauthserver.access_token', $data);

        return $data;
    }

    /**
     * @param   \Webmasterskaya\Component\OauthServer\Administrator\Table\AccessTokenTable  $table
     *
     * @return void
     * @since version
     */
    protected function prepareTable($table)
    {
        if ($table->expiry instanceof \DateTime || $table->expiry instanceof \DateTimeImmutable)
        {
            $table->expiry = $table->expiry->format($table->getDbo()->getDateFormat());
        }
    }

    /**
     * @param $name
     * @param $prefix
     * @param $options
     *
     * @return bool|Table|AccessTokenTable
     * @throws \Exception
     * @since version
     */
    public function getTable($name = 'AccessToken', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }
}
