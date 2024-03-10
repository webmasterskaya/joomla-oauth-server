<?php
/**
 * @package         Joomla.Site
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Plugin\System\OauthServer\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\SiteRouter;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;

// phpcs:enable PSR1.Files.SideEffects

class Plugin extends CMSPlugin implements SubscriberInterface
{
	public static function getSubscribedEvents(): array
	{
		return ['onAfterInitialise' => 'attachOauthRouter'];
	}

	/**
	 * @return void
	 * @since version
	 */
	public function attachOauthRouter(): void
	{
		/** @var \Joomla\CMS\Application\SiteApplication $app */
		$app = $this->getApplication();

		if (!$app->isClient('site'))
		{
			return;
		}

		/** @var \Joomla\CMS\Router\SiteRouter $siteRouter */
		$siteRouter = Factory::getContainer()->get(SiteRouter::class);
		$siteRouter->attachParseRule([$this, 'parseOauthRoute'], $siteRouter::PROCESS_BEFORE);
	}

	/**
	 * @param   \Joomla\CMS\Router\SiteRouter  $router
	 * @param   \Joomla\CMS\Uri\Uri            $uri
	 *
	 * @return void
	 * @since version
	 */
	public function parseOauthRoute(SiteRouter &$router, Uri &$uri): void
	{
		$route = trim($uri->getPath(), '/');

		if (empty($route))
		{
			return;
		}

		if (!str_starts_with($route, 'login/oauth'))
		{
			return;
		}

		$segments = explode('/', $route);

		$uri->setVar('option', 'com_oauthserver');
		$uri->setVar('task', 'login.' . $segments[2]);
		$uri->setVar('view', 'default');

		$uri->setPath('');
	}
}