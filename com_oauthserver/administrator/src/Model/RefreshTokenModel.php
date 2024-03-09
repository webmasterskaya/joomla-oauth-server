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
use Webmasterskaya\Component\OauthServer\Administrator\Table\RefreshTokenTable;

\defined('_JEXEC') or die;

class RefreshTokenModel extends AdminModel implements RevokedModelInterface
{
    use GetItemByIdentifierTrait;
    use RevokedModelTrait;

    public function getForm($data = [], $loadData = true): Form|bool
    {
        $form = $this->loadForm('com_oauthserver.refresh_token', 'refresh_token', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    public function getTable($name = 'RefreshToken', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    protected function loadFormData(): mixed
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_oauthserver.edit.refresh_token.data', []);

        if (empty($data))
        {
            $data = $this->getItem();
        }

        $this->preprocessData('com_oauthserver.refresh_token', $data);

        return $data;
    }

    /**
     * @param   RefreshTokenTable  $table
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
}
