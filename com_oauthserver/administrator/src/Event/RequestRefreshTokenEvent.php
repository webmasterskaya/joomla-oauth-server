<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Event;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

class RequestRefreshTokenEvent extends AbstractImmutableEvent
{
    public function __construct(string $name, array $arguments = [])
    {
        if (!array_key_exists('refreshToken', $arguments))
        {
            throw new \BadMethodCallException("Argument 'refreshToken' is required for event $name");
        }

        $this->setArgument('refreshToken', $arguments['refreshToken']);

        parent::__construct($name, $arguments);
    }

    protected function onSetRefreshToken(RefreshTokenEntityInterface $refreshToken): RefreshTokenEntityInterface
    {
        return $refreshToken;
    }

}
