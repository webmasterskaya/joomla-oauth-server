<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

use Joomla\CMS\Layout\LayoutHelper;

\defined('_JEXEC') or die;

/**
 * @var \Webmasterskaya\Component\OauthServer\Administrator\View\Clients\HtmlView $this
 */

$displayData = [
    'textPrefix' => 'COM_OAUTHSERVER_ORDERS',
    'icon'       => 'icon-copy',
];

$user = $this->getCurrentUser();

if ($user->authorise('core.create', 'com_oauthserver')
    || count($user->getAuthorisedCategories('com_oauthserver', 'core.create')) > 0
    || $user->authorise('product.create', 'com_oauthserver')
    || count($user->getAuthorisedCategories('com_oauthserver', 'client.create')) > 0
)
{
    $displayData['createURL'] = 'index.php?option=com_oauthserver&task=client.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
