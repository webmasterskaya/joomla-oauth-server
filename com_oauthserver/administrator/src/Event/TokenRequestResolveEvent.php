<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Event;

use Joomla\CMS\Event\AbstractEvent;
use Psr\Http\Message\ResponseInterface;

class TokenRequestResolveEvent extends AbstractEvent
{
    public function __construct(string $name, array $arguments = [])
    {
        if (!array_key_exists('response', $arguments))
        {
            throw new \BadMethodCallException("Argument 'response' is required for event $name");
        }

        parent::__construct($name, $arguments);
    }

    public function onSetResponse(ResponseInterface $response): ResponseInterface
    {
        return $response;
    }
}
