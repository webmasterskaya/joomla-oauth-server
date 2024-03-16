<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Model;

use Joomla\CMS\Crypt\Crypt;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Uri\Uri;
use Webmasterskaya\Component\OauthServer\Administrator\Table\ClientTable;

\defined('_JEXEC') or die;

/**
 * @method CMSObject|bool getItem($pk = null)
 * @since version
 */
class ClientModel extends AdminModel
{
    use GetItemByIdentifierTrait;

    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  version
     */
    public $typeAlias = 'com_oauthserver.client';

    /**
     * @param   array  $data
     * @param   bool   $loadData
     *
     * @return Form|bool
     * @throws \Exception
     * @since version
     */
    public function getForm($data = [], $loadData = true): Form|bool
    {
        $form = $this->loadForm('com_oauthserver.client', 'client', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @throws \Exception
     * @since   version
     */
    protected function loadFormData(): mixed
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_oauthserver.edit.client.data', []);

        if (empty($data))
        {
            $data = $this->getItem();
        }

        if ($data)
        {
            $uri = new Uri(Uri::root());

            $data->def('authorize_url', (string) $uri->setPath('/login/oauth/authorize'));
            $data->def('token_url', (string) $uri->setPath('/login/oauth/token'));
            $data->def('profile_url', (string) $uri->setPath('/login/oauth/profile'));
        }

        $this->preprocessData('com_oauthserver.client', $data);

        return $data;
    }

    public function validate($form, $data, $group = null): bool|array
    {
        // Since the client’s identifier and secret key are created on the server and completely
        // exclude the user’s influence on their value, we remove them from the request to eliminate
        // any possibility of substitution of this data.
        unset($data['identifier'], $data['secret']);

        return parent::validate($form, $data, $group);
    }

    /**
     * @param   ClientTable  $table
     *
     * @return void
     * @throws \Exception
     * @since version
     */
    protected function prepareTable($table): void
    {
        $app = Factory::getApplication();

        if (empty($table->id))
        {
            $table->identifier = $this->generateNewIdentifier();
        }

        if (empty($table->secret)
            && !$table->public
            && ($table->id > 0 || $app->getInput()->get('task') == 'save2reset'))
        {
            $table->secret = $this->generateNewSecret();
        }

        if ($table->public)
        {
            $table->secret = '';
        }

        $table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);

        parent::prepareTable($table);
    }

    /**
     * Generate a hash value of string for table field and check it unique
     *
     * @param   string  $field
     * @param   string  $algo
     * @param   int     $length
     *
     * @return string
     * @throws \Exception
     * @since version
     */
    protected function generateNewHash(string $field, string $algo = 'sha256', int $length = 16): string
    {
        $hash  = hash($algo, Crypt::genRandomBytes($length));
        $table = $this->getTable();

        if ($table->load([$field => $hash]))
        {
            return $this->generateNewHash($field, $algo, $length);
        }

        return $hash;
    }

    /**
     * Generate unique hash value for client identifier
     *
     * @return string
     * @throws \Exception
     * @since version
     */
    protected function generateNewIdentifier(): string
    {
        return $this->generateNewHash('identifier', 'md5');
    }

    /**
     * Generate unique hash value for client secret key
     *
     * @return string
     * @throws \Exception
     * @since version
     */
    protected function generateNewSecret(): string
    {
        return $this->generateNewHash('secret', 'sha512', 32);
    }
}
