<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Event;

use Psr\Http\Message\ServerRequestInterface;

class RequestEvent extends AbstractImmutableEvent
{
    public function __construct(string $name, array $arguments = [])
    {
        if (!array_key_exists('request', $arguments))
        {
            throw new \BadMethodCallException("Argument 'request' is required for event $name");
        }

        parent::__construct($name, ['request' => $arguments['request']]);
    }

    protected function onSetRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request;
    }
}
