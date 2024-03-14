<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Event;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;

class RequestAccessTokenEvent extends RequestEvent
{
    public function __construct(string $name, array $arguments = [])
    {
        if (!array_key_exists('accessToken', $arguments))
        {
            throw new \BadMethodCallException("Argument 'accessToken' is required for event $name");
        }

        $this->setArgument('accessToken', $arguments['accessToken']);

        parent::__construct($name, $arguments);
    }

    protected function onSetAccessToken(AccessTokenEntityInterface $accessToken): AccessTokenEntityInterface
    {
        return $accessToken;
    }
}
